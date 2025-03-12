<?php

namespace JenisPekerjaan\Controllers;

use \CodeIgniter\Files\File;

class JenisPekerjaan extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $jenispekerjaanModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->jenispekerjaanModel = new \JenisPekerjaan\Models\JenisPekerjaanModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/jenispekerjaan/';
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
        $this->data['title'] = 'Jenis Pekerjaan';
        echo view('JenisPekerjaan\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('jenis-pekerjaan');
        }
        $jenispekerjaanDelete = $this->jenispekerjaanModel->delete($id);
        if ($jenispekerjaanDelete) {
            set_message('toastr_msg', 'Jenis Pekerjaan berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('jenis-pekerjaan');
        } else {
            set_message('toastr_msg', 'Jenis Pekerjaan gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Jenis Pekerjaan gagal dihapus');
            return redirect()->to('jenis-pekerjaan');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $jenispekerjaanUpdate = $this->jenispekerjaanModel->update($id, array($field => $value));

        if ($jenispekerjaanUpdate) {
            set_message('toastr_msg', 'Jenis Pekerjaan berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Jenis Pekerjaan gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('master-jenis-pekerjaan');
    }
}
