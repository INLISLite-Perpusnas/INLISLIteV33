<?php

namespace UsulanKoleksi\Controllers\Api;

use App\Libraries\DataTable;
use CodeIgniter\API\ResponseTrait;

class UsulanKoleksi extends \Base\Controllers\BaseResourceController
{
    use ResponseTrait;

    public function datatable()
    {
        if (!is_member('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Akses ditolak.']);
        }

        $builder = db_connect('data')->table('requestcatalog a')
            ->select('a.ID, a.Title, a.Type, a.Author, a.Subject, a.Publisher, a.PublishLocation, a.PublishYear, a.Comments, a.DateRequest, a.CreateDate, a.Status');

        return DataTable::of($builder)
            ->addNumbering('no')
            ->edit('Title', static function ($row) {
                return '<strong>' . esc($row->Title) . '</strong><br><small class="text-muted">' . esc($row->Type ?: '-') . '</small>';
            })
            ->edit('Author', static function ($row) {
                return esc($row->Author ?: '-') . '<br><small class="text-muted">' . esc($row->Subject ?: '-') . '</small>';
            })
            ->edit('Publisher', static function ($row) {
                $location = implode(', ', array_filter([$row->PublishLocation, $row->PublishYear]));
                return esc($row->Publisher ?: '-') . '<br><small class="text-muted">' . esc($location ?: '-') . '</small>';
            })
            ->edit('Comments', static function ($row) {
                return nl2br(esc($row->Comments ?: '-'));
            })
            ->edit('DateRequest', static function ($row) {
                $date = $row->DateRequest ?: $row->CreateDate;
                return $date ? esc(date('d/m/Y H:i', strtotime($date))) : '-';
            })
            ->edit('Status', static function ($row) {
                $current = (string) ($row->Status ?? '');
                $options = ['Usulan', 'Diproses', 'Diterima', 'Ditolak'];
                $html = '<select class="form-control form-control-sm status-usulan" data-id="' . (int) $row->ID
                    . '" data-previous="' . esc($current, 'attr') . '" aria-label="Status usulan ' . (int) $row->ID . '">';
                if (!in_array($current, $options, true)) {
                    $html .= '<option value="' . esc($current, 'attr') . '" selected disabled>'
                        . esc($current ?: 'Pilih status') . '</option>';
                }
                foreach ($options as $option) {
                    $html .= '<option value="' . $option . '"' . ($current === $option ? ' selected' : '') . '>'
                        . $option . '</option>';
                }

                return $html . '</select>';
            })
            ->toJson();
    }

    public function updateStatus(int $id)
    {
        if (!is_member('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Akses ditolak.']);
        }

        $status = $this->request->getPost('status');
        if (!in_array($status, ['Usulan', 'Diproses', 'Diterima', 'Ditolak'], true)) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Status tidak valid.',
                'csrfHash' => csrf_hash(),
            ]);
        }

        $model = new \UsulanKoleksi\Models\UsulanKoleksiModel();
        if (!$model->find($id)) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Usulan koleksi tidak ditemukan.',
                'csrfHash' => csrf_hash(),
            ]);
        }

        if (!$model->update($id, [
            'Status' => $status,
            'UpdateDate' => date('Y-m-d H:i:s'),
            'UpdateBy' => login_id(),
        ])) {
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Gagal memperbarui status.',
                'csrfHash' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'message' => 'Status usulan berhasil diperbarui.',
            'csrfHash' => csrf_hash(),
        ]);
    }
}