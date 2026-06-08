<?php 
if (!isset($routes)) { 
	$routes = \Config\Services::routes(true); 
} 

$routes->group('master-nama-perpustakaan', ['namespace' => 'NamaPerpustakaan\Controllers'], function ($subroutes) { 
	// Existing routes
	$subroutes->add('', 'NamaPerpustakaan::index'); 
	$subroutes->add('index', 'NamaPerpustakaan::index'); 
	$subroutes->add('update', 'NamaPerpustakaan::update'); 
	$subroutes->add('edit', 'NamaPerpustakaan::edit'); 
	$subroutes->add('do_upload', 'NamaPerpustakaan::do_upload'); 
	$subroutes->add('upload_file', 'NamaPerpustakaan::upload_file'); 
	
	// New AJAX API routes untuk NPP search functionality
	$subroutes->group('api', function ($apiroutes) {
		$apiroutes->get('search-npp', 'NamaPerpustakaan::searchNpp');
		$apiroutes->get('branch/(:segment)', 'NamaPerpustakaan::getBranchByNpp/$1');
		$apiroutes->post('check-url', 'NamaPerpustakaan::checkUrlAvailability');
		$apiroutes->post('validate', 'NamaPerpustakaan::validateForm');
	});
	
	// Optional: Delete route jika diperlukan di masa depan
	$subroutes->delete('delete/(:num)', 'NamaPerpustakaan::delete/$1');
});