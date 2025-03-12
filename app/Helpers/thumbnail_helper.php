<?php
if (!function_exists('base64_to_jpeg')) {
	function base64_to_jpeg($base64_string, $output_file) {
		$ifp = fopen( $output_file, 'wb' ); 
		$data = explode( ',', $base64_string );
		fwrite( $ifp, base64_decode( $data[ 1 ] ) );
		fclose( $ifp ); 
	
		return $output_file; 
	}
}

if (!function_exists('create_thumbnail')) {
    function create_thumbnail($path, $file = null, $prefix = 'thumb_', $width = 200)
    {
		$thumbnails = service('thumbnails');
		$thumbnails->setImageType(IMAGETYPE_JPEG);
		$thumbnails->setWidth($width);

		if(!empty($file)){
			if(file_exists($path.'/'.$file)){
				$thumbnails->create($path.'/'.$file, $path.'/'.$prefix.$file);
			}
		}
    }
}

if (!function_exists('unlink_file')) {
    function unlink_file($path, $file = null)
    {
		$result = false;
		if(!empty($file)){
			if(file_exists($path.'/'.$file)){
				unlink($path.'/'.$file);
				$result = true;
			} 
		}

		return false;
    }
}

if (!function_exists('get_barcode')) {
    function get_barcode($barcode_str)
    {
		$result = '';
		$barcode = new \Picqer\Barcode\BarcodeGeneratorHTML();
		$barcode = $barcode->getBarcode($barcode_str, $barcode::TYPE_CODE_39);
		$result = $barcode;

		return $result;
    }
}

if (!function_exists('get_barcode_png')) {
	function get_barcode_png($barcodeData)
	{
		$result			  = '';
		$barcodeGenerator = new \Picqer\Barcode\BarcodeGeneratorPNG();
		$barcodeImage     = 'data:image/png;base64,' .base64_encode($barcodeGenerator->getBarcode($barcodeData, $barcodeGenerator::TYPE_CODE_128));
		$result			  = $barcodeImage;
		return $result;
	}
}

