<?php

namespace MasterKelas\Controllers;

use \CodeIgniter\Files\File;

class MasterKelas extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $kelasModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->kelasModel = new \MasterKelas\Models\MasterKelasModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/kelas/';
        helper('reference');
        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }
    public function index()
    {
        $this->data['title'] = 'Master Kelas';
        echo view('MasterKelas\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Error');
            $this->session->setFlashdata('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('master-kelas');
        }
        $kelasDelete = $this->kelasModel->delete($id);
        if ($kelasDelete) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Master Kelas berhasil dihapus');
            return redirect()->to('master-kelas');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Master Kelas gagal dihapus');
            return redirect()->to('master-kelas');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $kelasUpdate = $this->kelasModel->update($id, array($field => $value));

        if ($kelasUpdate) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Status Master Kelas berhasil diubah');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Status Master Kelas gagal diubah');
        }
        return redirect()->to('master-kelas');
    }
}
