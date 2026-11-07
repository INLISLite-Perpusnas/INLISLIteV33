<?php if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('laporan-opac-histori-pencarian', ['namespace' => 'LaporanHistoriPencarianOpac\Controllers'], function ($subroutes) {
    $subroutes->get('/', 'LaporanHistoriPencarianOpac::index');
    $subroutes->match(['get', 'post'], 'index', 'LaporanHistoriPencarianOpac::index', ['filter' => 'csrf']);
    $subroutes->post('export', 'LaporanHistoriPencarianOpac::export', ['filter' => 'csrf']);
    $subroutes->post('export_pdf', 'LaporanHistoriPencarianOpac::exportPdf', ['filter' => 'csrf']);
});
