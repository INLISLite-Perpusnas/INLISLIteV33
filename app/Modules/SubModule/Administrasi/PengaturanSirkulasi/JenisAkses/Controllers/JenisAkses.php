<?php

namespace JenisAkses\Controllers;

use \CodeIgniter\Files\File;

class JenisAkses extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $jenisaksesModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->jenisaksesModel = new \JenisAkses\Models\JenisAksesModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/jenisakses/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }
    public function index()
    {
        $this->data['title'] = 'Jenis Akses';
        echo view('JenisAkses\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('master-jenis-akses');
        }
        $jenisaksesDelete = $this->jenisaksesModel->delete($id);
        if ($jenisaksesDelete) {
            set_message('toastr_msg', 'Jenis Akses berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('master-jenis-akses');
        } else {
            set_message('toastr_msg', 'Jenis Akses gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Jenis Akses gagal dihapus');
            return redirect()->to('master-jenis-akses');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $jenisaksesUpdate = $this->jenisaksesModel->update($id, array($field => $value));

        if ($jenisaksesUpdate) {
            set_message('toastr_msg', 'Jenis Akses berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Jenis Akses gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('master-jenis-akses');
    }
}
