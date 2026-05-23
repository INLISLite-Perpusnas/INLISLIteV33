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
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('master-jenis-identitas');
        }
        $jenisidentitasDelete = $this->jenisidentitasModel->delete($id);
        if ($jenisidentitasDelete) {
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Jenis Identitas berhasil dihapus');
            return redirect()->to('master-jenis-identitas');
        } else {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Jenis Identitas gagal dihapus');
            return redirect()->to('master-jenis-identitas');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $jenisidentitasUpdate = $this->jenisidentitasModel->update($id, array($field => $value));

        if ($jenisidentitasUpdate) {
             set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Status Jenis Identitas berhasil diubah');
        } else {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Status Jenis Identitas gagal diubah');
        }
        return redirect()->to('master-jenis-identitas');
    }
}
