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
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('master-jenis-akses');
        }
        $jenisaksesDelete = $this->jenisaksesModel->delete($id);
        if ($jenisaksesDelete) {
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Jenis Akses berhasil dihapus');
            return redirect()->to('master-jenis-akses');
        } else {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Jenis Akses gagal dihapus');
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
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Jenis Akses berhasil diubah');
        } else {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Jenis Akses gagal diubah');
        }
        return redirect()->to('master-jenis-akses');
    }
}
