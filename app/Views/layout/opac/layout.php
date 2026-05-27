<?php
$db = db_connect();
$nama_perpustakaan = $db->table('settingparameters')->where('Name', 'NamaPerpustakaan')->get()->getRow()->Value ?: "Perpustakaan Mitra";
$alamat = $db->table('settingparameters')->where('Name', 'NamaLokasiPerpustakaan')->get()->getRow()->Value ?: "Jl.Perpustakaan Mitra";
$tentang_kami = $db->table('settingparameters')->where('Name', 'TentangKami')->get()->getRow()->Value ?: "Perpustakaan Mitra";
$logo = $db->table('settingparameters')->where('Name', 'Logo')->get()->getRow()->Value;
$phone = $db->table('settingparameters')->where('Name', 'Phone')->get()->getRow()->Value;
$email = $db->table('settingparameters')->where('Name', 'EmailPerpustakaan')->get()->getRow()->Value;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'OPAC - ' . $nama_perpustakaan; ?></title>

    <link rel="icon" href="<?= !empty($logo) ? base_url('uploads/branch/' . $logo) : base_url('assets/img/logo-inlislite-icon.webp') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css') ?>/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
            font-family: 'Plus Jakarta Sans', sans-serif;
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
        .navbar-custom {
            background-color: var(--brand-600);
            border-bottom: none;
            padding: 12px 0;
            transition: all 0.3s;
        }

        /* ✅ Brand area */
        .navbar-brand {
            max-width: 320px;
            flex-shrink: 1;
            min-width: 0;
            /* wajib agar clamp bekerja di flexbox */
        }

        .navbar-brand img {
            width: auto;
            /* ✅ lebar menyesuaikan */
            height: 52px;
            /* ✅ sesuaikan dengan tinggi 2 baris h1 + p */
            max-width: 52px;
            border-radius: 10px;
            object-fit: cover;
            flex-shrink: 0;
        }

        /* ✅ Wrapper teks brand */
        .navbar-brand .brand-text {
            min-width: 0;
        }

        /* ✅ Nama perpustakaan: maksimal 2 baris, tidak dipotong dengan ellipsis */
        .navbar-brand h1 {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
            color: #ffffff;
            white-space: normal;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .navbar-brand p {
            font-size: 0.65rem;
            margin: 0;
            font-weight: 600;
            text-transform: uppercase;
            color: #e2e8f0;
            letter-spacing: 1px;
            white-space: nowrap;
        }

        /* ✅ Nav link */
        /* ✅ Nav link — lebih besar */
        .nav-link {
            color: #f8fafc;
            font-weight: 500;
            font-size: 1rem;
            /* ✅ naik dari 0.875rem */
            margin: 0 6px;
            /* ✅ sedikit lebih lega */
            transition: 0.3s;
            white-space: nowrap;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #93c5fd;
        }

        /* ✅ Responsif */
        @media (max-width: 1200px) {
            .navbar-brand {
                max-width: 260px;
            }

            .navbar-brand h1 {
                font-size: 0.875rem;
            }

            .nav-link {
                font-size: 0.95rem;
                margin: 0 4px;
            }
        }

        @media (max-width: 992px) {
            .navbar-brand {
                max-width: 200px;
            }

            .navbar-brand h1 {
                font-size: 0.8rem;
            }

            .nav-link {
                font-size: 0.9rem;
                margin: 0 2px;
            }
        }

        /* ─── Footer ─── */
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
        .swal2-icon {
            display: flex !important;
        }

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
</head>

<body>

    <!-- ─── Navbar ─── -->
    <nav class="navbar navbar-expand-lg fixed-top navbar-custom shadow-sm">
        <div class="container">

            <!-- ✅ Brand: gap-2 lebih rapat, title tooltip untuk nama panjang -->
            <a class="navbar-brand d-flex align-items-center gap-2"
                href="<?= base_url('home') ?>"
                title="<?= esc($nama_perpustakaan) ?>">
                <img src="<?= !empty($logo) ? base_url('uploads/branch/' . $logo) : base_url('assets/img/default-perpus.png') ?>"
                    alt="Logo" class="bg-white p-1 shadow-sm">
                <div class="brand-text">
                    <h1><?= esc($nama_perpustakaan) ?></h1>
                    <p>INLISLIte 3.3</p>
                </div>
            </a>

            <button class="navbar-toggler border-0" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars navbar-toggler-icon-custom"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('home') ?>">
                            <i class="fa-solid fa-home me-1"></i>Beranda
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('opac') ?>">
                            <i class="fa-solid fa-magnifying-glass me-1"></i>OPAC
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('opac/browse') ?>">
                            <i class="fa-solid fa-compass me-1"></i>Browse
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#"
                            id="navbarDropdownLayanan" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-desktop me-1"></i>Layanan Mandiri
                        </a>
                        <ul class="dropdown-menu shadow-sm border-0" aria-labelledby="navbarDropdownLayanan">
                            <li>
                                <a class="dropdown-item" href="<?= base_url('baca-ditempat') ?>">
                                    <i class="fa-solid fa-book-reader fa-fw text-secondary me-2"></i>Baca Ditempat
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('buku-tamu') ?>">
                                    <i class="fa-solid fa-address-book fa-fw text-secondary me-2"></i>Buku Tamu
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('peminjaman-mandiri') ?>">
                                    <i class="fa-solid fa-hand-holding-hand fa-fw text-secondary me-2"></i>Peminjaman Mandiri
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('pengembalian-mandiri') ?>">
                                    <i class="fa-solid fa-rotate-left fa-fw text-secondary me-2"></i>Pengembalian Mandiri
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <?php if (env('Is_keanggotaan_online') == 1): ?>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('home/pendaftaran-online') ?>">
                                    <i class="fa-solid fa-id-card fa-fw text-secondary me-2"></i>Keanggotaan Online
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#"
                            id="navbarDropdownStatistik" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-chart-pie me-1"></i>Statistik
                        </a>
                        <ul class="dropdown-menu shadow-sm border-0" aria-labelledby="navbarDropdownStatistik">
                            <li>
                                <a class="dropdown-item" href="<?= base_url('opac/statistics') ?>">
                                    <i class="fa-solid fa-book-bookmark fa-fw text-secondary me-2"></i>Statistik Katalog
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('opac/statistics_anggota') ?>">
                                    <i class="fa-solid fa-users fa-fw text-secondary me-2"></i>Statistik Anggota
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>

                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
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

    <!-- ─── Main Content ─── -->
    <main style="min-height: 80vh;">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- ─── Footer ─── -->
    <footer class="footer-custom">
        <div class="container">
            <div class="row mb-5">

                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img src="<?=  base_url('assets/img/Logo-Inlislite.webp') ?>"
                            alt="Logo" class="rounded bg-white p-1" style="width:150px;height:70px;object-fit:cover;">
                        <h4 class="m-0 fw-bold"><?= esc($nama_perpustakaan) ?></h4>
                    </div>
                    <p class="text-secondary" style="font-size:0.9rem;line-height:1.8;">
                        <?= esc($tentang_kami) ?>
                    </p>
                </div>

                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0 offset-lg-1">
                    <h5 class="footer-title">Layanan</h5>
                    <ul class="footer-links">
                        <?php if (env('Is_keanggotaan_online') == 1): ?><li><a href="<?= base_url('home/pendaftaran_online') ?>">Keanggotaan Online</a></li><?php endif; ?>
                        <li><a href="<?= base_url('peminjaman-mandiri') ?>">Peminjaman Mandiri</a></li>
                        <li><a href="<?= base_url('pengembalian-mandiri') ?>">Pengembalian Mandiri</a></li>
                        <li><a href="<?= base_url('opac') ?>">OPAC</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h5 class="footer-title">Kontak & Lokasi</h5>
                    <ul class="footer-links footer-contact">
                        <li><i class="fa-solid fa-location-dot"></i> <span><?= esc($alamat) ?></span></li>
                        <li><i class="fa-solid fa-phone"></i> <span><?= esc($phone)  ?></span></li>
                        <li><i class="fa-solid fa-envelope"></i> <span><?= esc($email)  ?></span></li>
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

   <script src="<?= base_url('assets/js'); ?>/bootstrap.min.js"></script>
    <script src="<?= base_url('assets/js'); ?>/jquery-4.0.0.min.js"></script>
    <script src="<?= base_url('assets/js'); ?>/sweetalert2@8.js"></script>

    <?= $this->renderSection('script') ?>
</body>

</html>