<?php

namespace PenomoranKoleksi\Controllers;

use \CodeIgniter\Files\File;

class PenomoranKoleksi extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $PenomoranKoleksiModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->PenomoranKoleksiModel = new \PenomoranKoleksi\Models\PenomoranKoleksiModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/kategorikoleksi/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }

        $this->auth = \Myth\Auth\Config\Services::authentication();
        $this->authorize = \Myth\Auth\Config\Services::authorization();

        if (!$this->auth->check()) {
            $this->session->set('redirect_url', current_url());
            return redirect()->route('login');
        }
    }
    public function index()
    {
        $this->data['title'] = 'Jenis Akses';
        echo view('PenomoranKoleksi\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('master-media-koleksi');
        }
        $kategorikoleksiDelete = $this->PenomoranKoleksiModel->delete($id);
        if ($kategorikoleksiDelete) {
            set_message('toastr_msg', 'Jenis Akses berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('master-media-koleksi');
        } else {
            set_message('toastr_msg', 'Jenis Akses gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Jenis Akses gagal dihapus');
            return redirect()->to('master-media-koleksi');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $kategorikoleksiUpdate = $this->PenomoranKoleksiModel->update($id, array($field => $value));

        if ($kategorikoleksiUpdate) {
            set_message('toastr_msg', 'Jenis Akses berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Jenis Akses gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('master-media-koleksi');
    }
}
