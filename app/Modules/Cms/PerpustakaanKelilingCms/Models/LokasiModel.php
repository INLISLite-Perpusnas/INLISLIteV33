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
                ? '<span class="badge bg-success">Aktif</span>'
                : '<span class="badge bg-secondary">Nonaktif</span>';

            $koordinat = esc($item['latitude']) . ', ' . esc($item['longitude']);

            $statusBtnClass = $item['status'] === 'aktif' ? 'btn-warning' : 'btn-success';
            $statusBtnText  = $item['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan';
            $newStatus      = $item['status'] === 'aktif' ? 'nonaktif' : 'aktif';

            $action = '
                <button type="button" class="btn btn-sm btn-info btn-edit" data-id="' . $item['id'] . '" title="Edit Lokasi">
                    <i class="fa fa-edit"></i> Edit
                </button>
                <button type="button" class="btn btn-sm ' . $statusBtnClass . ' btn-toggle-status" data-id="' . $item['id'] . '" data-status="' . $newStatus . '" title="' . $statusBtnText . '">
                    ' . $statusBtnText . '
                </button>
            ';

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
