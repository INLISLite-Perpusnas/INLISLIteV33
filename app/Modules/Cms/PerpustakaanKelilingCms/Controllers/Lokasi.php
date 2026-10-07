<?php

namespace PerpustakaanKelilingCms\Controllers;

use PerpustakaanKelilingCms\Models\LokasiModel;

class Lokasi extends \Base\Controllers\BaseController
{
    protected $db;
    protected LokasiModel $lokasiModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->lokasiModel = new LokasiModel();
    }

    public function index()
    {
        $data['title'] = 'Master Lokasi Perpustakaan Keliling';
        return view('PerpustakaanKelilingCms\Views\lokasi\index', $data);
    }

    public function data()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $result = $this->lokasiModel->getDatatableData($this->request->getGetPost() ?: []);

        return $this->response->setJSON($result);
    }

    public function get(int $id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $lokasi = $this->lokasiModel->find($id);
        if (!$lokasi) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data lokasi tidak ditemukan']);
        }

        return $this->response->setJSON([
            'status' => true,
            'lokasi' => $lokasi,
        ]);
    }

    public function simpan()
    {
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setStatusCode(404);
        }

        $id = $this->request->getPost('id');

        $rules = [
            'nama_lokasi' => 'required|max_length[150]',
            'alamat'      => 'required',
            'latitude'    => 'required|numeric|greater_than_equal_to[-90]|less_than_equal_to[90]',
            'longitude'   => 'required|numeric|greater_than_equal_to[-180]|less_than_equal_to[180]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $dataSave = [
            'nama_lokasi' => trim($this->request->getPost('nama_lokasi')),
            'alamat'      => trim($this->request->getPost('alamat')),
            'latitude'    => (float) $this->request->getPost('latitude'),
            'longitude'   => (float) $this->request->getPost('longitude'),
            'keterangan'  => trim($this->request->getPost('keterangan')),
        ];

        if ($id) {
            $this->lokasiModel->update($id, $dataSave);
        } else {
            $dataSave['status'] = 'aktif';
            $this->lokasiModel->insert($dataSave);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => $id ? 'Lokasi berhasil diperbarui' : 'Lokasi berhasil ditambahkan',
        ]);
    }

    public function status()
    {
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setStatusCode(404);
        }

        $id     = (int) $this->request->getPost('id');
        $status = $this->request->getPost('status');

        if ($this->lokasiModel->setStatus($id, $status)) {
            return $this->response->setJSON(['status' => true, 'message' => 'Status lokasi berhasil diubah']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal mengubah status lokasi']);
    }
}
