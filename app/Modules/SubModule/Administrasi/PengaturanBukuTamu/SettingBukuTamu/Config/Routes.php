<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-setting-buku-tamu', ['namespace' => 'SettingBukuTamu\Controllers'], function ($subroutes) {
	$subroutes->add('', 'SettingBukuTamu::index');
	$subroutes->add('index', 'SettingBukuTamu::index');
});
