<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

/**
 * Alur admin mengikuti modul survey INLISLite 3:
 * Daftar Survei -> Daftar Pertanyaan -> Daftar Pilihan / Daftar Isian,
 * dengan modal Hasil Survei dari daftar survei.
 */
$routes->group('surveipemustaka', ['namespace' => 'SurveiPemustaka\Controllers'], function ($subroutes) {
	// === Survei ===
	$subroutes->get('', 'SurveyAdmin::index');
	$subroutes->get('index', 'SurveyAdmin::index');
	$subroutes->get('create', 'SurveyAdmin::create');
	$subroutes->post('create', 'SurveyAdmin::create', ['filter' => 'csrf']);
	$subroutes->get('edit/(:num)', 'SurveyAdmin::edit/$1');
	$subroutes->post('edit/(:num)', 'SurveyAdmin::edit/$1', ['filter' => 'csrf']);
	$subroutes->post('delete/(:num)', 'SurveyAdmin::delete/$1', ['filter' => 'csrf']);
	$subroutes->get('hasil/(:num)', 'SurveyAdmin::hasil/$1');

	// === Pertanyaan ===
	$subroutes->get('pertanyaan/(:num)', 'SurveyAdmin::pertanyaan/$1');
	$subroutes->get('pertanyaan/create/(:num)', 'SurveyAdmin::pertanyaanCreate/$1');
	$subroutes->post('pertanyaan/create/(:num)', 'SurveyAdmin::pertanyaanCreate/$1', ['filter' => 'csrf']);
	$subroutes->get('pertanyaan/edit/(:num)', 'SurveyAdmin::pertanyaanEdit/$1');
	$subroutes->post('pertanyaan/edit/(:num)', 'SurveyAdmin::pertanyaanEdit/$1', ['filter' => 'csrf']);
	$subroutes->post('pertanyaan/delete/(:num)', 'SurveyAdmin::pertanyaanDelete/$1', ['filter' => 'csrf']);

	// === Pilihan (pertanyaan pilihan ganda) ===
	$subroutes->get('pilihan/(:num)', 'SurveyAdmin::pilihan/$1');
	$subroutes->get('pilihan/create/(:num)', 'SurveyAdmin::pilihanCreate/$1');
	$subroutes->post('pilihan/create/(:num)', 'SurveyAdmin::pilihanCreate/$1', ['filter' => 'csrf']);
	$subroutes->get('pilihan/edit/(:num)', 'SurveyAdmin::pilihanEdit/$1');
	$subroutes->post('pilihan/edit/(:num)', 'SurveyAdmin::pilihanEdit/$1', ['filter' => 'csrf']);
	$subroutes->post('pilihan/delete/(:num)', 'SurveyAdmin::pilihanDelete/$1', ['filter' => 'csrf']);

	// === Isian (pertanyaan isian bebas, hanya baca) ===
	$subroutes->get('isian/(:num)', 'SurveyAdmin::isian/$1');
});
