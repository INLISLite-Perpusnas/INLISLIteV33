<?php

namespace UsulanKoleksi\Controllers;

use \CodeIgniter\Files\File;

class UsulanKoleksi extends \hamkamannan\adminigniter\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $usulankoleksiModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->usulankoleksiModel = new \UsulanKoleksi\Models\UsulanKoleksiModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/usulankoleksi/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }
    public function index()
    {
        $query = $this->usulankoleksiModel
            ->select('t_usulankoleksi.*')
            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_usulankoleksi.created_by', 'left')
            ->join('users updated', 'updated.id = t_usulankoleksi.updated_by', 'left');

        $usulankoleksis = $query->findAll();

        $this->data['title'] = 'UsulanKoleksi';
        $this->data['usulankoleksis'] = $usulankoleksis;
        echo view('UsulanKoleksi\Views\list', $this->data);
    }

    public function create()
    {
        $this->data['title'] = 'Tambah UsulanKoleksi';

        $this->validation->setRule('title', 'Judul', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $slug = url_title($this->request->getPost('title'), '-', TRUE);
            $save_data = [
                'title' => $this->request->getPost('title'),
                'author' => $this->request->getPost('author'),
                'publisher' => $this->request->getPost('publisher'),
                'publishyear' => $this->request->getPost('publishyear'),
                'isbn' => $this->request->getPost('isbn'),
                'slug' => $slug,
                'description' => $this->request->getPost('description'),
                'created_by' => user_id(),
            ];

            $newUsulanKoleksiId = $this->usulankoleksiModel->insert($save_data);

            if ($newUsulanKoleksiId) {
                set_message('toastr_msg', lang('UsulanKoleksi.info.successfully_saved'));
                set_message('toastr_type', 'success');
                return redirect()->to('/usulankoleksi');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('UsulanKoleksi.info.failed_saved'));
                echo view('UsulanKoleksi\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('usulankoleksi/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('UsulanKoleksi\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        $this->data['title'] = 'Ubah UsulanKoleksi';
        $usulankoleksi = $this->usulankoleksiModel->find($id);
        $this->data['usulankoleksi'] = $usulankoleksi;

        $this->validation->setRule('name', 'Nama', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $slug = url_title($this->request->getPost('name'), '-', TRUE);
                $update_data = [
                    'name' => $this->request->getPost('name'),
                    'slug' => $slug,
                    'sort' => $this->request->getPost('sort'),
                    'description' => $this->request->getPost('description'),
                    'updated_by' => user_id(),
                ];

                $usulankoleksiUpdate = $this->usulankoleksiModel->update($id, $update_data);

                if ($usulankoleksiUpdate) {
                    set_message('toastr_msg', 'UsulanKoleksi berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/usulankoleksi');
                } else {
                    set_message('toastr_msg', 'UsulanKoleksi gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'UsulanKoleksi gagal diubah');
                    return redirect()->to('/usulankoleksi/edit/' . $id);
                }
            }
        }


        $this->data['redirect'] = base_url('usulankoleksi/edit/' . $id);
        echo view('UsulanKoleksi\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/usulankoleksi');
        }
        $usulankoleksiDelete = $this->usulankoleksiModel->delete($id);
        if ($usulankoleksiDelete) {
            set_message('toastr_msg', lang('UsulanKoleksi.info.successfully_deleted'));
            set_message('toastr_type', 'success');
            return redirect()->to('/usulankoleksi');
        } else {
            set_message('toastr_msg', lang('UsulanKoleksi.info.failed_deleted'));
            set_message('toastr_type', 'warning');
            set_message('message', lang('UsulanKoleksi.info.failed_deleted'));
            return redirect()->to('/usulankoleksi/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $usulankoleksiUpdate = $this->usulankoleksiModel->update($id, array($field => $value));

        if ($usulankoleksiUpdate) {
            set_message('toastr_msg', ' UsulanKoleksi berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' UsulanKoleksi gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/usulankoleksi');
    }
}
