<?php 

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

// Route untuk OAI-PMH
$routes->group('oai', ['namespace' => 'Oai\Controllers'], function ($subroutes) {
    // Route utama OAI-PMH
    $subroutes->add('', 'Oai::index');
    $subroutes->add('index', 'Oai::index');
    
    // Route alternatif untuk OAI-PMH (bisa diakses dengan /oai/pmh)
    $subroutes->add('pmh', 'Oai::index');
    
    // Route spesifik untuk setiap verb OAI-PMH (opsional)
    $subroutes->add('identify', 'Oai::identify');
    $subroutes->add('listmetadataformats', 'Oai::listMetadataFormats');
    $subroutes->add('listsets', 'Oai::listSets');
    $subroutes->add('listidentifiers', 'Oai::listIdentifiers');
    $subroutes->add('listrecords', 'Oai::listRecords');
    $subroutes->add('getrecord', 'Oai::getRecord');
    
    // Route untuk testing/debugging
    $subroutes->add('test', 'Oai::test');
    $subroutes->add('validate', 'Oai::validate');
});

// Route alternatif tanpa group (jika diperlukan akses langsung)
$routes->add('oai-pmh', 'Oai\Controllers\Oai::index');
$routes->add('oai-pmh/(:any)', 'Oai\Controllers\Oai::$1');

// Route untuk backward compatibility
$routes->add('oaipmh', 'Oai\Controllers\Oai::index');
$routes->add('OAI-PMH', 'Oai\Controllers\Oai::index');

?>