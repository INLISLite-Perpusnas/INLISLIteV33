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
    <title><?= $title ?? 'OPAC - ' . $nama_perpustakaan ?></title>

    <link rel="icon" href="<?= !empty($logo) ? base_url('uploads/branch/' . $logo) : base_url('assets/img/default-perpus.png') ?>">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            /* Warna Tema Utama (Biru Modern) */
            --brand-50: #eff6ff;
            --brand-100: #dbeafe;
            --brand-500: #3b82f6;
            --brand-600: #1b3878;
            --brand-700: #1d4ed8;
            --brand-900: #1e3a8a;

            /* Warna Teks/Background */
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

        /* --- Global Utility --- */
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
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        }

        /* --- Navbar --- */
        /* --- Navbar --- */
        .navbar-custom {
            background-color: var(--brand-600);
            /* Ubah background menjadi biru */
            border-bottom: none;
            padding: 15px 0;
            transition: all 0.3s;
        }

        .navbar-brand img {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            object-fit: cover;
        }

        .navbar-brand h1 {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
            color: #ffffff;
        }

        /* Teks putih */
        .navbar-brand p {
            font-size: 0.65rem;
            margin: 0;
            font-weight: 600;
            text-transform: uppercase;
            color: #e2e8f0;
            letter-spacing: 1px;
        }

        /* Teks abu-abu terang */
        .nav-link {
            color: #f8fafc;
            font-weight: 500;
            font-size: 0.9rem;
            margin: 0 10px;
            transition: 0.3s;
        }

        /* Teks putih/terang */
        .nav-link:hover,
        .nav-link.active {
            color: #93c5fd;
        }

        /* Biru muda saat di-hover */

        /* Mengubah warna icon hamburger menu mobile menjadi putih */
        .navbar-toggler-icon-custom {
            color: #ffffff;
            font-size: 1.5rem;
        }

        /* --- Footer --- */
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

        /* Utility text clamp for lines */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
    <?= $this->renderSection('style') ?>
</head>

<body>

    <nav class="navbar navbar-expand-lg fixed-top navbar-custom shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3" href="<?= base_url('home') ?>">
                <img src="<?= !empty($logo) ? base_url('uploads/branch/' . $logo) : base_url('assets/img/default-perpus.png') ?>" alt="Logo" class="bg-white p-1 shadow-sm">
                <div>
                    <h1><?= strtok($nama_perpustakaan, " ") ?> <span><?= strstr($nama_perpustakaan, " ") ?></span></h1>
                    <p>Digital Library</p>
                </div>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fa-solid fa-bars navbar-toggler-icon-custom"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('home') ?>">
                            <i class="fa-solid fa-home me-1"></i> Beranda
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('opac') ?>">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> OPAC
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('opac/browse') ?>">
                            <i class="fa-solid fa-compass me-1"></i> Browse
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('baca-ditempat') ?>">
                            <i class="fa-solid fa-book-reader me-1"></i> Baca Ditempat
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownLayanan" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-desktop me-1"></i> Layanan Mandiri
                        </a>
                        <ul class="dropdown-menu shadow-sm border-0" aria-labelledby="navbarDropdownLayanan">
                            <li>
                                <a class="dropdown-item" href="<?= base_url('buku-tamu') ?>">
                                    <i class="fa-solid fa-address-book fa-fw text-secondary me-2"></i> Buku Tamu
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('peminjaman-mandiri') ?>">
                                    <i class="fa-solid fa-hand-holding-hand fa-fw text-secondary me-2"></i> Peminjaman Mandiri
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('pengembalian-mandiri') ?>">
                                    <i class="fa-solid fa-rotate-left fa-fw text-secondary me-2"></i> Pengembalian Mandiri
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('home/pendaftaran-online') ?>">
                                    <i class="fa-solid fa-id-card fa-fw text-secondary me-2"></i> Keanggotaan Online
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownStatistik" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-chart-pie me-1"></i> Statistik
                        </a>
                        <ul class="dropdown-menu shadow-sm border-0" aria-labelledby="navbarDropdownStatistik">
                            <li>
                                <a class="dropdown-item" href="<?= base_url('opac/statistics') ?>">
                                    <i class="fa-solid fa-book-bookmark fa-fw text-secondary me-2"></i> Statistik Katalog
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('statistik/anggota') ?>">
                                    <i class="fa-solid fa-users fa-fw text-secondary me-2"></i> Statistik Anggota
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    <?php if (session()->get('logged_in')) : ?>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-light text-primary rounded-pill px-4 py-2 fw-bold shadow-sm" style="font-size: 0.9rem;">
                            <i class="fa-solid fa-gauge me-2"></i>Dashboard
                        </a>
                    <?php else : ?>
                        <a href="<?= base_url('login') ?>" class="btn btn-light text-primary rounded-pill px-4 py-2 fw-bold shadow-sm" style="font-size: 0.9rem;">
                            <i class="fa-solid fa-right-to-bracket me-2"></i>Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main style="min-height: 80vh;">
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="footer-custom">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img src="<?= !empty($logo) ? base_url('uploads/branch/' . $logo) : base_url('assets/img/default-perpus.png') ?>" alt="Logo" class="rounded bg-white p-1" style="width:40px; height:40px;">
                        <h4 class="m-0 fw-bold"><?= $nama_perpustakaan ?></h4>
                    </div>
                    <p class="text-secondary" style="font-size: 0.9rem; line-height: 1.8;">
                        <?= $tentang_kami ?>
                    </p>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0 offset-lg-1">
                    <h5 class="footer-title">Layanan</h5>
                    <ul class="footer-links">
                        <li><a href="<?= base_url('home/pendaftaran_online') ?>">Keanggotaan Online</a></li>
                        <li><a href="<?= base_url('peminjaman-mandiri') ?>">Peminjaman Mandiri</a></li>
                        <li><a href="<?= base_url('pengembalian-mandiri') ?>">Pengembalian Mandiri</a></li>
                        <li><a href="<?= base_url('opac') ?>">OPAC</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5 class="footer-title">Kontak & Lokasi</h5>
                    <ul class="footer-links footer-contact">
                        <li><i class="fa-solid fa-location-dot"></i> <span><?= $alamat ?></span></li>
                        <li><i class="fa-solid fa-phone"></i> <span><?= $phone ?></span></li>
                        <li><i class="fa-solid fa-envelope"></i> <span><?= $email ?></span></li>
                    </ul>
                </div>
            </div>
            <div class="border-top border-secondary pt-4 mt-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <p class="text-secondary mb-0" style="font-size: 0.8rem;">&copy; <?= date('Y') ?> <?= $nama_perpustakaan ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?= $this->renderSection('script') ?>
</body>

</html>