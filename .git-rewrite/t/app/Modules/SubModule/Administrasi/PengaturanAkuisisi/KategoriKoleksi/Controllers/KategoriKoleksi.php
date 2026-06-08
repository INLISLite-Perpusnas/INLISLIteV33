<?php

namespace KategoriKoleksi\Controllers;

use \CodeIgniter\Files\File;

class KategoriKoleksi extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $kategorikoleksiModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->kategorikoleksiModel = new \KategoriKoleksi\Models\KategoriKoleksiModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/kategorikoleksi/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }
    public function index()
    {
        $this->data['title'] = 'Kategori Koleksi';
        echo view('KategoriKoleksi\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('master-kategori-koleksi');
        }
        $kategorikoleksiDelete = $this->kategorikoleksiModel->delete($id);
        if ($kategorikoleksiDelete) {
            set_message('toastr_msg', 'Kategori Koleksi berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('master-kategori-koleksi');
        } else {
            set_message('toastr_msg', 'Kategori Koleksi gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Kategori Koleksi gagal dihapus');
            return redirect()->to('master-kategori-koleksi');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $kategorikoleksiUpdate = $this->kategorikoleksiModel->update($id, array($field => $value));

        if ($kategorikoleksiUpdate) {
            set_message('toastr_msg', 'Kategori Koleksi berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Kategori Koleksi gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('master-kategori-koleksi');
    }
}
