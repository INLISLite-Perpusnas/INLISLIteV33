<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

// Route untuk OAI-PMH.
// Catatan: OAI-PMH memang cuma punya SATU gerbang publik (verb dibaca dari
// query string, sesuai standar OAI-PMH 2.0) — bukan satu route per verb.
// Method per-verb (identify(), listRecords(), dst.) di Oai::class sengaja
// private, jadi tidak bisa/perlu dirutekan langsung. Contoh pemakaian:
//   /oai-pmh?verb=Identify
//   /oai-pmh?verb=ListRecords&metadataPrefix=oai_dc
$routes->group('oai', ['namespace' => 'Oai\Controllers'], function ($subroutes) {
    $subroutes->add('', 'Oai::index');
    $subroutes->add('index', 'Oai::index');

    // Route alternatif untuk OAI-PMH (bisa diakses dengan /oai/pmh)
    $subroutes->add('pmh', 'Oai::index');
});

// Route alternatif tanpa group (jika diperlukan akses langsung)
$routes->add('oai-pmh', 'Oai\Controllers\Oai::index');

// Route untuk backward compatibility
$routes->add('oaipmh', 'Oai\Controllers\Oai::index');
$routes->add('OAI-PMH', 'Oai\Controllers\Oai::index');
