<?php

namespace App\Libraries;

use Config\PerpusKeliling;

class PerpusKelilingUpload
{
    protected PerpusKeliling $config;

    public function __construct()
    {
        $this->config = config('PerpusKeliling');
    }

    /**
     * Upload dan konversi file ke WebP dengan penanganan validasi.
     *
     * @param \CodeIgniter\HTTP\Files\UploadedFile $file
     * @param string $targetSubDir Subfolder misal 'unit/' atau 'dokumentasi/'
     * @return string Nama file WebP yang tersimpan
     * @throws \RuntimeException
     */
    public function uploadImage($file, string $targetSubDir): string
    {
        if (!$file || !$file->isValid()) {
            $errorMsg = $file ? $file->getErrorString() . ' (' . $file->getError() . ')' : 'File upload tidak valid.';
            if ($file && $file->getError() === UPLOAD_ERR_INI_SIZE) {
                $errorMsg = 'Ukuran file "' . esc($file->getClientName()) . '" melebihi batas upload_max_filesize di server PHP.';
            } elseif ($file && $file->getError() === UPLOAD_ERR_FORM_SIZE) {
                $errorMsg = 'Ukuran file "' . esc($file->getClientName()) . '" melebihi batas maksimal form.';
            }
            throw new \RuntimeException($errorMsg);
        }

        // 1. Cek ukuran file (dalam KB)
        $sizeKb = $file->getSizeByUnit('kb');
        if ($sizeKb > $this->config->maxUploadKb) {
            $maxMb = round($this->config->maxUploadKb / 1024, 1);
            $fileMb = round($sizeKb / 1024, 2);
            $fileName = $file->getClientName() ? esc($file->getClientName()) : 'gambar';
            throw new \RuntimeException("Ukuran file '{$fileName}' ({$fileMb} MB) melebihi batas maksimal {$maxMb} MB.");
        }

        // 2. Cek MIME Type asli
        $mime = $file->getMimeType();
        if (!in_array($mime, $this->config->allowedMime, true)) {
            throw new \RuntimeException('Format file harus JPG, PNG, atau WebP.');
        }

        // 3. Tentukan folder tujuan
        $uploadDir = ROOTPATH . 'public/uploads/perpus_keliling/' . trim($targetSubDir, '/\\') . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        helper('image');

        // 4. Upload & Konversi ke WebP
        $newWebpName = upload_and_convert_webp($file, $uploadDir, 85);
        if (!$newWebpName) {
            throw new \RuntimeException('Gagal mengunggah dan mengonversi gambar.');
        }

        return $newWebpName;
    }

    /**
     * Hapus file fisik dan thumbnail-nya (jika ada).
     */
    public function deleteImage(string $fileName, string $targetSubDir): bool
    {
        $fileName = basename($fileName);
        $uploadDir = ROOTPATH . 'public/uploads/perpus_keliling/' . trim($targetSubDir, '/\\') . '/';
        $filePath = $uploadDir . $fileName;

        $success = true;
        if (file_exists($filePath)) {
            $success = unlink($filePath);
        }

        $thumbPath = $uploadDir . 'thumb_' . $fileName;
        if (file_exists($thumbPath)) {
            unlink($thumbPath);
        }

        return $success;
    }
}
