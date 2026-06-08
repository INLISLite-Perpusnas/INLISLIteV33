<?php if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('member', ['namespace' => 'Member\Controllers'], function (
    $subroutes
) {
    
    $subroutes->add('', 'Member::index');
    $subroutes->add('index', 'Member::index');
    $subroutes->add('activate/(:any)', 'Member::activate/$1');
});

$routes->group(
    'api/member',
    ['namespace' => 'Member\Controllers\Api'],
    function ($subroutes) {
        //crud
        $subroutes->add('', 'Member::index');
        $subroutes->add('index', 'Member::index');
        $subroutes->add('detail/(:any)', 'Member::detail/$1');
        $subroutes->add('show/(:any)', 'Member::show/$1');
        $subroutes->add('create', 'Member::create');
        $subroutes->add('update/(:any)', 'Member::update/$1');
        $subroutes->add('delete/(:any)', 'Member::delete/$1');

        //custom
        $subroutes->add('datatable', 'Member::datatable');
        $subroutes->add('datatable/(:any)', 'Member::datatable/$1');
        $subroutes->add('upload_file', 'Member::upload_file');
        $subroutes->add('register', 'Member::register');
        $subroutes->add('login', 'Member::login');
        $subroutes->add('resend_email', 'Member::resend_email');
        $subroutes->add('reset_email', 'Member::reset_email');
        $subroutes->add('check', 'Member::check');
    }
);
