<?php if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}
$routes->group('visitor', ['namespace' => 'Visitor\Controllers'], function (
    $subroutes
) {
    
    $subroutes->add('', 'Visitor::index');
    $subroutes->add('index', 'Visitor::index');
    $subroutes->add('detail/(:any)', 'Visitor::detail/$1');
    $subroutes->add('edit/(:any)', 'Visitor::edit/$1');
    $subroutes->add('create', 'Visitor::create');
    $subroutes->add('delete/(:any)', 'Visitor::delete/$1');
    $subroutes->add('do_init', 'Visitor::do_init');
    $subroutes->add('do_upload', 'Visitor::do_upload');
    $subroutes->add('do_delete', 'Visitor::do_delete');
    $subroutes->add('flip', 'Visitor::flip');
    $subroutes->add('apply_status/(:any)', 'Visitor::apply_status/$1');
    $subroutes->add('export', 'Visitor::export');
    $subroutes->add('thumb', 'Visitor::thumb');
});

$routes->group(
    'api/visitor',
    ['namespace' => 'Visitor\Controllers\Api'],
    function ($subroutes) {
        //custom
        $subroutes->add('datatable', 'Visitor::datatable');
        $subroutes->add('datatable/(:any)', 'Visitor::datatable/$1');
        $subroutes->add('', 'Visitor::index');
        $subroutes->add('index/(:any)', 'Visitor::index/$1');
        $subroutes->add('detail/(:any)', 'Visitor::detail/$1');
        $subroutes->add('total', 'Visitor::total');
    }
);
