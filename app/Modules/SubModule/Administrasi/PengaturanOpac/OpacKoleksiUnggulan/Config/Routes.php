<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('master-opac-koleksi-unggulan', ['namespace' => 'OpacKoleksiUnggulan\Controllers'], function ($subroutes) {
	$subroutes->match(['get', 'post'], '/', 'OpacKoleksiUnggulan::index', ['filter' => 'csrf']);
	$subroutes->match(['get', 'post'], 'index', 'OpacKoleksiUnggulan::index', ['filter' => 'csrf']);
	// ID katalog dikirim lewat body POST agar URI cocok persis dengan permission create/delete
	$subroutes->post('create', 'OpacKoleksiUnggulan::create', ['filter' => 'csrf']);
	$subroutes->post('delete', 'OpacKoleksiUnggulan::delete', ['filter' => 'csrf']);
});

$routes->group('api/master-opac-koleksi-unggulan', ['namespace' => 'OpacKoleksiUnggulan\Controllers\Api'], function ($subroutes) {
	$subroutes->get('datatable', 'OpacKoleksiUnggulan::datatable');
	$subroutes->get('catalog', 'OpacKoleksiUnggulan::catalog');
});
