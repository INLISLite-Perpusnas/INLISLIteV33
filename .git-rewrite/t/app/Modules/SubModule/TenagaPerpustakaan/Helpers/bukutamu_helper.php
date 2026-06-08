<?php 
if (!function_exists('get_koleksi')) {
    function get_koleksi($id)
    {
		$db = db_connect();
		$builder = $db->table('collections as a')
			->select('a.*')
			->where('a.ID', $id);

		$data = $builder->get()->getRow();

        return $data;
    }
}
