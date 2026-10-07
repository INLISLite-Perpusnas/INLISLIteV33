<?php

namespace PerpustakaanKelilingCms\Controllers;

use PerpustakaanKelilingCms\Models\DokumentasiModel;
use PerpustakaanKelilingCms\Models\JadwalModel;
use PerpustakaanKelilingCms\Models\JadwalPetugasModel;
use PerpustakaanKelilingCms\Models\UnitModel;
use PerpustakaanKelilingCms\Models\LokasiModel;
use PerpustakaanKelilingCms\Models\PetugasModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Jadwal extends \Base\Controllers\BaseController
{
    protected $db;
    protected JadwalModel $jadwalModel;
    protected JadwalPetugasModel $jadwalPetugasModel;
    protected UnitModel $unitModel;
    protected LokasiModel $lokasiModel;
    protected PetugasModel $petugasModel;
    protected DokumentasiModel $dokumentasiModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->jadwalModel = new JadwalModel();
        $this->jadwalPetugasModel = new JadwalPetugasModel();
        $this->unitModel = new UnitModel();
        $this->lokasiModel = new LokasiModel();
        $this->petugasModel = new PetugasModel();
        $this->dokumentasiModel = new DokumentasiModel();
    }

    public function index()
    {
        $data['title'] = 'Kelola Jadwal Perpustakaan Keliling';
        $data['units'] = $this->unitModel->where('status', 'aktif')->findAll();
        $data['lokasis'] = $this->lokasiModel->where('status', 'aktif')->findAll();
        $data['petugas'] = $this->petugasModel->getPetugasWithUser(null, 'aktif');

        return view('PerpustakaanKelilingCms\Views\jadwal\index', $data);
    }

    public function data()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $params = array_merge(
            $this->request->getGetPost() ?: [],
            [
                'dari' => $this->request->getPost('dari') ?: $this->request->getGet('dari'),
                'sampai' => $this->request->getPost('sampai') ?: $this->request->getGet('sampai'),
                'unit_id' => $this->request->getPost('unit_id') ?: $this->request->getGet('unit_id'),
                'lokasi_id' => $this->request->getPost('lokasi_id') ?: $this->request->getGet('lokasi_id'),
                'petugas_id' => $this->request->getPost('petugas_id') ?: $this->request->getGet('petugas_id'),
                'status_tampil' => $this->request->getPost('status_tampil') ?: $this->request->getGet('status_tampil'),
            ]
        );

        $result = $this->jadwalModel->getDatatableData($params);

        return $this->response->setJSON($result);
    }



    public function detail($id = 0)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $row = $this->jadwalModel->getDetailJadwalById((int) $id);
        if (!$row) {
            return $this->response->setJSON(['status' => false, 'message' => 'Jadwal tidak ditemukan']);
        }

        $entity = new \App\Entities\PerpusKelilingJadwal($row);
        $petugas = $this->jadwalModel->getPetugasByJadwalId((int) $id);
        $dokumentasi = $this->dokumentasiModel->getDokumentasiByJadwalId((int) $id);

        return $this->response->setJSON([
            'status' => true,
            'jadwal' => [
                'tanggal' => date('d-m-Y', strtotime($row['tanggal'])),
                'jam' => date('H:i', strtotime($row['jam_mulai'])) . ' - ' . date('H:i', strtotime($row['jam_selesai'])),
                'unit' => ($row['nama_unit'] ?? '-') . ' (' . ($row['nomor_kendaraan'] ?? '-') . ')',
                'lokasi' => $row['nama_lokasi'] ?? '-',
                'alamat' => $row['alamat'] ?? '-',
                'keterangan' => $row['keterangan'] ?: '-',
                'alasan_batal' => $row['alasan_batal'] ?? '',
                'status_html' => $entity->getBadgeStatusHtml(),
            ],
            'dokumentasi' => array_map(fn($d) => [
                'url' => $d['url'],
                'keterangan' => $d['keterangan'] ?? '',
            ], $dokumentasi),
            'petugas' => $petugas,
        ]);
    }

    public function simpan()
    {
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setStatusCode(404);
        }

        $id = $this->request->getPost('id');
        $unitId = (int) $this->request->getPost('unit_id');
        $lokasiId = (int) $this->request->getPost('lokasi_id');
        $petugasIds = (array) $this->request->getPost('petugas_ids');
        $petugasIds = array_values(array_unique(array_filter(array_map('intval', $petugasIds))));
        $tanggal = trim((string) $this->request->getPost('tanggal'));
        $jamMulai = trim((string) $this->request->getPost('jam_mulai'));
        $jamSelesai = trim((string) $this->request->getPost('jam_selesai'));
        $keterangan = trim((string) $this->request->getPost('keterangan'));

        $rules = [
            'unit_id' => 'required|is_natural_no_zero',
            'lokasi_id' => 'required|is_natural_no_zero',
            'tanggal' => 'required|valid_date[Y-m-d]',
            'jam_mulai' => 'required|regex_match[/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/]',
            'jam_selesai' => 'required|regex_match[/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/]',
        ];

        if (empty($petugasIds)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Pilih minimal satu petugas.']);
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }
        if (empty($id) && $tanggal < date('Y-m-d')) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Tanggal kunjungan tidak boleh sebelum hari ini.',
            ]);
        }

        if (strtotime($jamSelesai) <= strtotime($jamMulai)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Jam selesai harus lebih akhir dari jam mulai.',
            ]);
        }

        $exceptId = $id ? (int) $id : null;

        if ($exceptId) {
            $existing = $this->jadwalModel->find($exceptId);
            if (!$existing || !$existing->bisaEdit()) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Jadwal hanya dapat diubah selama statusnya "Akan datang".',
                ]);
            }
        }

        // 1. Cek bentrok Unit
        if ($this->jadwalModel->adaBentrokUnit($unitId, $tanggal, $jamMulai, $jamSelesai, $exceptId)) {
            $unit = $this->unitModel->find($unitId);
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Unit "' . ($unit['nama_unit'] ?? '') . '" sudah terjadwal di lokasi lain pada tanggal & jam tersebut.',
            ]);
        }

        // 2. Cek bentrok Petugas
        $bentrokPetugas = $this->jadwalModel->petugasBentrok($petugasIds, $tanggal, $jamMulai, $jamSelesai, $exceptId);
        if (!empty($bentrokPetugas)) {
            $namaList = implode(', ', array_column($bentrokPetugas, 'username'));
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Petugas berikut sudah terjadwal pada waktu yang sama: ' . $namaList,
            ]);
        }

        $this->db->transStart();

        if ($exceptId) {
            $dataSave = [
                'unit_id' => $unitId,
                'lokasi_id' => $lokasiId,
                'tanggal' => $tanggal,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'keterangan' => $keterangan,
            ];
            $this->jadwalModel->update($exceptId, $dataSave);
            $jadwalId = $exceptId;
        } else {
            $lokasi = $this->lokasiModel->find($lokasiId);
            $slug = $this->jadwalModel->buatSlug($lokasi['nama_lokasi'] ?? 'lokasi', $tanggal);
            $dataSave = [
                'slug' => $slug,
                'unit_id' => $unitId,
                'lokasi_id' => $lokasiId,
                'tanggal' => $tanggal,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'keterangan' => $keterangan,
                'status' => 'aktif',
            ];
            $jadwalId = (int) $this->jadwalModel->insert($dataSave);
        }

        // Sync Pivot Petugas
        $this->jadwalPetugasModel->syncPetugas($jadwalId, $petugasIds);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyimpan data jadwal.']);
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => $id ? 'Jadwal berhasil diperbarui' : 'Jadwal berhasil ditambahkan',
        ]);
    }

    public function batalkan()
    {
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setStatusCode(404);
        }

        $id = (int) $this->request->getPost('id');
        $alasanBatal = trim((string) $this->request->getPost('alasan_batal'));

        if (mb_strlen($alasanBatal) < 5) {
            return $this->response->setJSON(['status' => false, 'message' => 'Alasan pembatalan minimal 5 karakter.']);
        }

        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal || !$jadwal->bisaBatalkan()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Jadwal yang sudah selesai atau dibatalkan tidak dapat dibatalkan.']);
        }

        $updateData = [
            'status' => 'dibatalkan',
            'alasan_batal' => $alasanBatal,
            'dibatalkan_by' => function_exists('user_id') ? user_id() : null,
            'dibatalkan_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->jadwalModel->update($id, $updateData)) {
            return $this->response->setJSON(['status' => true, 'message' => 'Jadwal berhasil dibatalkan.']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Gagal membatalkan jadwal.']);
    }

    public function export()
    {
        $params = $this->request->getGet();
        $rows = $this->jadwalModel->getFilteredBuilder($params)
            ->orderBy('j.tanggal', 'DESC')
            ->orderBy('j.jam_mulai', 'DESC')
            ->get()->getResultArray();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Jadwal Perpusling');

        // Header
        $headers = ['No', 'Tanggal', 'Jam', 'Unit', 'Lokasi', 'Petugas', 'Status', 'Jumlah Dokumentasi'];
        $colIndex = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($colIndex . '1', $h);
            $sheet->getStyle($colIndex . '1')->getFont()->setBold(true);
            $colIndex++;
        }

        $rowNum = 2;
        foreach ($rows as $idx => $r) {
            $entity = new \App\Entities\PerpusKelilingJadwal($r);
            $statusLabel = str_replace('_', ' ', strtoupper($entity->getStatusTampil()));

            $sheet->setCellValue('A' . $rowNum, $idx + 1);
            $sheet->setCellValueExplicit('B' . $rowNum, date('d-m-Y', strtotime($r['tanggal'])), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $rowNum, date('H:i', strtotime($r['jam_mulai'])) . ' - ' . date('H:i', strtotime($r['jam_selesai'])));
            $sheet->setCellValueExplicit('D' . $rowNum, ($r['nama_unit'] ?? '-') . ' (' . ($r['nomor_kendaraan'] ?? '-') . ')', DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('E' . $rowNum, $r['nama_lokasi'] ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('F' . $rowNum, $r['daftar_petugas'] ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValue('G' . $rowNum, $statusLabel);
            $sheet->setCellValue('H' . $rowNum, $r['total_dokumentasi']);
            $rowNum++;
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'jadwal-perpustakaan-keliling_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
