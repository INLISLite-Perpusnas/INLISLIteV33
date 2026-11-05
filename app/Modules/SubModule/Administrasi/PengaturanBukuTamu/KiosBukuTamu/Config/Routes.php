<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-kios-buku-tamu', ['namespace' => 'KiosBukuTamu\Controllers'], function ($subroutes) {
	$subroutes->get('/', 'KiosBukuTamu::index');
	$subroutes->get('index', 'KiosBukuTamu::index');
	$subroutes->post('create', 'KiosBukuTamu::create', ['filter' => 'csrf']);
	$subroutes->post('delete', 'KiosBukuTamu::delete', ['filter' => 'csrf']);
});
