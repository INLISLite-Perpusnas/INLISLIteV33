<?php

namespace DetailKatalog\Controllers;

use \CodeIgniter\Files\File;

class DetailKatalog extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $detailKatalogModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->detailKatalogModel = new \DetailKatalog\Models\DetailKatalogModel();
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
        $this->data['title'] = 'Master Detail Katalog';
        echo view('DetailKatalog\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('master-detail-katalog');
        }
        $kategorikoleksiDelete = $this->detailKatalogModel->delete($id);
        if ($kategorikoleksiDelete) {
            set_message('toastr_msg', 'Jenis Akses berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('master-detail-katalog');
        } else {
            set_message('toastr_msg', 'Jenis Akses gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Jenis Akses gagal dihapus');
            return redirect()->to('master-detail-katalog');
        }
    }
}
