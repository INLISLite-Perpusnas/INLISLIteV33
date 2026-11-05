<?php

namespace App\Libraries;

/**
 * Aturan file sementara untuk Dropzone (do_upload dan do_delete).
 *
 * File hanya disimpan di WRITEPATH/uploads, di luar folder publik, dengan nama acak.
 * Controller modul lalu memindahkannya ke folder tujuan setelah form disimpan.
 */
class TempUpload
{
    public const MAX_SIZE = 12 * 1024 * 1024;

    /** Ekstensi yang diterima beserta tipe isi file yang cocok. SVG sengaja tidak diterima karena bisa memuat skrip. */
    private const ALLOWED = [
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png'],
        'gif'  => ['image/gif'],
        'webp' => ['image/webp'],
        'pdf'  => ['application/pdf'],
        'doc'  => ['application/msword', 'application/vnd.ms-office', 'application/cdfv2', 'application/x-ole-storage'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/encrypted'],
    ];

    public static function directory(): string
    {
        return WRITEPATH . 'uploads/';
    }

    /**
     * Mengembalikan ekstensi aman bila nama dan isi file cocok dengan daftar, atau null bila ditolak.
     */
    public static function safeExtension(string $path, string $clientName): ?string
    {
        if (!is_file($path)) {
            return null;
        }

        $size = filesize($path);
        if ($size === false || $size === 0 || $size > self::MAX_SIZE) {
            return null;
        }

        $extension = strtolower(pathinfo($clientName, PATHINFO_EXTENSION));
        if (!isset(self::ALLOWED[$extension])) {
            return null;
        }

        $mime = strtolower((string) (new \finfo(FILEINFO_MIME_TYPE))->file($path));

        return in_array($mime, self::ALLOWED[$extension], true) ? $extension : null;
    }

    public static function randomName(string $extension): string
    {
        return bin2hex(random_bytes(16)) . '.' . $extension;
    }

    /**
     * Path file sementara yang boleh dihapus. Hanya nama hasil randomName() di folder sementara yang diterima.
     */
    public static function deletablePath(string $name): ?string
    {
        if (!preg_match('/^[a-f0-9]{32}\.[a-z0-9]{1,5}$/', $name)) {
            return null;
        }

        $path = self::directory() . $name;

        return is_file($path) ? $path : null;
    }
}
