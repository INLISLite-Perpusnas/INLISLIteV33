<?php if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

/**
 * Modul Artikel — halaman publik (gaya OPAC) untuk menelusuri artikel
 * terbitan berkala dan membaca konten digitalnya (kalau ada) tanpa login.
 */
$routes->group('profil', ['namespace' => 'Profil\Controllers'], function ($subroutes) {
    $subroutes->add('', 'Profil::index');
    $subroutes->add('index', 'Profil::index');
});
