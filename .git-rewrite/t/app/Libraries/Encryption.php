<?php

namespace App\Libraries;

class Encryption
{
    private const ALGORITHM = 'aes-256-cbc';
    private const KEY = '0123456789abcdef0123456789abcdef'; // 32 bytes for aes-256-cbc
    private const IV = 'abcdef0123456789'; // 16 bytes for aes-256-cbc

    public function encryptFile(string $filePath, string $encryptedFilePath): void
    {
        $data = file_get_contents($filePath);

        $encryptedData = openssl_encrypt($data, self::ALGORITHM, self::KEY, OPENSSL_RAW_DATA, self::IV);

        file_put_contents($encryptedFilePath, $encryptedData);
    }

    public function decryptFile(string $encryptedFilePath, string $decryptedFilePath): void
    {
        $encryptedData = file_get_contents($encryptedFilePath);

        $decryptedData = openssl_decrypt($encryptedData, self::ALGORITHM, self::KEY, OPENSSL_RAW_DATA, self::IV);

        file_put_contents($decryptedFilePath, $decryptedData);
    }
}
