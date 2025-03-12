<?php

namespace Kontak\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Kontak extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $kontakModel;
    public $uploadPath;
    public $modulePath;
    public $db;

    function __construct()
    {
        $this->kontakModel = new \Kontak\Models\KontakModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/kontak/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }

        helper('adminigniter');
        helper('reference');
    }

    public function index()
    {
        $this->data['title'] = ' Kontak';
        echo view('Kontak\Views\list', $this->data);
    }

    public function create()
    {
        $this->data['title'] = 'Tambah  Kontak';

        $this->validation->setRule('name', 'Nama', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $save_data = [
                'name' => $this->request->getPost('name'),
                'phone' => $this->request->getPost('phone'),
                'email' => $this->request->getPost('email'),
                'subject' => $this->request->getPost('subject'),
                'message' => $this->request->getPost('message'),
                'created_by' => user_id(),
            ];

            $newKontakId = $this->kontakModel->insert($save_data);
            if ($newKontakId) {
                set_message('toastr_msg', ' Kontak berhasil ditambah');
                set_message('toastr_type', 'success');

                return redirect()->to('/cms/kontak');
            } else {
                set_message('message', ' Kontak gagal ditambah');
                echo view('Kontak\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('kontak/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('Kontak\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        $this->data['title'] = 'Ubah Halaman';
        $kontak = $this->kontakModel->find($id);
        $this->data['kontak'] = $kontak;

        $this->validation->setRule('name', 'Nama', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $update_data = [
                    'name' => $this->request->getPost('name'),
                    'phone' => $this->request->getPost('phone'),
                    'email' => $this->request->getPost('email'),
                    'subject' => $this->request->getPost('subject'),
                    'message' => $this->request->getPost('message'),
                    'updated_by' => user_id(),
                ];

                $pageUpdate = $this->kontakModel->update($id, $update_data);
                if ($pageUpdate) {
                    set_message('toastr_msg', 'Kontak berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/cms/kontak');
                } else {
                    set_message('toastr_msg', 'Kontak gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Kontak gagal diubah');
                    return redirect()->to('/cms/kontak');
                }
            }
        }


        $this->data['redirect'] = base_url('cms/kontak/edit/' . $id);
        echo view('Kontak\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }
        $kontak = $this->kontakModel->find($id);
        $kontakDelete = $this->kontakModel->delete($id);
        if ($kontakDelete) {
            set_message('toastr_msg', ' Kontak berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/cms/kontak');
        } else {
            set_message('toastr_msg', ' Kontak gagal dihapus');
            set_message('toastr_type', 'warning');
            return redirect()->to('/cms/kontak');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $kontakUpdate = $this->kontakModel->update($id, array($field => $value));

        if ($kontakUpdate) {
            set_message('toastr_msg', ' Kontak berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Kontak gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/cms/kontak');
    }

    public function export()
    {
        $query = $this->kontakModel
            ->select('t_kontak.*')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_kontak.created_by', 'left')
            ->join('users updated', 'updated.id = t_kontak.updated_by', 'left');

        $results = $query->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue("A1", "Kontak");
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
            $sheet->setCellValue("G" . $col, base_url('uploads/kontak/' . $row->file_image));
            $sheet->setCellValue("H" . $col, base_url('uploads/kontak/' . $row->file_pdf));

            $col++;
            $no++;
            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $subject = 'Kontak';
        $filename = ucwords($subject) . '-' . date('Y-m-d');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}
