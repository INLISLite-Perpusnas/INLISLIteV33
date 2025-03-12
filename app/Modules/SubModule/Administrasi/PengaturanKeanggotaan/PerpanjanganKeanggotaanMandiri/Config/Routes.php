<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-perpanjangan-keanggotaan-mandiri', ['namespace' => 'PerpanjanganKeanggotaanMandiri\Controllers'], function ($subroutes) {$subroutes->add('', 'PerpanjanganKeanggotaanMandiri::index');
	$subroutes->add('index', 'PerpanjanganKeanggotaanMandiri::index');
	// $subroutes->post('index', 'PerpanjanganKeanggotaanMandiri::index');
});