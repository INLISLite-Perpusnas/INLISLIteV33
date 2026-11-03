<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-pengaturan-frontend', ['namespace' => 'PengaturanFrontend\Controllers'], function ($subroutes) {
	$subroutes->match(['get', 'post'], '/', 'PengaturanFrontend::index', ['filter' => 'csrf']);
	$subroutes->match(['get', 'post'], 'index', 'PengaturanFrontend::index', ['filter' => 'csrf']);
});
