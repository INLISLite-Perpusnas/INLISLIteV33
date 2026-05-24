<?php

namespace PeraturanPeminjamanHari\Controllers;

use \CodeIgniter\Files\File;

class PeraturanPeminjamanHari extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $peraturanpeminjamanhariModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->peraturanpeminjamanhariModel = new \PeraturanPeminjamanHari\Models\PeraturanPeminjamanHariModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/peraturanpeminjamanhari/';
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
        $this->data['title'] = 'Peraturan Peminjaman Hari';
        echo view('PeraturanPeminjamanHari\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('/master-peraturan-peminjaman-hari');
        }
        $peraturanpeminjamanhariDelete = $this->peraturanpeminjamanhariModel->delete($id);
        if ($peraturanpeminjamanhariDelete) {
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Jenis Bahan berhasil dihapus');
            return redirect()->to('/master-peraturan-peminjaman-hari');
        } else {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Jenis Bahan gagal dihapus');
            set_message('message', 'Jenis Bahan gagal dihapus');
            return redirect()->to('/master-peraturan-peminjaman-hari');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $peraturanpeminjamanhariUpdate = $this->peraturanpeminjamanhariModel->update($id, array($field => $value));

        if ($peraturanpeminjamanhariUpdate) {
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Jenis Bahan berhasil diubah');
        } else {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Jenis Bahan gagal diubah');
        }
        return redirect()->to('/master-peraturan-peminjaman-hari');
    }
}
