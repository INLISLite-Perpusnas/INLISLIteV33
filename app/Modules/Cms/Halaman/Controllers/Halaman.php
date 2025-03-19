<?php

namespace Halaman\Controllers;

use CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Halaman extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $halamanModel;
    public $uploadPath;
    public $modulePath;
    public $db;

    function __construct()
    {
        $this->language = \Config\Services::language();
        $this->language->setLocale('id');

        $this->halamanModel = new \Halaman\Models\HalamanModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/halaman/';

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
        $this->data['title'] = ' Halaman';
        $this->data['message'] = $this->validation->getErrors()
            ? $this->validation->listErrors()
            : $this->session->getFlashdata('message');
        echo view('Halaman\Views\list', $this->data);
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

        $halaman = $this->halamanModel->find($id);
        $this->data['title'] = 'Halaman - Detail';
        $this->data['halaman'] = $halaman;
        echo view('Halaman\Views\view', $this->data);
    }

    public function create()
    {
        $this->data['title'] = 'Tambah Halaman';
        $slug = $this->request->getGet('slug');

        $this->validation->setRule('title', 'Judul Halaman', 'required');
        if (
            $this->request->getPost() &&
            $this->validation->withRequest($this->request)->run()
        ) {
            $title_slug = url_title(
                $this->request->getPost('title'),
                '-',
                true
            );
            $save_data = [
                'title' => $this->request->getPost('title'),
                'slug' => $title_slug,
                'category' => $this->request->getPost('category'),
                'category_sub' => $this->request->getPost('category_sub') ?? '',
                'sort' => $this->request->getPost('sort'),
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

            $newPageId = $this->halamanModel->insert($save_data);
            if ($newPageId) {
                set_message('toastr_msg', 'Halaman berhasil ditambah');
                set_message('toastr_type', 'success');
                return redirect()->to('/cms/halaman');
            } else {
                set_message(
                    'message',
                    $this->validation->getErrors()
                        ? $this->validation->listErrors()
                        : lang('Page.info.failed_saved')
                );
                echo view('Halaman\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('halaman/create');
            set_message(
                'message',
                $this->validation->getErrors()
                    ? $this->validation->listErrors()
                    : $this->session->getFlashdata('message')
            );
            echo view('Halaman\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        $this->data['title'] = 'Ubah Halaman';
        $halaman = $this->halamanModel->find($id);
        $this->data['halaman'] = $halaman;

        $this->validation->setRule('title', 'Judul Halaman', 'required');
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

                // Logic Upload
                $files = (array) $this->request->getPost('file_cover');
                if (count($files)) {
                    $listed_file = [];
                    foreach ($files as $uuid => $name) {
                        if (file_exists($this->modulePath . $name)) {
                            $listed_file[] = $name;
                        } else {
                            if (file_exists($this->uploadPath . $name)) {
                                $file = new File($this->uploadPath . $name);
                                $newFileName =
                                    date('Ymd') . '_' . $file->getRandomName();
                                $file->move($this->modulePath, $newFileName);
                                $listed_file[] = $newFileName;
                            }
                        }
                    }
                    $update_data['file_cover'] = implode(',', $listed_file);
                }

                $files = (array) $this->request->getPost('file_image');
                if (count($files)) {
                    $listed_file = [];
                    foreach ($files as $uuid => $name) {
                        if (file_exists($this->modulePath . $name)) {
                            $listed_file[] = $name;
                        } else {
                            if (file_exists($this->uploadPath . $name)) {
                                $file = new File($this->uploadPath . $name);
                                $newFileName =
                                    date('Ymd') . '_' . $file->getRandomName();
                                $file->move($this->modulePath, $newFileName);
                                $listed_file[] = $newFileName;
                            }
                        }
                    }
                    $update_data['file_image'] = implode(',', $listed_file);
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

                $pageUpdate = $this->halamanModel->update($id, $update_data);

                if ($pageUpdate) {
                    set_message('toastr_msg', 'Page berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/cms/halaman');
                } else {
                    set_message('toastr_msg', 'Page gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Page gagal diubah');
                    return redirect()->to('/cms/halaman');
                }
            }
        }

        $this->data['message'] = $this->validation->getErrors()
            ? $this->validation->listErrors()
            : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('cms/halaman/edit/' . $id);
        echo view('Halaman\Views\update', $this->data);
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
        $halaman = $this->halamanModel->find($id);
        $halamanDelete = $this->halamanModel->delete($id);
        if ($halamanDelete) {
            unlink_file($this->modulePath, $halaman->file_image);
            unlink_file($this->modulePath, 'thumb_' . $halaman->file_image);
            unlink_file($this->modulePath, $halaman->file_pdf);

            set_message('toastr_msg', ' Halaman berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/cms/halaman');
        } else {
            set_message('toastr_msg', ' Halaman gagal dihapus');
            set_message('toastr_type', 'warning');
            return redirect()->to('/cms/halaman');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');
        $halaman = $this->halamanModel->find($id);

        $halamanUpdate = $this->halamanModel->update($id, [$field => $value]);

        if ($halamanUpdate) {
            set_message('toastr_msg', ' Halaman berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Halaman gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/cms/halaman');
    }

    public function export()
    {
        $query = $this->halamanModel
            ->select('t_halaman.*')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_halaman.created_by', 'left')
            ->join(
                'users updated',
                'updated.id = t_halaman.updated_by',
                'left'
            );

        $results = $query->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'Halaman');
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
                base_url('uploads/halaman/' . $row->file_image)
            );
            $sheet->setCellValue(
                'H' . $col,
                base_url('uploads/halaman/' . $row->file_pdf)
            );

            $col++;
            $no++;
            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $subject = 'Halaman';
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
            $halaman = $this->halamanModel->find($i);
            $newFileName = $halaman->file_image;
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
