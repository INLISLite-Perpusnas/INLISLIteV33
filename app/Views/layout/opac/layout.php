<?php
$db = db_connect();
$settingNames = ['NamaPerpustakaan', 'NamaLokasiPerpustakaan', 'TentangKami', 'Logo', 'Phone', 'EmailPerpustakaan', 'JamOperasional', 'UsulanKoleksiLKD'];
$settingRows = $db->table('settingparameters')
    ->select('Name, Value')
    ->whereIn('Name', $settingNames)
    ->get()
    ->getResultArray();
$settings = array_column($settingRows, 'Value', 'Name');

$nama_perpustakaan = $settings['NamaPerpustakaan'] ?? 'Perpustakaan Mitra';
$alamat = $settings['NamaLokasiPerpustakaan'] ?? 'Jl. Perpustakaan Mitra';
$tentang_kami = $settings['TentangKami'] ?? 'Perpustakaan Mitra';
$logo = $settings['Logo'] ?? '';
$phone = $settings['Phone'] ?? '';
$email = $settings['EmailPerpustakaan'] ?? '';
// Nullsafe (?->) karena setting 'JamOperasional' belum tentu ada barisnya di
// settingparameters — beda dari setting lain di atas yang sudah pasti ada.
$jam_operasional = $settings['JamOperasional'] ?? 'Jam Operasional Perpustakaan Mitra';
$isHomePage = !empty($is_home_page);
$isOpacIndex = !empty($is_opac_index);
$isGuestBookLocation = !empty($is_guestbook_location);
$isOptimizedPublicPage = $isHomePage || $isOpacIndex || $isGuestBookLocation;
$bootstrapFile = $isHomePage
    ? 'home-bootstrap.min.css'
    : ($isOpacIndex
        ? 'opac-bootstrap.min.css'
        : ($isGuestBookLocation ? 'guestbook-bootstrap.min.css' : 'bootstrap.min.css'));
