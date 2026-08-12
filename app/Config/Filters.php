<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array<string, string>
     * @phpstan-var array<string, class-string>
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'auth'             => \App\Filters\AuthFilter::class,
        'cors'             => \App\Filters\Cors::class,
        'permissions'     => \App\Filters\Permissions::class,
        'session'         => \Myth\Auth\Filters\LoginFilter::class,
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array<string, array<string, array<string, string>>>|array<string, array<string>>
     * @phpstan-var array<string, list<string>>|array<string, array<string, array<string, string>>>
     */
    public array $globals = [
        'before' => [
            // 'honeypot',
            // 'csrf',
            // 'invalidchars',
            'session' => ['except' => [
                '', 
                'logout*', 
                'login*',
                'news*', 
                'home*', 
                'auth/a/*', 
                'api/katalog/*', 
                'api/eksemplar/*',
                'api/sirkulasi-peminjaman/*',
                'api/sirkulasi-pengembalian/*',
                'buku-tamu*',
                'api-lokasi-ruang/check*',
                'baca-ditempat*',
                'peminjaman-mandiri*',
                'pengembalian-mandiri*',
                'oai*',
                'api/region/*',
                'api/member/*',
                
                // --- PENGECUALIAN UNTUK CONTROLLER OPAC ---
                'opac',                     // method: index
                'opac/index',               // method: index
                'opac/detail/*',            // method: detail
                'opac/search',              // method: search
                'opac/browse',              // method: browse
                'opac/export',              // method: export
                'opac/statistics',          // method: statistics
                'opac/statistics_anggota',  // method: statistics_anggota
                'opac/downloadMarc*',       // method: semua downloadMarc (Utf8, Xml, Mods, dll)
                'opac/member-login',        // method: memberLogin (login anggota via modal)
                // 'opac/bacaDigital/*' TIDAK DIMASUKKAN agar terkena filter session (wajib login)
            ]],
            'cors',
            'permissions' => ['except' => [
                '',
                'logout*',
                'oai*',
                'login*',
                'peminjaman-mandiri*',
                'pengembalian-mandiri*',
                'api-lokasi-ruang/check*',
                'buku-tamu*',
                'baca-ditempat*',
                'home*',
                'news*',
                'apply_status*',
                // 'api/*',
                'cetak-kartu/*',
                'template/*',
                'api/template/*',
                'api/region/*',
                'api/master-template/*',
                'master-template/*',
                'api/member/*',
                
                // --- PENGECUALIAN UNTUK CONTROLLER OPAC ---
                'opac',
                'opac/index',
                'opac/detail/*',
                'opac/search',
                'opac/browse',
                'opac/export',
                'opac/statistics',
                'opac/statistics_anggota',
                'opac/downloadMarc*',
                'opac/member-login',

            ]],
        ],
        'after' => [
            // 'toolbar',
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'post' => ['foo', 'bar']
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don't expect could bypass the filter.
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     */
    public array $filters = [];
}
