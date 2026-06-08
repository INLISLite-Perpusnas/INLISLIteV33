<?php

if (!function_exists('buildFilterUrl')) {
    /**
     * Generate URL dengan menambahkan atau mengganti parameter filter tertentu.
     *
     * @param string $key   Nama parameter filter, misalnya 'Publisher', 'Author'
     * @param string $value Nilai parameter
     * @param string $baseUrl (opsional) base url, default 'opac'
     * @return string URL lengkap
     */
    function buildFilterUrl(string $key, string $value, string $baseUrl = 'opac'): string
    {
        // Ambil semua parameter sekarang
        $currentQuery = $_GET;

        // Tambahkan / timpa parameter baru
        $currentQuery[$key] = $value;

        // Susun ulang jadi query string
        $newQuery = http_build_query($currentQuery);

        // Buat URL lengkap
        return base_url($baseUrl) . '?' . $newQuery;
    }
}
