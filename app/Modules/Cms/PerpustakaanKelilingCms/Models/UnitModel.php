<?php

namespace PerpustakaanKelilingCms\Models;

use CodeIgniter\Model;

class UnitModel extends Model
{
    protected $table            = 'perpus_keliling_unit';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'nama_unit',
        'nomor_kendaraan',
        'deskripsi',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

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

    public function setStatus(int $id, string $status): bool
    {
        if (!in_array($status, ['aktif', 'nonaktif'], true)) {
            return false;
        }
        return (bool) $this->update($id, ['status' => $status]);
    }

    /**
     * Mendapatkan data DataTables server-side secara manual dari model.
     */
    public function getDatatableData(array $requestParams): array
    {
        $searchValue = $requestParams['search']['value'] ?? '';
        $start       = (int) ($requestParams['start'] ?? 0);
        $length      = (int) ($requestParams['length'] ?? 10);
        $draw        = (int) ($requestParams['draw'] ?? 1);

        $builder = $this->db->table($this->table . ' u')
            ->select('u.id, u.nama_unit, u.nomor_kendaraan, u.deskripsi, u.status, (SELECT COUNT(f.id) FROM perpus_keliling_unit_foto f WHERE f.unit_id = u.id) as total_foto');

        $totalRecords = $builder->countAllResults(false);

        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('u.nama_unit', $searchValue)
                ->orLike('u.nomor_kendaraan', $searchValue)
                ->orLike('u.deskripsi', $searchValue)
                ->groupEnd();
        }

        $filteredRecords = $builder->countAllResults(false);

        $builder->orderBy('u.id', 'DESC');
        $builder->limit($length, $start);
        $data = $builder->get()->getResultArray();

        $rows = [];
        foreach ($data as $item) {
            $statusBadge = $item['status'] === 'aktif' 
                ? '<span class="ina-badge ina-badge-success">Aktif</span>' 
                : '<span class="ina-badge ina-badge-neutral">Nonaktif</span>';

            $totalFotoBadge = '<span class="ina-badge ina-badge-info">' . esc($item['total_foto']) . ' foto</span>';

            $statusBtnIcon  = $item['status'] === 'aktif' ? 'fa-eye-slash' : 'fa-eye';
            $statusBtnText  = $item['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan';
            $newStatus      = $item['status'] === 'aktif' ? 'nonaktif' : 'aktif';

            $action = '<div class="ina-actions">'
                . '<button type="button" class="ina-action btn-edit" data-id="' . $item['id'] . '" title="Edit Unit / Kelola Foto" aria-label="Edit Unit / Kelola Foto"><i class="fas fa-pen" aria-hidden="true"></i></button>'
                . '<button type="button" class="ina-action btn-toggle-status" data-id="' . $item['id'] . '" data-status="' . $newStatus . '" title="' . $statusBtnText . '" aria-label="' . $statusBtnText . '"><i class="fas ' . $statusBtnIcon . '" aria-hidden="true"></i></button>'
                . '</div>';

            $rows[] = [
                'id'                  => $item['id'],
                'nama_unit'           => esc($item['nama_unit']),
                'nomor_kendaraan'     => esc($item['nomor_kendaraan']),
                'deskripsi'           => esc($item['deskripsi']),
                'raw_nama_unit'       => $item['nama_unit'],
                'raw_nomor_kendaraan' => $item['nomor_kendaraan'],
                'raw_deskripsi'       => $item['deskripsi'],
                'total_foto'          => $totalFotoBadge,
                'status'              => $statusBadge,
                'action'              => $action,
            ];
        }

        return [
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $rows,
        ];
    }
}
