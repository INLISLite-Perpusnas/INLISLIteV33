<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-nama-perpustakaan', ['namespace' => 'NamaPerpustakaan\Controllers'], function ($subroutes) {
	$subroutes->add('', 'NamaPerpustakaan::index');
	$subroutes->add('index', 'NamaPerpustakaan::index');
	$subroutes->add('update', 'NamaPerpustakaan::update');
	$subroutes->add('edit', 'NamaPerpustakaan::edit');
	$subroutes->add('do_upload', 'NamaPerpustakaan::do_upload');
	$subroutes->add('upload_file', 'NamaPerpustakaan::upload_file');
});
