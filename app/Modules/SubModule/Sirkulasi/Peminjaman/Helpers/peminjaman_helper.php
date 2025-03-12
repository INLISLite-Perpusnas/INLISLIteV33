<?php 
if (!function_exists('get_cart_loan')) {
	function get_cart_loan($member_no = false)
	{
		$cart = new \App\Libraries\Cart();
		$contents = array();
		foreach($cart->contents() as $row){
			if(strtoupper($row['name']) == 'LOAN') {
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

if (!function_exists('get_loan_count')) {
	function get_loan_count($member_id)
	{
		$db = db_connect('data');
		$builder = $db->table('collectionloanitems cli')
			->select('count(cli.ID) as total')
			->where('cli.LoanStatus','Loan')
			->where('cli.member_id', $member_id);
		
		$result = $builder->get()->getRow();
		return $result->total ?? 0;
	}
}

if (!function_exists('insert_peminjaman')) {
	function insert_peminjaman($member_no)
	{
		
		return true;
		// $db = db_connect('data');
		// $builder = $db->table('collectionloanextends cle')
		// 	->select('count(cle.id) as total')
		// 	->where('cle.CollectionLoanItem_id', $CollectionLoanItem_id);
		
		// $result = $builder->get()->getRow();
		// return $result->total ?? 0;
	}
}