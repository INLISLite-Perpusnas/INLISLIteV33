<?php

namespace Profil\Models;

use CodeIgniter\Model;

class LayananModel extends Model
{
    protected $table = 'layanan_perpustakaan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'nama_layanan',
        'deskripsi',
        'foto',
        'location_id',
        'jam_layanan',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getLayananWithLocation()
    {
        return $this->db->table($this->table . ' l')
            ->select('l.*, loc.Name as nama_lokasi, loc.Code as kode_lokasi')
            ->join('locations loc', 'loc.ID = l.location_id', 'left')
            ->orderBy('l.urutan', 'ASC')
            ->get()
            ->getResultArray();
    }
}
