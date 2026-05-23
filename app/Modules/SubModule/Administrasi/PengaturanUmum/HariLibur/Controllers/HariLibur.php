<?php

namespace HariLibur\Controllers;

use \CodeIgniter\Files\File;

class HariLibur extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $hariliburModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->hariliburModel = new \HariLibur\Models\HariLiburModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/harilibur/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }
    public function index()
    {
        $this->data['title'] = 'Hari Libur';
        echo view('HariLibur\Views\list', $this->data);
    }


    public function delete(int $id = 0)
    {
        if (!$id) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Error');
            $this->session->setFlashdata('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('master-hari-libur');
        }
        $hariliburDelete = $this->hariliburModel->delete($id);
        if ($hariliburDelete) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Hari Libur berhasil dihapus');
            return redirect()->to('master-hari-libur');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Hari Libur gagal dihapus');
            return redirect()->to('master-hari-libur');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $hariliburUpdate = $this->hariliburModel->update($id, array($field => $value));

        if ($hariliburUpdate) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Hari Libur berhasil diubah');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Hari Libur gagal diubah');
        }
        return redirect()->to('/master-hari-libur');
    }
}
