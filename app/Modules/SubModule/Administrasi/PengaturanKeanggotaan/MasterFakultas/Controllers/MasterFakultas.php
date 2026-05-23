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
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Error');
            set_message('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('master-fakultas');
        }
        $fakultasDelete = $this->fakultasModel->delete($id);
        if ($fakultasDelete) {
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Master Fakultas berhasil dihapus');
            return redirect()->to('master-fakultas');
        } else {
            set_message('swal_icon', 'warning');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Master Fakultas gagal dihapus');
            return redirect()->to('master-fakultas');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $fakultasUpdate = $this->fakultasModel->update($id, array($field => $value));

        if ($fakultasUpdate) {
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Master Fakultas berhasil diubah');
        } else {
            set_message('swal_icon', 'warning');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Master Fakultas gagal diubah');
        }
        return redirect()->to('master-fakultas');
    }
}
