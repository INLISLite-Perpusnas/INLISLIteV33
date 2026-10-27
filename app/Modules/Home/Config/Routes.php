<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
// Update your existing routes to include recommendations

$routes->group('home', ['namespace' => 'Home\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Home::index');
	$subroutes->add('index', 'Home::index');
	$subroutes->add('pendaftaran-online', 'Home::pendaftaran_online');
	$subroutes->get('usulan-koleksi', 'Home::usulanKoleksi');
	$subroutes->get('survey', 'Home::survey');
	$subroutes->post('usulan-koleksi', 'Home::simpanUsulanKoleksi', ['filter' => 'csrf']);
	$subroutes->post('survei/(:num)', 'Home::simpanSurvey/$1', ['filter' => 'csrf']);

});

$routes->group('api/home', ['namespace' => 'Home\Controllers\Api'], function ($subroutes) {//crud
	$subroutes->add('', 'Home::index');
	$subroutes->add('index', 'Home::index');
	$subroutes->add('pendaftaran_online', 'Home::pendaftaran_online');
	$subroutes->add('index', 'Home::index');

	
});
