<?php

namespace JenisKelamin\Controllers;

use \CodeIgniter\Files\File;

class JenisKelamin extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $jeniskelaminModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->jeniskelaminModel = new \JenisKelamin\Models\JenisKelaminModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/jeniskelamin/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }
    public function index()
    {
        $this->data['title'] = 'Jenis Kelamin';
        echo view('JenisKelamin\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('master-jenis-kelamin');
        }
        $jeniskelaminDelete = $this->jeniskelaminModel->delete($id);
        if ($jeniskelaminDelete) {
            set_message('toastr_msg', 'Jenis Kelamin berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('master-jenis-kelamin');
        } else {
            set_message('toastr_msg', 'Jenis Kelamin gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Jenis Kelamin gagal dihapus');
            return redirect()->to('master-jenis-kelamin');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $jeniskelaminUpdate = $this->jeniskelaminModel->update($id, array($field => $value));

        if ($jeniskelaminUpdate) {
            set_message('toastr_msg', 'Jenis Kelamin berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Jenis Kelamin gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('master-jenis-kelamin');
    }
}
