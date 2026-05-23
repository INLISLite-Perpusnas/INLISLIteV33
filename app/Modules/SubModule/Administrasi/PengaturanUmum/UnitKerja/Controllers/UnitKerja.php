<?php

namespace UnitKerja\Controllers;

use \CodeIgniter\Files\File;

class UnitKerja extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $unitkerjaModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->unitkerjaModel = new \UnitKerja\Models\UnitKerjaModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/unitkerja/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }
    public function index()
    {
        $this->data['title'] = 'Jenis Identitas';
        echo view('UnitKerja\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Error');
            $this->session->setFlashdata('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('unit-kerja');
        }
        $unitkerjaDelete = $this->unitkerjaModel->delete($id);
        if ($unitkerjaDelete) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Unit Kerja berhasil dihapus');
            return redirect()->to('unit-kerja');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Unit Kerja gagal dihapus');
            return redirect()->to('unit-kerja');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $unitkerjaUpdate = $this->unitkerjaModel->update($id, array($field => $value));

        if ($unitkerjaUpdate) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Status Unit Kerja berhasil diubah');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Status Unit Kerja gagal diubah');
        }
        return redirect()->to('/unit-kerja');
    }
}
