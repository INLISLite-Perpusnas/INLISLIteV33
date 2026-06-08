<?php

namespace LokasiPerpustakaan\Controllers;

use \CodeIgniter\Files\File;

class LokasiPerpustakaan extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $lokasiperpustakaanModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->lokasiperpustakaanModel = new \LokasiPerpustakaan\Models\LokasiPerpustakaanModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/lokasiperpustakaan/';

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
        $this->data['title'] = 'Lokasi Perpustakaan';
        echo view('LokasiPerpustakaan\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('master-lokasi-perpustakaan');
        }
        $lokasiperpustakaanDelete = $this->lokasiperpustakaanModel->delete($id);
        if ($lokasiperpustakaanDelete) {
            set_message('toastr_msg', 'Lokasi Perpustakaan berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('master-lokasi-perpustakaan');
        } else {
            set_message('toastr_msg', 'Lokasi Perpustakaan gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Lokasi Perpustakaan gagal dihapus');
            return redirect()->to('master-lokasi-perpustakaan');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $lokasiperpustakaanUpdate = $this->lokasiperpustakaanModel->update($id, array($field => $value));

        if ($lokasiperpustakaanUpdate) {
            set_message('toastr_msg', 'Lokasi Perpustakaan berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Lokasi Perpustakaan gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('master-lokasi-perpustakaan');
    }
}
