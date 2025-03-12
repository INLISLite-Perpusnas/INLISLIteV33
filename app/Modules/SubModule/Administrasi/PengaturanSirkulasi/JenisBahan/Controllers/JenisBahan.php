<?php

namespace JenisBahan\Controllers;

use \CodeIgniter\Files\File;

class JenisBahan extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $jenisbahanModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->jenisbahanModel = new \JenisBahan\Models\JenisBahanModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/jenisbahan/';

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
        echo view('JenisBahan\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('master-jenis-bahan');
        }
        $jenisbahanDelete = $this->jenisbahanModel->delete($id);
        if ($jenisbahanDelete) {
            set_message('toastr_msg', 'Jenis Bahan berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('master-jenis-bahan');
        } else {
            set_message('toastr_msg', 'Jenis Bahan gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Jenis Bahan gagal dihapus');
            return redirect()->to('master-jenis-bahan');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $jenisbahanUpdate = $this->jenisbahanModel->update($id, array($field => $value));
        if ($jenisbahanUpdate) {
            set_message('toastr_msg', 'Jenis Bahan berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Jenis Bahan gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('master-jenis-bahan');
    }
}
