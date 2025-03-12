<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('notification', ['namespace' => 'App\Modules\Backend\Notification\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Notification::index');
	$subroutes->add('notification', 'Notification::index');
	$subroutes->add('index', 'Notification::index');
	$subroutes->add('detail/(:num)', 'Notification::detail/$1');
	$subroutes->add('edit/(:num)', 'Notification::edit/$1');
	$subroutes->add('create', 'Notification::create');
	$subroutes->add('delete/(:num)', 'Notification::delete/$1');
	$subroutes->add('do_init', 'Notification::do_init');
	$subroutes->add('do_upload', 'Notification::do_upload');
	$subroutes->add('do_delete', 'Notification::do_delete');
	$subroutes->add('flip', 'Notification::flip');
});
