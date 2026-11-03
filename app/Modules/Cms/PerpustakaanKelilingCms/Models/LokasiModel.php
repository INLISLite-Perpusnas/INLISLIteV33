<?php

namespace PerpustakaanKelilingCms\Models;

use CodeIgniter\Model;

class LokasiModel extends Model
{
    protected $table            = 'perpus_keliling_lokasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = [
        'nama_lokasi',
        'alamat',
        'latitude',
        'longitude',
        'keterangan',
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

        $builder = $this->builder()
            ->select('id, nama_lokasi, alamat, latitude, longitude, status, keterangan');

        $totalRecords = $builder->countAllResults(false);

        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('nama_lokasi', $searchValue)
                ->orLike('alamat', $searchValue)
                ->orLike('keterangan', $searchValue)
                ->groupEnd();
        }

        $filteredRecords = $builder->countAllResults(false);

        $builder->orderBy('id', 'DESC');
        $builder->limit($length, $start);
        $data = $builder->get()->getResultArray();

        $rows = [];
        foreach ($data as $item) {
            $statusBadge = $item['status'] === 'aktif'
                ? '<span class="ina-badge ina-badge-success">Aktif</span>'
                : '<span class="ina-badge ina-badge-neutral">Nonaktif</span>';

            $koordinat = esc($item['latitude']) . ', ' . esc($item['longitude']);

            $statusBtnIcon  = $item['status'] === 'aktif' ? 'fa-eye-slash' : 'fa-eye';
            $statusBtnText  = $item['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan';
            $newStatus      = $item['status'] === 'aktif' ? 'nonaktif' : 'aktif';

            $action = '<div class="ina-actions">'
                . '<button type="button" class="ina-action btn-edit" data-id="' . $item['id'] . '" title="Edit Lokasi" aria-label="Edit Lokasi"><i class="fas fa-pen" aria-hidden="true"></i></button>'
                . '<button type="button" class="ina-action btn-toggle-status" data-id="' . $item['id'] . '" data-status="' . $newStatus . '" title="' . $statusBtnText . '" aria-label="' . $statusBtnText . '"><i class="fas ' . $statusBtnIcon . '" aria-hidden="true"></i></button>'
                . '</div>';

            $rows[] = [
                'id'           => $item['id'],
                'nama_lokasi'  => esc($item['nama_lokasi']),
                'alamat'       => esc($item['alamat']),
                'koordinat'    => $koordinat,
                'keterangan'   => esc($item['keterangan']),
                'latitude'     => $item['latitude'],
                'longitude'    => $item['longitude'],
                'raw_nama'     => $item['nama_lokasi'],
                'raw_alamat'   => $item['alamat'],
                'raw_ket'      => $item['keterangan'],
                'status'       => $statusBadge,
                'action'       => $action,
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
