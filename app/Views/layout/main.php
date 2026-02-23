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
$logo = $db->table('settingparameters')->where('Name', 'Logo')->get()->getRow()->Value;
$nama_perpustakaan = $db->table('settingparameters')->where('Name', 'NamaPerpustakaan')->get()->getRow()->Value ?: "Perpustakaan";
$npp_perpustakaan=$db->table('settingparameters')->where('Name', 'NPPPerpustakaan')->get()->getRow()->Value?:"NPP Perpustakaan Mitra";

// Get breadcrumb info
$segment1 = $request->uri->getSegment(1) ?: 'dashboard';
$segment2 = $request->uri->getSegment(2) ?: '';
$page_title = ucfirst($segment2 ?: $segment1);
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $title ?? $page_title . ' - ' . $nama_perpustakaan; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />

    <link rel="stylesheet" href="<?= base_url('themes/uigniter'); ?>/css/base.css">
    <?= $this->include('App\Views\layout\partial\style'); ?>
    <?= $this->include('App\Views\layout\partial\style_custom'); ?>

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
            background: linear-gradient(135deg, #5e72e4 0%, #2F539B 100%);
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
            color: #8392ab;
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
    </style>
    <?= $this->renderSection('style'); ?>
</head>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.submenu-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                this.closest('.has-submenu').classList.toggle('open');
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('sidebarToggle');

        // Restore state
        if (localStorage.getItem('sidebar') === 'collapsed') {
            document.body.classList.add('sidebar-collapsed');
        }

        toggleBtn.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');

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


<body>
    <!-- Background Gradient -->
    <div class="argon-bg-gradient"></div>

    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="app-container">
        <!-- Sidebar -->
        <?= $this->include('App\Views\layout\partial\sidebar'); ?>

        <!-- Main Content -->
        <div class="app-main__outer">
            <!-- Header -->
            <header class="argon-header" style="padding-left: 20px;">
                <div class="header-left">


                    <h7 class="page-title-argon"><?= $nama_perpustakaan ?></h7>
                </div>

                <div class="header-actions" style="padding-right: 50px;">
                    <div id="clock-wrapper" style="display: flex; align-items: center; color: #fff; margin-right: 20px; font-size: 14px;">
                        <i class="fas fa-clock" style="margin-right: 10px;"></i>
                        <span id="live-clock" style="font-weight: 600; white-space: nowrap;">
                            Memuat waktu...
                        </span>
                    </div>

                    <a href="<?= base_url('user/profile') ?>" class="btn-header-argon">
                        <i class="fas fa-user"></i>
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
                    © <?= date('Y') ?> Licency By <a href="https://www.perpusnas.go.id">Perpustakaan Nasional RI</a>. All rights reserved.
                   
                </p>
            </footer>
        </div>
    </div>

    <?= $this->include('App\Views\layout\partial\script'); ?>
    <?= $this->include('App\Views\layout\partial\script_custom'); ?>

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
            document.getElementById('live-clock').innerHTML = `${dateString} | ${timeString.replace(/\./g, ':')} WIB`;
        }

        // Update setiap 1 detik
        setInterval(updateClock, 1000);
        updateClock();
    </script>
    <?= $this->renderSection('script'); ?>
</body>

</html>