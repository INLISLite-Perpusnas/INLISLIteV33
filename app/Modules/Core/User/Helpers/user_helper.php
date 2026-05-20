<?php
if (!function_exists('get_groups')) {
	function get_groups($slug = '')
	{
		helper('auth');

		$model = new \Group\Models\GroupModel();
		$groups = $model->where('level >', 0)->findAll();

		if (is_member('admin')) {
			$include = ['admin', 'sa_prov', 'sa_kabkot', 'sa_umum', 'sa_pkpt', 'sa_psm', 'anggota', 'kataloger', 'layanan'];
		} elseif (is_member('sa_prov')) {
			$include = ['sa_prov', 'sa_kabkot', 'sa_umum', 'sa_pkpt', 'sa_psm', 'keanggotaan', 'kataloger', 'layanan'];
		} elseif (is_member('sa_kabkot')) {
			$include = ['sa_kabkot', 'sa_umum', 'sa_pkpt', 'sa_psm', 'keanggotaan', 'kataloger', 'layanan'];
		} elseif (is_member('sa_pkpt')) {
			$include = ['sa_pkpt', 'keanggotaan', 'kataloger', 'layanan'];
		} elseif (is_member('sa_psm')) {
			$include = ['sa_psm', 'keanggotaan', 'kataloger', 'layanan'];
		} elseif (is_member('keanggotaan')) {
			$include = ['anggota'];
		} else {
			$include = [];
		}

		if (!empty($include)) {
			$groups = $model->whereIn('name', $include)->findAll();
		}

		return $groups;
	}
}

if (!function_exists('get__')) {
	function get__($id)
	{
		// $userModel = new \Auth\Models\UserModel();
		// $query = $userModel
		// 	->select('id, email, username, first_name, last_name, phone, avatar, cover, address, website, gender, birth_date')
		// 	->where('id', $id);

		// $data = $query->first();
		// if(!empty($data)){
		// 	$data->prefix_url = base_url('uploads/user/');
		// 	$data->bearer_token = "Bearer ".jwt_encode($data->id, $data->email);
		// } else {
		// 	$data = array();
		// }

		return true;
	}
}
