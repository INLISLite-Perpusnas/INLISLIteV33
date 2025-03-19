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

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('bacaditempat');
        }

        $slug = $this->request->getGet('slug') ?? 'anggota';

        $bacaditempatDelete = $this->bacaditempatModel->delete($id);
        if ($bacaditempatDelete) {
            set_message('toastr_msg', 'Baca Ditempat berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('bacaditempat?slug=' . $slug);
        } else {
            set_message('toastr_msg', 'Baca Ditempat gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Baca Ditempat gagal dihapus');
            return redirect()->to('bacaditempat?slug=' . $slug);
        }
    }
}
