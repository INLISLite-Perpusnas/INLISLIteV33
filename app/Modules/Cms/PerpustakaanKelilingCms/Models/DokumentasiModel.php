<?php

namespace PerpustakaanKelilingCms\Models;

use CodeIgniter\Model;

class DokumentasiModel extends Model
{
    protected $table            = 'perpus_keliling_dokumentasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = [
        'jadwal_id',
        'file',
        'keterangan',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $beforeInsert = ['setCreatedBy'];

    protected function setCreatedBy(array $data)
    {
        if (!isset($data['data']['created_by']) && function_exists('user_id')) {
            $data['data']['created_by'] = user_id();
        }
        return $data;
    }

    /**
     * Mendapatkan daftar dokumentasi berdasarkan ID Jadwal beserta URL file.
     */
    public function getDokumentasiByJadwalId(int $jadwalId): array
    {
        $dokumentasi = $this->where('jadwal_id', $jadwalId)->findAll();
        foreach ($dokumentasi as &$d) {
            $d['url'] = base_url('uploads/perpus_keliling/dokumentasi/' . $d['file']);
        }
        return $dokumentasi;
    }
}
