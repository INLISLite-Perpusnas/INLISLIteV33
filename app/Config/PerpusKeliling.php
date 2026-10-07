<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class PerpusKeliling extends BaseConfig
{
    public int    $maxUploadKb        = 2048;  // 2 MB
    public int    $maxFotoUnit        = 10;
    public int    $maxDokumentasi     = 20;    // per jadwal
    public array  $allowedMime        = ['image/jpeg', 'image/png', 'image/webp'];
    public string $uploadDirUnit      = 'uploads/perpus_keliling/unit/';
    public string $uploadDirDok       = 'uploads/perpus_keliling/dokumentasi/';
    public int    $perPageFrontend    = 9;
    public string $timezoneLabel      = 'WIB';
    // Pusat peta jika izin lokasi ditolak/tidak tersedia (tengah Indonesia)
    public float  $defaultLat         = -2.5489;
    public float  $defaultLng         = 118.0149;
    public int    $defaultZoom        = 5;
}
