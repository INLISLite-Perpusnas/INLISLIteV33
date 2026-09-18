<?php

/** Return a cached 160px logo for an 80px sidebar on high density screens. */
function backend_logo_url(string $logo = ''): string
{
    $relative = $logo !== '' ? 'uploads/branch/' . $logo : 'assets/img/default-perpus.png';
    $originalUrl = base_url($relative);
    $source = realpath(FCPATH . $relative);
    $root = realpath(FCPATH);
    if (!$source || !$root || strpos($source, $root . DIRECTORY_SEPARATOR) !== 0
        || !function_exists('imagewebp')) {
        return $originalUrl;
    }

    $directory = FCPATH . 'uploads/branch/thumbnails/';
    $filename = 'sidebar-' . sha1($relative . ':' . filemtime($source)) . '.webp';
    $destination = $directory . $filename;
    if (is_file($destination)) {
        return base_url('uploads/branch/thumbnails/' . $filename);
    }
    $size = @getimagesize($source);
    if (!$size || $size[0] < 1 || $size[1] < 1 || $size[0] * $size[1] > 20000000) {
        return $originalUrl;
    }
    if (!is_dir($directory) && !@mkdir($directory, 0755, true) && !is_dir($directory)) {
        return $originalUrl;
    }
    $loaders = [IMAGETYPE_PNG => 'imagecreatefrompng', IMAGETYPE_JPEG => 'imagecreatefromjpeg', IMAGETYPE_WEBP => 'imagecreatefromwebp'];
    $loader = $loaders[$size[2]] ?? null;
    $image = $loader && function_exists($loader) ? @$loader($source) : false;
    if (!$image) {
        return $originalUrl;
    }
    $scale = min(1, 160 / max($size[0], $size[1]));
    $width = max(1, (int) round($size[0] * $scale));
    $height = max(1, (int) round($size[1] * $scale));
    $thumbnail = imagecreatetruecolor($width, $height);
    imagealphablending($thumbnail, false);
    imagesavealpha($thumbnail, true);
    imagefill($thumbnail, 0, 0, imagecolorallocatealpha($thumbnail, 0, 0, 0, 127));
    imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
    $saved = @imagewebp($thumbnail, $destination, 82);
    imagedestroy($thumbnail);
    imagedestroy($image);

    return $saved ? base_url('uploads/branch/thumbnails/' . $filename) : $originalUrl;
}
