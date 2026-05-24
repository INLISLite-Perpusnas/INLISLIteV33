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
           set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('/master-jenis-denda');
        }
        $jenisdendaDelete = $this->jenisdendaModel->delete($id);
        if ($jenisdendaDelete) {
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Jenis Denda berhasil dihapus');
            return redirect()->to('/master-jenis-denda');
        } else {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Jenis Denda gagal dihapus');
            set_message('message', 'Jenis Denda gagal dihapus');
            return redirect()->to('/master-jenis-denda');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $jenisdendaUpdate = $this->jenisdendaModel->update($id, array($field => $value));

        if ($jenisdendaUpdate) {
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Jenis Denda berhasil diubah');
        } else {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Jenis Denda gagal diubah');
        }
        return redirect()->to('/master-jenis-denda');
    }
}
