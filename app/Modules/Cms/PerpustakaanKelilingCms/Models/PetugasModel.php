<?php

namespace PerpustakaanKelilingCms\Models;

use CodeIgniter\Model;

class PetugasModel extends Model
{
    protected $table            = 'perpus_keliling_petugas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = [
        'user_id',
        'tampilkan_email',
        'tampilkan_hp',
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

    public function getPetugasWithUser(?int $id = null, ?string $status = null)
    {
        $builder = $this->db->table('perpus_keliling_petugas p')
            ->select('p.*, u.username, u.email, u.phone')
            ->join('users u', 'u.id = p.user_id', 'left');

        if ($id !== null) {
            $builder->where('p.id', $id);
            return $builder->get()->getRowArray();
        }

        if ($status !== null) {
            $builder->where('p.status', $status);
        }

        return $builder->get()->getResultArray();
    }

    public function setStatus(int $id, string $status): bool
    {
        if (!in_array($status, ['aktif', 'nonaktif'], true)) {
            return false;
        }
        return (bool) $this->update($id, ['status' => $status]);
    }

    /**
     * Cari user yang belum terdaftar sebagai petugas untuk Select2 AJAX.
     */
    public function cariUserNonPetugas(string $q = ''): array
    {
        $existingUserIds = array_column($this->select('user_id')->findAll(), 'user_id');

        $builder = $this->db->table('users')
            ->select('id, username, email, phone')
            ->limit(20);

        if (!empty($existingUserIds)) {
            $builder->whereNotIn('id', $existingUserIds);
        }

        if ($q !== '') {
            $builder->groupStart()
                ->like('username', $q)
                ->orLike('email', $q)
                ->orLike('phone', $q)
                ->groupEnd();
        }

        $users = $builder->get()->getResultArray();

        return array_map(function ($u) {
            return [
                'id'   => $u['id'],
                'text' => $u['username'] . ' (' . ($u['email'] ?: 'Tanpa Email') . ' | ' . ($u['phone'] ?: 'Tanpa HP') . ')',
            ];
        }, $users);
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

        $builder = $this->db->table($this->table . ' p')
            ->select('p.id, p.user_id, p.tampilkan_email, p.tampilkan_hp, p.status, u.username, u.email, u.phone')
            ->join('users u', 'u.id = p.user_id', 'left');

        $totalRecords = $builder->countAllResults(false);

        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('u.username', $searchValue)
                ->orLike('u.email', $searchValue)
                ->orLike('u.phone', $searchValue)
                ->groupEnd();
        }

        $filteredRecords = $builder->countAllResults(false);

        $builder->orderBy('p.id', 'DESC');
        $builder->limit($length, $start);
        $data = $builder->get()->getResultArray();

        $rows = [];
        foreach ($data as $item) {
            $statusBadge = $item['status'] === 'aktif'
                ? '<span class="badge bg-success">Aktif</span>'
                : '<span class="badge bg-secondary">Nonaktif</span>';

            $tampilEmail = $item['tampilkan_email'] == 1 
                ? '<span class="badge bg-info">Tampil</span>' 
                : '<span class="badge bg-light text-dark">Sembunyi</span>';

            $tampilHp = $item['tampilkan_hp'] == 1 
                ? '<span class="badge bg-info">Tampil</span>' 
                : '<span class="badge bg-light text-dark">Sembunyi</span>';

            $statusBtnClass = $item['status'] === 'aktif' ? 'btn-warning' : 'btn-success';
            $statusBtnText  = $item['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan';
            $newStatus      = $item['status'] === 'aktif' ? 'nonaktif' : 'aktif';

            $action = '
                <button type="button" class="btn btn-sm btn-info btn-edit" data-id="' . $item['id'] . '" title="Edit Petugas">
                    <i class="fa fa-edit"></i> Edit
                </button>
                <button type="button" class="btn btn-sm ' . $statusBtnClass . ' btn-toggle-status" data-id="' . $item['id'] . '" data-status="' . $newStatus . '" title="' . $statusBtnText . '">
                    ' . $statusBtnText . '
                </button>
            ';

            $rows[] = [
                'id'                  => $item['id'],
                'username'            => esc($item['username']),
                'email'               => esc($item['email']),
                'phone'               => esc($item['phone']),
                'tampilkan_email'     => $tampilEmail,
                'tampilkan_hp'        => $tampilHp,
                'tampilkan_email_val' => (int) $item['tampilkan_email'],
                'tampilkan_hp_val'    => (int) $item['tampilkan_hp'],
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
