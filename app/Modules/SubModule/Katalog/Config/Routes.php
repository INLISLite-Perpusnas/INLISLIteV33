<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

$routes->group('report', ['namespace' => 'Katalog\Controllers'], function ($subroutes) {
	$subroutes->add('katalog', 'Katalog::report');
});

$routes->group('katalog', ['namespace' => 'Katalog\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Katalog::index');
	$subroutes->add('index', 'Katalog::index');
	$subroutes->add('create', 'Katalog::create');
	$subroutes->add('create_marc', 'Katalog::create_marc');
	$subroutes->add('create_marc2', 'Katalog::create_marc2');
	$subroutes->add('add_marc', 'Katalog::add_marc');
	$subroutes->add('edit/(:any)', 'Katalog::edit/$1');
	$subroutes->add('edit_marc/(:any)', 'Katalog::edit_marc/$1');
	$subroutes->add('detail/(:any)', 'Katalog::edit/$1/1');
	$subroutes->add('delete/(:any)', 'Katalog::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Katalog::apply_status/$1');
	$subroutes->add('do_init', 'Katalog::do_init');
	$subroutes->add('do_upload', 'Katalog::do_upload');
	$subroutes->add('do_delete', 'Katalog::do_delete');
	$subroutes->add('flip', 'Katalog::flip');
	$subroutes->add('importviews', 'Katalog::importviews');
	// app/Config/Routes.php
	$subroutes->add('ekspor_marc', 'Katalog::ekspor_marc');

	$subroutes->add('marc-import', 'Katalog::showCreateForm');
	$subroutes->add('create-marc-from-file', 'Katalog::createFromMarcFile');

	$subroutes->add('view_decrypted/(:any)', 'Katalog::view_decrypted/$1');
  $subroutes->add('view_decrypted_article/(:any)', 'Katalog::view_decrypted_article/$1');
	$subroutes->add('get_decrypted_content/(:any)', 'Katalog::get_decrypted_content/$1');
  $subroutes->add('get_decrypted_content_article/(:any)', 'Katalog::get_decrypted_content_article/$1');

	//custom
	$subroutes->add('karantina', 'Katalog::karantina');
	$subroutes->add('proses_karantina', 'Katalog::proses_karantina');
	$subroutes->add('proses_opac', 'Katalog::proses_opac');
	$subroutes->add('pulihkan_katalog', 'Katalog::pulihkan_katalog');
	$subroutes->add('hapus_permanen', 'Katalog::hapus_permanen');

  //artikel
	$subroutes->add('datatable_artikel', 'Katalog::datatable_artikel');
	$subroutes->add('create_artikel', 'Katalog::create_artikel');
	$subroutes->add('get_artikel/(:any)', 'Katalog::get_artikel/$1');
	$subroutes->add('edit_artikel/(:any)', 'Katalog::edit_artikel/$1');
	$subroutes->add('delete_artikel/(:any)', 'Katalog::delete_artikel/$1');

  //edisi serial
  $subroutes->add('delete-edisi-serial/(:any)/(:any)', 'Katalog::deleteEdisiSerial/$1/$2');
});

$routes->group('api/katalog', ['namespace' => 'Katalog\Controllers\Api'], function ($subroutes) {
	$subroutes->add('', 'Katalog::index');
	$subroutes->add('index', 'Katalog::index');
	$subroutes->add('detail/(:any)', 'Katalog::detail/$1');
	$subroutes->add('create', 'Katalog::create');
	$subroutes->add('edit/(:any)', 'Katalog::edit/$1');
	$subroutes->add('view_decrypted/(:any)', 'Katalog::view_decrypted/$1');
	$subroutes->add('get_decrypted_content/(:any)', 'Katalog::get_decrypted_content/$1');
	$subroutes->add('delete/(:any)', 'Katalog::delete/$1');

	//custom
	$subroutes->add('datatable', 'Katalog::datatable');
	$subroutes->add('datatable/(:any)', 'Katalog::datatable/$1');
	$subroutes->add('switch/(:any)', 'Katalog::switch/$1');
	$subroutes->add('upload_cover', 'Katalog::upload_cover');
	$subroutes->add('upload_cover/(:any)/(:any)', 'Katalog::upload_cover/$1/$2');
	$subroutes->add('upload_file', 'Katalog::upload_file');
	$subroutes->add('upload_file/(:any)/(:any)', 'Katalog::upload_file/$1/$2');
  $subroutes->add('upload_file_digital_artikel', 'Katalog::upload_file_digital_artikel');
	$subroutes->add('delete_file/(:any)', 'Katalog::delete_file/$1');
  $subroutes->add('delete_file_article/(:any)', 'Katalog::delete_file_article/$1');

	//artikel
	$subroutes->add('datatable_artikel', 'Katalog::datatable_artikel');
	$subroutes->add('create_artikel', 'Katalog::create_artikel');
	$subroutes->add('get_artikel/(:any)', 'Katalog::get_artikel/$1');
	$subroutes->add('edit_artikel/(:any)', 'Katalog::edit_artikel/$1');
	$subroutes->add('delete_artikel/(:any)', 'Katalog::delete_artikel/$1');

	//marc
	$subroutes->add('add_to_session/(:any)', 'Katalog::add_to_session/$1');
	$subroutes->add('remove_from_session/(:any)', 'Katalog::remove_from_session/$1');
	$subroutes->add('get_all_tags/(:any)', 'Katalog::get_all_tags/$1');
	$subroutes->add('get_field_indicator1/(:any)', 'Katalog::get_field_indicator1/$1');
	$subroutes->add('get_field_indicator2/(:any)', 'Katalog::get_field_indicator2/$1');
	$subroutes->add('get_field_content/(:any)', 'Katalog::get_field_content/$1');

  //edisi serial
  $subroutes->add('datatable-edisi-serial/(:any)', 'Katalog::datatableEdisiSerial/$1');
  $subroutes->add('create-edisi-serial', 'Katalog::createEdisiSerial');
});

$routes->group('karantina-katalog', ['namespace' => 'Katalog\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Katalog::karantina');
});
