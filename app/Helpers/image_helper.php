<?php

/**
 * image_helper.php
 *
 * Global helper untuk konversi dan manipulasi gambar.
 * Load via: helper('image') atau tambahkan ke $helpers di Autoload.php
 */

if (!function_exists('convert_to_webp')) {
    /**
     * Konversi file gambar (JPG/PNG/GIF) ke format WebP.
     *
     * @param  string $sourcePath  Path lengkap ke file sumber.
     * @param  int    $quality     Kualitas WebP 0–100 (default 80).
     * @param  bool   $deleteSource Hapus file sumber setelah konversi (default true).
     * @return string|false        Path file WebP yang dihasilkan, atau false jika gagal.
     */
    function convert_to_webp(string $sourcePath, int $quality = 80, bool $deleteSource = true)
    {
        if (!file_exists($sourcePath)) {
            log_message('error', "[image_helper] File tidak ditemukan: {$sourcePath}");
            return false;
        }

        if (!function_exists('imagewebp')) {
            log_message('error', '[image_helper] Fungsi imagewebp tidak tersedia. Pastikan ekstensi GD aktif dengan dukungan WebP.');
            return false;
        }

        $info = @getimagesize($sourcePath);
        if ($info === false) {
            log_message('error', "[image_helper] Bukan file gambar valid: {$sourcePath}");
            return false;
        }

        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                $image = @imagecreatefromjpeg($sourcePath);
                break;

            case IMAGETYPE_PNG:
                $image = @imagecreatefrompng($sourcePath);
                if ($image) {
                    // Pertahankan transparansi PNG
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                }
                break;

            case IMAGETYPE_GIF:
                $image = @imagecreatefromgif($sourcePath);
                break;

            case IMAGETYPE_WEBP:
                // Sudah WebP, tidak perlu konversi
                return $sourcePath;

            default:
                log_message('warning', "[image_helper] Tipe gambar tidak didukung (type={$info[2]}): {$sourcePath}");
                return false;
        }

        if (!$image) {
            log_message('error', "[image_helper] Gagal membaca gambar: {$sourcePath}");
            return false;
        }

        $webpPath = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $sourcePath);

        // Jika nama file tidak berubah (ekstensi aneh), tambahkan .webp
        if ($webpPath === $sourcePath) {
            $webpPath = $sourcePath . '.webp';
        }

        $success = imagewebp($image, $webpPath, $quality);
        imagedestroy($image);

        if (!$success) {
            log_message('error', "[image_helper] Gagal menulis file WebP: {$webpPath}");
            return false;
        }

        if ($deleteSource && $sourcePath !== $webpPath && file_exists($sourcePath)) {
            unlink($sourcePath);
        }

        return $webpPath;
    }
}

if (!function_exists('is_webp_supported')) {
    /**
     * Cek apakah server mendukung konversi WebP via GD.
     *
     * @return bool
     */
    function is_webp_supported(): bool
    {
        if (!function_exists('gd_info')) {
            return false;
        }
        $info = gd_info();
        return !empty($info['WebP Support']);
    }
}

if (!function_exists('get_catalog_thumb_url')) {
    /**
     * Kembalikan URL thumbnail katalog berukuran kecil (WebP).
     * Thumbnail di-generate sekali, lalu di-cache sebagai file.
     *
     * @param  string $coverURL   Nama file cover di uploads/katalog/
     * @param  int    $width      Lebar thumbnail (default 200)
     * @param  int    $height     Tinggi thumbnail (default 240)
     * @return string             URL thumbnail (atau URL gambar asli jika GD tidak tersedia)
     */
    function get_catalog_thumb_url(string $coverURL, int $width = 200, int $height = 240): string
    {
        $default   = base_url('assets/img/default-cover.webp');

        if (empty($coverURL)) {
            return $default;
        }

        $uploadDir  = ROOTPATH . 'public/uploads/katalog/';
        $sourcePath = $uploadDir . $coverURL;

        if (!file_exists($sourcePath)) {
            return $default;
        }

        $stem      = pathinfo($coverURL, PATHINFO_FILENAME);
        $thumbName = "thumb_{$width}x{$height}_{$stem}.webp";
        $thumbPath = $uploadDir . $thumbName;

        if (!file_exists($thumbPath)) {
            // GD + WebP harus tersedia
            if (!function_exists('imagecreatetruecolor') || !function_exists('imagewebp')) {
                return base_url('uploads/katalog/' . $coverURL);
            }

            $info = @getimagesize($sourcePath);
            if (!$info) {
                return base_url('uploads/katalog/' . $coverURL);
            }

            $creators = [
                IMAGETYPE_JPEG => 'imagecreatefromjpeg',
                IMAGETYPE_PNG  => 'imagecreatefrompng',
                IMAGETYPE_GIF  => 'imagecreatefromgif',
                IMAGETYPE_WEBP => 'imagecreatefromwebp',
            ];
            $creator = $creators[$info[2]] ?? null;

            if (!$creator) {
                return base_url('uploads/katalog/' . $coverURL);
            }

            $src = @$creator($sourcePath);
            if (!$src) {
                return base_url('uploads/katalog/' . $coverURL);
            }

            // Pertahankan transparansi PNG
            if ($info[2] === IMAGETYPE_PNG) {
                imagepalettetotruecolor($src);
            }

            $origW = imagesx($src);
            $origH = imagesy($src);

            // Contain-fit: skala proporsional agar gambar penuh terlihat (tidak dipotong)
            $scale  = min($width / $origW, $height / $origH);
            $dstW   = (int) round($origW * $scale);
            $dstH   = (int) round($origH * $scale);
            $dstX   = (int) round(($width  - $dstW) / 2);
            $dstY   = (int) round(($height - $dstH) / 2);

            $thumb = imagecreatetruecolor($width, $height);

            // Background abu-abu sesuai CSS .book-cover
            $bg = imagecolorallocate($thumb, 226, 232, 240); // #e2e8f0
            imagefill($thumb, 0, 0, $bg);

            imagecopyresampled($thumb, $src, $dstX, $dstY, 0, 0, $dstW, $dstH, $origW, $origH);
            imagewebp($thumb, $thumbPath, 82);

            imagedestroy($src);
            imagedestroy($thumb);
        }

        return file_exists($thumbPath)
            ? base_url('uploads/katalog/' . $thumbName)
            : base_url('uploads/katalog/' . $coverURL);
    }
}

if (!function_exists('get_webp_filename')) {
    /**
     * Kembalikan nama file dengan ekstensi diganti .webp.
     *
     * @param  string $filename  Nama file asli (boleh hanya nama, boleh path penuh).
     * @return string
     */
    function get_webp_filename(string $filename): string
    {
        return preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $filename) ?: $filename . '.webp';
    }
}
