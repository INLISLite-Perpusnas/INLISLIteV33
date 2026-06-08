<?php 
if (!function_exists('convert_doc_to_pdf')) {
    function convert_doc_to_pdf($file_from, $file_to)
    {       
		$message = ''; 
        try {
			$api_key = getenv('convertio.api_key') ?? 'da8988c0a512996ee54f48e36c622740';
			$api_protocol = getenv('convertio.api_protocol') ?? 'http';
			$API = new \Convertio\Convertio($api_key);  
			$API->settings(array('api_protocol' => $api_protocol));
			$API->start($file_from, 'pdf')->wait()->download($file_to)->delete();
		} catch (\Convertio\Exceptions\APIException $e) {
			$message = "API Exception: " . $e->getMessage() . " [Code: ".$e->getCode()."]" . "\n";
		} catch (\Convertio\Exceptions\CURLException $e) {
			$message = "HTTP Connection Exception: " . $e->getMessage() . " [CURL Code: ".$e->getCode()."]" . "\n";
		} catch (Exception $e) {
			$message = "Miscellaneous Exception occurred: " . $e->getMessage() . "\n";
		}

		return $message;
    }
}