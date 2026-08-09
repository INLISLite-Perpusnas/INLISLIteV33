<?php
if (!function_exists('get_img_url')) {
	function get_img_url($img_name, $hash_id = null, $date = null)
    {
		$url = get_prefix_url($hash_id, $date);

		$img_url = "$url/$img_name";

        return $img_url;
    }
}








