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

    public function create()
    {
        $this->data['title'] = 'Tambah Berita';
        $slug = $this->request->getGet('slug');

        $this->validation->setRule('title', 'Judul Berita', 'required');
        if (
            $this->request->getPost() &&
            $this->validation->withRequest($this->request)->run()
        ) {
            $db = \Config\Database::connect();
            $db->transStart();

            // Geser semua sort lama ke bawah
            $this->beritaModel
                ->where('sort >=', 1)
                ->set('sort', 'sort + 1', false)
                ->update();

            $title_slug = url_title(
                $this->request->getPost('title'),
                '-',
                true
            );
            $save_data = [
                'title' => $this->request->getPost('title'),
                'slug' => $title_slug,
                'category' => $this->request->getPost('category'),
                'sort' => 1,
                'description' => $this->request->getPost('description'),
                'content' => $this->request->getPost('content'),
                'created_by' => user_id(),
            ];

            // Logic Upload
            $files = (array) $this->request->getPost('file_cover');
            if (count($files)) {
                $listed_file = [];
                foreach ($files as $uuid => $name) {
                    if (file_exists($this->uploadPath . $name)) {
                        $file = new File($this->uploadPath . $name);
                        $newFileName =
                            date('Ymd') . '_' . $file->getRandomName();
                        $file->move($this->modulePath, $newFileName);
                        $listed_file[] = $newFileName;

                        create_thumbnail(
                            $this->modulePath,
                            $newFileName,
                            'thumb_',
                            250
                        );
                    }
                }
                $save_data['file_cover'] = implode(',', $listed_file);
            }

            $files = (array) $this->request->getPost('file_image');
            if (count($files)) {
                $listed_file = [];
                foreach ($files as $uuid => $name) {
                    if (file_exists($this->uploadPath . $name)) {
                        $file = new File($this->uploadPath . $name);
                        $newFileName =
                            date('Ymd') . '_' . $file->getRandomName();
                        $file->move($this->modulePath, $newFileName);
                        $listed_file[] = $newFileName;
                    }
                }
                $save_data['file_image'] = implode(',', $listed_file);
            }

            if (is_member('admin')) {
                $index_arr = $this->request->getPost('index');
                if (!empty($index_arr)) {
                    $meta = [];
                    foreach ($index_arr as $index => $value) {
                        $meta[] = [
                            'key' => $this->request->getPost('key')[$value],
                            'value' => $this->request->getPost('value')[$value],
                        ];
                    }
                    if (!empty($meta)) {
                        $save_data['meta'] = json_encode($meta);
                    }
                }
            }

            $newPageId = $this->beritaModel->insert($save_data);
            $db->transComplete();

            if ($db->transStatus() === false) {
                set_message('toastr_msg', 'Gagal menambah berita');
                set_message('toastr_type', 'warning');
                echo view('Berita\Views\add', $this->data);
            } else {
                set_message('toastr_msg', 'Berita berhasil ditambah');
                set_message('toastr_type', 'success');
                return redirect()->to('/cms/berita');
            }
        } else {
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

    public function do_upload()
    {
        // Pastikan request adalah AJAX dan ada file yang diunggah
        if ($this->request->isAJAX() && $this->request->getFile('file')) {
            $file = $this->request->getFile('file');

            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move($this->uploadPath, $newName);

                // Mengembalikan nama file sebagai respons JSON
                return $this->response->setJSON(['filename' => $newName, 'size' => $file->getSize()]);
            }
        }

        // Mengembalikan respons error jika ada masalah
        return $this->response->setStatusCode(400)->setJSON(['error' => 'File upload failed.']);
    }

    public function edit(int $id = null)
    {
        $this->data['title'] = 'Ubah Berita';
        $berita = $this->beritaModel->find($id);
        // Dapatkan data file cover yang lama, termasuk ukuran file
        $old_file_cover_data = [];
        if ($berita->file_cover) {
            $file_names = explode(',', $berita->file_cover);
            foreach ($file_names as $file_name) {
                $file_path = $this->modulePath . $file_name;
                if (file_exists($file_path)) {
                    $old_file_cover_data[] = [
                        'name' => $file_name,
                        'size' => filesize($file_path),
                    ];
                }
            }
        }

        // Dapatkan data file image yang lama, termasuk ukuran file
        $old_file_image_data = [];
        if ($berita->file_image) {
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

        $this->data['title'] = 'Ubah Berita';
        $this->data['berita'] = $berita;
        $this->data['old_file_cover_data'] = $old_file_cover_data;
        $this->data['old_file_image_data'] = $old_file_image_data;


        $this->validation->setRule('title', 'Judul Berita', 'required');
        $this->validation->setRule('sort', 'Urutan', 'required|is_natural_no_zero');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $title_slug = url_title(
                    $this->request->getPost('title'),
                    '-',
                    true
                );
                $update_data = [
                    'title' => $this->request->getPost('title'),
                    'slug' => $this->request->getPost('slug') ?? $title_slug,
                    'category' => $this->request->getPost('category'),
                    'category_sub' => $this->request->getPost('category_sub') ?? '',
                    'sort' => $this->request->getPost('sort'),
                    'description' => $this->request->getPost('description'),
                    'content' => $this->request->getPost('content'),
                    'updated_by' => user_id(),
                ];

                // Penyesuaian sort
                $oldSort = $berita->sort;
                $newSort = (int)$this->request->getPost('sort');
                if ($newSort != $oldSort) {
                    if ($newSort < $oldSort) {
                        // Geser ke atas: berita di range [newSort, oldSort-1] naik 1
                        $this->beritaModel
                            ->where('sort >=', $newSort)
                            ->where('sort <', $oldSort)
                            ->set('sort', 'sort + 1', false)
                            ->update();
                    } elseif ($newSort > $oldSort) {
                        // Geser ke bawah: berita di range [oldSort+1, newSort] turun 1
                        $this->beritaModel
                            ->where('sort <=', $newSort)
                            ->where('sort >', $oldSort)
                            ->set('sort', 'sort - 1', false)
                            ->update();
                    }
                }

                // Logic Upload untuk file_cover
                $files_cover = (array) $this->request->getPost('file_cover');
                if (count($files_cover)) {
                    $listed_cover = [];
                    foreach ($files_cover as $name) {
                        // Cek apakah file sudah ada di direktori permanen (file lama)
                        if (file_exists($this->modulePath . $name)) {
                            $listed_cover[] = $name;
                        }
                        // Jika file baru diunggah (masih di direktori sementara)
                        else if (file_exists($this->uploadPath . $name)) {
                            $file = new File($this->uploadPath . $name);
                            $newFileName = date('Ymd') . '_' . $file->getRandomName();
                            $file->move($this->modulePath, $newFileName);
                            $listed_cover[] = $newFileName;
                            create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                        }
                    }
                    $update_data['file_cover'] = implode(',', $listed_cover);
                } else {
                    // Jika tidak ada file baru di-upload, pertahankan file cover yang lama
                    $update_data['file_cover'] = $berita->file_cover;
                }

                // Logic Upload untuk file_image
                $files_image = (array) $this->request->getPost('file_image');
                if (count($files_image)) {
                    $listed_image = [];
                    foreach ($files_image as $name) {
                        if (file_exists($this->modulePath . $name)) {
                            $listed_image[] = $name;
                        } else if (file_exists($this->uploadPath . $name)) {
                            $file = new File($this->uploadPath . $name);
                            $newFileName = date('Ymd') . '_' . $file->getRandomName();
                            $file->move($this->modulePath, $newFileName);
                            $listed_image[] = $newFileName;
                        }
                    }
                    $update_data['file_image'] = implode(',', $listed_image);
                } else {
                    // Jika tidak ada file baru di-upload, pertahankan file image yang lama
                    $update_data['file_image'] = $berita->file_image;
                }

                if (is_member('admin')) {
                    $index_arr = $this->request->getPost('index');
                    if (!empty($index_arr)) {
                        $meta = [];
                        foreach ($index_arr as $index => $value) {
                            $meta[] = [
                                'key' => $this->request->getPost('key')[$value],
                                'value' => $this->request->getPost('value')[$value],
                            ];
                        }
                        if (!empty($meta)) {
                            $update_data['meta'] = json_encode($meta);
                        }
                    }
                }

                $pageUpdate = $this->beritaModel->update($id, $update_data);

                if ($pageUpdate) {
                    set_message('toastr_msg', 'Page berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/cms/berita');
                } else {
                    set_message('toastr_msg', 'Page gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Page gagal diubah');
                    return redirect()->to('/cms/berita');
                }
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
