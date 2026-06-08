<?php

namespace SumberKoleksi\Controllers;

use \CodeIgniter\Files\File;

class SumberKoleksi extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $sumberkoleksiModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->sumberkoleksiModel = new \SumberKoleksi\Models\SumberKoleksiModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/sumberkoleksi/';

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
        $this->data['title'] = 'Sumber Koleksi';
        echo view('SumberKoleksi\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('master-sumber-koleksi');
        }
        $sumberkoleksiDelete = $this->sumberkoleksiModel->delete($id);
        if ($sumberkoleksiDelete) {
            set_message('toastr_msg', 'Sumber Koleksi berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('master-sumber-koleksi');
        } else {
            set_message('toastr_msg', 'Sumber Koleksi gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Sumber Koleksi gagal dihapus');
            return redirect()->to('master-sumber-koleksi');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $sumberkoleksiUpdate = $this->sumberkoleksiModel->update($id, array($field => $value));

        if ($sumberkoleksiUpdate) {
            set_message('toastr_msg', 'Sumber Koleksi berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Sumber Koleksi gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('master-sumber-koleksi');
    }
}
