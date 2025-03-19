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
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('lokasi-ruang');
        }
        $lokasiruangDelete = $this->lokasiruangModel->delete($id);
        if ($lokasiruangDelete) {
            set_message('toastr_msg', 'Lokasi Ruang berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('lokasi-ruang');
        } else {
            set_message('toastr_msg', 'Lokasi Ruang gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Lokasi Ruang gagal dihapus');
            return redirect()->to('lokasi-ruang');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $lokasiruangUpdate = $this->lokasiruangModel->update($id, array($field => $value));

        if ($lokasiruangUpdate) {
            set_message('toastr_msg', 'Lokasi Ruang berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Lokasi Ruang gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/lokasi-ruang');
    }
}
