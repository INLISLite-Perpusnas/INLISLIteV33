<?php 
if (!isset($routes)) { 
    $routes = \Config\Services::routes(true); 
} 

// Group untuk master-nama-perpustakaan
$routes->group('master-nama-perpustakaan', ['namespace' => 'NamaPerpustakaan\Controllers'], function ($subroutes) { 
    $subroutes->add('', 'NamaPerpustakaan::index'); 
    $subroutes->add('index', 'NamaPerpustakaan::index'); 
    $subroutes->add('edit', 'NamaPerpustakaan::edit'); 
    $subroutes->add('update', 'NamaPerpustakaan::update'); 
    $subroutes->add('searchperpustakaan', 'NamaPerpustakaan::searchPerpustakaan');
    $subroutes->post('daftarkan-inlislite', 'NamaPerpustakaan::daftarkanInlisLite');
    $subroutes->add('logo-upload', 'NamaPerpustakaan::uploadLogo');
    $subroutes->add('logo-current', 'NamaPerpustakaan::getCurrentLogo');
    $subroutes->add('logo-delete', 'NamaPerpustakaan::deleteLogo');
});

// Group untuk api/master-nama-perpustakaan
$routes->group('api/master-nama-perpustakaan', ['namespace' => 'NamaPerpustakaan\Controllers\Api'], function ($subroutes) {
    $subroutes->get('search-npp', 'NamaPerpustakaan::searchNpp');
    $subroutes->get('branch/(:segment)', 'NamaPerpustakaan::getBranchByNpp/$1');
    $subroutes->post('check-url', 'NamaPerpustakaan::checkUrlAvailability');
    $subroutes->post('validate', 'NamaPerpustakaan::validateForm');
    $subroutes->post('update', 'NamaPerpustakaan::update');
    $subroutes->get('get-current-logo', 'NamaPerpustakaan::getCurrentLogo');
    $subroutes->post('delete-logo', 'NamaPerpustakaan::deleteLogo');
    // Logo upload routes - tambahan baru
    $subroutes->add('logo-upload', 'NamaPerpustakaan::uploadLogo');
    $subroutes->add('logo-current', 'NamaPerpustakaan::getCurrentLogo');
    $subroutes->add('logo-delete', 'NamaPerpustakaan::deleteLogo');

    // Optional: Delete route jika diperlukan
    $subroutes->delete('delete/(:num)', 'NamaPerpustakaan::delete/$1');
});
?>
