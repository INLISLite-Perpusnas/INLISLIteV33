<?php

namespace LokasiPerpustakaan\Controllers;

use \CodeIgniter\Files\File;

class LokasiPerpustakaan extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $lokasiperpustakaanModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->lokasiperpustakaanModel = new \LokasiPerpustakaan\Models\LokasiPerpustakaanModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/lokasiperpustakaan/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }

        helper('reference');
        helper('region');
    }
    public function index()
    {
        $this->data['title'] = 'Lokasi Perpustakaan';
        echo view('LokasiPerpustakaan\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Error');
            $this->session->setFlashdata('swal_text', 'Sorry you have to provide parameter (id)');
            return redirect()->to('master-lokasi-perpustakaan');
        }

        $lokasiRuangModel = new \LokasiRuang\Models\LokasiRuangModel();
        $childCount = $lokasiRuangModel->where('LocationLibrary_id', $id)->countAllResults();
        if ($childCount > 0) {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Tidak Dapat Dihapus');
            $this->session->setFlashdata('swal_text', 'Lokasi Perpustakaan tidak dapat dihapus karena masih memiliki ' . $childCount . ' Lokasi Ruang.');
            return redirect()->to('master-lokasi-perpustakaan');
        }

        $lokasiperpustakaanDelete = $this->lokasiperpustakaanModel->delete($id);
        if ($lokasiperpustakaanDelete) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Lokasi Perpustakaan berhasil dihapus');
            return redirect()->to('master-lokasi-perpustakaan');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Lokasi Perpustakaan gagal dihapus');
            return redirect()->to('master-lokasi-perpustakaan');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $lokasiperpustakaanUpdate = $this->lokasiperpustakaanModel->update($id, array($field => $value));

        if ($lokasiperpustakaanUpdate) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Lokasi Perpustakaan berhasil diubah');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Lokasi Perpustakaan gagal diubah');
        }
        return redirect()->to('master-lokasi-perpustakaan');
    }
}
