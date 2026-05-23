<?php

namespace MasterJurusan\Controllers;

use \CodeIgniter\Files\File;

class MasterJurusan extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $jurusanModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->jurusanModel = new \MasterJurusan\Models\MasterJurusanModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/jurusan/';
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


        $this->data['title'] = 'Master Jurusan';
        echo view('MasterJurusan\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('master-jurusan');
        }
        $jurusanDelete = $this->jurusanModel->delete($id);
        if ($jurusanDelete) {
          set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Master Jurusan berhasil dihapus');
            return redirect()->to('master-jurusan');
        } else {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Master Jurusan gagal dihapus');
            return redirect()->to('master-jurusan');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $jurusanUpdate = $this->jurusanModel->update($id, array($field => $value));

        if ($jurusanUpdate) {
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Master Jurusan berhasil diubah');
        } else {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Master Jurusan gagal diubah');
        }
        return redirect()->to('master-jurusan');
    }
}
