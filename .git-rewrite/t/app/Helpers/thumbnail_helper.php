<?php

// Include the Composer autoloader if it's not already loaded


use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

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

if (!function_exists('get_qrcode_png')) {
    /**
     * Generates a QR code image as a base64 PNG string.
     *
     * @param string $qrCodeData The data to encode in the QR code.
     * @return string The base64 encoded PNG image data URI.
     */
    function get_qrcode_png($qrCodeData)
    {
        try {
            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($qrCodeData)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(ErrorCorrectionLevel::High) // High quality
                ->size(300) // Size in pixels
                ->margin(10) // Margin in pixels
                ->build();

            return $result->getDataUri(); // This directly returns 'data:image/png;base64,...'

        } catch (Exception $e) {
            // Handle error, e.g., return a placeholder or log the error
            // For simplicity, we'll return an empty string on failure.
            error_log('QR Code Generation Failed: ' . $e->getMessage());
            return '';
        }
    }
}

