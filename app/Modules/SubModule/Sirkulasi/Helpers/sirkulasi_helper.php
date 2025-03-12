<?php
if (!function_exists('get_pad_number')) {
    function get_pad_number($counter, $prefix = 'TRX-', $zero_length = 4)
    {        
        $doc_number = strtoupper($prefix).str_pad($counter , $zero_length , "0" , STR_PAD_LEFT);

        return $doc_number;
    }
}

if (!function_exists('get_cart_return')) {
	function get_cart_return()
	{
		$cart = new \App\Libraries\Cart();
		$contents = array();
		foreach($cart->contents() as $row){
			if(strtoupper($row['name']) == 'RETURN') {
				$contents[] = $row;
			}
		}

		return json_decode(json_encode($contents), FALSE);
	}
}

if (!function_exists('get_cart_loan')) {
	function get_cart_loan()
	{
		$cart = new \App\Libraries\Cart();
		$contents = array();
		foreach($cart->contents() as $row){
			if(strtoupper($row['name']) == 'LOAN') {
				$contents[] = $row;
			}
		}

		return json_decode(json_encode($contents), FALSE);
	}
}