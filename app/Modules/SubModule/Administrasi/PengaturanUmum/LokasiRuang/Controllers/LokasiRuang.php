<?php

namespace LokasiRuang\Controllers;

use \CodeIgniter\Files\File;

class LokasiRuang extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $lokasiruangModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->lokasiruangModel = new \LokasiRuang\Models\LokasiRuangModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/lokasiruang/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }



        helper('reference');
        helper('region');
    }
    public function index()
    {
        $this->data['title'] = 'Lokasi Ruang';
        echo view('LokasiRuang\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Error');
            $this->session->setFlashdata('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('master-lokasi-ruang');
        }
        $lokasiruangDelete = $this->lokasiruangModel->delete($id);
        if ($lokasiruangDelete) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Lokasi Ruang berhasil dihapus');
            return redirect()->to('master-lokasi-ruang');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Lokasi Ruang gagal dihapus');
            return redirect()->to('master-lokasi-ruang');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $lokasiruangUpdate = $this->lokasiruangModel->update($id, array($field => $value));

        if ($lokasiruangUpdate) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Lokasi Ruang berhasil diubah');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Lokasi Ruang gagal diubah');
        }
        return redirect()->to('master-lokasi-ruang');
    }
}
