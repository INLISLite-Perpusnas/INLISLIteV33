<?php

namespace PerpustakaanKeliling\Controllers;

use PerpustakaanKelilingCms\Models\JadwalModel;
use PerpustakaanKelilingCms\Models\UnitFotoModel;
use PerpustakaanKelilingCms\Models\DokumentasiModel;

class PerpustakaanKeliling extends \App\Controllers\BaseController
{
    protected JadwalModel $jadwalModel;
    protected UnitFotoModel $unitFotoModel;
    protected DokumentasiModel $dokModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalModel();
        $this->unitFotoModel = new UnitFotoModel();
        $this->dokModel = new DokumentasiModel();
    }

    public function index()
    {
        $request = service('request');
        $tab = $request->getGet('tab') ?: 'jadwal-layanan';
        $q = trim((string) $request->getGet('q'));
        $rentang = $request->getGet('rentang') ?: 'semua';
        $dari = $request->getGet('dari');
        $sampai = $request->getGet('sampai');
        $page = (int) ($request->getGet('page') ?: 1);

        $config = config('PerpusKeliling');
        $perPage = $config->perPageFrontend;

        $filters = compact('tab', 'q', 'rentang', 'dari', 'sampai', 'page');
        $result = $this->jadwalModel->getJadwalPaginated($filters, $perPage);

        $pager = \Config\Services::pager();
        $paginationLinks = $pager->makeLinks($page, $perPage, $result['totalRows'], 'default_full');

        $data = [
            'title' => 'Jadwal Perpustakaan Keliling',
            'tab' => $tab,
            'q' => $q,
            'rentang' => $rentang,
            'dari' => $dari,
            'sampai' => $sampai,
            'page' => $page,
            'jadwals' => $result['items'],
            'paginationLinks' => $paginationLinks,
            'perPage' => $perPage,
            'totalRows' => $result['totalRows'],
        ];

        return view('PerpustakaanKeliling\Views\index', $data);
    }

    public function detail(string $slug)
    {
        $jadwalRow = $this->jadwalModel->getDetailJadwalBySlug($slug);

        if (!$jadwalRow) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Jadwal perpustakaan keliling tidak ditemukan.');
        }

        $jadwal = new \App\Entities\PerpusKelilingJadwal($jadwalRow);
        $jadwal->nama_unit = $jadwalRow['nama_unit'];
        $jadwal->nomor_kendaraan = $jadwalRow['nomor_kendaraan'];
        $jadwal->deskripsi_unit = $jadwalRow['deskripsi_unit'];
        $jadwal->nama_lokasi = $jadwalRow['nama_lokasi'];
        $jadwal->alamat = $jadwalRow['alamat'];
        $jadwal->latitude = $jadwalRow['latitude'];
        $jadwal->longitude = $jadwalRow['longitude'];
        $jadwal->keterangan_lokasi = $jadwalRow['keterangan_lokasi'];

        // Foto Unit
        $fotoUnit = $this->unitFotoModel->getFotoByUnitId((int) $jadwalRow['unit_id_val']);

        // Petugas
        $petugasList = $this->jadwalModel->getPetugasByJadwalId((int) $jadwalRow['id']);

        // Dokumentasi
        $dokumentasi = $this->dokModel->getDokumentasiByJadwalId((int) $jadwalRow['id']);

        $data = [
            'title' => 'Perpustakaan Keliling - ' . $jadwal->nama_lokasi,
            'jadwal' => $jadwal,
            'fotoUnit' => $fotoUnit,
            'petugasList' => $petugasList,
            'dokumentasi' => $dokumentasi,
        ];

        return view('PerpustakaanKeliling\Views\detail', $data);
    }
}


