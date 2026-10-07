<?php

namespace PerpustakaanKelilingCms\Controllers;

use PerpustakaanKelilingCms\Models\PetugasModel;

class Petugas extends \Base\Controllers\BaseController
{
    protected $db;
    protected PetugasModel $petugasModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->petugasModel = new PetugasModel();
    }

    public function index()
    {
        $data['title'] = 'Master Petugas Perpustakaan Keliling';
        return view('PerpustakaanKelilingCms\Views\petugas\index', $data);
    }

    public function data()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $result = $this->petugasModel->getDatatableData($this->request->getGetPost() ?: []);

        return $this->response->setJSON($result);
    }

    public function cariUser()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $q       = trim((string)$this->request->getGet('q'));
        $results = $this->petugasModel->cariUserNonPetugas($q);

        return $this->response->setJSON(['results' => $results]);
    }

    public function get(int $id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $petugas = $this->petugasModel->getPetugasWithUser($id);
        if (!$petugas) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data petugas tidak ditemukan']);
        }

        return $this->response->setJSON([
            'status'  => true,
            'petugas' => $petugas,
        ]);
    }

    public function simpan()
    {
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setStatusCode(404);
        }

        $id     = $this->request->getPost('id');
        $userId = $this->request->getPost('user_id');

        if (!$id) {
            $rules = [
                'user_id' => 'required|is_unique[perpus_keliling_petugas.user_id]',
            ];
            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => false,
                    'errors' => $this->validator->getErrors(),
                ]);
            }
        }

        $dataSave = [
            'tampilkan_email' => $this->request->getPost('tampilkan_email') ? 1 : 0,
            'tampilkan_hp'    => $this->request->getPost('tampilkan_hp') ? 1 : 0,
        ];

        if ($id) {
            $this->petugasModel->update($id, $dataSave);
        } else {
            $dataSave['user_id'] = (int) $userId;
            $dataSave['status']  = 'aktif';
            $this->petugasModel->insert($dataSave);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => $id ? 'Petugas berhasil diperbarui' : 'Petugas berhasil ditambahkan',
        ]);
    }

    public function status()
    {
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setStatusCode(404);
        }

        $id     = (int) $this->request->getPost('id');
        $status = $this->request->getPost('status');

        if ($this->petugasModel->setStatus($id, $status)) {
            return $this->response->setJSON(['status' => true, 'message' => 'Status petugas berhasil diubah']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal mengubah status petugas']);
    }
}
