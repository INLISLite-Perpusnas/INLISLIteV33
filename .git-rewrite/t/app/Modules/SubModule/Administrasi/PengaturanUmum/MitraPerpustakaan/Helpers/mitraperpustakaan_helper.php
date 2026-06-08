<?php
if (!function_exists('cookie_branch')) {
    function cookie_branch()
    {
		$data = false;
		if(isset($_COOKIE['branch_code'])){
			if($_COOKIE['branch_key'] == hash('sha256', $_COOKIE['branch_code'])) {
				$code = $_COOKIE['branch_code'];
				$data = get_branch($code);
			}
		}

        return $data;
    }
}

if (!function_exists('get_branch')) {
    function get_branch($code)
    {
		$db = db_connect();
		$builder = $db->table('branchs as a')
			->select('a.ID, a.Code, a.Name, a.Address')
			->where('a.Alias', $code);

		$data = $builder->get()->getRow();

        return $data;
    }
}

if (!function_exists('get_branch_by_slug')) {
    function get_branch_by_slug($slug)
    {
		$db = db_connect();
		$builder = $db->table('branchs as a')
			->select('a.ID, a.Code, a.Name, a.Alias, a.Address, a.NPP_Provinsi_id, NPP_KabKota_id, NPP_Kecamatan_id')
			->where('a.slug', $slug);

		$data = $builder->get()->getRow();

        return $data;
    }
}
