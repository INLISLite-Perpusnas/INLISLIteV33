<?php
helper(['parameter']);
$request = service('request');

$container_header_class = get_parameter('container-header-class') . " " . get_parameter('container-sidebar-class') . " " . get_parameter('container-footer-class');
if (is_profiling()) {
    $container_header_class = 'fixed-header';
}
if ($request->getVar('fullscreen') == 1) {
    $container_header_class .= ' closed-sidebar';
}

$db = db_connect();
$settingRows = $db->table('settingparameters')
    ->select('Name, Value')
    ->whereIn('Name', ['Logo', 'NamaPerpustakaan', 'NPPPerpustakaan'])
    ->get()
    ->getResultArray();
$settings = array_column($settingRows, 'Value', 'Name');
$logo = $settings['Logo'] ?? '';
$nama_perpustakaan = $settings['NamaPerpustakaan'] ?? 'Perpustakaan';
$npp_perpustakaan = $settings['NPPPerpustakaan'] ?? 'NPP Perpustakaan Mitra';

// Semua halaman backend harus memakai sumber logo yang sama dengan halaman
// Pengaturan Nama Perpustakaan. Versi file mencegah browser menampilkan logo
// lama setelah administrator menggantinya.
$logoFile = $logo !== '' ? basename((string) $logo) : '';
$logoRelativePath = $logoFile !== '' && is_file(FCPATH . 'uploads/branch/' . $logoFile)
    ? 'uploads/branch/' . $logoFile
    : 'assets/img/default-perpus.png';
$logoVersion = is_file(FCPATH . $logoRelativePath) ? (string) filemtime(FCPATH . $logoRelativePath) : '1';
$sidebarLogoUrl = base_url($logoRelativePath) . '?v=' . rawurlencode($logoVersion);

$isDashboardPage = !empty($is_dashboard_page);
$isCatalogListPage = !empty($is_catalog_list_page);
$isExemplarListPage = !empty($is_exemplar_list_page);
$isMemberListPage = !empty($is_member_list_page);
$isLightweightBackendPage = $isDashboardPage || $isCatalogListPage || $isExemplarListPage || $isMemberListPage;

