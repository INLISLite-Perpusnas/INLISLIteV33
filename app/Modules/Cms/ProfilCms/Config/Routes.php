<?php if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('cms/profil', ['namespace' => 'ProfilCms\Controllers'], function ($subroutes) {
    $subroutes->add('', 'Profil::index');
    $subroutes->add('index', 'Profil::index');
    $subroutes->post('edit', 'Profil::updateProfil');

    // CRUD Layanan
    $subroutes->add('layanan/datatable', 'Profil::layananDatatable');
    $subroutes->post('layanan/save', 'Profil::layananSave');
    $subroutes->post('layanan/update-order', 'Profil::layananUpdateOrder');
    $subroutes->add('layanan/get/(:num)', 'Profil::layananGet/$1');
    $subroutes->add('layanan/delete/(:num)', 'Profil::layananDelete/$1');
});

