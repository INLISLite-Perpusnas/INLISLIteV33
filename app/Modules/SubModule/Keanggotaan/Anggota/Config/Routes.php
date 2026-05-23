<?php

/**
 * Routes Anggota - Diperbarui untuk multi-controller
 *
 * Struktur controller:
 *  - AnggotaController        → CRUD utama (index, create, edit, delete, keranjang, status)
 *  - AnggotaOnlineController  → fitur anggota online (online, extend, aktifkan_online)
 *  - AnggotaImportController  → import Excel
 *  - AnggotaPrintController   → cetak kartu & bebas pustaka
 */

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

// -----------------------------------------------------------------------
// PROFIL ANGGOTA (akses mandiri anggota)
// -----------------------------------------------------------------------
$routes->get('profil_anggota', 'AnggotaController::profile', ['namespace' => 'Anggota\Controllers']);
$routes->post('profil_anggota', 'AnggotaController::edit', ['namespace' => 'Anggota\Controllers']);

// -----------------------------------------------------------------------
// ANGGOTA - Web Routes
// -----------------------------------------------------------------------
$routes->group('anggota', ['namespace' => 'Anggota\Controllers'], function ($subroutes) {

    // === AnggotaController (CRUD utama) ===
    $subroutes->add('',                       'AnggotaController::index');
    $subroutes->add('index',                  'AnggotaController::index');
    $subroutes->add('keranjang',              'AnggotaController::keranjang');
    $subroutes->add('detail/(:any)',          'AnggotaController::detail/$1');
    $subroutes->add('create',                 'AnggotaController::create');
    $subroutes->add('edit',                   'AnggotaController::edit');
    $subroutes->add('edit/(:any)',            'AnggotaController::edit/$1');
    $subroutes->add('edit/(:any)/(:any)',     'AnggotaController::edit/$1/$2');
    $subroutes->add('delete/(:any)',          'AnggotaController::delete/$1');
    $subroutes->add('apply_status/(:any)',    'AnggotaController::apply_status/$1');
    $subroutes->add('get_defaults/(:num)',    'AnggotaController::getDefaults/$1');
    $subroutes->add('proses_keranjang',       'AnggotaController::proses_keranjang');
    $subroutes->add('pulihkan_keranjang',     'AnggotaController::pulihkan_keranjang');
    $subroutes->add('hapus_permanen',         'AnggotaController::hapus_permanen');
    $subroutes->add('profile',                'AnggotaController::profile');

    // === AnggotaOnlineController ===
    $subroutes->add('online',                 'AnggotaOnlineController::online');
    $subroutes->add('aktifkan_online',        'AnggotaOnlineController::aktifkan_online');

    // === AnggotaImportController ===
    $subroutes->add('import_view',            'AnggotaImportController::import_view');
    $subroutes->add('import',                 'AnggotaImportController::import');

    // === AnggotaPrintController ===
    $subroutes->add('printanggota/(:any)',    'AnggotaPrintController::printanggota/$1');
    $subroutes->add('printkartubelakang/(:any)', 'AnggotaPrintController::printkartubelakang/$1');
    $subroutes->add('multipleprint',          'AnggotaPrintController::multipleprint');
    $subroutes->add('bebaspustaka/(:any)',    'AnggotaPrintController::bebaspustaka/$1');
    $subroutes->add('uploadBackground',       'AnggotaPrintController::uploadBackground');
});

// -----------------------------------------------------------------------
// KERANJANG (alias)
// -----------------------------------------------------------------------
$routes->group('keranjang-anggota', ['namespace' => 'Anggota\Controllers'], function ($subroutes) {
    $subroutes->add('', 'AnggotaController::keranjang');
});

// -----------------------------------------------------------------------
// API ANGGOTA
// -----------------------------------------------------------------------
$routes->group('api/anggota', ['namespace' => 'Anggota\Controllers\Api'], function ($subroutes) {
    $subroutes->add('',                   'Anggota::index');
    $subroutes->add('detail/(:any)',      'Anggota::detail/$1');
    $subroutes->add('create',             'Anggota::create');
    $subroutes->add('edit/(:any)',        'Anggota::edit/$1');
    $subroutes->add('delete/(:any)',      'Anggota::delete/$1');
    $subroutes->add('hapusall',           'Anggota::hapusall');
    $subroutes->add('cities',             'Anggota::cities');
    $subroutes->add('get_date',           'Anggota::get_date');

    // Custom
    $subroutes->add('datatable',          'Anggota::datatable');
    $subroutes->add('datatable/(:any)',   'Anggota::datatable/$1');
    $subroutes->add('switch/(:any)',      'Anggota::switch/$1');
    $subroutes->add('upload_file',        'Anggota::upload_file');
    $subroutes->add('capture_file',       'Anggota::capture_file');
});
