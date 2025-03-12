<?php

namespace KataSandang\Controllers;

use DataTables\DataTables;

class KataSandang extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $config;
    public $katasandangModel;

    function __construct()
    {
        $this->katasandangModel = new \KataSandang\Models\KataSandangModel();
    }

    public function index()
    {
        $this->data['title'] = 'KataSandang';
        echo view('KataSandang\Views\list', $this->data);
    }

    public function detail(int $id)
    {
        $this->data['title'] = 'Detail KataSandang';
        $param = $this->katasandangModel->find($id)->first();
        $this->data['param'] = $param;
        $this->data['auth'] = $this->auth;
        echo view('KataSandang\Views\view', $this->data);
    }

    public function create()
    {
        $this->data['title'] = 'Create Group';
        $this->validation->setRule('name', 'Nama KataSandang', 'required');
        $this->validation->setRule('value', 'Nilai KataSandang', 'trim');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $save_data = array(
                'name' => $this->request->getPost('name'),
                'value' => $this->request->getPost('value'),
                'description' => $this->request->getPost('description'),
            );

            $newParamId = $this->katasandangModel->insert($save_data);
            if ($newParamId) {
                set_message('toastr_msg', 'KataSandang berhasil disimpan');
                set_message('toastr_type', 'success');
                return redirect()->to('/master-kata-sandang');
            } else {
                set_message('message', 'KataSandang gagal disimpan');
                return redirect()->to('/master-kata-sandang/create/');
            }
        } else {
            $message = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
            $this->data['message'] = $message;
            echo view('KataSandang\Views\add', $this->data);
        }
    }

    public function edit(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide katasandang (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }

        $this->data['title'] = 'Edit KataSandang';
        $param = $this->katasandangModel->find($id);
        $this->validation->setRule('name', 'Nama KataSandang', 'required');
        $this->validation->setRule('value', 'Nilai KataSandang', 'trim');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $update_data = array(
                    'name' => $this->request->getPost('name'),
                    'value' => $this->request->getPost('value'),
                    'description' => $this->request->getPost('description'),
                );

                $paramUpdate = $this->katasandangModel->update($id, $update_data);
                if ($paramUpdate) {
                    set_message('toastr_msg', 'KataSandang berhasil disimpan');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/master-kata-sandang');
                } else {
                    set_message('toastr_msg', 'KataSandang gagal disimpan');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'KataSandang gagal disimpan');
                    return redirect()->to('/master-kata-sandang/edit/' . $id);
                }
            }
        }

        $this->data['param'] = $param;
        echo view('KataSandang\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide katasandang(id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }

        $paramDelete = $this->katasandangModel->delete($id);
        if ($paramDelete) {
            set_message('toastr_msg', 'KataSandang berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/master-kata-sandang');
        } else {
            set_message('toastr_msg', 'KataSandang gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'KataSandang gagal dihapus');
            return redirect()->to('/master-kata-sandang');
        }
    }

    public function set_access($status)
    {
        set_katasandang('layout_param', $status);
    }

    public function json()
    {
        return DataTables::use('c_katasandangs')->make(true);
    }
}
