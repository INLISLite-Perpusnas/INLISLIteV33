<?php

namespace JenisPelanggaran\Controllers;

use \CodeIgniter\Files\File;

class JenisPelanggaran extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $jenispelanggaranModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->jenispelanggaranModel = new \JenisPelanggaran\Models\JenisPelanggaranModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/jenispelanggaran/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }
    public function index()
    {
        $this->data['title'] = 'Jenis Pelanggaran';
        echo view('JenisPelanggaran\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
             set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('/master-jenis-pelanggaran');
        }
        $jenispelanggaranDelete = $this->jenispelanggaranModel->delete($id);
        if ($jenispelanggaranDelete) {
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Jenis Pelanggaran berhasil dihapus');
            return redirect()->to('/master-jenis-pelanggaran');
        } else {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Jenis Pelanggaran gagal dihapus');
            return redirect()->to('/master-jenis-pelanggaran');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $jenispelanggaranUpdate = $this->jenispelanggaranModel->update($id, array($field => $value));

        if ($jenispelanggaranUpdate) {
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Jenis Pelanggaran berhasil diubah');
        } else {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Jenis Pelanggaran gagal diubah');
        }
        return redirect()->to('/master-jenis-pelanggaran');
    }
}
