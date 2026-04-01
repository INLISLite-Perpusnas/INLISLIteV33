<?php

namespace Banner\Controllers;



class Banner extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $bannerModel;
    public $uploadPath;
    public $modulePath;
    public $db;

    function __construct()
    {
        $this->bannerModel = new \Banner\Models\BannerModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/banner/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }

        helper('adminigniter');
        helper('thumbnail');
        helper('reference');
    }

    public function index()
    {
        $this->data['title'] = ' Banner';
        echo view('Banner\Views\list', $this->data);
    }

    public function detail(int $id)
    {
        if (!$id) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
            $this->session->setFlashdata('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('/home');
        }

        $banner = $this->bannerModel->find($id);
        $this->data['title'] = 'Banner - Detail';
        $this->data['banner'] = $banner;
        echo view('Banner\Views\view', $this->data);
    }

    public function create()
    {
        $this->data['title'] = 'Tambah Banner';
        $slug = $this->request->getGet('slug');

        // 1. Set Validasi
        $this->validation->setRule('title', 'Judul Banner', 'required');
        
        // Validasi File Upload
        $this->validation->setRule('file_cover', 'File Banner', [
            'uploaded[file_cover]', // Wajib upload
            'is_image[file_cover]',
            'mime_in[file_cover,image/jpg,image/jpeg,image/png]',
            'max_size[file_cover,2048]', // Max 2MB
        ]);

        if (
            $this->request->getPost() &&
            $this->validation->withRequest($this->request)->run()
        ) {
            $db = \Config\Database::connect();
            $db->transStart();

            // Geser sort
            $this->bannerModel
                ->where('sort >=', 1)
                ->set('sort', 'sort + 1', false)
                ->update();

            $title_slug = url_title($this->request->getPost('title'), '-', true);
            
            $save_data = [
                'title'       => $this->request->getPost('title'),
                'slug'        => $title_slug,
                'category'    => $this->request->getPost('category'),
                'sort'        => 1,
                'description' => $this->request->getPost('description'),
                'created_by'  => user_id(),
            ];

            // --- Logic Upload Baru (Direct Upload) ---
            try {
                $file = $this->request->getFile('file_cover');

                if ($file->isValid() && !$file->hasMoved()) {
                    // Generate nama file baru
                    $newFileName = date('Ymd') . '_' . $file->getRandomName();
                    
                    // Pindahkan file ke folder tujuan
                    $file->move($this->modulePath, $newFileName);

                    // Buat Thumbnail
                    create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);

                    // Simpan nama file ke array data
                    $save_data['file_cover'] = $newFileName;
                }

                // Logic Meta Data (Jika Ada)
                if (is_member('admin')) {
                    $index_arr = $this->request->getPost('index');
                    if (!empty($index_arr)) {
                        $meta = array();
                        foreach ($index_arr as $index => $value) {
                            $k = $this->request->getPost('key')[$value] ?? '';
                            $v = $this->request->getPost('value')[$value] ?? '';
                            if ($k != '') {
                                $meta[] = ['key' => $k, 'value' => $v];
                            }
                        }
                        if (!empty($meta)) {
                            $save_data['meta'] = json_encode($meta);
                        }
                    }
                }

                // Insert Database
                $this->bannerModel->insert($save_data);
                $db->transComplete();

                if ($db->transStatus() === false) {
                    // Rollback & Error Handling
                    $dbError = $db->error();
                    if (!empty($dbError['message'])) {
                        log_message('error', 'DB Error: ' . $dbError['message']);
                    }
                    $this->session->setFlashdata('swal_icon', 'warning');
                    $this->session->setFlashdata('swal_title', 'Peringatan');
                    $this->session->setFlashdata('swal_text', 'Gagal menambah banner (DB Error)');
                    return redirect()->back()->withInput();
                } else {
                    $this->session->setFlashdata('swal_icon', 'success');
                    $this->session->setFlashdata('swal_title', 'Berhasil');
                    $this->session->setFlashdata('swal_text', 'Banner berhasil ditambah');
                    return redirect()->to('/cms/banner');
                }

            } catch (\Exception $e) {
                $db->transRollback();
                $this->session->setFlashdata('swal_icon', 'error');
                $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
                $this->session->setFlashdata('swal_text', 'Upload Error: ' . $e->getMessage());
                return redirect()->back()->withInput();
            }

        } else {
            // Tampilkan Form & SweetAlert Error Validasi
            if ($this->request->getPost() && $this->validation->getErrors()) {
                $error_msg = '<ul>';
                foreach ($this->validation->getErrors() as $error) {
                    $error_msg .= '<li>' . esc($error) . '</li>';
                }
                $error_msg .= '</ul>';

                $this->session->setFlashdata('swal_icon', 'error');
                $this->session->setFlashdata('swal_title', 'Validasi Gagal');
                $this->session->setFlashdata('swal_html', $error_msg); 
            }

            $this->data['redirect'] = base_url('banner/create');
            $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
            echo view('Banner\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        // 1. Cek Data
        if (!$id) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
            $this->session->setFlashdata('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('/cms/banner');
        }

        $banner = $this->bannerModel->find($id);
        if (!$banner) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Tidak Ditemukan');
            $this->session->setFlashdata('swal_text', 'Banner tidak ditemukan');
            return redirect()->to('/cms/banner');
        }

        $this->data['title'] = 'Ubah Banner';
        $this->data['banner'] = $banner;

        // 2. Set Validasi
        $this->validation->setRule('title', 'Judul Banner', 'required');
        $this->validation->setRule('sort', 'Urutan', 'required|is_natural_no_zero');
        
        // Validasi File
        $this->validation->setRule('file_cover', 'File Banner', [
            'permit_empty',
            'is_image[file_cover]',
            'mime_in[file_cover,image/jpg,image/jpeg,image/png]',
            'max_size[file_cover,2048]',
        ]);

        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                
                $db = \Config\Database::connect();
                $db->transStart();

                $title_slug = url_title($this->request->getPost('title'), '-', TRUE);
                
                $update_data = [
                    'title'       => $this->request->getPost('title'),
                    'slug'        => $this->request->getPost('slug') ?? $title_slug,
                    'category'    => $this->request->getPost('category'),
                    'sort'        => $this->request->getPost('sort'),
                    'description' => $this->request->getPost('description'),
                    'updated_by'  => user_id(),
                ];

                // A. Logic Sorting
                $oldSort = is_object($banner) ? $banner->sort : $banner['sort'];
                $newSort = (int)$this->request->getPost('sort');
                if ($newSort != $oldSort) {
                    if ($newSort < $oldSort) {
                        $this->bannerModel
                            ->where('sort >=', $newSort)
                            ->where('sort <', $oldSort)
                            ->set('sort', 'sort + 1', false)
                            ->update();
                    } elseif ($newSort > $oldSort) {
                        $this->bannerModel
                            ->where('sort <=', $newSort)
                            ->where('sort >', $oldSort)
                            ->set('sort', 'sort - 1', false)
                            ->update();
                    }
                }

                // B. Logic Upload File Baru
                try {
                    $file = $this->request->getFile('file_cover');
                    $oldCover = is_object($banner) ? ($banner->file_cover ?? '') : ($banner['file_cover'] ?? '');

                    if ($file && $file->isValid() && !$file->hasMoved()) {
                        $newFileName = date('Ymd') . '_' . $file->getRandomName();
                        
                        $file->move($this->modulePath, $newFileName);
                        create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);

                        $update_data['file_cover'] = $newFileName;

                        if (!empty($oldCover) && file_exists($this->modulePath . $oldCover)) {
                            unlink($this->modulePath . $oldCover);
                        }
                        if (!empty($oldCover) && file_exists($this->modulePath . 'thumb_' . $oldCover)) {
                            unlink($this->modulePath . 'thumb_' . $oldCover);
                        }
                    }

                    // C. Meta Data Logic
                    if (is_member('admin')) {
                        $index_arr = $this->request->getPost('index');
                        if (!empty($index_arr)) {
                            $meta = array();
                            foreach ($index_arr as $index => $value) {
                                $k = $this->request->getPost('key')[$value] ?? '';
                                $v = $this->request->getPost('value')[$value] ?? '';
                                if ($k != '') {
                                    $meta[] = ['key' => $k, 'value' => $v];
                                }
                            }
                            if (!empty($meta)) {
                                $update_data['meta'] = json_encode($meta);
                            }
                        }
                    }

                    $this->bannerModel->update($id, $update_data);
                    $db->transComplete();

                    if ($db->transStatus() === false) {
                        $this->session->setFlashdata('swal_icon', 'warning');
                        $this->session->setFlashdata('swal_title', 'Peringatan');
                        $this->session->setFlashdata('swal_text', 'Banner gagal diubah (Database Error)');
                        return redirect()->back()->withInput();
                    } else {
                        $this->session->setFlashdata('swal_icon', 'success');
                        $this->session->setFlashdata('swal_title', 'Berhasil');
                        $this->session->setFlashdata('swal_text', 'Banner berhasil diubah');
                        return redirect()->to('/cms/banner');
                    }

                } catch (\Exception $e) {
                    $db->transRollback();
                    $this->session->setFlashdata('swal_icon', 'error');
                    $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
                    $this->session->setFlashdata('swal_text', 'Error: ' . $e->getMessage());
                    return redirect()->back()->withInput();
                }
            } else {
                // Tampilkan SweetAlert jika validasi edit gagal
                $error_msg = '<ul>';
                foreach ($this->validation->getErrors() as $error) {
                    $error_msg .= '<li>' . esc($error) . '</li>';
                }
                $error_msg .= '</ul>';

                $this->session->setFlashdata('swal_icon', 'error');
                $this->session->setFlashdata('swal_title', 'Validasi Gagal');
                $this->session->setFlashdata('swal_html', $error_msg);
            }
        }

        $this->data['redirect'] = base_url('cms/banner/edit/' . $id);
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        
        echo view('Banner\Views\update', $this->data);
    }
    
    public function delete(int $id = 0)
    {
        if (!$id) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
            $this->session->setFlashdata('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('/dashboard');
        }

        $banner = $this->bannerModel->find($id);
        
        if ($banner) {
            $bannerDelete = $this->bannerModel->delete($id);
            if ($bannerDelete) {
                // Mengambil nilai cover lama dengan aman (antisipasi object atau array)
                $coverName = is_object($banner) ? ($banner->file_cover ?? '') : ($banner['file_cover'] ?? '');
                
                if (!empty($coverName)) {
                    unlink_file($this->modulePath, $coverName);
                    unlink_file($this->modulePath, 'thumb_' . $coverName);
                }
                
                // Jika ada file PDF (Sesuai kode awal Anda)
                $pdfName = is_object($banner) ? ($banner->file_pdf ?? '') : ($banner['file_pdf'] ?? '');
                if (!empty($pdfName)) {
                    unlink_file($this->modulePath, $pdfName);
                }

                $this->session->setFlashdata('swal_icon', 'success');
                $this->session->setFlashdata('swal_title', 'Berhasil');
                $this->session->setFlashdata('swal_text', 'Banner berhasil dihapus');
                return redirect()->to('/cms/banner');
            } else {
                $this->session->setFlashdata('swal_icon', 'warning');
                $this->session->setFlashdata('swal_title', 'Peringatan');
                $this->session->setFlashdata('swal_text', 'Banner gagal dihapus');
                return redirect()->to('/cms/banner');
            }
        } else {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Tidak Ditemukan');
            $this->session->setFlashdata('swal_text', 'Banner tidak ditemukan');
            return redirect()->to('/cms/banner');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $bannerUpdate = $this->bannerModel->update($id, array($field => $value));

        if ($bannerUpdate) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Status Banner berhasil diubah');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Status Banner gagal diubah');
        }
        return redirect()->to('/cms/banner');
    }

    public function thumb()
    {
        $from = $this->request->getGet('from');
        $to = $this->request->getGet('to');

        for ($i = $from; $i <= $to; $i++) {
            $banner = $this->bannerModel->find($i);
            if ($banner) {
                $newFileName = is_object($banner) ? ($banner->file_cover ?? '') : ($banner['file_cover'] ?? '');
                
                if (!empty($newFileName) && !file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                    create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                    echo "success generate thumbnail for ID: " . $i . " <br>";
                } else {
                    echo "already exist or missing file, failed generate thumbnail for ID: " . $i . " <br>";
                }
            }
        }
    }
}