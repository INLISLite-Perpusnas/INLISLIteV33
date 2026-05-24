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
           set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('/master-peraturan-peminjaman-tanggal');
        }
        $peraturanpeminjamantanggalDelete = $this->peraturanpeminjamantanggalModel->delete($id);
        if ($peraturanpeminjamantanggalDelete) {
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Jenis Bahan berhasil dihapus');
            return redirect()->to('/master-peraturan-peminjaman-tanggal');
        } else {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Jenis Bahan gagal dihapus');
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
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Jenis Bahan berhasil diubah');
        } else {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Jenis Bahan gagal diubah');
        }
        return redirect()->to('/master-peraturan-peminjaman-tanggal');
    }
}
