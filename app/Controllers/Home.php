<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use LZCompressor\LZString;
use App\Libraries\Encryption;
use PhpParser\Node\Scalar\Encapsed;

class Home extends BaseController
{
    function index()
    {
        echo "Welcome";
    }

    public function encrypt()
    {
        // Path to the input file
        $filePath = WRITEPATH . 'example.pdf'; // Adjust the path as necessary

        // Path to save the encrypted file
        $encryptedFilePath = WRITEPATH . 'example_encrypted.pdf';

        // Create an instance of SecurityManager
        $securityManager = new Encryption();

        // Encrypt the file
        $securityManager->encryptFile($filePath, $encryptedFilePath);

        echo "File encrypted successfully!";
    }

    public function decrypt()
    {
        // Path to the encrypted file
        $encryptedFilePath = WRITEPATH . 'example_encrypted.pdf'; // Adjust the path as necessary

        // Path to save the decrypted file
        $decryptedFilePath = WRITEPATH . 'example_decrypted.pdf';

        // Create an instance of SecurityManager
        $securityManager = new Encryption();

        // Decrypt the file
        $securityManager->decryptFile($encryptedFilePath, $decryptedFilePath);

        echo "File decrypted successfully!";
    }


    public static function stringDecrypt($key, $string)
    {
        // $abc = "SGVsbG8gd29ybGQ=";
        // dd(base64_decode($abc));

        $sha256 = hash('sha256', $key);
        // print_r($sha256);
        // echo "<br>";

        $key_hash = hex2bin(hash('sha256', $key));
        // Unpack the binary data and display the original characters
        // $unpackedData = unpack('C*', $key_hash);
        // dd($unpackedData);

        // echo "<br>";
        $iv = substr($key_hash, 0, 16);

        $base64decode = base64_decode($string);
        // dd($base64decode);
        $unpackedData = unpack('C*', $base64decode);
        // dd($unpackedData);

        $output = openssl_decrypt($base64decode, 'AES-256-CBC', $key_hash, OPENSSL_RAW_DATA, $iv);

        return $output;
    }

    protected function decompressed($dataString)
    {
        return LZString::decompressFromEncodedURIComponent($dataString);
    }

    public function test()
    {
        $string = "DJW11EJGblMxoxnrpJGyX5iqw0A92fWHw8pWytjby4SNfU2ijr5tgNi1aNlMYf6vudDchoW4BLSLpkeDTh59SlS51LwOw88+YIdSGlL8Jo90vBiXoZDXD8jPcuuOGLIBSxP6xijU/rM89HRnXnv96Ap6p96mcTpPwPexB3MGiaOLvAww1QMk77IVoYPQ/DNF9+qAp8+fJb9QFbRzXYQ29nkzAh3m/N9q8X9evgOv/Jn/4oa330U14+x/sK4Dvrza5+pIJZAv8IILi2J4MXvGfc0YyKtn3jmvRJpbFnJ13UY8QG90pImJwCcFwdxXyC/VQ47ReHo14RukZ10vgX3frAi+vwYMi5Nr5yc+jUv/OS0Yl+7nFOdE2oKi6yfe9TdxEUG3R8dplsuhs1T12AwJBXTPZ8vI2qBTq5N9jsSW8NM=";
        $key = "11895rsk24t0n1640762772";
        $decrypted = self::stringDecrypt($key, $string);
        // dd($decrypted);
        $json = self::decompressed($decrypted);
        dd($json);
        $data = json_decode($json, true);
        dd($data);
    }

