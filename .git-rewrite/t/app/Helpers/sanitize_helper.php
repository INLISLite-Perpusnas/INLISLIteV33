<?php

if (!function_exists('sanitizeSearch')) {
    /**
     * Membersihkan input search:
     * - Hilangkan karakter aneh yang tidak relevan
     * - Trim spasi
     * - Batasi panjang max
     * - Buat jadi lower-case (opsional)
     */
    function sanitizeSearch(?string $input, int $maxLength = 100): string
    {
        if (empty($input)) {
            return '';
        }

        // Hilangkan tag HTML dan trim spasi
        $input = trim(strip_tags($input));

        // Ubah ke lower-case (optional, sesuai kebutuhan)
        $input = strtolower($input);

        // Hilangkan karakter aneh: hanya huruf, angka, spasi, tanda baca umum
        $input = preg_replace('/[^a-z0-9\s\-\_\.\,\&\(\)\/]/', '', $input);

        // Batasi panjang max (default 100 karakter)
        if (strlen($input) > $maxLength) {
            $input = substr($input, 0, $maxLength);
        }

        return $input;
    }
}
