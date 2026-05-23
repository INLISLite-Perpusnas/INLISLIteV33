<?php

namespace JenisPendidikan\Controllers;

use \CodeIgniter\Files\File;

class JenisPendidikan extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $jenispendidikanModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->jenispendidikanModel = new \JenisPendidikan\Models\JenisPendidikanModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/jenispendidikan/';
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
        $this->data['title'] = 'Jenis Pendidikan';
        echo view('JenisPendidikan\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('jenis-pendidikan');
        }
        $jenispendidikanDelete = $this->jenispendidikanModel->delete($id);
        if ($jenispendidikanDelete) {
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Jenis Pendidikan berhasil dihapus');
            return redirect()->to('jenis-pendidikan');
        } else {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Jenis Pendidikan gagal dihapus');
            set_message('message', 'Jenis Pendidikan gagal dihapus');
            return redirect()->to('jenis-pendidikan');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $jenispendidikanUpdate = $this->jenispendidikanModel->update($id, array($field => $value));

        if ($jenispendidikanUpdate) {
            set_message('swal_icon', 'success');
            set_message('swal_title', 'Berhasil');
            set_message('swal_text', 'Jenis Pendidikan berhasil diubah');
        } else {
            set_message('swal_icon', 'error');
            set_message('swal_title', 'Gagal');
            set_message('swal_text', 'Jenis Pendidikan gagal diubah');
        }
        return redirect()->to('master-jenis-pendidikan');
    }
}
