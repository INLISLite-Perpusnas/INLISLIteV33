<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class PerpusKelilingJadwal extends Entity
{
    protected $dates = ['created_at', 'updated_at', 'dibatalkan_at'];
    /**
     * Hitung status tampil secara dinamis.
     */
    public function getStatusTampil(?\DateTimeInterface $now = null): string
    {
        if (($this->attributes['status'] ?? '') === 'dibatalkan') {
            return 'dibatalkan';
        }

        $now = $now ?? new \DateTimeImmutable('now', new \DateTimeZone('Asia/Jakarta'));
        $tgl = substr((string) ($this->attributes['tanggal'] ?? date('Y-m-d')), 0, 10);
        $mulai = $this->attributes['jam_mulai'] ?? '00:00:00';
        $selesai = $this->attributes['jam_selesai'] ?? '00:00:00';

        $start = new \DateTimeImmutable("{$tgl} {$mulai}", new \DateTimeZone('Asia/Jakarta'));
        $end = new \DateTimeImmutable("{$tgl} {$selesai}", new \DateTimeZone('Asia/Jakarta'));

        if ($now < $start) {
            return 'akan_datang';
        }
        if ($now <= $end) {
            return 'berlangsung';
        }
        return 'selesai';
    }

    public function getBadgeStatusHtml(?\DateTimeInterface $now = null): string
    {
        $st = $this->getStatusTampil($now);
        switch ($st) {
            case 'akan_datang':
                return '<span class="badge bg-primary">Akan datang</span>';
            case 'berlangsung':
                return '<span class="badge bg-success">Sedang berlangsung</span>';
            case 'selesai':
                return '<span class="badge bg-secondary">Selesai</span>';
            case 'dibatalkan':
                return '<span class="badge bg-danger">Dibatalkan</span>';
            default:
                return '<span class="badge bg-light text-dark">' . esc($st) . '</span>';
        }
    }

    public function bisaEdit(?\DateTimeInterface $now = null): bool
    {
        return $this->getStatusTampil($now) === 'akan_datang';
    }

    public function bisaBatalkan(?\DateTimeInterface $now = null): bool
    {
        return in_array($this->getStatusTampil($now), ['akan_datang', 'berlangsung'], true);
    }

    public function bisaDokumentasi(?\DateTimeInterface $now = null): bool
    {
        return in_array($this->getStatusTampil($now), ['berlangsung', 'selesai'], true);
    }
}
