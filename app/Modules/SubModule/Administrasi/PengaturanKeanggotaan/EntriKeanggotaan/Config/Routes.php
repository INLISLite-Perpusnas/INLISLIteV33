<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-entri-keanggotaan', ['namespace' => 'EntriKeanggotaan\Controllers'], function ($subroutes) {$subroutes->add('', 'EntriKeanggotaan::index');
	$subroutes->add('index', 'EntriKeangotaan::index');
});