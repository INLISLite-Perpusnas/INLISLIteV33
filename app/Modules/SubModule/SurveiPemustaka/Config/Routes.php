<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('surveipemustaka', ['namespace' => 'SurveiPemustaka\Controllers'], function ($subroutes) {
	$subroutes->add('', 'SurveiPemustaka::index');
	$subroutes->add('index', 'SurveiPemustaka::index');
	$subroutes->add('detail/(:any)', 'SurveiPemustaka::detail/$1');
	$subroutes->add('create', 'SurveiPemustaka::create');
	$subroutes->add('edit/(:any)', 'SurveiPemustaka::edit/$1');
	$subroutes->add('delete/(:any)', 'SurveiPemustaka::delete/$1');
	$subroutes->add('apply_status/(:any)', 'SurveiPemustaka::apply_status/$1');
	$subroutes->add('do_init', 'SurveiPemustaka::do_init');
	$subroutes->add('do_upload', 'SurveiPemustaka::do_upload');
	$subroutes->add('do_delete', 'SurveiPemustaka::do_delete');
	$subroutes->add('flip', 'SurveiPemustaka::flip');

	//custom
	$subroutes->add('question/(:any)', 'SurveiPemustaka::question/$1');
	$subroutes->add('items/(:any)', 'SurveiPemustaka::items/$1');
});

$routes->group('api/surveipemustaka', ['namespace' => 'SurveiPemustaka\Controllers\Api'], function ($subroutes) {
	$subroutes->add('detail/(:any)', 'SurveiPemustaka::detail/$1');
	$subroutes->add('create', 'SurveiPemustaka::create');
	$subroutes->add('edit/(:any)', 'SurveiPemustaka::edit/$1');
	$subroutes->add('delete/(:any)', 'SurveiPemustaka::delete/$1');

	//custom
	$subroutes->add('datatable', 'SurveiPemustaka::datatable');
	$subroutes->add('datatable/(:any)', 'SurveiPemustaka::datatable/$1');
	$subroutes->add('question_datatable', 'SurveiPemustaka::question_datatable');
	$subroutes->add('question_datatable/(:any)', 'SurveiPemustaka::question_datatable/$1');
	$subroutes->add('items_datatable', 'SurveiPemustaka::items_datatable');
	$subroutes->add('items_datatable/(:any)', 'SurveiPemustaka::items_datatable/$1');
});