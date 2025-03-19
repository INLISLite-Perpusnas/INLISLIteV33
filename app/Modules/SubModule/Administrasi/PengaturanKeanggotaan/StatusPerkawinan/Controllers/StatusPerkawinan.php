<?php

namespace StatusPerkawinan\Controllers;

use \CodeIgniter\Files\File;

class StatusPerkawinan extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $statusperkawinanModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->statusperkawinanModel = new \StatusPerkawinan\Models\StatusPerkawinanModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/statusperkawinan/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }
    public function index()
    {
        $this->data['title'] = 'Status Perkawinan';
        echo view('StatusPerkawinan\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('master-status-perkawinan');
        }
        $statusperkawinanDelete = $this->statusperkawinanModel->delete($id);
        if ($statusperkawinanDelete) {
            set_message('toastr_msg', 'Status Perkawinan berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('master-status-perkawinan');
        } else {
            set_message('toastr_msg', 'Status Perkawinan gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Status Perkawinan gagal dihapus');
            return redirect()->to('master-status-perkawinan');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $statusperkawinanUpdate = $this->statusperkawinanModel->update($id, array($field => $value));

        if ($statusperkawinanUpdate) {
            set_message('toastr_msg', 'Status Perkawinan berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Status Perkawinan gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('master-status-perkawinan');
    }
}
