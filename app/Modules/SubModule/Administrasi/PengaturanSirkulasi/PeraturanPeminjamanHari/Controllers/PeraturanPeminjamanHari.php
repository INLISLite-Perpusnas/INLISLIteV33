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
        $this->data['title'] = 'Jenis Bahan';
        echo view('PeraturanPeminjamanHari\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('jenis-bahan');
        }
        $peraturanpeminjamanhariDelete = $this->peraturanpeminjamanhariModel->delete($id);
        if ($peraturanpeminjamanhariDelete) {
            set_message('toastr_msg', 'Jenis Bahan berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('jenis-bahan');
        } else {
            set_message('toastr_msg', 'Jenis Bahan gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Jenis Bahan gagal dihapus');
            return redirect()->to('jenis-bahan');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $peraturanpeminjamanhariUpdate = $this->peraturanpeminjamanhariModel->update($id, array($field => $value));

        if ($peraturanpeminjamanhariUpdate) {
            set_message('toastr_msg', 'Jenis Bahan berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Jenis Bahan gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/jenis-bahan');
    }
}