// Get breadcrumb info
$segment1 = $request->getUri()->getSegment(1) ?? 'dashboard';
$segment2 = $request->getUri()->getSegment(2) ?? '';
$page_title = ucfirst($segment2 ?: $segment1);
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= esc($title ?? $page_title . ' - ' . $nama_perpustakaan) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= esc($meta_description ?? 'Sistem manajemen perpustakaan ' . $nama_perpustakaan) ?>">
    <link rel="icon" href="<?= esc($sidebarLogoUrl) ?>">
    <?php if ($isExemplarListPage): ?>
        <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
        <link rel="preload" href="<?= base_url('assets/fonts/catalog/fa-solid-subset.woff2') ?>" as="font" type="font/woff2" crossorigin>
        <link rel="stylesheet" href="<?= base_url('assets/css/catalog-icons.css') ?>">
    <?php elseif ($isMemberListPage): ?>
        <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
        <link rel="stylesheet" href="<?= base_url('assets/css/datatables.min.css') ?>">
        <link rel="stylesheet" href="<?= base_url('assets/css/sweetalert2.css') ?>">
            <link rel="stylesheet" href="<?= base_url('assets/vendors/magnific-popup/magnific-popup.css') ?>">
            <link rel="stylesheet" href="<?= base_url('assets/css/member-list.css') ?>">
        <link rel="preload" href="<?= base_url('assets/fonts/catalog/fa-solid-subset.woff2') ?>" as="font" type="font/woff2" crossorigin>
        <link rel="stylesheet" href="<?= base_url('assets/css/catalog-icons.css') ?>">
    <?php elseif (!$isDashboardPage && !$isCatalogListPage): ?>
        <link rel="stylesheet" href="<?= base_url('themes/uigniter'); ?>/css/base.css">
        <?= $this->include('App\Views\layout\partial\style'); ?>
        <?= $this->include('App\Views\layout\partial\style_custom'); ?>
    <?php elseif ($isCatalogListPage): ?>
        <link rel="preload" href="<?= base_url('assets/fonts/catalog/fa-solid-subset.woff2') ?>" as="font" type="font/woff2" crossorigin>
        <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
        <link rel="stylesheet" href="<?= base_url('assets/css/catalog-icons.css') ?>">
    <?php else: ?>
        <!-- Font Awesome lokal dipertahankan agar seluruh ikon menu tampil seperti semula. -->
        <link rel="stylesheet" href="<?= base_url('assets/vendors/fontawesome-pro-5/css/all.min.css') ?>">
    <?php endif; ?>

    <link rel="preload" href="<?= base_url('assets/fonts/inter/inter-latin.woff2') ?>" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= base_url('assets/css/inter.css') ?>">

    <style>
        /* Token desain admin (gaya INA Digital) */
        :root {
            --ina-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --ina-primary: #1d5fd1;
            --ina-primary-hover: #174ea9;
            --ina-primary-subtle: #eef4ff;
            --ina-text: #1f2937;
            --ina-text-subtle: #374151;
            --ina-text-muted: #6b7280;
            --ina-icon: #4b5563;
            --ina-border: #e5e7eb;
            --ina-hover: #f3f4f6;
            --ina-surface: #ffffff;
            --ina-page: #f7f7f8;
            --ina-success: #1f7a36;
            --ina-warning: #e8700a;
            --ina-danger: #c62828;
            --ina-danger-subtle: #fdecec;
            --ina-sidebar-width: 280px;
            /* Tinggi header kiri (logo sidebar) = header kanan (bar atas), termasuk garis bawah */
            --ina-header-height: 77px;
            --ina-sidebar-collapsed: 76px;
            --ina-radius: 12px;
            --ina-shadow: 0 1px 3px rgba(16, 24, 40, .08), 0 1px 2px rgba(16, 24, 40, .04);
            /* Skala tipografi INA Digital (font-size / line-height, letter-spacing 0%) */
            --ina-h4: 28px / 36px var(--ina-font);
            --ina-h5: 24px / 32px var(--ina-font);
            --ina-body: 18px / 26px var(--ina-font);
            --ina-body-sm: 16px / 24px var(--ina-font);
            --ina-caption: 14px / 20px var(--ina-font);
            --ina-caption-sm: 12px / 16px var(--ina-font);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--ina-surface);
            color: var(--ina-text);
            font-family: var(--ina-font);
            margin: 0;
            overflow-x: hidden;
        }

        .app-container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            padding-left: var(--ina-sidebar-width);
            transition: padding-left .2s ease;
        }

        body.sidebar-collapsed .app-container {
            padding-left: var(--ina-sidebar-collapsed);
        }

        .app-main__outer {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Judul halaman: teks gelap di atas latar terang */
        .app-page-title {
            color: var(--ina-text) !important;
            background: transparent !important;
            margin: 0 0 24px !important;
            padding: 8px 0 0 !important;
        }

        .app-page-title .page-title-heading,
        .app-page-title .page-title-heading h1 {
            font: var(--ina-h4);
            font-weight: 700;
            color: var(--ina-text);
            letter-spacing: 0;
        }

        .app-page-title .page-title-subheading,
        .app-page-title .page-title-heading .page-title-subheading {
            font: var(--ina-body-sm);
            font-weight: 400;
            letter-spacing: 0;
            color: var(--ina-text-muted);
            opacity: 1;
        }

        .app-page-title .page-title-icon {
            display: none !important;
        }

        .breadcrumb {
            background: transparent !important;
        }

        .breadcrumb-item,
        .breadcrumb-item.active {
            color: var(--ina-text-muted) !important;
        }

        .breadcrumb-item a {
            color: var(--ina-primary) !important;
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            text-decoration: underline;
        }

        /* Kartu konten */
        .main-card,
        .card {
            border: 1px solid var(--ina-border) !important;
            border-radius: var(--ina-radius) !important;
            box-shadow: var(--ina-shadow) !important;
        }

        /* Bar atas desktop: nama perpustakaan & jam di kanan */
        .app-topbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            box-sizing: border-box;
            height: var(--ina-header-height);
            padding: 0 36px;
            background: var(--ina-surface);
            border-bottom: 1px solid var(--ina-border);
        }

        /* Nama perpustakaan di kiri, jam di kanan */
        .app-topbar-info {
            display: flex;
            flex: 1;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            min-width: 0;
            font: var(--ina-caption);
            font-weight: 500;
            letter-spacing: 0;
            color: var(--ina-text-subtle);
        }

        .app-topbar-library,
        .app-topbar-clock {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
            white-space: nowrap;
        }

        .app-topbar-library {
            font: var(--ina-body-sm);
            font-weight: 600;
            color: var(--ina-text);
        }

        .app-topbar-library > span {
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 40vw;
        }

        .app-topbar-library svg,
        .app-topbar-clock svg {
            flex-shrink: 0;
            color: #2d659e;
        }

        .app-topbar-clock time {
            font-variant-numeric: tabular-nums;
        }

        .app-topbar-divider {
            display: none;
        }

        /* Bar atas versi mobile */
        .mobile-topbar {
            display: none;
            align-items: center;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 900;
            padding: 12px 16px;
            background: var(--ina-surface);
            border-bottom: 1px solid var(--ina-border);
        }

        .mobile-topbar strong {
            font: var(--ina-body);
            font-weight: 700;
            color: var(--ina-primary);
        }


        /* Header Content Area */
        .argon-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 20px 0 30px 0;
            color: white;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-left {
            flex: 1;
            min-width: 200px;
        }

        .breadcrumb-argon {
            font: var(--ina-caption);
            opacity: 0.9;
            margin-bottom: 8px;
            font-weight: 400;
        }

        .breadcrumb-argon a {
            color: white;
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.2s;
        }

        .breadcrumb-argon a:hover {
            opacity: 1;
        }

        .breadcrumb-argon span {
            margin: 0 8px;
            opacity: 0.6;
        }

        .page-title-argon {
            font: var(--ina-h4);
            font-weight: 700;
            margin: 0;
        }

        .page-subtitle-argon {
            font: var(--ina-caption);
            opacity: 0.9;
            margin-top: 5px;
            font-weight: 400;
        }

        /* Search and Actions Section */
        .header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-container-argon {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-input-argon {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 10px 15px 10px 40px;
            outline: none;
            width: 250px;
            color: white;
            font-size: 14px;
            transition: all 0.3s;
        }

        .search-input-argon::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-input-argon:focus {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            width: 300px;
        }

        .search-icon-argon {
            position: absolute;
            left: 15px;
            color: rgba(255, 255, 255, 0.8);
            pointer-events: none;
        }

        .btn-header-argon {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
             margin-right: 10px;
             margin-bottom: 5px;
            text-decoration: none;
        }

        .btn-header-argon:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            transform: translateY(-2px);
        }

        .btn-header-argon i {
            font-size: 16px;
        }

        /* Content Cards */
        .content-wrapper {
            flex: 1;
            background: transparent;
            position: relative;
            z-index: 1;
            padding: 32px 36px 0;
        }

        .card-argon {
            background: var(--ina-surface);
            border: 1px solid var(--ina-border);
            border-radius: var(--ina-radius);
            padding: 24px;
            box-shadow: var(--ina-shadow);
            margin-bottom: 24px;
        }

        .card-header-argon {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .card-title-argon {
            font-size: 18px;
            font-weight: 700;
            color: #344767;
            margin: 0;
        }

        /* Footer */
        .footer-argon {
            margin: 40px 36px 0;
            padding: 20px 0;
            text-align: center;
            color: var(--ina-text-muted);
            font-size: 13px;
            border-top: 1px solid var(--ina-border);
        }

        .footer-argon a {
            color: var(--ina-primary);
            text-decoration: none;
            font-weight: 600;
        }

        .footer-argon a:hover {
            color: var(--ina-primary-hover);
        }

        /* Tombol menu mobile */
        .mobile-menu-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            padding: 0;
            color: var(--ina-text-subtle);
            background: transparent;
            border: 1px solid var(--ina-border);
            border-radius: 8px;
            cursor: pointer;
        }

        .mobile-menu-toggle svg {
            width: 22px;
            height: 22px;
        }

        .mobile-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 999;
            background: rgba(15, 23, 42, .45);
        }

        .mobile-overlay.active {
            display: block;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--ina-surface);
            border: 1px solid var(--ina-border);
            border-radius: var(--ina-radius);
            padding: 24px;
            box-shadow: var(--ina-shadow);
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #fff;
        }

        .stat-icon.blue {
            background: var(--ina-primary);
        }

        .stat-icon.green {
            background: var(--ina-success);
        }

        .stat-icon.orange {
            background: var(--ina-warning);
        }

        .stat-icon.red {
            background: var(--ina-danger);
        }

        .stat-content {
            flex: 1;
        }

        .stat-label {
            font-size: 15px;
            color: var(--ina-text-subtle);
            margin-bottom: 6px;
            font-weight: 500;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--ina-text);
            line-height: 1.1;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .app-container,
            body.sidebar-collapsed .app-container {
                padding-left: 0;
            }

            .mobile-topbar {
                display: flex;
            }

            .app-topbar {
                height: 44px;
                padding: 0 16px;
                justify-content: flex-start;
            }

            .app-topbar-info {
                gap: 10px;
                font: var(--ina-caption-sm);
                font-weight: 500;
            }

            .app-topbar-library {
                font: var(--ina-caption);
                font-weight: 600;
            }

            .app-page-title .page-title-heading,
            .app-page-title .page-title-heading h1 {
                font: var(--ina-h5);
                font-weight: 700;
            }

            .content-wrapper {
                padding: 20px 16px 0;
            }

            .footer-argon {
                margin: 32px 16px 0;
            }

            .argon-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-actions {
                width: 100%;
            }

            .search-input-argon {
                width: 100%;
            }

            .search-input-argon:focus {
                width: 100%;
            }

            .page-title-argon {
                font: var(--ina-body);
                font-weight: 700;
            }
        }

        @media (max-width: 576px) {
            .stats-container {
                grid-template-columns: 1fr;
            }
        }

        <?php if ($isLightweightBackendPage): ?>
        /* Ikon Pe7 yang masih dipakai beberapa data menu lama. */
        @font-face {
            font-family: "Pe-icon-7-stroke";
            src: url("<?= base_url('assets/fonts/catalog/pe7-subset.woff2') ?>") format("woff2");
            font-display: swap;
        }
        .lightweight-backend-page [class^="pe-7s-"],
        .lightweight-backend-page [class*=" pe-7s-"] {
            font-family: "Pe-icon-7-stroke";
            font-style: normal;
            font-weight: normal;
        }
        .lightweight-backend-page .pe-7s-albums::before { content: "\e67d"; }
        .lightweight-backend-page .pe-7s-angle-left-circle::before { content: "\e687"; }
        .lightweight-backend-page .pe-7s-note::before { content: "\e66c"; }
        .lightweight-backend-page .pe-7s-server::before { content: "\e617"; }
        .lightweight-backend-page .pe-7s-users::before { content: "\e693"; }
        .lightweight-backend-page [class^="fe-"]::before,
        .lightweight-backend-page [class*=" fe-"]::before { content: "\25C6"; }
        .lightweight-backend-page .text-danger { color: #b42318 !important; }
        <?php endif; ?>
    </style>
    <?= $this->renderSection('style'); ?>
</head>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle submenu on click
        document.querySelectorAll('.submenu-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const menu = this.closest('.has-submenu');
                menu.classList.toggle('open');
                this.setAttribute('aria-expanded', menu.classList.contains('open') ? 'true' : 'false');
            });
        });

        // Auto-open parent menus and highlight them when a child is active
        document.querySelectorAll('.nav-menu-argon a.active').forEach(function(activeLink) {
            let parent = activeLink.closest('.has-submenu');
            while (parent) {
                parent.classList.add('open');
                const parentToggle = parent.querySelector(':scope > a.submenu-toggle');
                if (parentToggle) {
                    parentToggle.classList.add('active');
					parentToggle.setAttribute('aria-expanded', 'true');
                }
                const grandParent = parent.parentElement;
                parent = grandParent ? grandParent.closest('.has-submenu') : null;
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const body = document.body;
        const toggleBtn = document.getElementById('sidebarToggle');
        const readPref = function() {
            try { return localStorage.getItem('sidebar'); } catch (e) { return null; }
        };
        const savePref = function(value) {
            try { localStorage.setItem('sidebar', value); } catch (e) {}
        };
        const syncToggle = function() {
            if (!toggleBtn) return;
            const collapsed = body.classList.contains('sidebar-collapsed');
            toggleBtn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            toggleBtn.setAttribute('title', collapsed ? 'Lebarkan sidebar' : 'Ciutkan sidebar');
        };

        // Expand untuk desktop; Collapse otomatis di layar sempit (tablet/laptop kecil)
        // bila pengguna belum pernah memilih sendiri.
        const pref = readPref();
        if (pref === 'collapsed' || (pref === null && window.innerWidth > 992 && window.innerWidth < 1280)) {
            body.classList.add('sidebar-collapsed');
        }
        syncToggle();

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                body.classList.toggle('sidebar-collapsed');
                savePref(body.classList.contains('sidebar-collapsed') ? 'collapsed' : 'expanded');
                syncToggle();
            });
        }

        // Label menu tampil sebagai tooltip saat sidebar diciutkan; tandai halaman aktif
        document.querySelectorAll('.nav-menu-argon li a').forEach(function(link) {
            const label = link.querySelector('span');
            if (label && !link.getAttribute('title')) link.setAttribute('title', label.textContent.trim());
            if (link.classList.contains('active') && !link.classList.contains('submenu-toggle')) {
                link.setAttribute('aria-current', 'page');
            }
        });

        // Saat diciutkan, klik menu induk melebarkan sidebar agar submenu terlihat
        document.querySelectorAll('.submenu-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                if (body.classList.contains('sidebar-collapsed') && window.innerWidth > 992) {
                    body.classList.remove('sidebar-collapsed');
                    this.closest('.has-submenu').classList.add('open');
                    this.setAttribute('aria-expanded', 'true');
                    savePref('expanded');
                    syncToggle();
                }
            });
        });
    });

    // Jam real-time di bar atas (zona waktu Jakarta)
    (function() {
        var dateFmt = new Intl.DateTimeFormat('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', timeZone: 'Asia/Jakarta' });
        var timeFmt = new Intl.DateTimeFormat('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZone: 'Asia/Jakarta' });

        function tick() {
            var clock = document.getElementById('live-clock');
            if (!clock) return;
            var now = new Date();
            clock.textContent = dateFmt.format(now) + ' · ' + timeFmt.format(now).replace(/\./g, ':') + ' WIB';
            clock.setAttribute('datetime', now.toISOString());
        }

        document.addEventListener('DOMContentLoaded', function() {
            tick();
            setInterval(tick, 1000);
        });
    })();

    // Sidebar off-canvas di mobile
    function toggleSidebar(force) {
        const sidebar = document.getElementById('dashboard-sidebar');
        const overlay = document.querySelector('.mobile-overlay');
        const toggle = document.querySelector('.mobile-menu-toggle');
        if (!sidebar) return;

        const open = typeof force === 'boolean' ? force : !sidebar.classList.contains('open');
        sidebar.classList.toggle('open', open);
        if (overlay) overlay.classList.toggle('active', open);
        if (toggle) toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    document.addEventListener('click', function(event) {
        if (event.target.closest('.mobile-overlay')) toggleSidebar(false);
    });

    document.addEventListener('keydown', function(event) {
        const sidebar = document.getElementById('dashboard-sidebar');
        if (event.key === 'Escape' && sidebar && sidebar.classList.contains('open')) toggleSidebar(false);
    });
</script>


<body class="<?= $isDashboardPage ? 'dashboard-page lightweight-backend-page' : ($isCatalogListPage ? 'catalog-list-page lightweight-backend-page' : ($isExemplarListPage ? 'exemplar-list-page lightweight-backend-page' : ($isMemberListPage ? 'member-list-page lightweight-backend-page' : ''))) ?>">
    <div class="mobile-overlay" aria-hidden="true"></div>

    <div class="app-container">
        <!-- Sidebar -->
        <?= $this->include('App\Views\layout\partial\sidebar'); ?>

        <!-- Main Content -->
        <div class="app-main__outer">
            <!-- Bar atas (hanya mobile) -->
            <header class="mobile-topbar">
                <button class="mobile-menu-toggle" type="button" onclick="toggleSidebar()"
                    aria-label="Buka menu navigasi" aria-controls="dashboard-sidebar" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <path d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                </button>
                <strong>INLISLite</strong>
            </header>

            <!-- Bar atas desktop: nama perpustakaan & jam real-time -->
            <header class="app-topbar">
                <div class="app-topbar-info">
                    <span class="app-topbar-library" title="<?= esc($nama_perpustakaan, 'attr') ?>">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 21h18" />
                            <path d="M5 21V10l7-5 7 5v11" />
                            <path d="M9 21v-6h6v6" />
                        </svg>
                        <span><?= esc($nama_perpustakaan) ?></span>
                    </span>
                    <span class="app-topbar-divider" aria-hidden="true"></span>
                    <span class="app-topbar-clock">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 7v5l3 2" />
                        </svg>
                        <time id="live-clock" datetime="">Memuat waktu...</time>
                    </span>
                </div>
            </header>

            <!-- Content Area -->
            <main class="content-wrapper">
                <?= $this->renderSection('page'); ?>
            </main>

            <!-- Footer -->
            <footer class="footer-argon">
                <p>
                    &copy; <?= date('Y') ?> Lisensi oleh <a href="https://www.perpusnas.go.id">Perpustakaan Nasional RI</a>. Hak cipta dilindungi.
                   
                </p>
            </footer>
        </div>
    </div>

    <?php if ($isMemberListPage): ?>
        <script src="<?= base_url('assets/js/jquery-4.0.0.min.js') ?>"></script>
        <script src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>
        <script src="<?= base_url('assets/js/datatables.min.js') ?>"></script>
        <script src="<?= base_url('assets/vendors/form-components/toggle-switch.min.js') ?>"></script>
        <script src="<?= base_url('assets/vendors/magnific-popup/jquery.magnific-popup.min.js') ?>"></script>
        <script src="<?= base_url('assets/js/sweetalert2.all.min.js') ?>"></script>
    <?php elseif ($isLightweightBackendPage): ?>
        <script src="<?= base_url('assets/js/sweetalert2@8.js') ?>"></script>
    <?php elseif (!$isLightweightBackendPage): ?>
        <?= $this->include('App\Views\layout\partial\script'); ?>
        <?= $this->include('App\Views\layout\partial\script_custom'); ?>
    <?php endif; ?>

    <?php $swal_permission = session()->getFlashdata('swal_permission'); ?>
    <?php if ($isLightweightBackendPage && is_array($swal_permission)): ?>
    <script>
        Swal.fire({
            icon: '<?= esc((string)($swal_permission['icon'] ?? 'error')) ?>',
            title: '<?= esc((string)($swal_permission['title'] ?? 'Akses Ditolak')) ?>',
            text: '<?= esc((string)($swal_permission['text'] ?? 'Maaf anda tidak memiliki hak akses')) ?>',
            confirmButtonColor: '#d33',
            timer: 4000,
            timerProgressBar: true,
        });
    </script>
    <?php endif; ?>

    <?php if (!$isLightweightBackendPage): ?>
    <script>
        // Initialize tooltips
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();

            // Initialize DataTable if exists
            if ($('.datatable-argon').length) {
                $('.datatable-argon').DataTable({
                    "pagingType": "simple_numbers",
                    "language": {
                        "search": "_INPUT_",
                        "searchPlaceholder": "Cari data...",
                        "lengthMenu": "Tampilkan _MENU_ data per halaman",
                        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        "infoEmpty": "Tidak ada data",
                        "infoFiltered": "(disaring dari _MAX_ total data)",
                        "zeroRecords": "Tidak ada data yang cocok",
                        "paginate": {
                            "first": "Pertama",
                            "last": "Terakhir",
                            "next": "Selanjutnya",
                            "previous": "Sebelumnya"
                        }
                    },
                    "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                        '<"row"<"col-sm-12"tr>>' +
                        '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
                });
            }

            // Smooth scroll for anchor links
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 500);
                }
            });
        });
    </script>
    <?php endif; ?>
    <?= $this->renderSection('script'); ?>
</body>

</html>
