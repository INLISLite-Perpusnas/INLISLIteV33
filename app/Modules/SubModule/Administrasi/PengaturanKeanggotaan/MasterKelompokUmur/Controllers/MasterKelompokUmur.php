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
            return redirect()->to('jenis-pendidikan');
        }
        $masterKelompokUmurDelete = $this->masterKelompokUmurModel->delete($id);
        if ($masterKelompokUmurDelete) {
            set_message('toastr_msg', 'Jenis Pendidikan berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('jenis-pendidikan');
        } else {
            set_message('toastr_msg', 'Jenis Pendidikan gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Jenis Pendidikan gagal dihapus');
            return redirect()->to('jenis-pendidikan');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $masterKelompokUmurUpdate = $this->masterKelompokUmurModel->update($id, array($field => $value));

        if ($masterKelompokUmurUpdate) {
            set_message('toastr_msg', 'Jenis Pendidikan berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Jenis Pendidikan gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('master-masterKelompokUmur');
    }
}
