<?php

namespace BukuTamu\Controllers;

use \CodeIgniter\Files\File;

class BukuTamu extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $memberguestModel;
    public $groupguestModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->memberguestModel = new \BukuTamu\Models\MemberGuestModel();
        $this->groupguestModel = new \BukuTamu\Models\GroupGuestModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/bukutamu/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }

    public function index()
    {
        $this->data['title'] = 'Buku Tamu - Anggota';
        echo view('BukuTamu\Views\list', $this->data);
    }

    public function non_anggota()
    {
        $this->data['title'] = 'Buku Tamu - Non Anggota';
        echo view('BukuTamu\Views\list_non_anggota', $this->data);
    }

    public function rombongan()
    {
        $this->data['title'] = 'Buku Tamu - Rombongan';
        echo view('BukuTamu\Views\list_rombongan', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('bukutamu');
        }

        $slug = $this->request->getGet('slug') ?? 'anggota';
        if ($slug == 'rombongan') {
            $bukutamuDelete = $this->groupguestModel->delete($id);
        } else {
            $bukutamuDelete = $this->memberguestModel->delete($id);
        }

        if ($bukutamuDelete) {
            set_message('toastr_msg', 'Buku Tamu berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('bukutamu?slug=' . $slug);
        } else {
            set_message('toastr_msg', 'Buku Tamu gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Buku Tamu gagal dihapus');
            return redirect()->to('bukutamu?slug=' . $slug);
        }
    }
}
