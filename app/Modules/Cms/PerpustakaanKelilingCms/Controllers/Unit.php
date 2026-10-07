<?php

namespace PerpustakaanKelilingCms\Controllers;

use PerpustakaanKelilingCms\Models\UnitModel;
use PerpustakaanKelilingCms\Models\UnitFotoModel;
use App\Libraries\PerpusKelilingUpload;

class Unit extends \Base\Controllers\BaseController
{
    protected $db;
    protected UnitModel $unitModel;
    protected UnitFotoModel $unitFotoModel;
    protected PerpusKelilingUpload $uploadService;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->unitModel = new UnitModel();
        $this->unitFotoModel = new UnitFotoModel();
        $this->uploadService = new PerpusKelilingUpload();
    }

    public function index()
    {
        $data['title'] = 'Master Unit Perpustakaan Keliling';
        return view('PerpustakaanKelilingCms\Views\unit\index', $data);
    }

    public function data()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $result = $this->unitModel->getDatatableData($this->request->getGetPost() ?: []);

        return $this->response->setJSON($result);
    }

    public function get(int $id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $unit = $this->unitModel->find($id);
        if (!$unit) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data unit tidak ditemukan']);
        }

        $foto = $this->unitFotoModel
            ->where('unit_id', $id)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        foreach ($foto as &$f) {
            $f['url'] = base_url('uploads/perpus_keliling/unit/' . $f['file']);
        }

        return $this->response->setJSON([
            'status' => true,
            'unit' => $unit,
            'foto' => $foto,
        ]);
    }

    public function simpan()
    {
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setStatusCode(404);
        }

        $id = $this->request->getPost('id');

        $rules = [
            'nama_unit' => 'required|max_length[150]',
            'nomor_kendaraan' => 'required|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $dataSave = [
            'nama_unit' => trim($this->request->getPost('nama_unit')),
            'nomor_kendaraan' => strtoupper(trim($this->request->getPost('nomor_kendaraan'))),
            'deskripsi' => trim($this->request->getPost('deskripsi')),
        ];

        $this->db->transStart();

        if ($id) {
            $this->unitModel->update($id, $dataSave);
            $unitId = (int) $id;
        } else {
            $dataSave['status'] = 'aktif';
            $unitId = (int) $this->unitModel->insert($dataSave);
        }

        // Handle upload multi foto jika ada
        $files = $this->request->getFiles();
        if (isset($files['foto']) && is_array($files['foto'])) {
            $config = config('PerpusKeliling');
            $currentFotoCount = $this->unitFotoModel->where('unit_id', $unitId)->countAllResults();

            foreach ($files['foto'] as $file) {
                if ($file && $file->getError() != UPLOAD_ERR_NO_FILE) {
                    if ($currentFotoCount >= $config->maxFotoUnit) {
                        $this->db->transRollback();
                        return $this->response->setJSON([
                            'status' => false,
                            'message' => 'Gagal upload. Jumlah foto maksimal ' . $config->maxFotoUnit . ' per unit.',
                        ]);
                    }

                    try {
                        $fileName = $this->uploadService->uploadImage($file, 'unit');
                        $this->unitFotoModel->insert([
                            'unit_id' => $unitId,
                            'file' => $fileName,
                            'urutan' => $currentFotoCount + 1,
                            'is_utama' => 0,
                        ]);
                        $currentFotoCount++;
                    } catch (\Exception $e) {
                        $this->db->transRollback();
                        return $this->response->setJSON([
                            'status' => false,
                            'message' => $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        // Pastikan ada foto utama jika unit punya foto
        $this->unitFotoModel->pastikanAdaUtama($unitId);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyimpan data unit.']);
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => $id ? 'Unit berhasil diperbarui' : 'Unit berhasil ditambahkan',
        ]);
    }

    public function status()
    {
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setStatusCode(404);
        }

        $id = (int) $this->request->getPost('id');
        $status = $this->request->getPost('status');

        if ($this->unitModel->setStatus($id, $status)) {
            return $this->response->setJSON(['status' => true, 'message' => 'Status unit berhasil diubah']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal mengubah status unit']);
    }

    public function hapusFoto()
    {
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setStatusCode(404);
        }

        $fotoId = (int) $this->request->getPost('foto_id');
        $foto = $this->unitFotoModel->find($fotoId);

        if (!$foto) {
            return $this->response->setJSON(['status' => false, 'message' => 'Foto tidak ditemukan']);
        }

        $unitId = (int) $foto['unit_id'];
        $this->uploadService->deleteImage($foto['file'], 'unit');
        $this->unitFotoModel->delete($fotoId);

        // Pastikan foto utama tetap ada jika masih ada sisa foto
        $this->unitFotoModel->pastikanAdaUtama($unitId);

        return $this->response->setJSON(['status' => true, 'message' => 'Foto berhasil dihapus']);
    }

    public function setFotoUtama()
    {
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setStatusCode(404);
        }

        $unitId = (int) $this->request->getPost('unit_id');
        $fotoId = (int) $this->request->getPost('foto_id');

        if ($this->unitFotoModel->setUtama($unitId, $fotoId)) {
            return $this->response->setJSON(['status' => true, 'message' => 'Foto utama berhasil diatur']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal mengeset foto utama']);
    }
}