    public function jamkesnews()
    {
        $string = "5TQrY34ZGaROrzDQYfabyHozK6P4aI13WrxarhTdLSV0NxBeD6qP656CE46VE4eZHX2+C+VHBNf19oBc1LJ40SLBBwO6stPgKkpD5Xsv5QDyJ0dLe2/MaDi9DvD9XwB7ajFaDa3TK/2P9PyhbMrBLeTfuBjiGGoi98+8lyLZJ9U3vJ3y9oC+mYRBXJ8vGXlAclvhpZzrT+ue3tgc4dTBjjhoGAXXuZsL933B5AJoo8dLMsfShkq2nwaO4I8L2rRqtL2ogL0oHWrcnnBpNMmJznsgqduKciMl5gQACS3mDzQGVo8fpQN6T3tCsHO+lIYp/oNnHpdQzma4kGRQIqK/XbIbnYQWCQxtOWxt3aYFfhcXk3rI79R2zyqCYHIEoMvJHRvs99XSRttdFHY14PD6ZzVBXiWf64mfmqpAc/01GvwV/aqn0Pi4oJ1Dq+D9+4863FkZCri6K9MLOxnjxnZz8169d8nFUT1+G6nh7Uf2Updq01UcKSkiXShjApmJPVVR0nm9blXikG1q47marbb+dNcNemIQ16NIBpNPSCoDPBPIYv/8PbKPRO6DCFSDEgP7T8pAM92rsAwmVyX8BgVQEYZfm8V38WYgV/c2dvharlhYQOLL9PujRuqXX8Jll22mqsT/8hgpxp9KcCt9aP8KQ0eRtJdb2JynyCHF41RQ49HWgd0kZ/0AZOcIZw2Y5kUiP16CS/KpNxTOxhtyrMR5gZLobVHm4eCk83kE+L9SH4nR2kjqc1OEbpqsJbLHIlwsmS6Vk3vvCgdCai2/qzw+e8c4xTiv82lTp+sFmg1RMbojLqkaxLn7XMvW9wwtDWt16W24FOcdPedYtWoN3kfE8jVoyfrBa0rns9facc2/YcUKlsHC7UGpPq8hUG7yVsje1IqPPiK9theKI6rNyEhGHD5pWP0p9JAqOxmKfU8sNktNWOyX6MG7g9g6F01ffjpBeEOjtsUqJbNsr8DEa79JsvYVsd/zPXyyDOwMSjBgPpCMvAsRNU0gCq+6or8vzG+A5tk/mi4rGDFtS3VXxhDWFgFv5JlrQao4h+JJKXUop++6pLsa8nBbLbC7fXdO4Qb3PzC2KVtoT8zCgnpwe+Z3NaR6na+QrKD/PhEd0OJIBE9ty0oHkRpRAk7qlbsdjcJnxfSsZL49W9CtAvHtX1GlVvSucLNo16pOnyv1cMDIb5LqMkxiLJwFo8PAOzQBiXuKRj1bIsbCNPYspRy1LuDWi7pYDobbyxTtzDPzXzyv/0VYjqF/3npkyP7ftDCKITwk/h76Oyv0R2jsAsVEY05refnz6D03gIUsNtoUl5Q5n/ZlreD//JNoYFzr2gNHIJkhorIKl9AUJfRauL/1JQ7xA5gkkQVa3+25OnNDl/W1fOXXHXpeM0Cig3ypFJDZQyxpNZiJoFHC/KFY4qx5dAokDuEDxwaxKO3oaXXjg/N+DCoUnNx6se+DlOfaI3SZYRBg8qJBsG4nfr9Ubkv5urOJC2+/UF+E/XIIFUwiHtvHbels9FQuRqsNIv0Ox5tjfd3hcFSacc9WP0l4cQHPb124HY92hMHXLG2XazDPI/2Wxcq464vZQOPr+uO3aeaehsDO";
        $key = "1797rR5EE2211701224649";

        $decrypted = self::stringDecrypt($key, $string);
        dd($decrypted);
        $json = self::decompressed($decrypted);
        dd($json);
        $data = json_decode($json, true);
        dd($data);
    }

    public function jamkesnews2()
    {
        $decrypted = "3c53220f04e2520b35c3935e12812c0271899700c4d1550c0421286ec0d490123d714f89846b42136a6a88a29d9f80d7a2742866fb33e095a46fa17a57b8e7e19ca56915f0c74eaccd289829f2c50c079f255d415b4e27824dd61680e99df4fbf1b4eaecffff5bd3d9a95bcf0d46f8d57ee172cfc6fd5a9db95177a7d21200438467c1d6592a6e01271b5fc8a0fbdaf46e6d0074272a7f35c14c7074b1d04fffbce1ca04c97585e2622f0de63f7eff5ed0f1f9de7c335808dc5164b4c2e7760259b5aff33a266811056811af9ff4e44cf8b1fd7e764ca0e1";
        $json = self::decompressed($decrypted);
        $data = json_decode($json, true);
        dd($data);
    }
}
