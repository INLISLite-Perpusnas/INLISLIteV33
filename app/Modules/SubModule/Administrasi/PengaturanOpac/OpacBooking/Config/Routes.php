<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-opac-booking', ['namespace' => 'OpacBooking\Controllers'], function ($subroutes) {
	$subroutes->match(['get', 'post'], '/', 'OpacBooking::index', ['filter' => 'csrf']);
	$subroutes->match(['get', 'post'], 'index', 'OpacBooking::index', ['filter' => 'csrf']);
});
