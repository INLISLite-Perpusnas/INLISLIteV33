<?php

namespace SurveiPemustaka\Controllers;

use \CodeIgniter\Files\File;

class SurveiPemustaka extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $surveiModel;
    public $pertanyaanModel;
    public $pilihanModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->surveiModel = new \SurveiPemustaka\Models\SurveiModel();
        $this->pertanyaanModel = new \SurveiPemustaka\Models\PertanyaanModel();
        $this->pilihanModel = new \SurveiPemustaka\Models\PilihanModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/survei/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }

    public function index()
    {
        $this->data['title'] = 'Survei Pemustaka';
        echo view('SurveiPemustaka\Views\list', $this->data);
    }

    public function question(int $id = 0)
    {
        $survey = $this->surveiModel->find($id);
        $this->data['survey'] = $survey;

        $this->data['title'] = 'Pertanyaan Survei Pemustaka';
        echo view('SurveiPemustaka\Views\list_question', $this->data);
    }

    public function items(int $pertanyaan_id = 0)
    {
        $question = $this->pertanyaanModel->find($pertanyaan_id);
        $this->data['question'] = $question;

        $this->data['title'] = 'Pilihan Survei Pemustaka';
        echo view('SurveiPemustaka\Views\list_items', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('survei');
        }
        $surveiDelete = $this->surveiModel->delete($id);
        if ($surveiDelete) {
            set_message('toastr_msg', 'Survei Pemustaka berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('survei');
        } else {
            set_message('toastr_msg', 'Survei Pemustaka gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Survei Pemustaka gagal dihapus');
            return redirect()->to('survei');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $surveiUpdate = $this->surveiModel->update($id, array($field => (int) $value));
        if ($surveiUpdate) {
            set_message('toastr_msg', 'Survei Pemustaka berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Survei Pemustaka gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/surveipemustaka');
    }
}
