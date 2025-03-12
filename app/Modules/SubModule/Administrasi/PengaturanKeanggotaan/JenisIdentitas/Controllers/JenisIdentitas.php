<?php

namespace JenisIdentitas\Controllers;

use \CodeIgniter\Files\File;

class JenisIdentitas extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $jenisidentitasModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->jenisidentitasModel = new \JenisIdentitas\Models\JenisIdentitasModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/jenisidentitas/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }
    public function index()
    {
        $this->data['title'] = 'Jenis Identitas';
        echo view('JenisIdentitas\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('master-jenis-identitas');
        }
        $jenisidentitasDelete = $this->jenisidentitasModel->delete($id);
        if ($jenisidentitasDelete) {
            set_message('toastr_msg', 'Jenis Identitas berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('master-jenis-identitas');
        } else {
            set_message('toastr_msg', 'Jenis Identitas gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Jenis Identitas gagal dihapus');
            return redirect()->to('master-jenis-identitas');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $jenisidentitasUpdate = $this->jenisidentitasModel->update($id, array($field => $value));

        if ($jenisidentitasUpdate) {
            set_message('toastr_msg', 'Jenis Identitas berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Jenis Identitas gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('master-jenis-identitas');
    }
}
