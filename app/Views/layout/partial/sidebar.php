<?php
$request = service('request');
helper('menu');
$user = user();
$group = isset($user->category) ? $user->category : 'admin';

// View partial dirender dalam scope tersendiri oleh CodeIgniter, sehingga
// variabel lokal dari layout utama tidak selalu tersedia di sini.
$sidebarSettingsRows = db_connect()->table('settingparameters')
    ->select('Name, Value')
    ->whereIn('Name', ['Logo', 'NamaPerpustakaan'])
    ->get()
    ->getResultArray();
$sidebarSettings = array_column($sidebarSettingsRows, 'Value', 'Name');
$nama_perpustakaan = $sidebarSettings['NamaPerpustakaan'] ?? 'Perpustakaan';
$sidebarLogoFile = !empty($sidebarSettings['Logo']) ? basename((string) $sidebarSettings['Logo']) : '';
$sidebarLogoRelativePath = $sidebarLogoFile !== '' && is_file(FCPATH . 'uploads/branch/' . $sidebarLogoFile)
    ? 'uploads/branch/' . $sidebarLogoFile
    : 'assets/img/default-perpus.png';
$sidebarLogoVersion = is_file(FCPATH . $sidebarLogoRelativePath)
    ? (string) filemtime(FCPATH . $sidebarLogoRelativePath)
    : '1';
$sidebarLogoUrl = base_url($sidebarLogoRelativePath) . '?v=' . rawurlencode($sidebarLogoVersion);

$menuHtml = null;
if (!empty($isLightweightBackendPage)) {
    $menuCacheKey = 'dashboard_menu_' . hash('sha256', $group . ':' . $request->getUri()->getPath());
    $menuHtml = cache($menuCacheKey);
}

if (!is_string($menuHtml)) {
    $menuHtml = display_menu_backend(0, 1, $group);
    if (!empty($menuCacheKey)) {
        cache()->save($menuCacheKey, $menuHtml, 300);
    }
}

$username = (string) ($user->username ?? 'Pengguna');
$initials = strtoupper(mb_substr(preg_replace('/[^A-Za-z0-9]/', '', $username) ?: 'U', 0, 2));
?>

