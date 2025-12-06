<?php

namespace Berita\Controllers;

use CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            set_message(
                'toastr_msg',
                'Sorry you have to provide parameter (id)'
            );
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }

        $berita = $this->beritaModel->find($id);
        $this->data['title'] = 'Berita - Detail';
        $this->data['berita'] = $berita;
        echo view('Berita\Views\view', $this->data);
    }

   
// ... (di dalam class controller Anda)

public function create()
{
    $this->data['title'] = 'Tambah Berita';
    $slug = $this->request->getGet('slug');

    // 1. SET VALIDASI
    // Kita gunakan rules bawaan CI4 untuk file upload
   // Tambahkan validasi untuk file

    $this->validation->setRule('title', 'Judul Berita', 'required');

    $this->validation->setRules([

    'file_cover' => [

    'label' => 'File Cover',
    'rules' => 'permit_empty|uploaded[file_cover]|max_size[file_cover,2048]|is_image[file_cover]',
    'errors' => [
    'uploaded' => 'File cover harus dipilih.',
    'max_size' => 'Ukuran file cover maksimal 2MB.',
    'is_image' => 'File cover harus berupa gambar (jpg, png, gif).', ]
    ],

    // Validasi untuk setiap file di 'file_image'

    'file_image.*' => [
    'label' => 'File Image',
    'rules' => 'permit_empty|uploaded[file_image]|max_size[file_image,2048]|is_image[file_image]',
    'errors' => [
    'uploaded' => 'Salah satu file image harus dipilih.',
    'max_size' => 'Ukuran salah satu file image maksimal 2MB.',
    'is_image' => 'Salah satu file image harus berupa gambar.',]
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

            // Cek apakah user mengupload file cover
            if ($fileCover->isValid() && !$fileCover->hasMoved()) {
                $coverName = date('Ymd') . '_' . $fileCover->getRandomName();
                
                // Upload file
                $fileCover->move($this->modulePath, $coverName);
                
                // Generate Thumbnail (Helper function Anda)
                create_thumbnail($this->modulePath, $coverName, 'thumb_', 250);

                // Masukkan nama file ke array data
                $save_data['file_cover'] = $coverName;
            }

            // --- B. PROSES UPLOAD IMAGES (Multiple) ---
            $filesImage = $this->request->getFileMultiple('file_image');
            $uploadedImages = [];

            if ($filesImage) {
                foreach ($filesImage as $img) {
                    // Cek validitas setiap file
                    if ($img->isValid() && !$img->hasMoved()) {
                        $imgName = date('Ymd') . '_' . $img->getRandomName();
                        
                        // Upload file
                        $img->move($this->modulePath, $imgName);
                        
                        // Tampung nama file yang berhasil diupload
                        $uploadedImages[] = $imgName;
                    }
                }
            }

            // Jika ada image yang terupload, gabungkan dengan koma
            if (!empty($uploadedImages)) {
                $save_data['file_image'] = implode(',', $uploadedImages);
            }

            // Insert Database
            $this->beritaModel->insert($save_data);
            $db->transComplete();

            if ($db->transStatus() === false) {
                // Rollback otomatis terjadi jika transStatus false
                set_message('toastr_msg', 'Gagal menambah berita (Database Error)');
                set_message('toastr_type', 'warning');
                return redirect()->back()->withInput();
            } else {
                set_message('toastr_msg', 'Berita berhasil ditambah');
                set_message('toastr_type', 'success');
                return redirect()->to('/cms/berita');
            }

        } catch (\Exception $e) {
            // Tangkap error upload/file system
            $db->transRollback();
            set_message('toastr_msg', 'Terjadi Kesalahan: ' . $e->getMessage());
            set_message('toastr_type', 'error');
            return redirect()->back()->withInput();
        }

    } else {
        // Tampilkan Form
        $this->data['redirect'] = base_url('berita/create');
        set_message(
            'message',
            $this->validation->getErrors()
                ? $this->validation->listErrors()
                : $this->session->getFlashdata('message')
        );
        echo view('Berita\Views\add', $this->data);
    }
}

   

   public function edit(int $id = null)
{
    // 1. Cek ID
    if (!$id) {
        set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
        set_message('toastr_type', 'error');
        return redirect()->to('/cms/berita');
    }

    $berita = $this->beritaModel->find($id);
    if (!$berita) {
        set_message('toastr_msg', 'Berita tidak ditemukan');
        set_message('toastr_type', 'error');
        return redirect()->to('/cms/berita');
    }

    $this->data['title'] = 'Ubah Berita';
    $this->data['berita'] = $berita;

    // Persiapkan data gambar lama untuk View (agar bisa ditampilkan previewnya)
    // Digunakan untuk menampilkan list gallery di view edit
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
    $this->validation->setRule('title', 'Judul Berita', 'required');
    $this->validation->setRule('sort', 'Urutan', 'required|is_natural_no_zero');

    // Validasi File (Opsional saat Edit)
    $this->validation->setRules([
        'file_cover' => [
            'label' => 'File Cover',
            'rules' => 'permit_empty|is_image[file_cover]|max_size[file_cover,2048]',
        ],
        'file_image.*' => [
            'label' => 'File Image',
            'rules' => 'permit_empty|is_image[file_image]|max_size[file_image,2048]',
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
                    // Geser ke atas: berita di range [newSort, oldSort-1] naik +1
                    $this->beritaModel
                        ->where('sort >=', $newSort)
                        ->where('sort <', $oldSort)
                        ->set('sort', 'sort + 1', false)
                        ->update();
                } elseif ($newSort > $oldSort) {
                    // Geser ke bawah: berita di range [oldSort+1, newSort] turun -1
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

                // Jika user upload cover baru
                if ($fileCover && $fileCover->isValid() && !$fileCover->hasMoved()) {
                    $newCoverName = date('Ymd') . '_' . $fileCover->getRandomName();
                    $fileCover->move($this->modulePath, $newCoverName);
                    
                    // Generate Thumbnail Baru
                    create_thumbnail($this->modulePath, $newCoverName, 'thumb_', 250);

                    // Masukkan ke array update
                    $update_data['file_cover'] = $newCoverName;

                    // Hapus File Cover Lama (Cleanup)
                    if (!empty($berita->file_cover) && file_exists($this->modulePath . $berita->file_cover)) {
                        unlink($this->modulePath . $berita->file_cover);
                    }
                    if (!empty($berita->file_cover) && file_exists($this->modulePath . 'thumb_' . $berita->file_cover)) {
                        unlink($this->modulePath . 'thumb_' . $berita->file_cover);
                    }
                } else {
                    // Jika tidak upload baru, pakai yang lama
                    $update_data['file_cover'] = $berita->file_cover;
                }

                // --- D. HANDLER FILE IMAGE (GALLERY - MULTIPLE) ---
                
                // 1. Ambil data gallery lama & bersihkan array kosong
                $current_gallery = array_filter(explode(',', $berita->file_image ?? ''));

                // 2. Logic Hapus Gambar (Berdasarkan Checkbox dari View)
                $remove_gallery = $this->request->getPost('remove_gallery'); // Array nama file yg mau dihapus
                if (!empty($remove_gallery)) {
                    foreach ($remove_gallery as $del_img) {
                        // Hapus dari Array
                        if (($key = array_search($del_img, $current_gallery)) !== false) {
                            unset($current_gallery[$key]);
                            
                            // Hapus File Fisik
                            if (file_exists($this->modulePath . $del_img)) {
                                unlink($this->modulePath . $del_img);
                            }
                        }
                    }
                }

                // 3. Logic Tambah Gambar Baru
                $newImages = $this->request->getFileMultiple('file_image');
                if ($newImages) {
                    foreach ($newImages as $img) {
                        if ($img->isValid() && !$img->hasMoved()) {
                            $imgName = date('Ymd') . '_' . $img->getRandomName();
                            $img->move($this->modulePath, $imgName);
                            
                            // Masukkan nama file baru ke array gallery
                            $current_gallery[] = $imgName;
                        }
                    }
                }

                // 4. Gabungkan kembali array menjadi string koma
                $update_data['file_image'] = implode(',', $current_gallery);


                // --- E. UPDATE META (Khusus Admin) ---
                if (is_member('admin')) {
                    $index_arr = $this->request->getPost('index');
                    if (!empty($index_arr)) {
                        $meta = [];
                        foreach ($index_arr as $idx => $value) {
                            // Pastikan key dan value ada
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
                    set_message('toastr_msg', 'Berita gagal diubah (Database Error)');
                    set_message('toastr_type', 'warning');
                    return redirect()->back()->withInput();
                }

                set_message('toastr_msg', 'Berita berhasil diubah');
                set_message('toastr_type', 'success');
                return redirect()->to('/cms/berita');

            } catch (\Exception $e) {
                $db->transRollback();
                set_message('toastr_msg', 'Terjadi kesalahan: ' . $e->getMessage());
                set_message('toastr_type', 'error');
                return redirect()->back()->withInput();
            }
        }
    }

    // Load View jika tidak submit
    $this->data['message'] = $this->validation->getErrors()
        ? $this->validation->listErrors()
        : $this->session->getFlashdata('message');
        
    $this->data['redirect'] = base_url('cms/berita/edit/' . $id);
    echo view('Berita\Views\update', $this->data);
}

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message(
                'toastr_msg',
                'Sorry you have to provide parameter (id)'
            );
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }
        $berita = $this->beritaModel->find($id);
        $beritaDelete = $this->beritaModel->delete($id);
        if ($beritaDelete) {
            unlink_file($this->modulePath, $berita->file_image);
            unlink_file($this->modulePath, 'thumb_' . $berita->file_image);
            unlink_file($this->modulePath, $berita->file_cover);
            unlink_file($this->modulePath, 'thumb_' . $berita->file_cover);
            // unlink_file($this->modulePath, $berita->file_pdf);

            set_message('toastr_msg', ' Berita berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/cms/berita');
        } else {
            set_message('toastr_msg', ' Berita gagal dihapus');
            set_message('toastr_type', 'warning');
            return redirect()->to('/cms/berita');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');
        $berita = $this->beritaModel->find($id);

        $beritaUpdate = $this->beritaModel->update($id, [$field => $value]);

        if ($beritaUpdate) {
            set_message('toastr_msg', ' Berita berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Berita gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/cms/berita');
    }

    public function export()
    {
        $query = $this->beritaModel
            ->select('t_berita.*')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_berita.created_by', 'left')
            ->join(
                'users updated',
                'updated.id = t_berita.updated_by',
                'left'
            );

        $results = $query->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'Berita');
        $sheet
            ->getStyle('A1:H1')
            ->getFont()
            ->setBold(true)
            ->setSize(12);

        $sheet->setCellValue('A2', 'No');
        $sheet->setCellValue('B2', 'Judul Artikel');
        $sheet->setCellValue('C2', 'Pengarang/Penulis');
        $sheet->setCellValue('D2', 'Aktif');
        $sheet->setCellValue('E2', 'Created By');
        $sheet->setCellValue('F2', 'Updated By');
        $sheet->setCellValue('G2', 'Foto Cover');
        $sheet->setCellValue('H2', 'Konten Digital');

        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);

        $sheet
            ->getStyle('A2:H2')
            ->getFont()
            ->setBold(true)
            ->setSize(12);

        $col = 3;
        $no = 1;
        $i = 1;
        foreach ($results as $row) {
            $sheet->setCellValue('A' . $col, $no);
            $sheet->setCellValue('B' . $col, $row->title);
            $sheet->setCellValue('C' . $col, $row->author);
            $sheet->setCellValue('D' . $col, $row->active);
            $sheet->setCellValue(
                'E' . $col,
                $row->created_at . ' | ' . strtoupper($row->created_name)
            );
            $sheet->setCellValue(
                'F' . $col,
                $row->updated_at . ' | ' . strtoupper($row->updated_name)
            );
            $sheet->setCellValue(
                'G' . $col,
                base_url('uploads/berita/' . $row->file_image)
            );
            $sheet->setCellValue(
                'H' . $col,
                base_url('uploads/berita/' . $row->file_pdf)
            );

            $col++;
            $no++;
            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $subject = 'Berita';
        $filename = ucwords($subject) . '-' . date('Y-m-d');

        header('Content-Type: application/vnd.ms-excel');
        header(
            'Content-Disposition: attachment;filename="' . $filename . '.xlsx"'
        );
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function thumb()
    {
        $from = $this->request->getGet('from');
        $to = $this->request->getGet('to');

        for ($i = $from; $i <= $to; $i++) {
            $berita = $this->beritaModel->find($i);
            $newFileName = $berita->file_image;
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
