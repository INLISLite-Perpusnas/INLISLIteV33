<?php 
if (!function_exists('get_cart_return')) {
	function get_cart_return($member_no = false)
	{
		$cart = new \App\Libraries\Cart();
		$contents = array();
		foreach($cart->contents() as $row){
			if(strtoupper($row['name']) == 'RETURN') {
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

