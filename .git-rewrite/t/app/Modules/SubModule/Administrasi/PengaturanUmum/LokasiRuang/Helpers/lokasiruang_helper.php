<?php
if (!function_exists('cookie_location')) {
    function cookie_location()
    {
		$data = false;
		if(isset($_COOKIE['location_code'])){
			if($_COOKIE['location_key'] == hash('sha256', $_COOKIE['location_code'])) {
				$code = $_COOKIE['location_code'];
				$data = get_location($code);
			}
		}

        return $data;
    }
}

if (!function_exists('get_location')) {
    function get_location($code)
    {
		$db = db_connect();
		$builder = $db->table('locations as a')
			->select('a.ID, a.Code, a.Name')
			->select('a.LocationLibrary_id, a.Branch_id, c.Name as Branch_name')
			->select('b.Code as LocationLibrary_code, b.Name as LocationLibrary_name, b.Address as LocationLibrary_address')
			->join('location_library as b','b.ID = a.LocationLibrary_id')
			->join('branchs as c','c.ID = a.Branch_id')
			->where('a.Code', $code);

		$data = $builder->get()->getRow();

        return $data;
    }
}