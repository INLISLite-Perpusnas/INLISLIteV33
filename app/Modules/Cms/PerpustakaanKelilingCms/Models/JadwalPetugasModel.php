<?php

namespace PerpustakaanKelilingCms\Models;

use CodeIgniter\Model;

class JadwalPetugasModel extends Model
{
    protected $table            = 'perpus_keliling_jadwal_petugas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = [
        'jadwal_id',
        'petugas_id',
    ];

    protected $useTimestamps = false;

    public function syncPetugas(int $jadwalId, array $petugasIds)
    {
        $existing = array_column(
            $this->where('jadwal_id', $jadwalId)->findAll(),
            'petugas_id'
        );

        $toInsert = array_diff($petugasIds, $existing);
        $toDelete = array_diff($existing, $petugasIds);

        if (!empty($toDelete)) {
            $this->where('jadwal_id', $jadwalId)->whereIn('petugas_id', $toDelete)->delete();
        }

        foreach ($toInsert as $pId) {
            $this->insert([
                'jadwal_id'  => $jadwalId,
                'petugas_id' => $pId,
            ]);
        }
    }
}
