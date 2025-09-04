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
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('master-jurusan');
        }
        $jurusanDelete = $this->jurusanModel->delete($id);
        if ($jurusanDelete) {
            set_message('toastr_msg', 'Master Jurusan berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('master-jurusan');
        } else {
            set_message('toastr_msg', 'Master Jurusan gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Master Jurusan gagal dihapus');
            return redirect()->to('jmaster-jurusan');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $jurusanUpdate = $this->jurusanModel->update($id, array($field => $value));

        if ($jurusanUpdate) {
            set_message('toastr_msg', 'Master Jurusan berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Master Jurusan gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('master-jurusan');
    }
}
