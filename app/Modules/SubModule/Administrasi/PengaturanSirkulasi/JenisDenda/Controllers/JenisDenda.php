<?php

namespace JenisDenda\Controllers;

use \CodeIgniter\Files\File;

class JenisDenda extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $jenisdendaModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->jenisdendaModel = new \JenisDenda\Models\JenisDendaModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/jenisdenda/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }
    public function index()
    {
        $this->data['title'] = 'Jenis Denda';
        echo view('JenisDenda\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('jenis-denda');
        }
        $jenisdendaDelete = $this->jenisdendaModel->delete($id);
        if ($jenisdendaDelete) {
            set_message('toastr_msg', 'Jenis Denda berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('jenis-denda');
        } else {
            set_message('toastr_msg', 'Jenis Denda gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Jenis Denda gagal dihapus');
            return redirect()->to('jenis-denda');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $jenisdendaUpdate = $this->jenisdendaModel->update($id, array($field => $value));

        if ($jenisdendaUpdate) {
            set_message('toastr_msg', 'Jenis Denda berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Jenis Denda gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/jenis-denda');
    }
}
