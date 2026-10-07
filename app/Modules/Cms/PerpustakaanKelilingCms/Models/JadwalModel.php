<?php

namespace PerpustakaanKelilingCms\Models;

use CodeIgniter\Model;

class JadwalModel extends Model
{
    protected $table = 'perpus_keliling_jadwal';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = \App\Entities\PerpusKelilingJadwal::class;

    protected $allowedFields = [
        'slug',
        'unit_id',
        'lokasi_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
        'status',
        'alasan_batal',
        'dibatalkan_by',
        'dibatalkan_at',
        'created_by',
        'updated_by',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $beforeInsert = ['setCreatedBy'];
    protected $beforeUpdate = ['setUpdatedBy'];

    protected function setCreatedBy(array $data)
    {
        if (!isset($data['data']['created_by']) && function_exists('user_id')) {
            $data['data']['created_by'] = user_id();
        }
        return $data;
    }

    protected function setUpdatedBy(array $data)
    {
        if (!isset($data['data']['updated_by']) && function_exists('user_id')) {
            $data['data']['updated_by'] = user_id();
        }
        return $data;
    }

    /**
     * Scope status tampil untuk query database (SQL).
     */
    public function scopeStatus($builder, string $status, ?string $nowStr = null, string $alias = 'perpus_keliling_jadwal')
    {
        $nowStr = $nowStr ?? date('Y-m-d H:i:s');
        $start = "CONCAT({$alias}.tanggal, ' ', {$alias}.jam_mulai)";
        $end = "CONCAT({$alias}.tanggal, ' ', {$alias}.jam_selesai)";

        switch ($status) {
            case 'akan_datang':
                $builder->where("{$alias}.status", 'aktif')->where("$start >", $nowStr);
                break;
            // ... case lain sama, ganti perpus_keliling_jadwal. dengan {$alias}.
        }
        return $builder;
    }

    /**
     * Cek bentrok unit pada tanggal & jam overlap.
     */
    public function adaBentrokUnit(int $unitId, string $tanggal, string $mulai, string $selesai, ?int $exceptId = null): bool
    {
        $b = $this->where('unit_id', $unitId)
            ->where('tanggal', $tanggal)
            ->where('status', 'aktif')
            ->where('jam_mulai <', $selesai)
            ->where('jam_selesai >', $mulai);

        if ($exceptId) {
            $b->where('id !=', $exceptId);
        }

        return $b->countAllResults() > 0;
    }

    /**
     * Mengembalikan daftar petugas_id yang bentrok pada tanggal & jam overlap.
     */
    public function petugasBentrok(array $petugasIds, string $tanggal, string $mulai, string $selesai, ?int $exceptId = null): array
    {
        if (empty($petugasIds)) {
            return [];
        }

        $b = $this->db->table('perpus_keliling_jadwal_petugas jp')
            ->select('jp.petugas_id, u.username, j.jam_mulai, j.jam_selesai, l.nama_lokasi')
            ->distinct()
            ->join('perpus_keliling_jadwal j', 'j.id = jp.jadwal_id')
            ->join('perpus_keliling_petugas p', 'p.id = jp.petugas_id', 'left')
            ->join('users u', 'u.id = p.user_id', 'left')
            ->join('perpus_keliling_lokasi l', 'l.id = j.lokasi_id', 'left')
            ->whereIn('jp.petugas_id', $petugasIds)
            ->where('j.tanggal', $tanggal)
            ->where('j.status', 'aktif')
            ->where('j.jam_mulai <', $selesai)
            ->where('j.jam_selesai >', $mulai);

        if ($exceptId) {
            $b->where('j.id !=', $exceptId);
        }

        return $b->get()->getResultArray();
    }

    /**
     * Buat slug unik dengan format nama-lokasi-tanggal.
     */
    public function buatSlug(string $namaLokasi, string $tanggal, ?int $exceptId = null): string
    {
        helper('url');
        $base = url_title($namaLokasi . ' ' . $tanggal, '-', true);

        do {
            $slug = $base . '-' . bin2hex(random_bytes(4)); // 8 karakter hex
        } while ($this->slugAda($slug, $exceptId));

        return $slug;
    }

    public function slugAda(string $slug, ?int $exceptId = null): bool
    {
        $b = $this->where('slug', $slug);
        if ($exceptId) {
            $b->where('id !=', $exceptId);
        }
        return $b->countAllResults() > 0;
    }

    /**
     * Mendapatkan daftar jadwal berpaginasi beserta filter tab, keyword, dan rentang tanggal.
     */
    public function getJadwalPaginated(array $filters, int $perPage = 10): array
    {
        $tab = $filters['tab'] ?? 'akan-datang';
        $q = $filters['q'] ?? '';
        $rentang = $filters['rentang'] ?? 'semua';
        $dari = $filters['dari'] ?? null;
        $sampai = $filters['sampai'] ?? null;
        $page = (int) ($filters['page'] ?? 1);

        $nowStr = date('Y-m-d H:i:s');
        $todayStr = date('Y-m-d');

        $builder = $this->db->table($this->table . ' j')
            ->select('j.*, u.nama_unit, u.nomor_kendaraan, l.nama_lokasi, l.alamat, (SELECT f.file FROM perpus_keliling_unit_foto f WHERE f.unit_id = j.unit_id ORDER BY f.is_utama DESC, f.urutan ASC, f.id ASC LIMIT 1) as foto_unit', false)
            ->join('perpus_keliling_unit u', 'u.id = j.unit_id', 'left')
            ->join('perpus_keliling_lokasi l', 'l.id = j.lokasi_id', 'left');

        // Filter Tab & Order
        if ($tab === 'riwayat') {
            $builder->groupStart()
                ->where('j.status', 'aktif')->where("CONCAT(j.tanggal, ' ', j.jam_selesai) <", $nowStr)
                ->orGroupStart()->where('j.status', 'dibatalkan')->where('j.tanggal <', $todayStr)->groupEnd()
                ->groupEnd()
                ->orderBy('j.tanggal', 'DESC')
                ->orderBy('j.jam_mulai', 'DESC');
        } else {
            // tab = akan-datang / jadwal-layanan
            $builder->groupStart()
                ->where('j.status', 'aktif')->where("CONCAT(j.tanggal, ' ', j.jam_selesai) >=", $nowStr)
                ->orGroupStart()->where('j.status', 'dibatalkan')->where('j.tanggal >=', $todayStr)->groupEnd()
                ->groupEnd()
                ->orderBy("(CASE WHEN j.status='aktif' AND CONCAT(j.tanggal, ' ', j.jam_mulai) <= '{$nowStr}' AND CONCAT(j.tanggal, ' ', j.jam_selesai) >= '{$nowStr}' THEN 0 ELSE 1 END)", 'ASC', false)
                ->orderBy('j.tanggal', 'ASC')
                ->orderBy('j.jam_mulai', 'ASC');

            // Capsule filter rentang (jika dari & sampai kosong)
            if (empty($dari) && empty($sampai)) {
                if ($rentang === 'hari-ini') {
                    $builder->where('j.tanggal', $todayStr);
                } elseif ($rentang === 'minggu-ini') {
                    $endOfWeek = date('Y-m-d', strtotime('Sunday this week'));
                    $builder->where('j.tanggal >=', $todayStr)->where('j.tanggal <=', $endOfWeek);
                } elseif ($rentang === 'bulan-ini') {
                    $endOfMonth = date('Y-m-t');
                    $builder->where('j.tanggal >=', $todayStr)->where('j.tanggal <=', $endOfMonth);
                }
            }
        }

        // Search Q
        if ($q !== '') {
            $builder->groupStart()
                ->like('l.nama_lokasi', $q)
                ->orLike('l.alamat', $q)
                ->groupEnd();
        }

        // Custom Date Range
        if ($dari) {
            $builder->where('j.tanggal >=', $dari);
        }
        if ($sampai) {
            $builder->where('j.tanggal <=', $sampai);
        }

        // Count Total for Pagination
        $countBuilder = clone $builder;
        $totalRows = $countBuilder->countAllResults();

        // Pagination Limit & Offset
        $offset = ($page - 1) * $perPage;
        $items = $builder->limit($perPage, $offset)->get()->getResultArray();

        // Cast items to Entity for Badge calculation
        $jadwalEntities = array_map(function ($row) {
            $item = new \App\Entities\PerpusKelilingJadwal($row);
            $item->nama_unit = $row['nama_unit'];
            $item->nomor_kendaraan = $row['nomor_kendaraan'];
            $item->nama_lokasi = $row['nama_lokasi'];
            $item->alamat = $row['alamat'];
            $item->foto_unit = !empty($row['foto_unit']) ? base_url('uploads/perpus_keliling/unit/' . $row['foto_unit']) : null;
            return $item;
        }, $items);

        return [
            'items' => $jadwalEntities,
            'totalRows' => $totalRows,
        ];
    }

    /**
     * Mendapatkan detail jadwal berdasarkan ID beserta relasi unit dan lokasi.
     */
    public function getDetailJadwalById(int $id): ?array
    {
        return $this->db->table($this->table . ' j')
            ->select('j.*, u.nama_unit, u.nomor_kendaraan, l.nama_lokasi, l.alamat, l.latitude, l.longitude')
            ->join('perpus_keliling_unit u', 'u.id = j.unit_id', 'left')
            ->join('perpus_keliling_lokasi l', 'l.id = j.lokasi_id', 'left')
            ->where('j.id', $id)
            ->get()->getRowArray();
    }

    /**
     * Mendapatkan detail jadwal berdasarkan slug beserta relasi unit dan lokasi.
     */
    public function getDetailJadwalBySlug(string $slug): ?array
    {
        return $this->db->table($this->table . ' j')
            ->select('j.*, u.nama_unit, u.nomor_kendaraan, u.deskripsi as deskripsi_unit, u.id as unit_id_val, l.nama_lokasi, l.alamat, l.latitude, l.longitude, l.keterangan as keterangan_lokasi')
            ->join('perpus_keliling_unit u', 'u.id = j.unit_id', 'left')
            ->join('perpus_keliling_lokasi l', 'l.id = j.lokasi_id', 'left')
            ->where('j.slug', $slug)
            ->get()->getRowArray();
    }

    /**
     * Mendapatkan daftar petugas berdasarkan ID Jadwal.
     */
    public function getPetugasByJadwalId(int $jadwalId): array
    {
        return $this->db->table('perpus_keliling_jadwal_petugas jp')
            ->select('jp.petugas_id as id, p.tampilkan_email, p.tampilkan_hp, u.username, u.email, u.phone')
            ->join('perpus_keliling_petugas p', 'p.id = jp.petugas_id', 'left')
            ->join('users u', 'u.id = p.user_id', 'left')
            ->where('jp.jadwal_id', $jadwalId)
            ->get()->getResultArray();
    }

    /**
     * Mendapatkan data DataTables server-side secara manual dari model.
     */
    public function getDatatableData(array $requestParams): array
    {
        $searchValue = $requestParams['search']['value'] ?? '';
        $start = (int) ($requestParams['start'] ?? 0);
        $length = (int) ($requestParams['length'] ?? 10);
        $draw = (int) ($requestParams['draw'] ?? 1);

        $dari = $requestParams['dari'] ?? null;
        $sampai = $requestParams['sampai'] ?? null;
        $unitId = $requestParams['unit_id'] ?? null;
        $lokasiId = $requestParams['lokasi_id'] ?? null;
        $petugasId = $requestParams['petugas_id'] ?? null;
        $statusParam = $requestParams['status_tampil'] ?? null;

        $builder = $this->getFilteredBuilder($requestParams);

        $totalRecords = $builder->countAllResults(false);

        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('l.nama_lokasi', $searchValue)
                ->orLike('u.nama_unit', $searchValue)
                ->orLike('u.nomor_kendaraan', $searchValue)
                ->groupEnd();
        }

        $filteredRecords = $builder->countAllResults(false);

        $builder->orderBy('j.tanggal', 'DESC');
        $builder->orderBy('j.jam_mulai', 'DESC');
        if ($length > 0) {
            $builder->limit($length, $start);
        }
        $data = $builder->get()->getResultArray();

        $rows = [];
        foreach ($data as $r) {
            $entity = new \App\Entities\PerpusKelilingJadwal($r);

            $btnDetail = '<button type="button" class="btn btn-sm btn-info btn-detail" data-id="' . $r['id'] . '" title="Detail"><i class="fa fa-eye"></i></button>';
            $btnEdit = $entity->bisaEdit()
                ? '<button type="button" class="btn btn-sm btn-primary btn-edit" data-id="' . $r['id'] . '" title="Edit"><i class="fa fa-edit"></i></button>'
                : '';
            $btnBatal = $entity->bisaBatalkan()
                ? '<button type="button" class="btn btn-sm btn-danger btn-batal" data-id="' . $r['id'] . '" title="Batalkan"><i class="fa fa-times"></i></button>'
                : '';
            $btnDok = $entity->bisaDokumentasi()
                ? '<button type="button" class="btn btn-sm btn-warning btn-dokumentasi" data-id="' . $r['id'] . '" title="Kelola Dokumentasi"><i class="fa fa-camera"></i></button>'
                : '';
            $btnDup = '<button type="button" class="btn btn-sm btn-secondary btn-duplikat" data-id="' . $r['id'] . '" title="Duplikat Jadwal"><i class="fa fa-copy"></i></button>';

            $action = '<div class="btn-group">' . $btnDetail . $btnEdit . $btnBatal . $btnDok . $btnDup . '</div>';

            $rows[] = [
                'id' => $r['id'],
                'unit_id' => $r['unit_id'],
                'lokasi_id' => $r['lokasi_id'],
                'raw_tanggal' => $r['tanggal'],
                'jam_mulai' => date('H:i', strtotime($r['jam_mulai'])),
                'jam_selesai' => date('H:i', strtotime($r['jam_selesai'])),
                'keterangan' => esc($r['keterangan']),
                'raw_keterangan' => $r['keterangan'],
                'tanggal' => date('d-m-Y', strtotime($r['tanggal'])),
                'jam' => date('H:i', strtotime($r['jam_mulai'])) . ' - ' . date('H:i', strtotime($r['jam_selesai'])),
                'unit' => esc($r['nama_unit'] ?? '-') . ' (' . esc($r['nomor_kendaraan'] ?? '-') . ')',
                'nama_lokasi' => esc($r['nama_lokasi'] ?? '-'),
                'daftar_petugas' => esc($r['daftar_petugas'] ?? '-'),
                'status_tampil' => $entity->getBadgeStatusHtml(),
                'total_dokumentasi' => '<span class="badge bg-info">' . esc($r['total_dokumentasi']) . ' foto</span>',
                'action' => $action,
            ];
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $rows,
        ];
    }

