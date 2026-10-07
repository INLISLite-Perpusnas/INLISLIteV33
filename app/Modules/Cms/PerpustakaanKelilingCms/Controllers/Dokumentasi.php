<?php

namespace PerpustakaanKelilingCms\Controllers;

use PerpustakaanKelilingCms\Models\DokumentasiModel;
use PerpustakaanKelilingCms\Models\JadwalModel;
use App\Libraries\PerpusKelilingUpload;

class Dokumentasi extends \Base\Controllers\BaseController
{
    protected DokumentasiModel $dokumentasiModel;
    protected JadwalModel $jadwalModel;
    protected PerpusKelilingUpload $uploadService;

    public function __construct()
    {
        $this->dokumentasiModel = new DokumentasiModel();
        $this->jadwalModel = new JadwalModel();
        $this->uploadService = new PerpusKelilingUpload();
    }

    public function listData()
    {
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setStatusCode(404);
        }

        $jadwalId = (int) $this->request->getPost('jadwal_id');
        $list = $this->dokumentasiModel->getDokumentasiByJadwalId($jadwalId);
        foreach ($list as &$item) {
            $item['url'] = base_url('uploads/perpus_keliling/dokumentasi/' . $item['file']);
        }

        return $this->response->setJSON(['status' => true, 'data' => $list]);
    }

    public function upload()
    {
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setStatusCode(404);
        }

        $jadwalId = (int) $this->request->getPost('jadwal_id');
        $keterangan = trim((string) $this->request->getPost('keterangan'));

        $jadwal = $this->jadwalModel->find($jadwalId);
        if (!$jadwal || !$jadwal->bisaDokumentasi()) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Dokumentasi hanya dapat dikelola untuk jadwal yang sedang berlangsung atau sudah selesai.',
            ]);
        }

        $config = config('PerpusKeliling');
        $countEx = $this->dokumentasiModel->where('jadwal_id', $jadwalId)->countAllResults();
        $files = $this->request->getFiles();

        if (!isset($files['file']) || !is_array($files['file'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Pilih setidaknya satu file foto.']);
        }

        $uploadedCount = 0;
        foreach ($files['file'] as $file) {
            if ($file && $file->getError() != UPLOAD_ERR_NO_FILE) {
                if ($countEx >= $config->maxDokumentasi) {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => "{$uploadedCount} foto terunggah. Batas maksimal {$config->maxDokumentasi} foto tercapai.",
                    ]);
                }

                try {
                    $fileName = $this->uploadService->uploadImage($file, 'dokumentasi');
                    $this->dokumentasiModel->insert([
                        'jadwal_id' => $jadwalId,
                        'file' => $fileName,
                        'keterangan' => $keterangan ?: null,
                    ]);
                    $countEx++;
                    $uploadedCount++;
                } catch (\Exception $e) {
                    return $this->response->setJSON(['status' => false, 'message' => $e->getMessage()]);
                }
            }
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => "{$uploadedCount} foto dokumentasi berhasil diunggah.",
        ]);
    }

    public function caption()
    {
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setStatusCode(404);
        }

        $id = (int) $this->request->getPost('id');
        $keterangan = trim((string) $this->request->getPost('keterangan'));

        $dok = $this->dokumentasiModel->find($id);
        if (!$dok) {
            return $this->response->setJSON(['status' => false, 'message' => 'Dokumentasi tidak ditemukan.']);
        }

        $jadwal = $this->jadwalModel->find($dok['jadwal_id']);
        if (!$jadwal || !$jadwal->bisaDokumentasi()) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Dokumentasi hanya dapat dikelola untuk jadwal yang sedang berlangsung atau sudah selesai.',
            ]);
        }

        $this->dokumentasiModel->update($id, ['keterangan' => $keterangan]);
        return $this->response->setJSON(['status' => true]);
    }

    public function hapus()
    {
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setStatusCode(404);
        }

        $id = (int) $this->request->getPost('id');
        $dok = $this->dokumentasiModel->find($id);

        if (!$dok) {
            return $this->response->setJSON(['status' => false, 'message' => 'Dokumentasi tidak ditemukan.']);
        }

        $jadwal = $this->jadwalModel->find($dok['jadwal_id']);
        if (!$jadwal || !$jadwal->bisaDokumentasi()) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Dokumentasi hanya dapat dikelola untuk jadwal yang sedang berlangsung atau sudah selesai.',
            ]);
        }

        $this->dokumentasiModel->delete($id);
        $this->uploadService->deleteImage($dok['file'], 'dokumentasi');

        return $this->response->setJSON(['status' => true, 'message' => 'Foto dokumentasi berhasil dihapus.']);
    }
}
