<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-form-sabtuminggu', ['namespace' => 'FormSabtuMinggu\Controllers'], function ($subroutes) {
	$subroutes->add('', 'FormSabtuMinggu::index');
	$subroutes->add('index', 'FormSabtuMinggu::index');
});
