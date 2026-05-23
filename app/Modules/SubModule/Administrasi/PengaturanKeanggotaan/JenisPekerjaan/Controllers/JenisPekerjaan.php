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
           set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('master-jenis-pekerjaan');
        }
        $jenispekerjaanDelete = $this->jenispekerjaanModel->delete($id);
        if ($jenispekerjaanDelete) {
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Jenis Pekerjaan berhasil dihapus');
            return redirect()->to('master-jenis-pekerjaan');
        } else {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Jenis Pekerjaan gagal dihapus');
            set_message('message', 'Jenis Pekerjaan gagal dihapus');
            return redirect()->to('master-jenis-pekerjaan');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $jenispekerjaanUpdate = $this->jenispekerjaanModel->update($id, array($field => $value));

        if ($jenispekerjaanUpdate) {
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Status Jenis Pekerjaan berhasil diubah');
        } else {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Jenis Pekerjaan gagal diubah');
        }
        return redirect()->to('master-jenis-pekerjaan');
    }
}
