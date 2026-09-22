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
$isDashboardPage = !empty($is_dashboard_page);
$isCatalogListPage = !empty($is_catalog_list_page);
$isExemplarListPage = !empty($is_exemplar_list_page);
if ($isExemplarListPage) {
    helper('backend_logo');
    $sidebarLogoUrl = backend_logo_url((string) $logo);
}
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
    <link rel="icon" href="<?= esc($sidebarLogoUrl ?? (!empty($logo) ? base_url('uploads/branch/' . $logo) : base_url('assets/img/logo-inlislite-icon.png'))) ?>">
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

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            overflow-x: hidden;
        }

        /* Background Gradient Top */
        .argon-bg-gradient {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 350px;
            background: linear-gradient(135deg, #4054b2 0%, #243f7e 100%);
            z-index: 0;
        }

        .app-container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            padding-left: 290px;
            transition: padding-left 0.3s;
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
            font-size: 14px;
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
            font-size: 32px;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }

        .page-subtitle-argon {
            font-size: 14px;
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
            background: transparent;
            position: relative;
            z-index: 1;

            padding-left: 20px;
            padding-right: 30px;
            min-height: 100vh;
            transition: padding-left 0.3s ease;
        }

        .card-argon {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
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
            margin-top: 50px;
            padding: 25px 0;
            text-align: center;
            color: #526079;
            font-size: 14px;
        }

        .footer-argon a {
            color: #5e72e4;
            text-decoration: none;
            font-weight: 600;
        }

        .footer-argon a:hover {
            color: #4c5fd6;
        }

        /* Mobile Toggle Button */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 30px;
            left: 20px;
            z-index: 1001;
            background: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            color: #344767;
            font-size: 18px;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stat-icon.blue {
            background: linear-gradient(135deg, #5e72e4, #825ee4);
        }

        .stat-icon.green {
            background: linear-gradient(135deg, #2dce89, #2dcecc);
        }

        .stat-icon.orange {
            background: linear-gradient(135deg, #fb6340, #fbb140);
        }

        .stat-icon.red {
            background: linear-gradient(135deg, #f5365c, #f56036);
        }

        .stat-content {
            flex: 1;
        }

        .stat-label {
            font-size: 13px;
            color: #8392ab;
            margin-bottom: 5px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #344767;
            line-height: 1;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .app-container {
                padding-left: 0;
            }

            .mobile-menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .app-main__outer {
                padding: 80px 20px 20px 20px;
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
                font-size: 18px;
            }
        }

        @media (max-width: 576px) {
            .stats-container {
                grid-template-columns: 1fr;
            }
        }

        .app-page-title {
            color: #fff;
        }

        .breadcrumb-item a {
            color: #fff !important;
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
        .lightweight-backend-page .mobile-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 999;
            background: rgba(15, 23, 42, .55);
        }
        @media (max-width: 992px) {
            .lightweight-backend-page .sidebar-argon {
                transform: translateX(-120%);
                transition: transform .25s ease;
            }
            .lightweight-backend-page .sidebar-argon.open { transform: translateX(0); }
            .lightweight-backend-page .mobile-overlay.active { display: block; }
        }
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
        const toggleBtn = document.getElementById('sidebarToggle');
		if (!toggleBtn) return;

        // Restore state
        if (localStorage.getItem('sidebar') === 'collapsed') {
            document.body.classList.add('sidebar-collapsed');
			toggleBtn.setAttribute('aria-expanded', 'false');
        }

        toggleBtn.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
			toggleBtn.setAttribute('aria-expanded', document.body.classList.contains('sidebar-collapsed') ? 'false' : 'true');

            // Save state
            localStorage.setItem(
                'sidebar',
                document.body.classList.contains('sidebar-collapsed') ?
                'collapsed' :
                'expanded'
            );
        });
    });
</script>


