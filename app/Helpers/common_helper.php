<?php
if (!function_exists('get_img_url')) {
	function get_img_url($img_name, $hash_id = null, $date = null)
    {
		$url = get_prefix_url($hash_id, $date);

		$img_url = "$url/$img_name";

        return $img_url;
    }
}

if (!function_exists('get_prefix_url')) {
	function get_prefix_url($hash_id = null, $date = null)
    {
		$prefixUrl = getenv('view.prefixUrl') ?? 'https://statics.indozone.news';
		$suffixUrl = getenv('view.suffixUrl') ?? 'content';
		$prefixUrl = "$prefixUrl/$suffixUrl";
		
		$date_str = str_replace("-","/", substr($date, 0, 10));

		$url = "$prefixUrl/$date_str/$hash_id";

        return $url;
    }
}

if (!function_exists('send_mail')) {
	function send_mail($email, $subject, $message, $debug = false)
    {
		$email = new \App\Libraries\Mailer();
		return $email->send_via_google($email,$subject, $message, $debug);
    }
}




