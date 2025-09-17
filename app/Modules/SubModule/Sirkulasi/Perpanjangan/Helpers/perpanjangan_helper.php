<?php 
if (!function_exists('get_cart_extend')) {
	function get_cart_extend($member_no = false)
	{
		$cart = new \App\Libraries\Cart();
		$contents = array();
		foreach($cart->contents() as $row){
			if(strtoupper($row['name']) == 'EXTEND') {
				if(!empty($member_no)) {
					if($row['options']['member']->MemberNo == $member_no){
						$contents[] = $row;
					}
				} else {
					$contents[] = $row;
				}
			}
		}

		return json_decode(json_encode($contents), FALSE);
	}
}

if (!function_exists('count_extend')) {
	function count_extend($CollectionLoanItem_id)
	{
		$db = db_connect();
		$builder = $db->table('collectionloanextends cle')
			->select('count(cle.id) as total')
			->where('cle.CollectionLoanItem_id', $CollectionLoanItem_id);
		
		$result = $builder->get()->getRow();
		return $result->total ?? 0;
	}
}