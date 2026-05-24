<?php

namespace BacaDitempat\Controllers;

use \CodeIgniter\Files\File;

class BacaDitempat extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $bacaditempatModel;
    public $groupguestModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->bacaditempatModel = new \BacaDitempat\Models\BacaDitempatModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/bacaditempat/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }

    public function index()
    {
        $this->data['title'] = 'Baca Ditempat - Anggota';
        echo view('BacaDitempat\Views\list', $this->data);
    }

    public function non_anggota()
    {
        $this->data['title'] = 'Baca Ditempat - Non Anggota';
        echo view('BacaDitempat\Views\list_non_anggota', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            session()->setFlashdata('swal_icon', 'error');
            session()->setFlashdata('swal_title', 'Gagal');
            session()->setFlashdata('swal_text', 'Parameter ID tidak ditemukan');
            return redirect()->to('baca-di-tempat');
        }

        $slug = $this->request->getGet('slug') ?? 'anggota';

        $bacaditempatDelete = $this->bacaditempatModel->delete($id);
        if ($bacaditempatDelete) {
            session()->setFlashdata('swal_icon', 'success');
            session()->setFlashdata('swal_title', 'Berhasil');
            session()->setFlashdata('swal_text', 'Baca Ditempat berhasil dihapus');
            return redirect()->to('baca-di-tempat');
        } else {
            session()->setFlashdata('swal_icon', 'error');
            session()->setFlashdata('swal_title', 'Gagal');
            session()->setFlashdata('swal_text', 'Baca Ditempat gagal dihapus');
            return redirect()->to('baca-di-tempat');
        }
    }
}
