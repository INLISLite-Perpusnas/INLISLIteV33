<?php

namespace MasterFakultas\Controllers;

use \CodeIgniter\Files\File;

class MasterFakultas extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $fakultasModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->fakultasModel = new \MasterFakultas\Models\MasterFakultasModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/fakultas/';
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


        $this->data['title'] = 'Master Fakultas';
        echo view('MasterFakultas\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('master-fakultas');
        }
        $fakultasDelete = $this->fakultasModel->delete($id);
        if ($fakultasDelete) {
            set_message('toastr_msg', 'Master Fakultas berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('master-fakultas');
        } else {
            set_message('toastr_msg', 'Master Fakultas gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Master Fakultas gagal dihapus');
            return redirect()->to('master-fakultas');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $fakultasUpdate = $this->fakultasModel->update($id, array($field => $value));

        if ($fakultasUpdate) {
            set_message('toastr_msg', 'Master Fakultas berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Master Fakultas gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('master-fakultas');
    }
}
