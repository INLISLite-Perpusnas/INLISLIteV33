<?php

/**
 * Routes Eksemplar - Diperbarui untuk multi-controller
 *
 * Struktur controller:
 *  - EksemplarController       → list, karantina, OPAC, delete, status
 *  - EksemplarFormController   → create & edit
 *  - EksemplarImportController → import Excel & download template
 *  - EksemplarLabelController  → cetak label
 */

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

// -----------------------------------------------------------------------
// EKSEMPLAR - Web Routes
// -----------------------------------------------------------------------
$routes->group('eksemplar', ['namespace' => 'Eksemplar\Controllers'], function ($subroutes) {

    // === EksemplarController (list, karantina, OPAC, delete) ===
    $subroutes->add('',                  'EksemplarController::index');
    $subroutes->add('index',             'EksemplarController::index');
    $subroutes->add('delete/(:any)',     'EksemplarController::delete/$1');
    $subroutes->add('proses_karantina',  'EksemplarController::proses_karantina');
    $subroutes->add('proses_opac',       'EksemplarController::proses_opac');
    $subroutes->add('pulihkan_eksemplar','EksemplarController::pulihkan_eksemplar');
    $subroutes->add('hapus_permanen',    'EksemplarController::hapus_permanen');

    // === EksemplarFormController (create & edit) ===
    $subroutes->add('create',        'EksemplarFormController::create');
    $subroutes->add('edit/(:any)',   'EksemplarFormController::edit/$1');

    // === EksemplarImportController (import & template) ===
    $subroutes->add('importviews',       'EksemplarImportController::importviews');
    $subroutes->add('uploadexcel',       'EksemplarImportController::uploadexcel');
    $subroutes->add('download-template', 'EksemplarImportController::downloadTemplate');

    // === EksemplarLabelController (cetak label) ===
    $subroutes->add('print_label', 'EksemplarLabelController::print_label');
});

// -----------------------------------------------------------------------
// KARANTINA (alias)
// -----------------------------------------------------------------------
$routes->group('karantina-eksemplar', ['namespace' => 'Eksemplar\Controllers'], function ($subroutes) {
    $subroutes->add('', 'EksemplarController::karantina');
});

// -----------------------------------------------------------------------
// API EKSEMPLAR
// -----------------------------------------------------------------------
$routes->group('api/eksemplar', ['namespace' => 'Eksemplar\Controllers\Api'], function ($subroutes) {
    $subroutes->add('',                   'Eksemplar::index');
    $subroutes->add('index',              'Eksemplar::index');
    $subroutes->add('index/(:any)',       'Eksemplar::index/$1');
    $subroutes->add('detail/(:any)',      'Eksemplar::detail/$1');
    $subroutes->add('create',             'Eksemplar::create');
    $subroutes->add('add_partner',        'Eksemplar::add_partner');
    $subroutes->add('get_partner/(:any)', 'Eksemplar::get_partner/$1');
    $subroutes->add('edit_partner/(:any)','Eksemplar::edit_partner/$1');
    $subroutes->add('edit/(:any)',        'Eksemplar::edit/$1');
    $subroutes->add('delete/(:any)',      'Eksemplar::delete/$1');

    $subroutes->add('datatable',               'Eksemplar::datatable');
    $subroutes->add('datatable/(:any)',        'Eksemplar::datatable/$1');
    $subroutes->add('datatable/(:any)/(:any)', 'Eksemplar::datatable/$1/$2');
    $subroutes->add('switch/(:any)',           'Eksemplar::switch/$1');

    $subroutes->get('collectionsources',   'Eksemplar::get_collectionsources');
    $subroutes->get('collectionpartners',  'Eksemplar::get_collectionpartners');
    $subroutes->get('collectionrules',     'Eksemplar::get_collectionrules');
    $subroutes->get('collectionmedias',    'Eksemplar::get_collectionmedias');
    $subroutes->get('collectioncategory',  'Eksemplar::get_collectioncategory');
    $subroutes->get('collectionstatus',    'Eksemplar::get_collectionstatus');
    $subroutes->get('collectioncurrency',  'Eksemplar::get_collectioncurrency');
    $subroutes->get('collectionpricetype', 'Eksemplar::get_collectionpricetype');

    $subroutes->add('katalog',                     'Eksemplar::katalog');
    $subroutes->add('locationlibrary',             'Eksemplar::get_locationlibrary');
    $subroutes->add('locations',                   'Eksemplar::get_locations/0');
    $subroutes->add('locations/(:any)',            'Eksemplar::get_locations/$1');
    $subroutes->add('eksemplar_number/(:any)',     'Eksemplar::get_eksemplar_number/$1');
});
