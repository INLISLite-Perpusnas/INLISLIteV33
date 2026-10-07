<?php

namespace PerpustakaanKelilingCms\Models;

use CodeIgniter\Model;

class UnitFotoModel extends Model
{
    protected $table            = 'perpus_keliling_unit_foto';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = [
        'unit_id',
        'file',
        'urutan',
        'is_utama',
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

    public function setUtama(int $unitId, int $fotoId): bool
    {
        $this->where('unit_id', $unitId)->set(['is_utama' => 0])->update();
        return (bool) $this->update($fotoId, ['is_utama' => 1]);
    }

    public function pastikanAdaUtama(int $unitId)
    {
        $utama = $this->where('unit_id', $unitId)->where('is_utama', 1)->first();
        if (!$utama) {
            $pertama = $this->where('unit_id', $unitId)->orderBy('urutan', 'ASC')->orderBy('id', 'ASC')->first();
            if ($pertama) {
                $this->update($pertama['id'], ['is_utama' => 1]);
            }
        }
    }

    /**
     * Mendapatkan foto unit beserta URL file.
     */
    public function getFotoByUnitId(int $unitId): array
    {
        $fotoUnit = $this->where('unit_id', $unitId)
            ->orderBy('is_utama', 'DESC')
            ->orderBy('urutan', 'ASC')
            ->findAll();

        foreach ($fotoUnit as &$fu) {
            $fu['url'] = base_url('uploads/perpus_keliling/unit/' . $fu['file']);
        }

        return $fotoUnit;
    }
}
