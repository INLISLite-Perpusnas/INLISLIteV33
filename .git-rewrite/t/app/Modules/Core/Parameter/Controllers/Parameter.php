<?php

namespace Parameter\Controllers;

use DataTables\DataTables;

class Parameter extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $config;
    public $parameterModel;

    function __construct()
    {
        $this->parameterModel = new \Parameter\Models\ParameterModel();

        helper(['reference', 'thumbnail']);
    }

    public function index()
    {
        $this->data['title'] = 'Parameter';
        echo view('Parameter\Views\list', $this->data);
    }

    public function detail(int $id)
    {
        $this->data['title'] = 'Detail Parameter';
        $param = $this->parameterModel->find($id)->first();
        $this->data['param'] = $param;
        $this->data['auth'] = $this->auth;
        echo view('Parameter\Views\view', $this->data);
    }

    public function create()
    {
        $this->data['title'] = 'Create Group';
        $this->validation->setRule('name', 'Nama Parameter', 'required');
        $this->validation->setRule('value', 'Nilai Parameter', 'trim');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $save_data = array(
                'name' => $this->request->getPost('name'),
                'value' => $this->request->getPost('value'),
                'description' => $this->request->getPost('description'),
            );

            $newParamId = $this->parameterModel->insert($save_data);
            if ($newParamId) {
                set_message('toastr_msg', 'Parameter berhasil disimpan');
                set_message('toastr_type', 'success');
                return redirect()->to('/parameter');
            } else {
                set_message('message', 'Parameter gagal disimpan');
                return redirect()->to('/parameter/create/');
            }
        } else {
            $message = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
            $this->data['message'] = $message;
            echo view('Parameter\Views\add', $this->data);
        }
    }

    public function edit(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }

        $this->data['title'] = 'Edit Parameter';
        $param = $this->parameterModel->find($id);
        $this->validation->setRule('name', 'Nama Parameter', 'required');
        $this->validation->setRule('value', 'Nilai Parameter', 'trim');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $update_data = array(
                    'name' => $this->request->getPost('name'),
                    'value' => $this->request->getPost('value'),
                    'description' => $this->request->getPost('description'),
                );

                $paramUpdate = $this->parameterModel->update($id, $update_data);
                if ($paramUpdate) {
                    set_message('toastr_msg', 'Parameter berhasil disimpan');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/parameter');
                } else {
                    set_message('toastr_msg', 'Parameter gagal disimpan');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Parameter gagal disimpan');
                    return redirect()->to('/parameter/edit/' . $id);
                }
            }
        }

        $this->data['param'] = $param;
        echo view('Parameter\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter(id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }

        $paramDelete = $this->parameterModel->delete($id);
        if ($paramDelete) {
            set_message('toastr_msg', 'Parameter berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/parameter');
        } else {
            set_message('toastr_msg', 'Parameter gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Parameter gagal dihapus');
            return redirect()->to('/parameter');
        }
    }

    public function set_access($status)
    {
        set_parameter('layout_param', $status);
    }

    public function crud()
    {
        $this->data['title'] = 'Catalog';

        $crud = $this->_getGroceryCrudEnterprise();

        $crud->setCsrfTokenName(csrf_token());
        $crud->setCsrfTokenValue(csrf_hash());

        $crud->setTable('c_parameters');
        $crud->setSubject('Catalog', 'Catalogs');

        $output = $crud->render();

        if (isset($output->isJSONResponse) && $output->isJSONResponse) {
            header('Content-Type: application/json; charset=utf-8');
            echo $output->output;
            exit;
        }

        return view('Catalog\Views\crud', (array)$output);
    }
}
