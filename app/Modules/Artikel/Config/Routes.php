<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

/**
 * Modul Artikel — halaman publik (gaya OPAC) untuk menelusuri artikel
 * terbitan berkala dan membaca konten digitalnya (kalau ada) tanpa login.
 */
$routes->group('artikel', ['namespace' => 'Artikel\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Artikel::index');
	$subroutes->add('index', 'Artikel::index');
	$subroutes->add('detail/(:num)', 'Artikel::detail/$1');
});
