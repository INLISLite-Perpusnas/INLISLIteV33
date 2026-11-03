<?php

namespace Opac\Libraries\Semantic;

/**
 * Sisi web pencarian semantik OPAC: meminta ID katalog termirip ke service `spark semantic:serve` (localhost).
 * Tidak memuat model/FFI di request web. Bila service mati atau lambat, {@see search()} mengembalikan null dan
 * service ditandai tidak tersedia selama {@see DOWN_TTL} detik agar pengunjung berikutnya tidak ikut menunggu.
 */
final class SemanticSearch
{
    private const DOWN_TTL = 30;
    private const DOWN_CACHE_KEY = 'opac_semantic_down';

    public static function enabled(): bool
    {
        return filter_var(config('Semantic')->enabled, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * ID katalog terurut paling mirip dengan kata kunci, atau null bila service tidak tersedia.
     *
     * @return int[]|null
     */
    public static function search(string $keyword, int $limit): ?array
    {
        $config  = config('Semantic');
        $keyword = trim(mb_substr($keyword, 0, 200));
        if (!self::enabled() || $keyword === '' || cache(self::DOWN_CACHE_KEY)) {
            return null;
        }

        $url     = 'http://' . $config->host . ':' . (int) $config->port . '/search?' . http_build_query(['q' => $keyword, 'k' => $limit]);
        $context = stream_context_create(['http' => ['timeout' => (float) $config->timeout, 'ignore_errors' => true]]);
        $body    = @file_get_contents($url, false, $context);
        $result  = $body === false ? null : json_decode($body, true);

        if (!is_array($result) || !isset($result['ids']) || !is_array($result['ids'])) {
            log_message('warning', 'Pencarian semantik tidak tersedia ({url}); memakai FULLTEXT.', ['url' => $url]);
            cache()->save(self::DOWN_CACHE_KEY, 1, self::DOWN_TTL);
            return null;
        }
        return array_map('intval', $result['ids']);
    }

    /** Teks dokumen yang di-embed untuk satu katalog (juga dasar hash perubahan di semantic:index). */
    public static function documentText(?string $title, ?string $author, ?string $subject, ?string $publisher): string
    {
        $parts = [];
        foreach ([$title, $author, $subject, $publisher] as $part) {
            // Tanpa flag /u: data lama bisa berisi byte non-UTF-8, dan yang dibuang hanya karakter ASCII.
            $clean = trim(preg_replace('/[\s\/:;,.=]+$/', '', preg_replace('/\s+/', ' ', (string) $part)));
            if ($clean !== '') {
                $parts[] = $clean;
            }
        }
        return implode('. ', $parts);
    }
}