    public function getFilteredBuilder(array $p)
    {
        $builder = $this->db->table($this->table . ' j')
            ->select('j.*, u.nama_unit, u.nomor_kendaraan, l.nama_lokasi,
            (SELECT GROUP_CONCAT(DISTINCT usr.username SEPARATOR ", ")
             FROM perpus_keliling_jadwal_petugas jp
             LEFT JOIN perpus_keliling_petugas p ON p.id = jp.petugas_id
             LEFT JOIN users usr ON usr.id = p.user_id
             WHERE jp.jadwal_id = j.id) as daftar_petugas,
            (SELECT COUNT(d.id) FROM perpus_keliling_dokumentasi d WHERE d.jadwal_id = j.id) as total_dokumentasi')
            ->join('perpus_keliling_unit u', 'u.id = j.unit_id', 'left')
            ->join('perpus_keliling_lokasi l', 'l.id = j.lokasi_id', 'left');

        if (!empty($p['dari']))
            $builder->where('j.tanggal >=', $p['dari']);
        if (!empty($p['sampai']))
            $builder->where('j.tanggal <=', $p['sampai']);
        if (!empty($p['unit_id']))
            $builder->where('j.unit_id', (int) $p['unit_id']);
        if (!empty($p['lokasi_id']))
            $builder->where('j.lokasi_id', (int) $p['lokasi_id']);
        if (!empty($p['petugas_id'])) {
            $builder->where('j.id IN (SELECT jadwal_id FROM perpus_keliling_jadwal_petugas WHERE petugas_id = ' . (int) $p['petugas_id'] . ')');
        }
        if (!empty($p['status_tampil'])) {
            $this->scopeStatus($builder, $p['status_tampil'], null, 'j');
        }

        return $builder;
    }
}
