<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

$routes->group('user', ['namespace' => 'User\Controllers'], function ($subroutes) {
	$subroutes->add('', 'User::index');
	$subroutes->add('login', 'User::login');
	$subroutes->add('index', 'User::index');
	$subroutes->add('profile', 'User::profile');
	$subroutes->add('detail/(:any)', 'User::detail/$1');
	$subroutes->add('detail/(:any)/(:any)', 'User::detail/$1/$1');
	$subroutes->add('create', 'User::create');
	$subroutes->add('edit/(:any)', 'User::edit/$1');
	$subroutes->add('delete/(:any)', 'User::delete/$1');
	$subroutes->add('enable/(:any)', 'User::enable/$1');
	$subroutes->add('enable/(:any)/(:any)', 'User::enable/$1/$1');
	$subroutes->add('disable/(:any)', 'User::disable/$1');
	$subroutes->add('change_password', 'User::change_password');
	$subroutes->add('change_avatar', 'User::change_avatar');
	$subroutes->add('do_upload', 'User::do_upload');
	$subroutes->add('apply_status/(:any)', 'User::apply_status/$1');

	$subroutes->add('permissions/(:any)', 'User::permissions/$1');
});

$routes->group('api/user', ['namespace' => 'User\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'User::detail/$1');
	$subroutes->add('edit/(:any)', 'User::edit/$1');
	$subroutes->add('create', 'User::create');
	$subroutes->add('delete/(:any)', 'User::delete/$1');
	$subroutes->add('upload_file', 'User::upload_file');
	$subroutes->add('datatable', 'User::datatable');
	$subroutes->add('select2_branch', 'User::select2_branch');
	$subroutes->add('select2_branch/(:any)', 'User::select2_branch/$1');
});
