<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

// Routes Backoffice (CMS)
$routes->group('cms/perpustakaan-keliling', ['namespace' => 'PerpustakaanKelilingCms\Controllers'], function ($subroutes) {
    // Master Unit
    $subroutes->add('unit', 'Unit::index');
    $subroutes->post('unit/data', 'Unit::data');
    $subroutes->add('unit/get/(:num)', 'Unit::get/$1');
    $subroutes->post('unit/simpan', 'Unit::simpan');
    $subroutes->post('unit/status', 'Unit::status');
    $subroutes->post('unit/foto/upload', 'Unit::uploadFoto');
    $subroutes->post('unit/foto/hapus', 'Unit::hapusFoto');
    $subroutes->post('unit/foto/utama', 'Unit::setFotoUtama');

    // Master Lokasi
    $subroutes->add('lokasi', 'Lokasi::index');
    $subroutes->post('lokasi/data', 'Lokasi::data');
    $subroutes->add('lokasi/get/(:num)', 'Lokasi::get/$1');
    $subroutes->post('lokasi/simpan', 'Lokasi::simpan');
    $subroutes->post('lokasi/status', 'Lokasi::status');

    // Master Petugas
    $subroutes->add('petugas', 'Petugas::index');
    $subroutes->post('petugas/data', 'Petugas::data');
    $subroutes->add('petugas/get/(:num)', 'Petugas::get/$1');
    $subroutes->post('petugas/simpan', 'Petugas::simpan');
    $subroutes->post('petugas/status', 'Petugas::status');
    $subroutes->add('petugas/cari-user', 'Petugas::cariUser');

    // CRUD Jadwal
    $subroutes->add('jadwal', 'Jadwal::index');
    $subroutes->post('jadwal/data', 'Jadwal::data');
    $subroutes->add('jadwal/detail/(:num)', 'Jadwal::detail/$1');
    $subroutes->post('jadwal/simpan', 'Jadwal::simpan');
    $subroutes->post('jadwal/batalkan', 'Jadwal::batalkan');
    $subroutes->get('jadwal/export', 'Jadwal::export');

    // Dokumentasi
    $subroutes->post('dokumentasi/list', 'Dokumentasi::listData');
    $subroutes->post('dokumentasi/upload', 'Dokumentasi::upload');
    $subroutes->post('dokumentasi/caption', 'Dokumentasi::caption');
    $subroutes->post('dokumentasi/hapus', 'Dokumentasi::hapus');
});
