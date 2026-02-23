<?php

/**
 * Routes Katalog - Diperbarui untuk multi-controller
 * 
 * Struktur controller:
 *  - KatalogController       → list, karantina, OPAC, delete, status
 *  - KatalogFormController   → create & edit form sederhana
 *  - KatalogMarcController   → create/edit/ekspor/import MARC
 *  - KatalogArtikelController→ CRUD artikel serial
 *  - KatalogFileController   → view & decrypt PDF
 */

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

// -----------------------------------------------------------------------
// REPORT
// -----------------------------------------------------------------------
$routes->group('report', ['namespace' => 'Katalog\Controllers'], function ($subroutes) {
    $subroutes->add('katalog', 'KatalogController::report');
});

// -----------------------------------------------------------------------
// KATALOG - Web Routes
// -----------------------------------------------------------------------
$routes->group('katalog', ['namespace' => 'Katalog\Controllers'], function ($subroutes) {

    // === KatalogController (list, karantina, opac, delete, status) ===
    $subroutes->add('',                      'KatalogController::index');
    $subroutes->add('index',                 'KatalogController::index');
    $subroutes->add('delete/(:any)',         'KatalogController::delete/$1');
    $subroutes->add('apply_status/(:any)',   'KatalogController::apply_status/$1');
    $subroutes->add('karantina',             'KatalogController::karantina');
    $subroutes->add('proses_karantina',      'KatalogController::proses_karantina');
    $subroutes->add('proses_opac',           'KatalogController::proses_opac');
    $subroutes->add('pulihkan_katalog',      'KatalogController::pulihkan_katalog');
    $subroutes->add('hapus_permanen',        'KatalogController::hapus_permanen');
    $subroutes->add('delete-edisi-serial/(:any)/(:any)', 'KatalogController::deleteEdisiSerial/$1/$2');

    // === KatalogFormController (form sederhana) ===
    $subroutes->add('create',            'KatalogFormController::create');
    $subroutes->add('edit/(:any)',        'KatalogFormController::edit/$1');
    $subroutes->add('detail/(:any)',      'KatalogFormController::edit/$1/1');

    // === KatalogMarcController (form MARC & ekspor & import) ===
    $subroutes->add('create_marc',             'KatalogMarcController::create_marc');
    $subroutes->add('edit_marc/(:any)',         'KatalogMarcController::edit_marc/$1');
    $subroutes->add('ekspor_marc',             'KatalogMarcController::ekspor_marc');
    $subroutes->add('marc-import',             'KatalogMarcController::showCreateForm');
    $subroutes->add('create-marc-from-file',   'KatalogMarcController::createFromMarcFile');

    // === KatalogArtikelController (artikel serial) ===
    $subroutes->add('create_artikel',          'KatalogArtikelController::create_artikel');
    $subroutes->add('get_artikel/(:any)',       'KatalogArtikelController::get_artikel/$1');
    $subroutes->add('edit_artikel/(:any)',      'KatalogArtikelController::edit_artikel/$1');
    $subroutes->add('delete_artikel/(:any)',    'KatalogArtikelController::delete_artikel/$1');

    // === KatalogFileController (view & decrypt PDF) ===
    $subroutes->add('view_decrypted/(:any)',              'KatalogFileController::view_decrypted/$1');
    $subroutes->add('view_decrypted_article/(:any)',      'KatalogFileController::view_decrypted_article/$1');
    $subroutes->add('get_decrypted_content/(:any)',       'KatalogFileController::get_decrypted_content/$1');
    $subroutes->add('get_decrypted_content_article/(:any)', 'KatalogFileController::get_decrypted_content_article/$1');

});

// -----------------------------------------------------------------------
// KARANTINA (alias)
// -----------------------------------------------------------------------
$routes->group('karantina-katalog', ['namespace' => 'Katalog\Controllers'], function ($subroutes) {
    $subroutes->add('', 'KatalogController::karantina');
});

// -----------------------------------------------------------------------
// API KATALOG
// -----------------------------------------------------------------------
$routes->group('api/katalog', ['namespace' => 'Katalog\Controllers\Api'], function ($subroutes) {
    $subroutes->add('',                          'Katalog::index');
    $subroutes->add('index',                     'Katalog::index');
    $subroutes->add('detail/(:any)',             'Katalog::detail/$1');
    $subroutes->add('create',                    'Katalog::create');
    $subroutes->add('edit/(:any)',               'Katalog::edit/$1');
    $subroutes->add('view_decrypted/(:any)',      'Katalog::view_decrypted/$1');
    $subroutes->add('get_decrypted_content/(:any)', 'Katalog::get_decrypted_content/$1');
    $subroutes->add('delete/(:any)',             'Katalog::delete/$1');

    // Custom
    $subroutes->add('datatable',                 'Katalog::datatable');
    $subroutes->add('datatable/(:any)',          'Katalog::datatable/$1');
    $subroutes->add('switch/(:any)',             'Katalog::switch/$1');
    $subroutes->add('upload_cover',              'Katalog::upload_cover');
    $subroutes->add('upload_cover/(:any)/(:any)','Katalog::upload_cover/$1/$2');
    $subroutes->add('upload_file',               'Katalog::upload_file');
    $subroutes->add('upload_file/(:any)/(:any)', 'Katalog::upload_file/$1/$2');
    $subroutes->add('upload_file_digital_artikel','Katalog::upload_file_digital_artikel');
    $subroutes->add('delete_file/(:any)',        'Katalog::delete_file/$1');
    $subroutes->add('delete_file_article/(:any)','Katalog::delete_file_article/$1');

    // Artikel
    $subroutes->add('datatable_artikel',         'Katalog::datatable_artikel');
    $subroutes->add('create_artikel',            'Katalog::create_artikel');
    $subroutes->add('get_artikel/(:any)',        'Katalog::get_artikel/$1');
    $subroutes->add('edit_artikel/(:any)',       'Katalog::edit_artikel/$1');
    $subroutes->add('delete_artikel/(:any)',     'Katalog::delete_artikel/$1');

    // MARC
    $subroutes->add('add_to_session/(:any)',     'Katalog::add_to_session/$1');
    $subroutes->add('remove_from_session/(:any)','Katalog::remove_from_session/$1');
    $subroutes->add('get_all_tags/(:any)',       'Katalog::get_all_tags/$1');
    $subroutes->add('get_field_indicator1/(:any)','Katalog::get_field_indicator1/$1');
    $subroutes->add('get_field_indicator2/(:any)','Katalog::get_field_indicator2/$1');
    $subroutes->add('get_field_content/(:any)', 'Katalog::get_field_content/$1');

    // Edisi serial
    $subroutes->add('datatable-edisi-serial/(:any)', 'Katalog::datatableEdisiSerial/$1');
    $subroutes->add('create-edisi-serial',       'Katalog::createEdisiSerial');
    $subroutes->add('get-edisi-serial/(:any)',   'Katalog::get_edisi_serial_by_catalog/$1');
});