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
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('jenis-pelanggaran');
        }
        $jenispelanggaranDelete = $this->jenispelanggaranModel->delete($id);
        if ($jenispelanggaranDelete) {
            set_message('toastr_msg', 'Jenis Pelanggaran berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('jenis-pelanggaran');
        } else {
            set_message('toastr_msg', 'Jenis Pelanggaran gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Jenis Pelanggaran gagal dihapus');
            return redirect()->to('jenis-pelanggaran');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $jenispelanggaranUpdate = $this->jenispelanggaranModel->update($id, array($field => $value));

        if ($jenispelanggaranUpdate) {
            set_message('toastr_msg', 'Jenis Pelanggaran berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Jenis Pelanggaran gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/jenis-pelanggaran');
    }
}
