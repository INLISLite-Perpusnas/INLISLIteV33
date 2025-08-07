<?php if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}
$routes->group('cms/berita', ['namespace' => 'Berita\Controllers'], function (
    $subroutes
) {
    
    $subroutes->add('', 'Berita::index');
    $subroutes->add('index', 'Berita::index');
    $subroutes->add('detail/(:any)', 'Berita::detail/$1');
    $subroutes->add('edit/(:any)', 'Berita::edit/$1');
    $subroutes->add('create', 'Berita::create');
    $subroutes->add('delete/(:any)', 'Berita::delete/$1');
    $subroutes->add('do_init', 'Berita::do_init');
    $subroutes->add('do_upload', 'Berita::do_upload');
    $subroutes->add('do_delete', 'Berita::do_delete');
    $subroutes->add('flip', 'Berita::flip');
    $subroutes->add('apply_status/(:any)', 'Berita::apply_status/$1');
    $subroutes->add('export', 'Berita::export');
    $subroutes->add('thumb', 'Berita::thumb');
});

$routes->group(
    'api/berita',
    ['namespace' => 'Berita\Controllers\Api'],
    function ($subroutes) {
        //crud
        $subroutes->add('', 'Berita::index');
        $subroutes->add('index', 'Berita::index');
        $subroutes->add('detail/(:any)', 'Berita::detail/$1');
        $subroutes->add('show/(:any)', 'Berita::show/$1');
        $subroutes->add('create', 'Berita::create');
        $subroutes->add('update/(:any)', 'Berita::update/$1');
        $subroutes->add('delete/(:any)', 'Berita::delete/$1');

        //custom
        $subroutes->add('datatable', 'Berita::datatable');
        $subroutes->add('datatable/(:any)', 'Berita::datatable/$1');
        $subroutes->add('(:any)', 'Berita::detail/$1');
    }
);