<body class="<?= $isDashboardPage ? 'dashboard-page lightweight-backend-page' : ($isCatalogListPage ? 'catalog-list-page lightweight-backend-page' : ($isExemplarListPage ? 'exemplar-list-page lightweight-backend-page' : ($isMemberListPage ? 'member-list-page lightweight-backend-page' : ''))) ?>">
    <!-- Background Gradient -->
    <div class="argon-bg-gradient" aria-hidden="true"></div>

    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" type="button" onclick="toggleSidebar()"
        aria-label="Buka menu navigasi" aria-controls="dashboard-sidebar" aria-expanded="false">
        <i class="fas fa-bars" aria-hidden="true"></i>
    </button>
    <div class="mobile-overlay" aria-hidden="true"></div>

    <div class="app-container">
        <!-- Sidebar -->
        <?= $this->include('App\Views\layout\partial\sidebar'); ?>

        <!-- Main Content -->
        <div class="app-main__outer">
            <!-- Header -->
            <header class="argon-header" style="padding-left: 20px;">
                <div class="header-left">


                    <p class="page-title-argon"><?= esc($nama_perpustakaan) ?></p>
                </div>

                <div class="header-actions" style="padding-right: 50px;">
                    <div id="clock-wrapper" style="display: flex; align-items: center; color: #fff; margin-right: 20px; font-size: 14px;">
                        <i class="fas fa-clock" style="margin-right: 10px;" aria-hidden="true"></i>
                        <span id="live-clock" role="timer" style="font-weight: 600; white-space: nowrap;">
                            Memuat waktu...
                        </span>
                    </div>

                    <a href="<?= base_url('user/profile') ?>" class="btn-header-argon">
                        <i class="fas fa-user" aria-hidden="true"></i>
                        <span>Profile</span>
                    </a>
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
    <?php elseif (!$isLightweightBackendPage): ?>
        <?= $this->include('App\Views\layout\partial\script'); ?>
        <?= $this->include('App\Views\layout\partial\script_custom'); ?>
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

        // Toggle sidebar on mobile
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar-argon');
            const overlay = document.querySelector('.mobile-overlay');

            if (sidebar && overlay) {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('active');
            }
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 992) {
                const sidebar = document.querySelector('.sidebar-argon');
                const toggle = document.querySelector('.mobile-menu-toggle');

                if (sidebar && toggle &&
                    !sidebar.contains(e.target) &&
                    !toggle.contains(e.target) &&
                    sidebar.classList.contains('open')) {
                    toggleSidebar();
                }
            }
        });
    </script>
    <?php endif; ?>
    <?php if ($isLightweightBackendPage): ?>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('dashboard-sidebar');
            const overlay = document.querySelector('.mobile-overlay');
            const toggle = document.querySelector('.mobile-menu-toggle');
            if (!sidebar) return;

            sidebar.classList.toggle('open');
            if (overlay) overlay.classList.toggle('active');
            if (toggle) toggle.setAttribute('aria-expanded', sidebar.classList.contains('open') ? 'true' : 'false');
        }

        document.addEventListener('click', function(event) {
            if (window.innerWidth > 992) return;
            const sidebar = document.getElementById('dashboard-sidebar');
            const toggle = document.querySelector('.mobile-menu-toggle');
            if (sidebar && toggle && sidebar.classList.contains('open') &&
                !sidebar.contains(event.target) && !toggle.contains(event.target)) {
                toggleSidebar();
            }
        });
    </script>
    <?php endif; ?>
    <script>
        function updateClock() {
            const now = new Date();

            // Pengaturan format untuk zona waktu Jakarta
            const dateOptions = {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                timeZone: 'Asia/Jakarta'
            };

            const timeOptions = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
                timeZone: 'Asia/Jakarta'
            };

            const dateString = now.toLocaleDateString('id-ID', dateOptions);
            const timeString = now.toLocaleTimeString('id-ID', timeOptions);

            // Update elemen teks
            const clock = document.getElementById('live-clock');
            if (clock) clock.textContent = `${dateString} | ${timeString.replace(/\./g, ':')} WIB`;
        }

        // Update setiap 1 detik
        setInterval(updateClock, 1000);
        updateClock();
    </script>
    <?= $this->renderSection('script'); ?>
</body>

</html>
