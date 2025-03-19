<?php 
if (!function_exists('get_kartuanggota')) {
    function get_kartuanggota($branch_id, $template_id)
    {
		$db = db_connect('backend');
		$builder = $db
			->table('t_kartu_anggota')
			->select('t_kartu_anggota.id, t_kartu_anggota.template_id, t_kartu_anggota.branch_id, t_kartu_anggota.active, t_kartu_anggota.file_image')
			->where('branch_id', $branch_id)
			->where('template_id', $template_id);

		$data = $builder->get()->getRow();

        return $data;
    }

	function upsert_kartuanggota($id, $branch_id, $template_id, $active)
    {
		$db = db_connect('backend');
		$builder = $db->table('t_kartu_anggota');
		$data = [
			'template_id'   => $template_id,
			'branch_id' 	=> $branch_id,
			'active' 		=> $active,
			'description'	=> date('Y-m-d hh:nn:ss')
		];
		
		if($id > 0){
			
			$result = $builder->update($data, array('id' => $id));
		} else {
			$data = array_merge($data, array('id' => $id));
			$result = $builder->insert($data);
		}
	
        return $result;
    }

}
