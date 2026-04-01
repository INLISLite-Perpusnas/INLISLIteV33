<?php

namespace Berita\Controllers;



class Berita extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $beritaModel;
    public $uploadPath;
    public $modulePath;
    public $db;

    function __construct()
    {
        $this->language = \Config\Services::language();
        $this->language->setLocale('id');

        $this->beritaModel = new \Berita\Models\BeritaModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/berita/';

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
        $this->data['title'] = ' Berita';
        $this->data['message'] = $this->validation->getErrors()
            ? $this->validation->listErrors()
            : $this->session->getFlashdata('message');
        echo view('Berita\Views\list', $this->data);
    }

    public function detail(int $id)
    {
        if (!$id) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
            $this->session->setFlashdata('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('/home');
        }

        $berita = $this->beritaModel->find($id);
        $this->data['title'] = 'Berita - Detail';
        $this->data['berita'] = $berita;
        echo view('Berita\Views\view', $this->data);
    }

    public function create()
    {
        $this->data['title'] = 'Tambah Berita';
        $slug = $this->request->getGet('slug');

        $this->validation->setRules([
            'title' => [
                'label' => 'Judul Berita',
                'rules' => 'required',
                'errors' => [
                    'required' => 'Judul Berita wajib diisi.',
                ]
            ],
            'file_cover' => [
                'label' => 'File Cover',
                'rules' => 'permit_empty|max_size[file_cover,2048]|is_image[file_cover]',
                'errors' => [
                    'max_size' => 'Ukuran file cover maksimal 2MB.',
                    'is_image' => 'File cover harus berupa gambar (jpg, png, gif).',
                ]
            ],
            'file_image.*' => [
                'label' => 'File Image',
                'rules' => 'permit_empty|max_size[file_image,2048]|is_image[file_image]',
                'errors' => [
                    'max_size' => 'Ukuran salah satu file image maksimal 2MB.',
                    'is_image' => 'Salah satu file image harus berupa gambar.',
                ]
            ]
        ]);

        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $db = \Config\Database::connect();
            $db->transStart();

            // Update sorting lama
            $this->beritaModel
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
                'content'     => $this->request->getPost('content'),
                'created_at'  => date('Y-m-d H:i:s'),
                'created_by'  => user_id(),
            ];

            try {
                // --- A. PROSES UPLOAD COVER (Single) ---
                $fileCover = $this->request->getFile('file_cover');

                if ($fileCover->isValid() && !$fileCover->hasMoved()) {
                    $coverName = date('Ymd') . '_' . $fileCover->getRandomName();
                    $fileCover->move($this->modulePath, $coverName);
                    create_thumbnail($this->modulePath, $coverName, 'thumb_', 250);
                    $save_data['file_cover'] = $coverName;
                }

                // --- B. PROSES UPLOAD IMAGES (Multiple) ---
                $filesImage = $this->request->getFileMultiple('file_image');
                $uploadedImages = [];

                if ($filesImage) {
                    foreach ($filesImage as $img) {
                        if ($img->isValid() && !$img->hasMoved()) {
                            $imgName = date('Ymd') . '_' . $img->getRandomName();
                            $img->move($this->modulePath, $imgName);
                            $uploadedImages[] = $imgName;
                        }
                    }
                }

                if (!empty($uploadedImages)) {
                    $save_data['file_image'] = implode(',', $uploadedImages);
                }

                // Insert Database
                $this->beritaModel->insert($save_data);
                $db->transComplete();

                if ($db->transStatus() === false) {
                    $this->session->setFlashdata('swal_icon', 'warning');
                    $this->session->setFlashdata('swal_title', 'Peringatan');
                    $this->session->setFlashdata('swal_text', 'Gagal menambah berita (Database Error)');
                    return redirect()->back()->withInput();
                } else {
                    $this->session->setFlashdata('swal_icon', 'success');
                    $this->session->setFlashdata('swal_title', 'Berhasil');
                    $this->session->setFlashdata('swal_text', 'Berita berhasil ditambah');
                    return redirect()->to('/cms/berita');
                }

            } catch (\Exception $e) {
                $db->transRollback();
                $this->session->setFlashdata('swal_icon', 'error');
                $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
                $this->session->setFlashdata('swal_text', $e->getMessage());
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

            $this->data['redirect'] = base_url('berita/create');
            echo view('Berita\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        // 1. Cek ID
        if (!$id) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
            $this->session->setFlashdata('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('/cms/berita');
        }

        $berita = $this->beritaModel->find($id);
        if (!$berita) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Tidak Ditemukan');
            $this->session->setFlashdata('swal_text', 'Berita tidak ditemukan');
            return redirect()->to('/cms/berita');
        }

        $this->data['title'] = 'Ubah Berita';
        $this->data['berita'] = $berita;

        // Persiapkan data gambar lama untuk View
        $old_file_image_data = [];
        if (!empty($berita->file_image)) {
            $file_names = explode(',', $berita->file_image);
            foreach ($file_names as $file_name) {
                $file_path = $this->modulePath . $file_name;
                if (file_exists($file_path)) {
                    $old_file_image_data[] = [
                        'name' => $file_name,
                        'size' => filesize($file_path),
                    ];
                }
            }
        }
        $this->data['old_file_image_data'] = $old_file_image_data;

        // 2. Set Validasi
        $this->validation->setRules([
            'title' => [
                'label' => 'Judul Berita',
                'rules' => 'required',
                'errors' => [
                    'required' => 'Judul Berita wajib diisi.',
                ]
            ],
            'file_cover' => [
                'label' => 'File Cover',
                'rules' => 'permit_empty|max_size[file_cover,2048]|is_image[file_cover]',
                'errors' => [
                    'max_size' => 'Ukuran file cover maksimal 2MB.',
                    'is_image' => 'File cover harus berupa gambar (jpg, png, gif).',
                ]
            ],
            'file_image.*' => [
                'label' => 'File Image',
                'rules' => 'permit_empty|max_size[file_image,2048]|is_image[file_image]',
                'errors' => [
                    'max_size' => 'Ukuran salah satu file image maksimal 2MB.',
                    'is_image' => 'Salah satu file image harus berupa gambar.',
                ]
            ]
        ]);

        // 3. Proses Submit
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                
                $db = \Config\Database::connect();
                $db->transStart();

                // A. Update Data Teks
                $title_slug = url_title($this->request->getPost('title'), '-', true);
                
                $update_data = [
                    'title'        => $this->request->getPost('title'),
                    'slug'         => $this->request->getPost('slug') ?? $title_slug,
                    'category'     => $this->request->getPost('category'),
                    'category_sub' => $this->request->getPost('category_sub') ?? '',
                    'sort'         => $this->request->getPost('sort'),
                    'description'  => $this->request->getPost('description'),
                    'content'      => $this->request->getPost('content'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                    'updated_by'   => user_id(),
                ];

                // B. Logic Sorting (Geser urutan jika berubah)
                $oldSort = $berita->sort;
                $newSort = (int)$this->request->getPost('sort');
                
                if ($newSort != $oldSort) {
                    if ($newSort < $oldSort) {
                        $this->beritaModel
                            ->where('sort >=', $newSort)
                            ->where('sort <', $oldSort)
                            ->set('sort', 'sort + 1', false)
                            ->update();
                    } elseif ($newSort > $oldSort) {
                        $this->beritaModel
                            ->where('sort <=', $newSort)
                            ->where('sort >', $oldSort)
                            ->set('sort', 'sort - 1', false)
                            ->update();
                    }
                }

                try {
                    // --- C. HANDLER FILE COVER (SINGLE) ---
                    $fileCover = $this->request->getFile('file_cover');

                    if ($fileCover && $fileCover->isValid() && !$fileCover->hasMoved()) {
                        $newCoverName = date('Ymd') . '_' . $fileCover->getRandomName();
                        $fileCover->move($this->modulePath, $newCoverName);
                        
                        create_thumbnail($this->modulePath, $newCoverName, 'thumb_', 250);
                        $update_data['file_cover'] = $newCoverName;

                        if (!empty($berita->file_cover) && file_exists($this->modulePath . $berita->file_cover)) {
                            unlink($this->modulePath . $berita->file_cover);
                        }
                        if (!empty($berita->file_cover) && file_exists($this->modulePath . 'thumb_' . $berita->file_cover)) {
                            unlink($this->modulePath . 'thumb_' . $berita->file_cover);
                        }
                    } else {
                        $update_data['file_cover'] = $berita->file_cover;
                    }

                    // --- D. HANDLER FILE IMAGE (GALLERY - MULTIPLE) ---
                    $current_gallery = array_filter(explode(',', $berita->file_image ?? ''));

                    $remove_gallery = $this->request->getPost('remove_gallery'); 
                    if (!empty($remove_gallery)) {
                        foreach ($remove_gallery as $del_img) {
                            if (($key = array_search($del_img, $current_gallery)) !== false) {
                                unset($current_gallery[$key]);
                                if (file_exists($this->modulePath . $del_img)) {
                                    unlink($this->modulePath . $del_img);
                                }
                            }
                        }
                    }

                    $newImages = $this->request->getFileMultiple('file_image');
                    if ($newImages) {
                        foreach ($newImages as $img) {
                            if ($img->isValid() && !$img->hasMoved()) {
                                $imgName = date('Ymd') . '_' . $img->getRandomName();
                                $img->move($this->modulePath, $imgName);
                                $current_gallery[] = $imgName;
                            }
                        }
                    }

                    $update_data['file_image'] = implode(',', $current_gallery);

                    // --- E. UPDATE META (Khusus Admin) ---
                    if (is_member('admin')) {
                        $index_arr = $this->request->getPost('index');
                        if (!empty($index_arr)) {
                            $meta = [];
                            foreach ($index_arr as $idx => $value) {
                                $k = $this->request->getPost('key')[$value] ?? '';
                                $v = $this->request->getPost('value')[$value] ?? '';
                                if($k != '') {
                                    $meta[] = ['key' => $k, 'value' => $v];
                                }
                            }
                            if (!empty($meta)) {
                                $update_data['meta'] = json_encode($meta);
                            }
                        }
                    }

                    // F. Eksekusi Update
                    $this->beritaModel->update($id, $update_data);
                    
                    $db->transComplete();

                    if ($db->transStatus() === false) {
                        $this->session->setFlashdata('swal_icon', 'warning');
                        $this->session->setFlashdata('swal_title', 'Peringatan');
                        $this->session->setFlashdata('swal_text', 'Berita gagal diubah (Database Error)');
                        return redirect()->back()->withInput();
                    }

                    $this->session->setFlashdata('swal_icon', 'success');
                    $this->session->setFlashdata('swal_title', 'Berhasil');
                    $this->session->setFlashdata('swal_text', 'Berita berhasil diubah');
                    return redirect()->to('/cms/berita');

                } catch (\Exception $e) {
                    $db->transRollback();
                    $this->session->setFlashdata('swal_icon', 'error');
                    $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
                    $this->session->setFlashdata('swal_text', $e->getMessage());
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

        $this->data['message'] = $this->validation->getErrors()
            ? $this->validation->listErrors()
            : $this->session->getFlashdata('message');
            
        $this->data['redirect'] = base_url('cms/berita/edit/' . $id);
        echo view('Berita\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
            $this->session->setFlashdata('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('/dashboard');
        }

        $berita = $this->beritaModel->find($id);
        
        if ($berita) {
            $beritaDelete = $this->beritaModel->delete($id);
            if ($beritaDelete) {
                unlink_file($this->modulePath, $berita->file_image);
                unlink_file($this->modulePath, 'thumb_' . $berita->file_image);
                unlink_file($this->modulePath, $berita->file_cover);
                unlink_file($this->modulePath, 'thumb_' . $berita->file_cover);

                $this->session->setFlashdata('swal_icon', 'success');
                $this->session->setFlashdata('swal_title', 'Berhasil');
                $this->session->setFlashdata('swal_text', 'Berita berhasil dihapus');
                return redirect()->to('/cms/berita');
            } else {
                $this->session->setFlashdata('swal_icon', 'warning');
                $this->session->setFlashdata('swal_title', 'Peringatan');
                $this->session->setFlashdata('swal_text', 'Berita gagal dihapus');
                return redirect()->to('/cms/berita');
            }
        } else {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Tidak Ditemukan');
            $this->session->setFlashdata('swal_text', 'Berita tidak ditemukan');
            return redirect()->to('/cms/berita');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $beritaUpdate = $this->beritaModel->update($id, [$field => $value]);

        if ($beritaUpdate) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Status Berita berhasil diubah');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Status Berita gagal diubah');
        }
        return redirect()->to('/cms/berita');
    }

    public function thumb()
    {
        $from = $this->request->getGet('from');
        $to = $this->request->getGet('to');

        for ($i = $from; $i <= $to; $i++) {
            $berita = $this->beritaModel->find($i);
            if ($berita && isset($berita['file_image'])) {
                $newFileName = $berita['file_image'];
                if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                    create_thumbnail(
                        $this->modulePath,
                        $newFileName,
                        'thumb_',
                        250
                    );
                    echo 'success generate thumbnail for ID: ' . $i . ' <br>';
                } else {
                    echo 'already exist, failed generate thumbnail for ID: ' .
                        $i .
                        ' <br>';
                }
            }
        }
    }
}