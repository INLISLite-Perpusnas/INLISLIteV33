<?php

namespace TujuanKunjungan\Controllers;

use \CodeIgniter\Files\File;

class TujuanKunjungan extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $tujuankunjunganModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->tujuankunjunganModel = new \TujuanKunjungan\Models\TujuanKunjunganModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/tujuankunjungan/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }
    public function index()
    {
        $this->data['title'] = 'Tujuan Kunjungan';
        echo view('TujuanKunjungan\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('tujuan-kunjungan');
        }
        $tujuankunjunganDelete = $this->tujuankunjunganModel->delete($id);
        if ($tujuankunjunganDelete) {
            set_message('toastr_msg', 'Tujuan Kunjungan berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('tujuan-kunjungan');
        } else {
            set_message('toastr_msg', 'Tujuan Kunjungan gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Tujuan Kunjungan gagal dihapus');
            return redirect()->to('tujuan-kunjungan');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $tujuankunjunganUpdate = $this->tujuankunjunganModel->update($id, array($field => $value));

        if ($tujuankunjunganUpdate) {
            set_message('toastr_msg', 'Tujuan Kunjungan berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Tujuan Kunjungan gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/tujuan-kunjungan');
    }
}
