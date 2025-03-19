<?php

namespace Faq\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Faq extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $faqModel;
    public $uploadPath;
    public $modulePath;
    public $db;

    function __construct()
    {
        $this->faqModel = new \Faq\Models\FaqModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/faq/';

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
        $this->data['title'] = ' Faq';
        echo view('Faq\Views\list', $this->data);
    }

    public function detail(int $id)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }

        $faq = $this->faqModel->find($id);
        $this->data['title'] = 'Faq - Detail';
        $this->data['faq'] = $faq;
        echo view('Faq\Views\view', $this->data);
    }

    public function create()
    {
        $this->data['title'] = 'Tambah Faq';
        $slug = $this->request->getGet('slug');

        $this->validation->setRule('title', 'Judul Faq', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $title_slug = url_title($this->request->getPost('title'), '-', TRUE);
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
                $listed_file = array();
                foreach ($files as $uuid => $name) {
                    if (file_exists($this->uploadPath . $name)) {
                        $file = new File($this->uploadPath . $name);
                        $newFileName = date('Ymd') . '_' . $file->getRandomName();
                        $file->move($this->modulePath, $newFileName);
                        $listed_file[] = $newFileName;

                        create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                    }
                }
                $save_data['file_cover'] = implode(',', $listed_file);
            }

            $files = (array) $this->request->getPost('file_image');
            if (count($files)) {
                $listed_file = array();
                foreach ($files as $uuid => $name) {
                    if (file_exists($this->uploadPath . $name)) {
                        $file = new File($this->uploadPath . $name);
                        $newFileName = date('Ymd') . '_' . $file->getRandomName();
                        $file->move($this->modulePath, $newFileName);
                        $listed_file[] = $newFileName;
                    }
                }
                $save_data['file_image'] = implode(',', $listed_file);
            }

            if (is_member('admin')) {
                $index_arr = $this->request->getPost('index');
                if (!empty($index_arr)) {
                    $meta = array();
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

            $newPageId = $this->faqModel->insert($save_data);
            if ($newPageId) {
                set_message('toastr_msg', 'Faq berhasil ditambah');
                set_message('toastr_type', 'success');
                return redirect()->to('/cms/faq');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Page.info.failed_saved'));
                echo view('Faq\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('faq/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('Faq\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        $this->data['title'] = 'Ubah Faq';
        $faq = $this->faqModel->find($id);
        $this->data['faq'] = $faq;

        $this->validation->setRule('title', 'Judul Faq', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $title_slug = url_title($this->request->getPost('title'), '-', TRUE);
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
                    $listed_file = array();
                    foreach ($files as $uuid => $name) {
                        if (file_exists($this->modulePath . $name)) {
                            $listed_file[] = $name;
                        } else {
                            if (file_exists($this->uploadPath . $name)) {
                                $file = new File($this->uploadPath . $name);
                                $newFileName = date('Ymd') . '_' . $file->getRandomName();
                                $file->move($this->modulePath, $newFileName);
                                $listed_file[] = $newFileName;
                            }
                        }
                    }
                    $update_data['file_cover'] = implode(',', $listed_file);
                }

                $files = (array) $this->request->getPost('file_image');
                if (count($files)) {
                    $listed_file = array();
                    foreach ($files as $uuid => $name) {
                        if (file_exists($this->modulePath . $name)) {
                            $listed_file[] = $name;
                        } else {
                            if (file_exists($this->uploadPath . $name)) {
                                $file = new File($this->uploadPath . $name);
                                $newFileName = date('Ymd') . '_' . $file->getRandomName();
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
                        $meta = array();
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

                $pageUpdate = $this->faqModel->update($id, $update_data);

                if ($pageUpdate) {
                    set_message('toastr_msg', 'Page berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/cms/faq');
                } else {
                    set_message('toastr_msg', 'Page gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Page gagal diubah');
                    return redirect()->to('/cms/faq');
                }
            }
        }


        $this->data['redirect'] = base_url('cms/faq/edit/' . $id);
        echo view('Faq\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }
        $faq = $this->faqModel->find($id);
        $faqDelete = $this->faqModel->delete($id);
        if ($faqDelete) {
            unlink_file($this->modulePath, $faq->file_image);
            unlink_file($this->modulePath, 'thumb_' . $faq->file_image);
            unlink_file($this->modulePath, $faq->file_pdf);

            set_message('toastr_msg', ' Faq berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/cms/faq');
        } else {
            set_message('toastr_msg', ' Faq gagal dihapus');
            set_message('toastr_type', 'warning');
            return redirect()->to('/cms/faq');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');
        $faq = $this->faqModel->find($id);

        $faqUpdate = $this->faqModel->update($id, array($field => $value));

        if ($faqUpdate) {
            set_message('toastr_msg', ' Faq berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Faq gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/cms/faq');
    }

    public function export()
    {
        $query = $this->faqModel
            ->select('t_faq.*')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_faq.created_by', 'left')
            ->join('users updated', 'updated.id = t_faq.updated_by', 'left');

        $results = $query->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue("A1", "Faq");
        $sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(12);

        $sheet->setCellValue("A2", "No");
        $sheet->setCellValue("B2", "Judul Artikel");
        $sheet->setCellValue("C2", "Pengarang/Penulis");
        $sheet->setCellValue("D2", "Aktif");
        $sheet->setCellValue("E2", "Created By");
        $sheet->setCellValue("F2", "Updated By");
        $sheet->setCellValue("G2", "Foto Cover");
        $sheet->setCellValue("H2", "Konten Digital");

        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);

        $sheet->getStyle('A2:H2')->getFont()->setBold(true)->setSize(12);

        $col = 3;
        $no = 1;
        $i = 1;
        foreach ($results as $row) {
            $sheet->setCellValue("A" . $col, $no);
            $sheet->setCellValue("B" . $col, $row->title);
            $sheet->setCellValue("C" . $col, $row->author);
            $sheet->setCellValue("D" . $col, $row->active);
            $sheet->setCellValue("E" . $col, $row->created_at . ' | ' . strtoupper($row->created_name));
            $sheet->setCellValue("F" . $col, $row->updated_at . ' | ' . strtoupper($row->updated_name));
            $sheet->setCellValue("G" . $col, base_url('uploads/faq/' . $row->file_image));
            $sheet->setCellValue("H" . $col, base_url('uploads/faq/' . $row->file_pdf));

            $col++;
            $no++;
            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $subject = 'Faq';
        $filename = ucwords($subject) . '-' . date('Y-m-d');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function thumb()
    {
        $from = $this->request->getGet('from');
        $to = $this->request->getGet('to');

        for ($i = $from; $i <= $to; $i++) {
            $faq = $this->faqModel->find($i);
            $newFileName = $faq->file_image;
            if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                echo "success generate thumbnail for ID: " . $i . " <br>";
            } else {
                echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
            }
        }
    }
}
