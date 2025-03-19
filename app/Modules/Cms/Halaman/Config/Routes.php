<?php if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}
$routes->group('cms/halaman', ['namespace' => 'Halaman\Controllers'], function (
    $subroutes
) {
    
    $subroutes->add('', 'Halaman::index');
    $subroutes->add('index', 'Halaman::index');
    $subroutes->add('detail/(:any)', 'Halaman::detail/$1');
    $subroutes->add('edit/(:any)', 'Halaman::edit/$1');
    $subroutes->add('create', 'Halaman::create');
    $subroutes->add('delete/(:any)', 'Halaman::delete/$1');
    $subroutes->add('do_init', 'Halaman::do_init');
    $subroutes->add('do_upload', 'Halaman::do_upload');
    $subroutes->add('do_delete', 'Halaman::do_delete');
    $subroutes->add('flip', 'Halaman::flip');
    $subroutes->add('apply_status/(:any)', 'Halaman::apply_status/$1');
    $subroutes->add('export', 'Halaman::export');
    $subroutes->add('thumb', 'Halaman::thumb');
});

$routes->group(
    'api/halaman',
    ['namespace' => 'Halaman\Controllers\Api'],
    function ($subroutes) {
        //crud
        $subroutes->add('', 'Halaman::index');
        $subroutes->add('index', 'Halaman::index');
        $subroutes->add('detail/(:any)', 'Halaman::detail/$1');
        $subroutes->add('show/(:any)', 'Halaman::show/$1');
        $subroutes->add('create', 'Halaman::create');
        $subroutes->add('update/(:any)', 'Halaman::update/$1');
        $subroutes->add('delete/(:any)', 'Halaman::delete/$1');

        //custom
        $subroutes->add('datatable', 'Halaman::datatable');
        $subroutes->add('datatable/(:any)', 'Halaman::datatable/$1');
        $subroutes->add('(:any)', 'Halaman::detail/$1');
    }
);
