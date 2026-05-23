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
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Error');
            set_message('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('master-kelompok-umur');
        }
        $masterKelompokUmurDelete = $this->masterKelompokUmurModel->delete($id);
        if ($masterKelompokUmurDelete) {
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Master Kelompok Umur berhasil dihapus');
            return redirect()->to('master-kelompok-umur');
        } else {
            set_message('swal_icon', 'warning');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Master Kelompok Umur gagal dihapus');
            return redirect()->to('master-kelompok-umur');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $masterKelompokUmurUpdate = $this->masterKelompokUmurModel->update($id, array($field => $value));

        if ($masterKelompokUmurUpdate) {
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Master Kelompok Umur berhasil diubah');
        } else {
            set_message('swal_icon', 'warning');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Master Kelompok Umur gagal diubah');
        }
        return redirect()->to('master-kelompok-umur');
    }
}
