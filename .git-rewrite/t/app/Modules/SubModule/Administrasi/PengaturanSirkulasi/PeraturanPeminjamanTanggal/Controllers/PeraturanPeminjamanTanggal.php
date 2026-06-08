<?php

namespace PeraturanPeminjamanTanggal\Controllers;

use \CodeIgniter\Files\File;

class PeraturanPeminjamanTanggal extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $peraturanpeminjamantanggalModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->peraturanpeminjamantanggalModel = new \PeraturanPeminjamanTanggal\Models\PeraturanPeminjamanTanggalModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/peraturanpeminjamantanggal/';
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
        $this->data['title'] = 'Peraturan Peminjaman Tanggal';
        echo view('PeraturanPeminjamanTanggal\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/master-peraturan-peminjaman-tanggal');
        }
        $peraturanpeminjamantanggalDelete = $this->peraturanpeminjamantanggalModel->delete($id);
        if ($peraturanpeminjamantanggalDelete) {
            set_message('toastr_msg', 'Jenis Bahan berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/master-peraturan-peminjaman-tanggal');
        } else {
            set_message('toastr_msg', 'Jenis Bahan gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Jenis Bahan gagal dihapus');
            return redirect()->to('/master-peraturan-peminjaman-tanggal');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');
        $peraturanpeminjamantanggalUpdate = $this->peraturanpeminjamantanggalModel->update($id, array($field => $value));

        if ($peraturanpeminjamantanggalUpdate) {
            set_message('toastr_msg', 'Jenis Bahan berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Jenis Bahan gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/master-peraturan-peminjaman-tanggal');
    }
}
