<?php

namespace KeranjangAnggota\Controllers;

use \CodeIgniter\Files\File;

class KeranjangAnggota extends \hamkamannan\adminigniter\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $keranjanganggotaModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->keranjanganggotaModel = new \KeranjangAnggota\Models\KeranjangAnggotaModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/keranjanganggota/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }
    public function index()
    {
        $query = $this->keranjanganggotaModel
            ->select('t_keranjanganggota.*')
            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->select('t_anggota_id.name as nama')
            ->select('t_anggota_id.MemberNo as MembersNo')
            ->join('users created', 'created.id = t_keranjanganggota.created_by', 'left')
            ->join('users updated', 'updated.id = t_keranjanganggota.updated_by', 'left')
            ->join('t_anggota t_anggota_id', 't_anggota_id.id = t_keranjanganggota.t_anggota_id', 'left');

        $keranjanganggotas = $query->findAll();

        $this->data['title'] = 'KeranjangAnggota';
        $this->data['keranjanganggotas'] = $keranjanganggotas;
        echo view('KeranjangAnggota\Views\list', $this->data);
    }

    public function create()
    {

        $baseModel = new \hamkamannan\adminigniter\Models\BaseModel();
        $baseModel->setTable('t_anggota');
        $anggota = $baseModel
            ->select('t_anggota.*')
            ->orderBy('name', 'asc')
            ->findAll();

        $this->data['title'] = 'Tambah Sumbangan';
        $this->data['anggota'] = $anggota;
        $this->data['title'] = 'Tambah KeranjangAnggota';

        $this->validation->setRule('t_anggota_id', 't_anggota_id', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {

            $save_data = [
                't_anggota_id' => $this->request->getPost('t_anggota_id'),
                'sort' => $this->request->getPost('sort'),
                'description' => $this->request->getPost('description'),
                'created_by' => user_id(),
            ];

            $newKeranjangAnggotaId = $this->keranjanganggotaModel->insert($save_data);

            if ($newKeranjangAnggotaId) {
                set_message('toastr_msg', lang('KeranjangAnggota.info.successfully_saved'));
                set_message('toastr_type', 'success');
                return redirect()->to('/keranjanganggota');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('KeranjangAnggota.info.failed_saved'));
                echo view('KeranjangAnggota\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('keranjanganggota/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('KeranjangAnggota\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        $this->data['title'] = 'Ubah KeranjangAnggota';
        $keranjanganggota = $this->keranjanganggotaModel->find($id);
        $this->data['keranjanganggota'] = $keranjanganggota;

        $this->validation->setRule('t_anggota_id', 't_anggota_id', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $update_data = [
                    't_anggota_id' => $this->request->getPost('t_anggota_id'),
                    'sort' => $this->request->getPost('sort'),
                    'description' => $this->request->getPost('description'),
                    'updated_by' => user_id(),
                ];

                $keranjanganggotaUpdate = $this->keranjanganggotaModel->update($id, $update_data);

                if ($keranjanganggotaUpdate) {
                    set_message('toastr_msg', 'KeranjangAnggota berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/keranjanganggota');
                } else {
                    set_message('toastr_msg', 'KeranjangAnggota gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'KeranjangAnggota gagal diubah');
                    return redirect()->to('/keranjanganggota/edit/' . $id);
                }
            }
        }


        $this->data['redirect'] = base_url('keranjanganggota/edit/' . $id);
        echo view('KeranjangAnggota\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/keranjanganggota');
        }
        $keranjanganggotaDelete = $this->keranjanganggotaModel->delete($id);
        if ($keranjanganggotaDelete) {
            set_message('toastr_msg', lang('KeranjangAnggota.info.successfully_deleted'));
            set_message('toastr_type', 'success');
            return redirect()->to('/keranjanganggota');
        } else {
            set_message('toastr_msg', lang('KeranjangAnggota.info.failed_deleted'));
            set_message('toastr_type', 'warning');
            set_message('message', lang('KeranjangAnggota.info.failed_deleted'));
            return redirect()->to('/keranjanganggota/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $keranjanganggotaUpdate = $this->keranjanganggotaModel->update($id, array($field => $value));

        if ($keranjanganggotaUpdate) {
            set_message('toastr_msg', ' KeranjangAnggota berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' KeranjangAnggota gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/keranjanganggota');
    }
}