// Keranjang pesan anggota: tautan hanya tampil jika ada pesanan aktif (seperti INLISLite 3)
$bookingCount = 0;
$sessionUsername = (string) (session()->get('username') ?? '');
if ($sessionUsername !== '' && session()->get('logged_in')
    && (string) get_setting_parameter('IsBookingActivated') === '1') {
    $bookingCount = $db->table('collections')
        ->where('BookingMemberID', $sessionUsername)
        ->where('BookingExpiredDate >', date('Y-m-d H:i:s'))
        ->countAllResults();
}
$currentNavPath = trim(uri_string(), '/');
$pageTitle = isset($title) ? $title : 'OPAC - ' . $nama_perpustakaan;
$pageDescription = isset($meta_description)
    ? $meta_description
    : 'Temukan koleksi, layanan, berita, dan informasi terbaru dari ' . $nama_perpustakaan . '.';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle) ?></title>
    <meta name="description" content="<?= esc($pageDescription) ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= current_url() ?>">

    <link rel="icon" href="<?= !empty($logo) ? base_url('uploads/branch/' . $logo) : base_url('assets/img/logo-inlislite-icon.webp') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/' . $bootstrapFile) ?>">
    <?php if ($isOptimizedPublicPage): ?>
        <link rel="preload" href="<?= base_url('assets/fonts/home/fa-solid-subset.woff2') ?>" as="font" type="font/woff2" crossorigin>
        <link rel="stylesheet" href="<?= base_url('assets/css/home-icons.css') ?>">
    <?php elseif (!$isOptimizedPublicPage): ?>
        <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php endif; ?>

    <style>
        :root {
            --brand-50: #eff6ff;
            --brand-100: #dbeafe;
            --brand-500: #3b82f6;
            --brand-600: #1b3878;
            --brand-700: #1d4ed8;
            --brand-900: #1e3a8a;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-500: #64748b;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
        }

        body {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
            background-color: var(--slate-50);
            color: var(--slate-800);
            -webkit-font-smoothing: antialiased;
        }

        /* ─── Global Utility ─── */
        .text-brand {
            color: var(--brand-600);
        }

        .bg-brand {
            background-color: var(--brand-600) !important;
            color: white;
        }

        .btn-brand {
            background-color: var(--brand-600);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-brand:hover {
            background-color: var(--brand-700);
            color: white;
            transform: translateY(-2px);
        }

        .rounded-xl {
            border-radius: 1rem !important;
        }

        .rounded-2xl {
            border-radius: 1.5rem !important;
        }

        .shadow-soft {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, .05),
                0 8px 10px -6px rgba(0, 0, 0, .01);
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ─── Navbar ─── */
        .site-header .navbar-custom {
            background: #2d659e;
            padding: 12px 0;
            box-shadow: 0 2px 12px rgba(15, 23, 42, .14);
        }

        .site-header .navbar-brand { max-width: 340px; min-width: 0; flex-shrink: 1; }
        .site-header .navbar-brand img { width: auto; height: 52px; max-width: 52px; border-radius: 10px; object-fit: cover; flex-shrink: 0; }
        .site-header .brand-text { display: block; min-width: 0; }
        .site-header .brand-name {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            /* .navbar-brand Bootstrap = nowrap; nama panjang harus bisa turun ke baris kedua */
            white-space: normal;
            overflow-wrap: anywhere;
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.3;
        }
        .site-header .brand-subtitle {
            display: block;
            color: #e2e8f0;
            font-size: .65rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .site-header .navbar-nav { gap: .1rem; }
        .site-header .navbar-nav .nav-link {
            display: block;
            margin: 0;
            padding: .65rem .75rem;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: #fff;
            font-size: .95rem;
            text-decoration: none;
            transition: background-color .2s ease;
        }
        .site-header .navbar-nav .nav-link:hover,
        .site-header .navbar-nav .nav-link:focus-visible,
        .site-header .navbar-nav .nav-link.active {
            background: rgba(255, 255, 255, .16);
            color: #fff;
        }
        .site-header .navbar-nav .nav-link.active { box-shadow: inset 0 -2px #fff; }
        .site-header .dropdown-menu {
            min-width: 220px;
            padding: .4rem;
            border: 1px solid #dbe4f0;
            border-radius: 8px;
            box-shadow: 0 12px 24px rgba(15, 23, 42, .13);
        }
        .site-header .dropdown-item { padding: .65rem .75rem; border-radius: 5px; color: #1e293b; white-space: normal; }
        .site-header .dropdown-item:hover,
        .site-header .dropdown-item:focus-visible { background: #eff6ff; color: #1b3878; }
        .site-header .dropdown-item.active { background: #1b3878; color: #fff; }
        .site-header .navbar-toggler { border: 1px solid rgba(255, 255, 255, .8); color: #fff; padding: .5rem .7rem; }
        .site-header .nav-actions .btn { border-radius: 8px !important; box-shadow: none !important; padding: .55rem .8rem !important; }
        .site-header .nav-actions .btn-light { color: #1b3878 !important; }
        .site-header .navbar-brand:focus-visible,
        .site-header .navbar-toggler:focus-visible,
        .site-header .nav-link:focus-visible,
        .site-header .dropdown-item:focus-visible,
        .site-header .nav-actions .btn:focus-visible,
        .skip-link:focus-visible { outline: 3px solid #ffcd5b; outline-offset: 3px; }
        .skip-link {
            position: fixed;
            top: -100px;
            left: 12px;
            z-index: 1100;
            padding: .65rem 1rem;
            background: #fff;
            color: #1b3878;
        }
        .skip-link:focus { top: 12px; }
        #main-content { scroll-margin-top: 90px; }

        @media (min-width: 1400px) {
            .site-header .navbar-toggler { display: none; }
            .site-header .navbar-collapse { display: flex !important; flex-basis: auto; align-items: center; }
            .site-header .navbar-nav { flex-direction: row; margin-left: auto; margin-right: auto; }
            .site-header .navbar-nav .dropdown-menu { position: absolute; }
            .site-header .nav-actions { margin-top: 0 !important; }
        }
        @media (max-width: 1399.98px) {
            .site-header .navbar-brand { max-width: calc(100% - 68px); }
            .site-header .navbar-collapse {
                max-height: calc(100dvh - 85px);
                overflow-y: auto;
                padding: .75rem 0 1rem;
            }
            .site-header .navbar-nav { gap: 0; }
            .site-header .navbar-nav .nav-link { padding: .75rem; }
            .site-header .dropdown-menu { margin: 0 .75rem .5rem; box-shadow: none; }
            .site-header .nav-actions { flex-wrap: wrap; padding: 0 .75rem; }
        }
        .footer-custom {
            background-color: var(--slate-900);
            color: white;
            padding: 60px 0 30px;
            margin-top: 80px;
            border-top: 1px solid var(--slate-800);
        }

        .footer-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: white;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .footer-links a:hover {
            color: var(--brand-500);
            padding-left: 5px;
        }

        .footer-contact li {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .footer-contact i {
            color: var(--brand-500);
            margin-top: 4px;
        }

        .footer-custom .text-secondary {
            color: #cbd5e1 !important;
        }

        /* ─── Pagination Custom ─── */
        .pagination .page-link {
            color: #1b3878;
            border: 1.5px solid #dbeafe;
            background-color: #f8fafc;
            font-size: 0.9rem;
            min-width: 40px;
            text-align: center;
            transition: all 0.2s ease;
        }

        .pagination .page-link:hover {
            background-color: #1b3878;
            border-color: #1b3878;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(27, 56, 120, 0.25);
        }

        .pagination .page-item.active .page-link {
            background-color: #1b3878;
            border-color: #1b3878;
            color: white;
            box-shadow: 0 4px 12px rgba(27, 56, 120, 0.35);
        }

        .pagination .page-item.disabled .page-link {
            color: #cbd5e1;
            background-color: #f8fafc;
            border-color: #e2e8f0;
        }

        /* ─── SweetAlert2 Fix ─── */
        .swal2-icon.swal2-warning {
            border-color: #f8bb86 !important;
            color: #f8bb86 !important;
        }

        .swal2-styled.swal2-confirm {
            background-color: #3085d6 !important;
            color: #fff !important;
            border: none !important;
        }

        .swal2-styled.swal2-cancel {
            background-color: #d33 !important;
            color: #fff !important;
            border: none !important;
        }
    </style>

    <?= $this->renderSection('style') ?>
    <?php if ($currentNavPath === 'opac' || str_starts_with($currentNavPath, 'opac/')): ?>
    <style>
        :root {
            --opac-button-blue: #2d659e;
            --opac-button-hover: #23517f;
        }

        #main-content .btn-primary,
        #main-content .btn-brand,
        #main-content .btn-primary-custom,
        #main-content .btn-op-primary,
        #modalBooking .btn-primary {
            background: var(--opac-button-blue);
            border-color: var(--opac-button-blue);
            color: #fff;
        }

        #main-content .btn-primary:hover,
        #main-content .btn-brand:hover,
        #main-content .btn-primary-custom:hover,
        #main-content .btn-op-primary:hover,
        #modalBooking .btn-primary:hover {
            background: var(--opac-button-hover);
            border-color: var(--opac-button-hover);
            color: #fff;
            filter: none;
        }

        #main-content .btn-outline-primary {
            border-color: var(--opac-button-blue);
            color: var(--opac-button-blue);
        }

        #main-content .btn-outline-primary:hover,
        #main-content .btn-outline-primary:focus-visible {
            background: var(--opac-button-blue);
            border-color: var(--opac-button-blue);
            color: #fff;
        }
    </style>
    <?php endif; ?>
</head>

<body>

    <!-- ─── Navbar ─── -->
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>
    <header class="site-header">
    <nav class="navbar navbar-expand-xxl fixed-top navbar-custom" aria-label="Navigasi utama">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= base_url('home') ?>" title="<?= esc($nama_perpustakaan, 'attr') ?>">
                <img src="<?= esc(!empty($logo) ? base_url('uploads/branch/' . $logo) : base_url('assets/img/default-perpus.png'), 'attr') ?>"
                     alt="Logo <?= esc($nama_perpustakaan, 'attr') ?>" class="bg-white p-1" width="52" height="52" fetchpriority="high">
                <span class="brand-text">
                    <span class="brand-name"><?= esc($nama_perpustakaan) ?></span>
                    <span class="brand-subtitle">INLISLite 3.3</span>
                </span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Menu navigasi">
                <i class="fa-solid fa-bars" aria-hidden="true"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-xxl-auto">
                    <li class="nav-item">
                        <a class="nav-link<?= in_array($currentNavPath, ['', 'home', 'home/index'], true) ? ' active' : '' ?>"
                           href="<?= base_url('home') ?>"<?= in_array($currentNavPath, ['', 'home', 'home/index'], true) ? ' aria-current="page"' : '' ?>>Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= in_array($currentNavPath, ['opac', 'opac/index'], true) ? ' active' : '' ?>"
                           href="<?= base_url('opac') ?>"<?= in_array($currentNavPath, ['opac', 'opac/index'], true) ? ' aria-current="page"' : '' ?>>OPAC</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= str_starts_with($currentNavPath, 'profil') ? ' active' : '' ?>"
                           href="<?= base_url('profil') ?>"<?= str_starts_with($currentNavPath, 'profil') ? ' aria-current="page"' : '' ?>>Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= str_starts_with($currentNavPath, 'opac/browse') ? ' active' : '' ?>"
                           href="<?= base_url('opac/browse') ?>"<?= str_starts_with($currentNavPath, 'opac/browse') ? ' aria-current="page"' : '' ?>>Browse</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= str_starts_with($currentNavPath, 'artikel') ? ' active' : '' ?>"
                           href="<?= base_url('artikel') ?>"<?= str_starts_with($currentNavPath, 'artikel') ? ' aria-current="page"' : '' ?>>Artikel</a>
                    </li>
                    <?php if (setting_enabled('is_perpustakaan_keliling')): ?>
                        <li class="nav-item">
                            <a class="nav-link<?= str_starts_with($currentNavPath, 'perpustakaan-keliling') ? ' active' : '' ?>"
                               href="<?= site_url('perpustakaan-keliling') ?>"<?= str_starts_with($currentNavPath, 'perpustakaan-keliling') ? ' aria-current="page"' : '' ?>>Perpustakaan Keliling</a>
                        </li>
                    <?php endif; ?>
                    <?php $isStatisticsActive = str_starts_with($currentNavPath, 'opac/statistics'); ?>
                    <li class="nav-item dropdown">
                        <button class="nav-link dropdown-toggle<?= $isStatisticsActive ? ' active' : '' ?>" type="button"
                                id="navbarDropdownStatistik" data-bs-toggle="dropdown" aria-expanded="false">Statistik</button>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownStatistik">
                            <li><a class="dropdown-item<?= $currentNavPath === 'opac/statistics' ? ' active' : '' ?>" href="<?= base_url('opac/statistics') ?>"<?= $currentNavPath === 'opac/statistics' ? ' aria-current="page"' : '' ?>>Statistik Katalog</a></li>
                            <li><a class="dropdown-item<?= $currentNavPath === 'opac/statistics_anggota' ? ' active' : '' ?>" href="<?= base_url('opac/statistics_anggota') ?>"<?= $currentNavPath === 'opac/statistics_anggota' ? ' aria-current="page"' : '' ?>>Statistik Anggota</a></li>
                            <li><a class="dropdown-item<?= $currentNavPath === 'opac/statistics_kunjungan' ? ' active' : '' ?>" href="<?= base_url('opac/statistics_kunjungan') ?>"<?= $currentNavPath === 'opac/statistics_kunjungan' ? ' aria-current="page"' : '' ?>>Statistik Kunjungan</a></li>
                            <li><a class="dropdown-item<?= $currentNavPath === 'opac/statistics_sirkulasi' ? ' active' : '' ?>" href="<?= base_url('opac/statistics_sirkulasi') ?>"<?= $currentNavPath === 'opac/statistics_sirkulasi' ? ' aria-current="page"' : '' ?>>Statistik Sirkulasi</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="nav-actions d-flex align-items-center gap-3 mt-3 mt-xxl-0">
                    <?php if ($bookingCount > 0): ?>
                        <a href="javascript:void(0)" onclick="tampilBooking()"
                            class="btn btn-warning rounded-pill px-3 py-2 fw-bold shadow-sm"
                            style="font-size: 0.9rem; white-space: nowrap;">
                            <i class="fa-solid fa-bookmark me-2"></i>Keranjang (<?= $bookingCount ?>)
                        </a>
                    <?php endif; ?>
                    <?php if (session()->get('logged_in')): ?>
                        <a href="<?= base_url('dashboard') ?>"
                            class="btn btn-light text-primary rounded-pill px-4 py-2 fw-bold shadow-sm"
                            style="font-size: 0.9rem; white-space: nowrap;">
                            <i class="fa-solid fa-gauge me-2"></i>Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>"
                            class="btn btn-light text-primary rounded-pill px-4 py-2 fw-bold shadow-sm"
                            style="font-size: 0.9rem; white-space: nowrap;">
                            <i class="fa-solid fa-right-to-bracket me-2"></i>Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    </header>

    <!-- ─── Main Content ─── -->
    <main id="main-content" style="min-height: 80vh;">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- ─── Footer ─── -->
    <footer class="footer-custom">
        <div class="container">
            <div class="row mb-5">

                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img src="<?=  base_url('assets/img/Logo-Inlislite.webp') ?>"
                            alt="Logo INLISLite" class="rounded bg-white p-1" style="width:150px;height:70px;object-fit:cover;" width="150" height="70" loading="lazy">
                        <p class="m-0 fs-4 fw-bold"><?= esc($nama_perpustakaan) ?></p>
                    </div>
                    <p class="text-secondary" style="font-size:0.9rem;line-height:1.8;">
                        <?= esc($tentang_kami) ?>
                    </p>
                </div>

                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0 offset-lg-1">
                    <h2 class="footer-title">Layanan</h2>
                    <ul class="footer-links">
                        <?php if (setting_enabled('is_keanggotaan_online')): ?><li><a href="<?= base_url('home/pendaftaran-online') ?>">Keanggotaan Online</a></li><?php endif; ?>
                        <?php if (setting_enabled('is_peminjaman_mandiri', true)): ?><li><a href="<?= base_url('peminjaman-mandiri') ?>">Peminjaman Mandiri</a></li><?php endif; ?>
                        <?php if (setting_enabled('is_pengembalian_mandiri', true)): ?><li><a href="<?= base_url('pengembalian-mandiri') ?>">Pengembalian Mandiri</a></li><?php endif; ?>
                        <li><a href="<?= base_url('opac') ?>">OPAC</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h2 class="footer-title">Kontak & Lokasi</h2>
                    <ul class="footer-links footer-contact">
                        <li><i class="fa-solid fa-location-dot"></i> <span><?= esc($alamat) ?></span></li>
                        <li><i class="fa-solid fa-phone"></i> <span><?= esc($phone)  ?></span></li>
                        <li><i class="fa-solid fa-envelope"></i> <span><?= esc($email)  ?></span></li>
                        <li><i class="fa-solid fa-clock"></i> <span><?= nl2br(esc($jam_operasional)) ?></span></li>
                    </ul>
                </div>

            </div>
            <div class="border-top border-secondary pt-4 mt-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <p class="text-secondary mb-0" style="font-size:0.8rem;">
                    &copy; <?= date('Y') ?> <?= esc($nama_perpustakaan) ?>. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <?php if (!$isOptimizedPublicPage): ?>
        <script src="<?= base_url('assets/js'); ?>/jquery-4.0.0.min.js"></script>
        <script src="<?= base_url('assets/js'); ?>/sweetalert2@8.js"></script>
    <?php endif; ?>
    <script src="<?= base_url('assets/js'); ?>/bootstrap.min.js"></script>

    <?php if ($bookingCount > 0): ?>
        <!-- Keranjang Pesan (Booking Detail) -->
        <div class="modal fade" id="modalBooking" tabindex="-1" aria-labelledby="modalBookingLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalBookingLabel"><i class="fa-solid fa-bookmark me-2"></i>Keranjang Pesan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body" id="BookingShow"></div>
                    <div class="modal-footer">
                        <a href="<?= base_url('opac/booking/cetak') ?>" target="_blank" rel="noopener" class="btn btn-primary">
                            <i class="fa-solid fa-print me-1"></i>Cetak
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function tampilBooking() {
                var body = document.getElementById('BookingShow');
                body.innerHTML = '<p class="text-muted mb-0">Memuat...</p>';
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalBooking')).show();
                fetch(<?= json_encode(base_url('opac/booking')) ?>, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (res) { return res.text(); })
                    .then(function (html) { body.innerHTML = html; })
                    .catch(function () { body.innerHTML = '<p class="text-danger mb-0">Gagal memuat keranjang pesan.</p>'; });
            }

            (function () {
                var csrfHash = <?= json_encode(csrf_hash()) ?>;
                document.addEventListener('click', function (e) {
                    var btn = e.target.closest('[data-booking-cancel]');
                    if (!btn || !confirm('Hapus koleksi ini dari keranjang pesan?')) return;
                    btn.disabled = true;
                    fetch(<?= json_encode(base_url('opac/booking/cancel')) ?> + '/' + encodeURIComponent(btn.getAttribute('data-booking-cancel')), {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', <?= json_encode(config('Security')->headerName) ?>: csrfHash },
                    })
                        .then(function (res) { return res.json(); })
                        .then(function (data) {
                            if (data.csrf) csrfHash = data.csrf;
                            alert(data.message);
                            if (!data.error) window.location.reload();
                            else btn.disabled = false;
                        })
                        .catch(function () {
                            btn.disabled = false;
                            alert('Terjadi kesalahan. Muat ulang halaman lalu coba lagi.');
                        });
                });
            })();
        </script>
    <?php endif; ?>

    <?= $this->renderSection('script') ?>
</body>

</html>
