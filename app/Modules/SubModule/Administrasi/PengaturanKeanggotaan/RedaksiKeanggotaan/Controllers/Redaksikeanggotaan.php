<?php

namespace RedaksiKeanggotaan\Controllers;

use \CodeIgniter\Files\File;

class RedaksiKeanggotaan extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $RedaksiKeanggotaanModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->RedaksiKeanggotaanModel = new \RedaksiKeanggotaan\Models\RedaksiKeanggotaanModel();


        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/redaksikeanggotaan/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }

        helper('reference');

        helper('tag');
    }
    public function index()
    {
        $this->data['title'] = 'RedaksiKeanggotaan';
        echo view('RedaksiKeanggotaan\Views\list', $this->data);
    }

    public function create()
    {
        $this->data['title'] = 'Tambah RedaksiKeanggotaan';
        $this->validation->setRule('NameCategory', 'Kategori', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {

            $created_terminal = getClientIpAddress();


            // Field
            $save_data = [
                'NameCategory'                 => $this->request->getPost('NameCategory'),
                'SortNum'                 => $this->request->getPost('SortNum'),
                'Contents'                 => $this->request->getPost('Contents'),
                'UpdateBy'                 => user_id(),
                'CreateTerminal'     => $created_terminal,
            ];

            $newRedaksiKeanggotaanId = $this->RedaksiKeanggotaanModel->insert($save_data);


            if ($newRedaksiKeanggotaanId) {
                set_message('toastr_msg', 'Redaksi Keanggotaan berhasil ditambah');
                set_message('toastr_type', 'success');
                return redirect()->to('/master-redaksi-keanggotaan');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('RedaksiKeanggotaan.info.failed_saved'));
                echo view('RedaksiKeanggotaan\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('RedaksiKeanggotaan/create');
            $this->data['created_terminal'] = getClientIpAddress();
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('RedaksiKeanggotaan\Views\add', $this->data);
        }
    }

    public function edit(int $ID = null)
    {        // $RedaksiKeanggotaan = $this->RedaksiKeanggotaanModel->find($id);
        $this->data['title'] = 'Ubah RedaksiKeanggotaan';
        $this->validation->setRule('NameCategory', 'Kategori', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {

            $updated_terminal = getClientIpAddress();
            // Field
            $update_data = [
                'NameCategory'                 => $this->request->getPost('NameCategory'),
                'SortNum'                 => $this->request->getPost('SortNum'),
                'Contents'                 => $this->request->getPost('Contents'),
                'UpdateBy'         => user_id(),
                'UpdateTerminal'     => $updated_terminal,
            ];

            $updateRedaksiKeanggotaan = $this->RedaksiKeanggotaanModel->update($ID, $update_data);

            if ($updateRedaksiKeanggotaan) {
                set_message('toastr_msg', 'Redaksi Keanggotaan berhasil diubah');
                set_message('toastr_type', 'success');
                return redirect()->to('/master-redaksi-keanggotaan');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('RedaksiKeanggotaan.info.failed_saved'));
                echo view('RedaksiKeanggotaan\Views\update', $this->data);
            }
        } else {
            $RedaksiKeanggotaan = $this->RedaksiKeanggotaanModel->find($ID);
            $this->data['RedaksiKeanggotaan'] = $RedaksiKeanggotaan;
            $this->data['redirect'] = base_url('RedaksiKeanggotaan/edit/' . $ID);

            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('RedaksiKeanggotaan\Views\update', $this->data);
        }
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('RedaksiKeanggotaan');
        }
        $RedaksiKeanggotaanDelete = $this->RedaksiKeanggotaanModel->delete($id);
        if ($RedaksiKeanggotaanDelete) {
            set_message('toastr_msg', 'Redaksi Keanggotaan berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('master-redaksi-keanggotaan');
        } else {
            set_message('toastr_msg', 'Redaksi Keanggotaan gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Redaksi Keanggotaan gagal dihapus');
            return redirect()->to('master-redaksi-keanggotaan');
        }
    }

    public function apply_status($ID)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $RedaksiKeanggotaanUpdate = $this->fieldModel->update($ID, array($field => $value));

        if ($RedaksiKeanggotaanUpdate) {
            set_message('toastr_msg', 'Redaksi Keanggotaan berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Redaksi Keanggotaan gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/RedaksiKeanggotaan');
    }
}
