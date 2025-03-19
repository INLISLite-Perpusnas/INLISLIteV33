<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-form-encrypt', ['namespace' => 'FormEncrypt\Controllers'], function ($subroutes) {
	$subroutes->add('', 'FormEncrypt::index');
	$subroutes->add('index', 'FormEncrypt::index');
});
