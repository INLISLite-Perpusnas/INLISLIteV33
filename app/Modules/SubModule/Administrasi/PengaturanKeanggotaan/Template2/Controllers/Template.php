<?php

namespace Template\Controllers;

use CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory2;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

use \Convertio\Convertio;

class Template extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $templateModel;
    public $uploadPath;
    public $modulePath;
    public $db;

    function __construct()
    {
        $this->language = \Config\Services::language();
        $this->language->setLocale('id');

        $this->templateModel = new \Template\Models\TemplateModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/master-template/';

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
        $this->data['slug'] = ' depan';
        $this->data['title'] = ' Template';
        $this->data['message'] = $this->validation->getErrors()
            ? $this->validation->listErrors()
            : $this->session->getFlashdata('message');
        echo view('Template\Views\list', $this->data);
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

        $template = $this->templateModel->find($id);
        $this->data['title'] = 'Template - Detail';
        $this->data['template'] = $template;
        echo view('Template\Views\view', $this->data);
    }

    public function create()
    {
        $this->data['title'] = 'Tambah Template';
        $slug = $this->request->getGet('slug');

        $this->validation->setRule('title', 'Judul Template', 'required');
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

            $newPageId = $this->templateModel->insert($save_data);
            if ($newPageId) {
                set_message('toastr_msg', 'Template berhasil ditambah');
                set_message('toastr_type', 'success');
                return redirect()->to('/master-template-kartu');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Page.info.failed_saved'));
                echo view('Template\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('template/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('Template\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        $this->data['title'] = 'Ubah Template';
        $template = $this->templateModel->find($id);
        $this->data['template'] = $template;

        $this->validation->setRule('title', 'Judul Template', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $title_slug = url_title($this->request->getPost('title'), '-', true);
                $update_data = [
                    'title' => $this->request->getPost('title'),
                    'slug' => $this->request->getPost('slug') ?? $title_slug,
                    'category' => $this->request->getPost('category'),
                    'category_sub' => $this->request->getPost('category_sub') ?? '',
                    'layout' => $this->request->getPost('layout') ?? '',
                    'sort' => $this->request->getPost('sort'),
                    'description' => $this->request->getPost('description'),
                    'content' => $this->request->getPost('content'),
                    'updated_by' => user_id(),
                ];

                $pageUpdate = $this->templateModel->update($id, $update_data);
                if ($pageUpdate) {
                    set_message('toastr_msg', 'Page berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/master-template-kartu');
                } else {
                    set_message('toastr_msg', 'Page gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Page gagal diubah');
                    return redirect()->to('/master-template-kartu');
                }
            }
        }


        $this->data['redirect'] = base_url('master-template-kartu/edit/' . $id);
        echo view('Template\Views\update', $this->data);
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
        $template = $this->templateModel->find($id);
        $templateDelete = $this->templateModel->delete($id);
        if ($templateDelete) {
            set_message('toastr_msg', ' Template berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/master-template-kartu');
        } else {
            set_message('toastr_msg', ' Template gagal dihapus');
            set_message('toastr_type', 'warning');
            return redirect()->to('/master-template-kartu');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');
        $template = $this->templateModel->find($id);

        $templateUpdate = $this->templateModel->update($id, [$field => $value]);
        if ($templateUpdate) {
            set_message('toastr_msg', ' Template berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Template gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/master-template-kartu');
    }

    public function export()
    {
        $query = $this->templateModel
            ->select('t_template.*')
            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_template.created_by', 'left')
            ->join('users updated', 'updated.id = t_template.updated_by', 'left');

        $results = $query->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'Template');
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
                base_url('uploads/master-template/' . $row->file_image)
            );
            $sheet->setCellValue(
                'H' . $col,
                base_url('uploads/master-template/' . $row->file_pdf)
            );

            $col++;
            $no++;
            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $subject = 'Template';
        $filename = ucwords($subject) . '-' . date('Y-m-d');

        header('Content-Type: application/vnd.ms-excel');
        header(
            'Content-Disposition: attachment;filename="' . $filename . '.xlsx"'
        );
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function word()
    {
        $template = $this->modulePath . '/psp/sk.docx';
        $filename = date('Ymd_His');
        $output_doc = $this->modulePath . '/' . $filename . '.docx';
        $output_pdf = $this->modulePath . '/' . $filename . '.pdf';

        $templateProcessor = new TemplateProcessor($template);
        $templateProcessor->setValues([
            'nama_satker' => 'Biro Keuangan dan BMN',
            'total' => '500.000.000',
            'terbilang' => 'lima ratus juta rupiah',
        ]);
        $templateProcessor->saveAs($output_doc);

        // You can obtain API Key here: https://convertio.co/api/
        $API = new Convertio("51f954bcc379cb2e247932c51ffbd8d4"); // paid          
        $API = new Convertio("da8988c0a512996ee54f48e36c622740"); // free        
        $API->start($output_doc, 'pdf')->wait()->download($output_pdf)->delete();
    }
}
