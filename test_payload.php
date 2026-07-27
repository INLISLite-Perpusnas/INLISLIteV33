<?php
require 'public/index.php';
$app = \Config\Services::codeigniter();
$app->initialize();
$anggotaModel = new \Member\Models\MemberModel();
$memberguestModel = new \BukuTamu\Models\MemberGuestModel();
$katalogModel = new \Katalog\Models\KatalogModel();
$koleksiModel = new \Peminjaman\Models\CollectionModel();
$peminjamanModel = new \Peminjaman\Models\CollectionLoanItemModel();
$settingModel = new \PenomoranKoleksi\Models\PenomoranKoleksiModel();

function getSettingValue($settingModel, $name, $default = null)
{
    $setting = $settingModel->where('Name', $name)->first();
    return $setting ? $setting->Value : $default;
}

$payload = [
    'nama_perpustakaan' => getSettingValue($settingModel, 'NamaPerpustakaan', 'Perpustakaan Mitra'),
    'npp' => getSettingValue($settingModel, 'NPPPerpustakaan', 'NPP Perpustakaan Mitra'),
    'alamat' => getSettingValue($settingModel, 'NamaLokasiPerpustakaan', 'Alamat Perpustakaan Mitra'),
    'email' => getSettingValue($settingModel, 'EmailPerpustakaan', 'Email Perpustakaan Mitra'),
    'Provinsi_kode' => getSettingValue($settingModel, 'ProvinsiID', '32'),
    'kabkota_kode' => getSettingValue($settingModel, 'KabKotaID', '3171'),
    'kecamatan_kode' => getSettingValue($settingModel, 'KecamatanID', '3171010'),
    'kelurahan_kode' => getSettingValue($settingModel, 'KelurahanID', '3171010001'),
    'periode' => date('Y-m-d'),
    'jumlah_anggota' => $anggotaModel->countAllResults(),
    'kunjungan_anggota' => $memberguestModel->where('NoAnggota !=', null)->countAllResults(),
    'kunjungan_non_anggota' => $memberguestModel->where('NoAnggota', null)->countAllResults(),
    'total_katalog' => $katalogModel->countAllResults(),
    'total_koleksi' => $koleksiModel->countAllResults(),
    'total_peminjaman' => $peminjamanModel->countAllResults(),
    'total_baca_ditempat' => $memberguestModel->where('NoAnggota !=', null)->countAllResults()+$memberguestModel->where('NoAnggota', null)->countAllResults(),
];
echo json_encode($payload, JSON_PRETTY_PRINT);
