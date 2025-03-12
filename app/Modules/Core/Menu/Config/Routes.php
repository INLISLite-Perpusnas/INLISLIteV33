<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('menu', ['namespace' => 'Menu\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Menu::index');
	$subroutes->add('index', 'Menu::index');
	$subroutes->add('slug/(:any)', 'Menu::slug/$1');
	$subroutes->add('detail/(:any)', 'Menu::detail/$1');
	$subroutes->add('edit/(:any)', 'Menu::edit/$1');
	$subroutes->add('create', 'Menu::create');
	$subroutes->add('delete/(:any)', 'Menu::delete/$1');
	$subroutes->add('do_init', 'Menu::do_init');
	$subroutes->add('do_upload', 'Menu::do_upload');
	$subroutes->add('do_delete', 'Menu::do_delete');
	$subroutes->add('flip', 'Menu::flip');
	$subroutes->add('enable/(:any)', 'Menu::enable/$1');
	$subroutes->add('disable/(:any)', 'Menu::disable/$1');
	$subroutes->add('category_delete/(:any)', 'Menu::category_delete/$1');
});

$routes->group('api/menu', ['namespace' => 'Menu\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'Menu::detail/$1');
	$subroutes->add('edit/(:any)', 'Menu::edit/$1');
	$subroutes->add('create', 'Menu::create');
	$subroutes->add('delete/(:any)', 'Menu::delete/$1');
	$subroutes->add('set_status', 'Menu::set_status');
	$subroutes->add('save_ordering', 'Menu::save_ordering');
	$subroutes->add('category_create', 'Menu::category_create');
	$subroutes->add('category_detail/(:any)', 'Menu::category_detail/$1');
	$subroutes->add('category_edit/(:any)', 'Menu::category_edit/$1');

	$subroutes->add('config', 'Menu::config_menu');
});
