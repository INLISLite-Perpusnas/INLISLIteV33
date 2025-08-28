<?php

namespace MasterKelompokUmur\Controllers;

use \CodeIgniter\Files\File;

class MasterKelompokUmur extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $masterKelompokUmurModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->masterKelompokUmurModel = new \MasterKelompokUmur\Models\MasterKelompokUmurModel();
      
    }
    public function index()
    {


        $this->data['title'] = 'Kelompok Umur';
        echo view('MasterKelompokUmur\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('master-kelompok-umur');
        }
        $masterKelompokUmurDelete = $this->masterKelompokUmurModel->delete($id);
        if ($masterKelompokUmurDelete) {
            set_message('toastr_msg', 'Master Kelompok Umur berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('master-kelompok-umur');
        } else {
            set_message('toastr_msg', 'Master Kelompok Umur gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Master Kelompok Umur gagal dihapus');
            return redirect()->to('master-kelompok-umur');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $masterKelompokUmurUpdate = $this->masterKelompokUmurModel->update($id, array($field => $value));

        if ($masterKelompokUmurUpdate) {
            set_message('toastr_msg', 'Master Kelompok Umur berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Master Kelompok Umur diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('master-kelompok-umur');
    }
}
