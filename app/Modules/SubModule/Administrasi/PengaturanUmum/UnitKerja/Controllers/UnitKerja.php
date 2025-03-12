<?php

namespace UnitKerja\Controllers;

use \CodeIgniter\Files\File;

class UnitKerja extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $unitkerjaModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->unitkerjaModel = new \UnitKerja\Models\UnitKerjaModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/unitkerja/';

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
        echo view('UnitKerja\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('unit-kerja');
        }
        $unitkerjaDelete = $this->unitkerjaModel->delete($id);
        if ($unitkerjaDelete) {
            set_message('toastr_msg', 'Jenis Identitas berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('unit-kerja');
        } else {
            set_message('toastr_msg', 'Jenis Identitas gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Jenis Identitas gagal dihapus');
            return redirect()->to('unit-kerja');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $unitkerjaUpdate = $this->unitkerjaModel->update($id, array($field => $value));

        if ($unitkerjaUpdate) {
            set_message('toastr_msg', 'Jenis Identitas berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Jenis Identitas gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/unit-kerja');
    }
}