<style>
    /*
     * Sidebar mengikuti pola INA Digital (design.inadigital.go.id/pattern/sidebar):
     * - Expand: ikon + label + status aktif (desktop)
     * - Collapse: hanya ikon (tablet / laptop kecil), label tampil sebagai tooltip
     * - State: default, hover (sorotan), selected (halaman aktif)
     * - Hirarki menu maksimal 2 level
     */
    .sidebar-argon {
        position: fixed;
        inset: 0 auto 0 0;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        width: var(--ina-sidebar-width);
        background: var(--ina-surface);
        border-right: 1px solid var(--ina-border);
        transition: width .2s ease, transform .25s ease;
    }

    /* Header: logo + aksi */
    .sidebar-header {
        display: flex;
        align-items: center;
        gap: 8px;
        box-sizing: border-box;
        flex-shrink: 0;
        /* Sejajar dengan bar atas di area konten */
        height: var(--ina-header-height, 77px);
        padding: 0 16px 0 20px;
        margin: 0 4px;
        border-bottom: 1px solid var(--ina-border);
    }

    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        flex: 1;
        color: var(--ina-text);
        text-decoration: none;
    }

    .sidebar-brand img {
        width: 40px;
        height: 40px;
        flex-shrink: 0;
        object-fit: contain;
        border-radius: 8px;
    }

    .sidebar-brand-text {
        display: flex;
        flex-direction: column;
        min-width: 0;
        line-height: 1.2;
    }

    .sidebar-brand-text strong {
        font-size: 20px;
        font-weight: 800;
        letter-spacing: -.02em;
        color: var(--ina-primary);
    }

    .sidebar-brand-text small {
        font-size: 12px;
        color: var(--ina-text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sidebar-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        padding: 0;
        color: var(--ina-text-subtle);
        background: transparent;
        border: 0;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color .15s ease, color .15s ease;
    }

    .sidebar-icon-btn:hover {
        background: var(--ina-hover);
        color: var(--ina-text);
    }

    .sidebar-icon-btn svg {
        width: 20px;
        height: 20px;
    }

    /* Area menu */
    .sidebar-nav {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 16px 20px;
    }

    .nav-menu-argon,
    .submenu-argon {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .nav-label-argon {
        margin: 20px 12px 8px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--ina-text-muted);
    }

    .nav-menu-argon > .nav-label-argon:first-child {
        margin-top: 4px;
    }

    .nav-menu-argon li a {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 44px;
        padding: 10px 12px;
        margin-bottom: 4px;
        color: var(--ina-text-subtle);
        font-size: 15px;
        font-weight: 500;
        line-height: 1.3;
        text-decoration: none;
        border-radius: 8px;
        transition: background-color .15s ease, color .15s ease;
    }

    .nav-menu-argon li a > i:first-child {
        flex-shrink: 0;
        width: 22px;
        font-size: 17px;
        text-align: center;
        color: var(--ina-icon);
        transition: color .15s ease;
    }

    .nav-menu-argon li a > span {
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Hover: sorot item yang ditunjuk */
    .nav-menu-argon li a:hover {
        background: var(--ina-hover);
        color: var(--ina-text);
    }

    .nav-menu-argon li a:hover > i:first-child {
        color: var(--ina-text);
    }

    /* Selected: halaman aktif */
    .nav-menu-argon li a.active {
        background: var(--ina-primary-subtle);
        color: var(--ina-primary);
        font-weight: 600;
    }

    .nav-menu-argon li a.active > i:first-child {
        color: var(--ina-primary);
    }

    /* Induk menu yang anaknya aktif: cukup teks biru, latar tetap netral */
    .has-submenu > a.submenu-toggle.active {
        background: transparent;
    }

    .has-submenu > a.submenu-toggle.active:hover {
        background: var(--ina-hover);
    }

    .nav-menu-argon li a:focus-visible,
    .sidebar-icon-btn:focus-visible,
    .sidebar-footer a:focus-visible {
        outline: 2px solid var(--ina-primary);
        outline-offset: 2px;
    }

    /* Submenu (level 2) */
    .submenu-argon {
        display: none;
        margin: 0 0 4px 23px;
        padding-left: 12px;
        border-left: 1px solid var(--ina-border);
    }

    .has-submenu.open > .submenu-argon {
        display: block;
    }

    .submenu-argon li a {
        min-height: 38px;
        padding: 8px 12px;
        font-size: 14px;
    }

    .caret {
        margin-left: auto;
        font-size: 11px !important;
        width: auto !important;
        color: var(--ina-text-muted);
        transition: transform .2s ease;
    }

    .has-submenu.open > a > .caret {
        transform: rotate(180deg);
    }

    /* Footer: pengguna */
    .sidebar-footer {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        margin: 0 4px;
        border-top: 1px solid var(--ina-border);
    }

    .sidebar-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        font-size: 13px;
        font-weight: 700;
        color: var(--ina-primary);
        background: var(--ina-primary-subtle);
        border-radius: 50%;
    }

    .sidebar-user {
        flex: 1;
        min-width: 0;
        line-height: 1.3;
    }

    .sidebar-user strong,
    .sidebar-user a {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sidebar-user strong {
        font-size: 14px;
        font-weight: 600;
        color: var(--ina-text);
    }

    .sidebar-user a {
        font-size: 13px;
        color: var(--ina-text-muted);
        text-decoration: none;
    }

    .sidebar-user a:hover {
        color: var(--ina-primary);
    }

    .sidebar-logout {
        color: var(--ina-danger);
    }

    .sidebar-logout:hover {
        background: var(--ina-danger-subtle);
        color: var(--ina-danger);
    }

    /* Scrollbar */
    .sidebar-nav::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-nav::-webkit-scrollbar-thumb {
        background: var(--ina-border);
        border-radius: 10px;
    }

    /* ===== Collapse: hanya ikon ===== */
    body.sidebar-collapsed .sidebar-argon {
        width: var(--ina-sidebar-collapsed);
    }

    body.sidebar-collapsed .sidebar-header {
        flex-direction: column;
        height: auto;
        padding: 16px 8px;
    }

    body.sidebar-collapsed .sidebar-brand {
        flex: none;
    }

    body.sidebar-collapsed .sidebar-brand-text,
    body.sidebar-collapsed .nav-menu-argon li a > span,
    body.sidebar-collapsed .caret,
    body.sidebar-collapsed .sidebar-user,
    body.sidebar-collapsed .submenu-argon {
        display: none !important;
    }

    body.sidebar-collapsed .sidebar-nav {
        padding: 16px 12px;
    }

    body.sidebar-collapsed .nav-label-argon {
        height: 1px;
        margin: 12px 8px;
        overflow: hidden;
        color: transparent;
        background: var(--ina-border);
    }

    body.sidebar-collapsed .nav-menu-argon li a {
        justify-content: center;
        padding: 10px 0;
    }

    body.sidebar-collapsed .has-submenu > a.submenu-toggle.active {
        background: var(--ina-primary-subtle);
    }

    body.sidebar-collapsed .sidebar-footer {
        flex-direction: column;
        padding: 16px 8px;
    }

    body.sidebar-collapsed .sidebar-toggle svg {
        transform: scaleX(-1);
    }

    /* ===== Mobile: sidebar off-canvas ===== */
    @media (max-width: 992px) {
        .sidebar-argon {
            width: var(--ina-sidebar-width) !important;
            transform: translateX(-100%);
        }

        .sidebar-argon.open {
            transform: translateX(0);
            box-shadow: 0 10px 30px rgba(15, 23, 42, .15);
        }

        .sidebar-toggle {
            display: none;
        }

        body.sidebar-collapsed .sidebar-header,
        body.sidebar-collapsed .sidebar-footer {
            flex-direction: row;
            padding: 16px 20px;
        }

        body.sidebar-collapsed .sidebar-brand-text,
        body.sidebar-collapsed .nav-menu-argon li a > span,
        body.sidebar-collapsed .sidebar-user {
            display: flex !important;
        }

        body.sidebar-collapsed .nav-menu-argon li a {
            justify-content: flex-start;
            padding: 10px 12px;
        }
    }
</style>

<aside class="sidebar-argon" id="dashboard-sidebar" aria-label="Navigasi utama">
    <div class="sidebar-header">
        <a class="sidebar-brand" href="<?= base_url('dashboard') ?>" title="<?= esc($nama_perpustakaan) ?>">
            <img src="<?= esc($sidebarLogoUrl) ?>" alt="Logo <?= esc($nama_perpustakaan) ?>" width="40" height="40" fetchpriority="high">
            <span class="sidebar-brand-text">
                <strong>INLISLite</strong>
                <small><?= esc($nama_perpustakaan) ?></small>
            </span>
        </a>
        <button class="sidebar-icon-btn sidebar-toggle" id="sidebarToggle" type="button"
            title="Ciutkan sidebar" aria-label="Ciutkan atau lebarkan sidebar"
            aria-controls="dashboard-sidebar" aria-expanded="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="4" width="18" height="16" rx="2" />
                <path d="M9 4v16" />
                <path d="M15 10l-2 2 2 2" />
            </svg>
        </button>
    </div>

    <nav class="sidebar-nav" aria-label="Menu">
        <?= $menuHtml ?>
    </nav>

    <div class="sidebar-footer">
        <span class="sidebar-avatar" aria-hidden="true"><?= esc($initials) ?></span>
        <div class="sidebar-user">
            <strong><?= esc($username) ?></strong>
            <a href="<?= base_url('user/profile') ?>">Profil Saya</a>
        </div>
        <a href="<?= base_url('logout') ?>" class="sidebar-icon-btn sidebar-logout" title="Keluar" aria-label="Keluar">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3" />
                <path d="M10 17l-5-5 5-5" />
                <path d="M5 12h11" />
            </svg>
        </a>
    </div>
</aside>
