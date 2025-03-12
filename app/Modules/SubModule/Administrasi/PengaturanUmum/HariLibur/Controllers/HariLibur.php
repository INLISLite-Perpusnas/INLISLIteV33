<?php

namespace HariLibur\Controllers;

use \CodeIgniter\Files\File;

class HariLibur extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $hariliburModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->hariliburModel = new \HariLibur\Models\HariLiburModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/harilibur/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }
    public function index()
    {
        $this->data['title'] = 'Hari Libur';
        echo view('HariLibur\Views\list', $this->data);
    }


    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('master-hari-libur');
        }
        $hariliburDelete = $this->hariliburModel->delete($id);
        if ($hariliburDelete) {
            set_message('toastr_msg', 'Jenis Identitas berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('master-hari-libur');
        } else {
            set_message('toastr_msg', 'Jenis Identitas gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Jenis Identitas gagal dihapus');
            return redirect()->to('master-hari-libur');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $hariliburUpdate = $this->hariliburModel->update($id, array($field => $value));

        if ($hariliburUpdate) {
            set_message('toastr_msg', 'Jenis Identitas berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Jenis Identitas gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/master-hari-libur');
    }
}
