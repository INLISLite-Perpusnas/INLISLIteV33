<?php

namespace FormatKartu\Controllers;

use DataTables\DataTables;

class FormatKartu extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $config;
    public $formatkartuModel;

    function __construct()
    {
        $this->formatkartuModel = new \FormatKartu\Models\FormatKartuModel();
    }

    public function index()
    {
        $this->data['title'] = 'FormatKartu';
        echo view('FormatKartu\Views\list', $this->data);
    }

    public function detail(int $id)
    {
        $this->data['title'] = 'Detail FormatKartu';
        $param = $this->formatkartuModel->find($id)->first();
        $this->data['param'] = $param;
        $this->data['auth'] = $this->auth;
        echo view('FormatKartu\Views\view', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide formatkartu(id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }

        $paramDelete = $this->formatkartuModel->delete($id);
        if ($paramDelete) {
            set_message('toastr_msg', 'FormatKartu berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/master-format-kartu');
        } else {
            set_message('toastr_msg', 'FormatKartu gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'FormatKartu gagal dihapus');
            return redirect()->to('/master-format-kartu');
        }
    }

    public function set_access($status)
    {
        set_formatkartu('layout_param', $status);
    }

    public function json()
    {
        return DataTables::use('c_formatkartus')->make(true);
    }
}
