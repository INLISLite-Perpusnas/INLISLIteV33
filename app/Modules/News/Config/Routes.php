<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('news', ['namespace' => 'News\Controllers'], function($subroutes) {
    $subroutes->add('', 'News::index');
	$subroutes->add('index', 'News::index');
    $subroutes->add('detail/(:num)/(:segment)', 'News::detail/$1/$2');
    
});