<?php
if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('laporan-ai', ['namespace' => 'LaporanAI\Controllers'], function ($subroutes) {
    $subroutes->add('/', 'LaporanAI::index');
});

$routes->group('api/laporan-ai', ['namespace' => 'LaporanAI\Controllers'], function ($subroutes) {
    // Database explorer endpoints
    $subroutes->add('tables', 'LaporanAI::getTables');
    $subroutes->add('table-structure/(:segment)', 'LaporanAI::getTableStructure/$1');
    $subroutes->add('table-data/(:segment)', 'LaporanAI::getTableData/$1');
    
    // SQL execution
    $subroutes->add('execute-query', 'LaporanAI::executeQuery');
    $subroutes->add('natural-to-sql', 'LaporanAI::naturalToSQL');
    
    // Export
    $subroutes->add('export-excel', 'LaporanAI::exportToExcel');
    
    // Original routes
    $subroutes->add('', 'LaporanAI::index');
    $subroutes->add('index', 'LaporanAI::index');
    $subroutes->add('visitor_datatable', 'LaporanAI::visitor_datatable');
    $subroutes->add('visitor_datatable/(:any)/(:any)', 'LaporanAI::visitor_datatable/$1/$2');
    $subroutes->add('member_datatable', 'LaporanAI::member_datatable');
    $subroutes->add('member_datatable/(:any)', 'LaporanAI::member_datatable/$1');
});