<?php

namespace MasterKelas\Controllers;

use \CodeIgniter\Files\File;

class MasterKelas extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $kelasModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->kelasModel = new \MasterKelas\Models\MasterKelasModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/kelas/';
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
        $this->data['title'] = 'Master Kelas';
        echo view('MasterKelas\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('master-kelas');
        }
        $kelasDelete = $this->kelasModel->delete($id);
        if ($kelasDelete) {
            set_message('toastr_msg', 'Master Kelas berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('master-kelas');
        } else {
            set_message('toastr_msg', 'Master Kelas gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Master Kelas gagal dihapus');
            return redirect()->to('master-kelas');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $kelasUpdate = $this->kelasModel->update($id, array($field => $value));

        if ($kelasUpdate) {
            set_message('toastr_msg', 'Status Master Kelas berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Status Master Kelas gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('master-kelas');
    }
}
