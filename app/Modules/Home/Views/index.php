<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<?php if (!empty($banners[0]->file_cover)): ?>
<link rel="preload" as="image" href="<?= base_url('uploads/banner/' . rawurlencode($banners[0]->file_cover)) ?>" fetchpriority="high">
<?php endif; ?>
<style>
    #main-content {
        --home-button-blue: #2d659e;
        --home-button-blue-hover: #23517f;
    }

    #main-content .btn-brand {
        background: var(--home-button-blue);
        border-color: var(--home-button-blue);
        color: #ffffff;
    }

    #main-content .btn-brand:hover,
    #main-content .btn-brand:focus-visible {
        background: var(--home-button-blue-hover);
        border-color: var(--home-button-blue-hover);
        color: #ffffff;
    }

    #main-content .btn-outline-primary {
        border-color: var(--home-button-blue);
        color: var(--home-button-blue);
    }

    #main-content .btn-outline-primary:hover,
    #main-content .btn-outline-primary:focus-visible {
        background: var(--home-button-blue);
        border-color: var(--home-button-blue);
        color: #ffffff;
    }

    #main-content .sd-panel-header a {
        color: var(--home-button-blue);
    }

    #main-content .sd-panel-header a:hover {
        color: var(--home-button-blue-hover);
    }

    #main-content .unggulan-nav:focus-visible,
    #main-content .popular-slider-nav:focus-visible {
        outline: 3px solid var(--home-button-blue);
        outline-offset: 3px;
    }

    /* Hero Section (Murni Gambar) */
    /* Menjadi: */
    .hero-section {
        margin-top: 76px;
        position: relative;
        overflow: hidden;
        background-color: #e2e8f0;
        aspect-ratio: 3229 / 937;
    }

    .hero-bg {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }

    /* Overlapping Search Box */
    .search-overlap {
        margin-top: -40px;
        /* Menarik kotak pencarian ke atas agar menimpa gambar banner */
        position: relative;
        z-index: 10;
    }

    .search-input-group i {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--slate-500);
        z-index: 5;
    }

    .search-input-group input {
        padding-left: 55px;
        border-radius: 1rem !important;
        background-color: var(--slate-50);
        border: 1px solid var(--slate-100);
        font-size: 1rem;
    }

    .search-input-group input:focus {
        background-color: white;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        border-color: var(--brand-500);
    }

    /* Cards & Hover Effects */
    .hover-card {
        transition: all 0.3s ease;
        border: 1px solid var(--slate-100);
    }

    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.1);
        border-color: var(--brand-100);
    }

    /* Image Utils */
    .book-cover {
    aspect-ratio: 2 / 3;      /* kunci rasio portrait sesuai cover buku */
    height: auto;
    object-fit: cover;         /* crop rapi tanpa letterbox, ukuran seragam */
    object-position: center;
    width: 100%;
    background-color: #e2e8f0;
}

    .news-cover {
        height: 100px;
        object-fit: cover;
        width: 100%;
        border-radius: 0.75rem;
    }

    .news-main-cover {
        height: 380px;
        object-fit: cover;
        width: 100%;
    }

    /* Custom Title Color */
    .section-title {
        color: #1b3878 !important;
    }

    /* Koleksi Populer Slider */
    .popular-slider-wrapper {
        position: relative;
        padding: 0 40px;
    }

    .popular-slider {
        --items-per-view: 5;
        --slider-gap: 1.25rem;
        display: flex;
        gap: var(--slider-gap);
        overflow-x: auto;
        scroll-behavior: smooth;
        scroll-snap-type: x mandatory;
        padding: 4px 0 16px;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .popular-slider::-webkit-scrollbar {
        display: none;
    }

    /* Lebar kartu dihitung persis agar pas N kartu per baris, tidak ada yang terpotong */
    .popular-slider .popular-card-item {
        flex: 0 0 auto;
        width: calc((100% - (var(--items-per-view) - 1) * var(--slider-gap)) / var(--items-per-view));
        scroll-snap-align: start;
    }

    @media (max-width: 1199px) {
        .popular-slider {
            --items-per-view: 4;
        }
    }

    @media (max-width: 991px) {
        .popular-slider {
            --items-per-view: 3;
        }
    }

    @media (max-width: 575px) {
        .popular-slider {
            --items-per-view: 2;
        }
    }

    .popular-slider-nav {
        position: absolute;
        top: 40%;
        transform: translateY(-50%);
        z-index: 5;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: none;
        background: var(--home-button-blue);
        color: #ffffff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .popular-slider-nav:hover {
        background: var(--home-button-blue-hover);
        color: #ffffff;
    }

    .popular-slider-nav.prev {
        left: 0;
    }

    .popular-slider-nav.next {
        right: 0;
    }

    @media (max-width: 767px) {
        .popular-slider-wrapper {
            padding: 0;
        }

        .popular-slider-nav {
            display: none;
        }
    }

    .unggulan-nav {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 1px solid var(--home-button-blue);
        background: var(--home-button-blue);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .unggulan-nav:hover {
        background: var(--home-button-blue-hover);
        color: #ffffff;
    }

    /* Koleksi Sering Dipinjam - panel putih berheader + slider kartu */
    .sd-panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1.5rem;
        overflow: hidden;
    }

    .sd-panel-header {
        padding: 1.5rem 1.75rem;
        border-bottom: 1px solid #eef2f7;
    }

    .sd-panel-header h3 {
        font-size: 1.35rem;
    }

    .sd-panel-body {
        padding: 1.5rem 1.75rem;
    }

    .sd-slider {
        --items-per-view: 5;
        --slider-gap: 1.25rem;
        display: flex;
        gap: var(--slider-gap);
        overflow-x: auto;
        scroll-behavior: smooth;
        scroll-snap-type: x mandatory;
        padding-bottom: 4px;
        scrollbar-width: none;
    }

    .sd-slider::-webkit-scrollbar {
        display: none;
    }

    .sd-slider .popular-card-item {
        flex: 0 0 auto;
        width: calc((100% - (var(--items-per-view) - 1) * var(--slider-gap)) / var(--items-per-view));
        scroll-snap-align: start;
    }

    .sd-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        padding: 1rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1.25rem;
        color: inherit;
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }

    .sd-card:hover {
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        transform: translateY(-2px);
        color: inherit;
    }

    .sd-card .sd-cover-wrap {
        position: relative;
        border-radius: 0.75rem;
        overflow: hidden;
        background-color: #e2e8f0;
        aspect-ratio: 2 / 3;
    }

    .sd-card .sd-cover {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .sd-card .sd-badge {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        padding: 0.2rem 0.55rem;
        font-size: 0.75rem;
        border-radius: 0.4rem;
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .sd-card .sd-badge.is-empty {
        background: #fef2f2;
        color: #991b1b;
        border-color: #fecaca;
    }

    @media (max-width: 1199px) {
        .sd-slider {
            --items-per-view: 4;
        }
    }

    @media (max-width: 991px) {
        .sd-slider {
            --items-per-view: 3;
        }
    }

    @media (max-width: 575px) {
        .sd-slider {
            --items-per-view: 2;
        }

        .sd-panel-header,
        .sd-panel-body {
            padding: 1rem;
        }
    }

    /* Variasi warna latar antar-section: putih & abu-abu tipis berselang-seling,
       supaya tiap section sedikit berbeda dari section di atas/bawahnya. */
    .section-tone-1 {
        background-color: #ffffff;
    }

    .section-tone-2 {
        background-color: var(--slate-100);
    }

    .search-suggestions {
        max-height: 250px;
        overflow-y: auto;
        top: 100%;
        border-radius: 1rem;
    }

    .suggestion-item {
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .suggestion-item:hover,
    .suggestion-item:focus {
        background-color: var(--slate-100);
    }

    /* Layanan Mandiri: kartu ikon di bawah kotak pencarian */
    .layanan-card {
        border: 1px solid #e2e8f0;
        border-radius: 1.5rem;
        background: #fff;
        box-shadow: 0 4px 20px -8px rgba(15, 23, 42, 0.08);
    }

    .layanan-card .layanan-header {
        padding: 1.5rem 1.75rem 1rem;
        border-bottom: 1px solid #eef2f7;
    }

    /* home-bootstrap.min.css tidak memuat row-cols-*, jadi grid ditulis sendiri */
    .layanan-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    @media (min-width: 768px) {
        .layanan-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (min-width: 1200px) {
        .layanan-grid {
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        }
    }

    .layanan-tile {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        padding: 1.5rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        background: #f8fafc;
        color: #0f172a;
        text-align: center;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .layanan-tile:hover,
    .layanan-tile:focus {
        transform: translateY(-3px);
        background: #fff;
        box-shadow: 0 10px 24px -8px rgba(15, 23, 42, 0.15);
        color: #0f172a;
    }

    .layanan-icon {
        width: 64px;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 1rem;
        font-size: 1.6rem;
        margin-bottom: 1rem;
    }

    .layanan-tile .layanan-title {
        font-weight: 700;
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
    }

    .layanan-tile .layanan-desc {
        font-size: 0.82rem;
        color: #64748b;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="hero-section">
    <?php if (!empty($banners)): ?>
        <div id="heroBannerCarousel" class="carousel slide h-100">
            <div class="carousel-inner h-100">
                <?php foreach ($banners as $i => $b): ?>
                    <div class="carousel-item h-100 <?= $i === 0 ? 'active' : '' ?>">
                        <img src="<?= base_url('uploads/banner/' . esc($b->file_cover)) ?>"
                            alt="Banner <?= $i + 1 ?>"
                            class="hero-bg"
                            width="1600"
                            height="465"
                            decoding="async"
                            <?= $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($banners) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#heroBannerCarousel" data-bs-slide="prev" aria-label="Banner sebelumnya">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroBannerCarousel" data-bs-slide="next" aria-label="Banner berikutnya">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
                <div class="carousel-indicators">
                    <?php foreach ($banners as $i => $b): ?>
                        <button type="button" data-bs-target="#heroBannerCarousel" data-bs-slide-to="<?= $i ?>" aria-label="Tampilkan banner <?= $i + 1 ?>" <?= $i === 0 ? 'class="active" aria-current="true"' : '' ?>></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <img src="<?= base_url('assets/img/Logo-Inlislite.webp') ?>" alt="Banner Perpustakaan" class="hero-bg" width="1600" height="465" fetchpriority="high" decoding="async">
    <?php endif; ?>
</section>

<div class="container search-overlap">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card rounded-2xl shadow-soft border-0 p-2 bg-white">
                <div class="card-body p-3">
                    <form id="searchForm" action="<?= base_url('opac') ?>" method="GET" class="row g-2">
                        <div class="col-md-9 position-relative search-input-group">
                            <i class="fa-solid fa-search"></i>
                            <label for="searchInput" class="visually-hidden">Cari koleksi perpustakaan</label>
                            <input type="text" id="searchInput" name="search" class="form-control form-control-lg py-3" placeholder="Cari judul buku, penulis, atau penerbit..." autocomplete="off">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-brand w-100 h-100 rounded-xl fw-bold" style="font-size: 1.05rem;">
                                Cari Buku
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Pindahan dropdown "Layanan Mandiri" dari navbar; syarat tampil sama dengan menu lama.
$usulanRow = db_connect()->table('settingparameters')->select('Value')->where('Name', 'UsulanKoleksiLKD')->get()->getRow();
$layananMandiri = [
    ['show' => setting_enabled('is_keanggotaan_online'), 'url' => base_url('home/pendaftaran-online'), 'icon' => 'fa-id-card', 'bg' => '#e0f2fe', 'color' => '#0284c7', 'title' => 'Anggota Online', 'desc' => 'Daftar anggota'],
    ['show' => setting_enabled('is_peminjaman_mandiri', true), 'url' => base_url('peminjaman-mandiri'), 'icon' => 'fa-hand-holding-hand', 'bg' => '#dcfce7', 'color' => '#16a34a', 'title' => 'Peminjaman Mandiri', 'desc' => 'Pinjam koleksi'],
    ['show' => setting_enabled('is_pengembalian_mandiri', true), 'url' => base_url('pengembalian-mandiri'), 'icon' => 'fa-rotate-left', 'bg' => '#fef3c7', 'color' => '#d97706', 'title' => 'Pengembalian Mandiri', 'desc' => 'Kembalikan koleksi'],
    ['show' => true, 'url' => base_url('baca-ditempat'), 'icon' => 'fa-book-reader', 'bg' => '#ffe4e6', 'color' => '#e11d48', 'title' => 'Baca di Tempat', 'desc' => 'Layanan baca'],
    ['show' => true, 'url' => base_url('buku-tamu'), 'icon' => 'fa-address-book', 'bg' => '#fae8ff', 'color' => '#c026d3', 'title' => 'Buku Tamu', 'desc' => 'Isi kunjungan'],
    ['show' => $usulanRow && strtoupper(trim((string) $usulanRow->Value)) === 'TRUE', 'url' => site_url('home/usulan-koleksi'), 'icon' => 'fa-book-open', 'bg' => '#ccfbf1', 'color' => '#0d9488', 'title' => 'Usulan Koleksi', 'desc' => 'Usulkan buku'],
    ['show' => true, 'url' => site_url('home/survey'), 'icon' => 'fa-list', 'bg' => '#ede9fe', 'color' => '#7c3aed', 'title' => 'Survei', 'desc' => 'Saran layanan'],
    ['show' => true, 'url' => base_url('opac/statistics'), 'icon' => 'fa-chart-pie', 'bg' => '#dbeafe', 'color' => '#2563eb', 'title' => 'Statistik Katalog', 'desc' => 'Data koleksi'],
];
$layananMandiri = array_filter($layananMandiri, fn($item) => $item['show']);
?>
<section class="mt-5" id="layanan-mandiri">
    <div class="container">
        <div class="layanan-card">
            <div class="layanan-header">
                <h4 class="fw-bold mb-1">Layanan Mandiri</h4>
                <p class="text-muted mb-0">Akses layanan perpustakaan dengan cepat.</p>
            </div>
            <div class="p-3 p-md-4">
                <div class="layanan-grid">
                    <?php foreach ($layananMandiri as $item): ?>
                        <a href="<?= $item['url'] ?>" class="layanan-tile">
                            <span class="layanan-icon" style="background: <?= $item['bg'] ?>; color: <?= $item['color'] ?>;">
                                <i class="fa-solid <?= $item['icon'] ?>"></i>
                            </span>
                            <span class="layanan-title"><?= esc($item['title']) ?></span>
                            <span class="layanan-desc"><?= esc($item['desc']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 mt-3 section-tone-1">
    <div class="container">
        <div class="row text-center g-4 justify-content-center">

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-2xl h-100 hover-card bg-white p-3 p-md-4">
                    <div class="card-body p-0 d-flex flex-column align-items-center justify-content-center">
                        <i class="fa-solid fa-book-bookmark fa-2x mb-3" style="color: #1b3878;"></i>
                        <h2 class="fw-bolder text-brand mb-2 stat-number display-6"><?= $statistics['total_books'] ?? 0 ?></h2>
                        <p class="text-dark fw-bold text-uppercase mb-0" style="letter-spacing: 1px; font-size: 0.85rem;">Koleksi Buku</p>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-2xl h-100 hover-card bg-white p-3 p-md-4">
                    <div class="card-body p-0 d-flex flex-column align-items-center justify-content-center">
                        <i class="fa-solid fa-users fa-2x mb-3" style="color: #1b3878;"></i>
                        <h2 class="fw-bolder text-brand mb-2 stat-number display-6"><?= $statistics['total_members'] ?? 0 ?></h2>
                        <p class="text-dark fw-bold text-uppercase mb-0" style="letter-spacing: 1px; font-size: 0.85rem;">Anggota Aktif</p>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-2xl h-100 hover-card bg-white p-3 p-md-4">
                    <div class="card-body p-0 d-flex flex-column align-items-center justify-content-center">
                        <i class="fa-solid fa-hand-holding-hand fa-2x mb-3" style="color: #1b3878;"></i>
                        <h2 class="fw-bolder text-brand mb-2 stat-number display-6"><?= $statistics['books_borrowed'] ?? 0 ?></h2>
                        <p class="text-dark fw-bold text-uppercase mb-0" style="letter-spacing: 1px; font-size: 0.85rem;">Buku Dipinjam</p>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-2xl h-100 hover-card bg-white p-3 p-md-4">
                    <div class="card-body p-0 d-flex flex-column align-items-center justify-content-center">
                        <i class="fa-solid fa-person-walking fa-2x mb-3" style="color: #1b3878;"></i>
                        <h2 class="fw-bolder text-brand mb-2 stat-number display-6"><?= $statistics['visitors_today'] ?? 0 ?></h2>
                        <p class="text-dark fw-bold text-uppercase mb-0" style="letter-spacing: 1px; font-size: 0.85rem;">Pengunjung Hari Ini</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php if (!empty($koleksi_unggulan_books)): ?>
    <section class="py-5 section-tone-2" id="koleksi-unggulan">
        <div class="container">
            <div class="sd-panel">
                <div class="sd-panel-header d-flex justify-content-between align-items-center gap-3">
                    <div>
                        <h3 class="fw-bold mb-1 section-title">Koleksi Unggulan</h3>
                        <p class="text-secondary mb-0">Koleksi pilihan yang direkomendasikan perpustakaan untuk Anda.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="unggulan-nav" id="koleksiUnggulanSliderPrev" aria-label="Sebelumnya">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <button type="button" class="unggulan-nav" id="koleksiUnggulanSliderNext" aria-label="Berikutnya">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                        <a href="<?= base_url('opac') ?>" class="fw-semibold text-decoration-none ms-2 text-nowrap">Lihat semua <i class="fa-solid fa-arrow-right ms-1"></i></a>
                    </div>
                </div>

                <div class="sd-panel-body">
                    <div class="sd-slider" id="koleksiUnggulanSlider">
                        <?php $defaultCover = base_url('assets/img/default-cover.webp'); ?>
                        <?php foreach ($koleksi_unggulan_books as $book): ?>
                            <?php
                            $thumbUrl = get_catalog_thumb_url($book->CoverURL ?: '', 300, 450);
                            $total    = (int) $book->TotalItem;
                            $tersedia = (int) $book->TersediaItem;
                            ?>
                            <div class="popular-card-item">
                                <a href="<?= base_url('opac/detail/' . $book->ID) ?>" class="sd-card text-decoration-none">
                                    <div class="sd-cover-wrap mb-3">
                                        <img src="<?= $thumbUrl ?>"
                                             class="sd-cover"
                                             alt="<?= esc($book->Title) ?>"
                                             width="300"
                                             height="450"
                                             loading="lazy"
                                             decoding="async"
                                             onerror="this.onerror=null; this.src='<?= $defaultCover ?>';">
                                        <?php if ($total > 0): ?>
                                            <span class="sd-badge<?= $tersedia === 0 ? ' is-empty' : '' ?>">Tersedia <?= $tersedia ?> dari <?= $total ?> item</span>
                                        <?php endif; ?>
                                    </div>
                                    <h4 class="fs-6 fw-bold mb-2 line-clamp-2 text-dark" title="<?= esc($book->Title) ?>"><?= esc($book->Title) ?></h4>
                                    <p class="text-secondary small mb-2 text-truncate" title="<?= esc($book->Author) ?>"><?= esc($book->Author ?: 'Anonim') ?></p>
                                    <span class="text-secondary small mt-auto"><?= esc($book->PublishYear ?: '-') ?></span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (!empty($sering_dipinjam_books)): ?>
    <section class="py-5 section-tone-1" id="koleksi-sering-dipinjam">
        <div class="container">
            <div class="sd-panel">
                <div class="sd-panel-header d-flex justify-content-between align-items-center gap-3">
                    <div>
                        <h3 class="fw-bold mb-1 section-title">Koleksi Sering Dipinjam</h3>
                        <p class="text-secondary mb-0">Buku dengan jumlah peminjaman terbanyak.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="unggulan-nav" id="seringDipinjamSliderPrev" aria-label="Sebelumnya">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <button type="button" class="unggulan-nav" id="seringDipinjamSliderNext" aria-label="Berikutnya">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                        <a href="<?= base_url('opac') ?>" class="fw-semibold text-decoration-none ms-2 text-nowrap">Lihat semua <i class="fa-solid fa-arrow-right ms-1"></i></a>
                    </div>
                </div>

                <div class="sd-panel-body">
                    <div class="sd-slider" id="seringDipinjamSlider">
                        <?php $defaultCover = base_url('assets/img/default-cover.webp'); ?>
                        <?php foreach ($sering_dipinjam_books as $book): ?>
                            <?php
                            $thumbUrl = get_catalog_thumb_url($book->CoverURL ?: '', 300, 450);
                            $total    = (int) $book->TotalItem;
                            $tersedia = (int) $book->TersediaItem;
                            ?>
                            <div class="popular-card-item">
                                <a href="<?= base_url('opac/detail/' . $book->ID) ?>" class="sd-card text-decoration-none">
                                    <div class="sd-cover-wrap mb-3">
                                        <img src="<?= $thumbUrl ?>"
                                             class="sd-cover"
                                             alt="<?= esc($book->Title) ?>"
                                             width="300"
                                             height="450"
                                             loading="lazy"
                                             decoding="async"
                                             onerror="this.onerror=null; this.src='<?= $defaultCover ?>';">
                                        <?php if ($total > 0): ?>
                                            <span class="sd-badge<?= $tersedia === 0 ? ' is-empty' : '' ?>">Tersedia <?= $tersedia ?> dari <?= $total ?> item</span>
                                        <?php endif; ?>
                                    </div>
                                    <h4 class="fs-6 fw-bold mb-2 line-clamp-2 text-dark" title="<?= esc($book->Title) ?>"><?= esc($book->Title) ?></h4>
                                    <p class="text-secondary small mb-2 text-truncate" title="<?= esc($book->Author) ?>"><?= esc($book->Author ?: 'Anonim') ?></p>
                                    <span class="text-secondary small mt-auto"><?= esc($book->PublishYear ?: '-') ?></span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (!empty($sering_dibaca_books)): ?>
    <section class="py-5 section-tone-2" id="koleksi-sering-dibaca">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h3 class="fw-bold mb-1 section-title"><i class="fa-solid fa-chair text-info me-2"></i>Sering Dibaca di Tempat</h3>
                    <p class="text-secondary mb-0">Buku yang paling banyak dibaca langsung di perpustakaan.</p>
                </div>
            </div>

            <div class="popular-slider-wrapper">
                <button type="button" class="popular-slider-nav prev" id="seringDibacaSliderPrev" aria-label="Sebelumnya">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <div class="popular-slider" id="seringDibacaSlider">
                    <?php foreach ($sering_dibaca_books as $i => $book): ?>
                        <?php
                        $defaultCover = base_url('assets/img/default-cover.webp');
                        $thumbUrl     = get_catalog_thumb_url($book->CoverURL ?: '', 200, 340);
                        ?>
                        <div class="popular-card-item">
                            <div class="card h-100 border-0 shadow-sm hover-card rounded-xl overflow-hidden bg-white position-relative">
                                <span class="badge bg-info position-absolute" style="top: 10px; left: 10px; z-index: 2;">
                                    <i class="fa-solid fa-chair me-1"></i>Sering Dibaca
                                </span>
                                <img src="<?= $thumbUrl ?>"
                                     class="card-img-top book-cover"
                                     alt="<?= esc($book->Title) ?>"
                                     width="200"
                                     height="340"
                                     loading="lazy"
                                     decoding="async"
                                     onerror="this.onerror=null; this.src='<?= $defaultCover ?>';">
                                <div class="card-body d-flex flex-column p-3">
                                    <h4 class="card-title fw-bold fs-6 mb-1 line-clamp-2" title="<?= esc($book->Title) ?>"><?= esc($book->Title) ?></h4>
                                    <p class="card-text text-secondary small mb-3 text-truncate" title="<?= esc($book->Author) ?>">
                                        <?= esc($book->Author ?: 'Anonim') ?>
                                    </p>
                                    <a href="<?= base_url('opac/detail/' . $book->ID) ?>" class="btn btn-outline-primary btn-sm mt-auto fw-semibold w-100 rounded-lg">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="button" class="popular-slider-nav next" id="seringDibacaSliderNext" aria-label="Berikutnya">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (!empty($featured_books)): ?>
    <section class="py-5 section-tone-2" id="koleksi">
        <div class="container">
            <div class="sd-panel">
                <div class="sd-panel-header d-flex justify-content-between align-items-center gap-3">
                    <div>
                        <h3 class="fw-bold mb-1 section-title">Koleksi Terbaru</h3>
                        <p class="text-secondary mb-0">Koleksi yang baru ditambahkan.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="unggulan-nav" id="koleksiTerbaruSliderPrev" aria-label="Sebelumnya">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <button type="button" class="unggulan-nav" id="koleksiTerbaruSliderNext" aria-label="Berikutnya">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                        <a href="<?= base_url('opac') ?>" class="fw-semibold text-decoration-none ms-2 text-nowrap">Lihat semua <i class="fa-solid fa-arrow-right ms-1"></i></a>
                    </div>
                </div>

                <div class="sd-panel-body">
                    <div class="sd-slider" id="koleksiTerbaruSlider">
                        <?php $defaultCover = base_url('assets/img/default-cover.webp'); ?>
                        <?php foreach ($featured_books as $book): ?>
                            <?php
                            $thumbUrl = get_catalog_thumb_url($book->CoverURL ?: '', 300, 450);
                            $total    = (int) $book->TotalItem;
                            $tersedia = (int) $book->TersediaItem;
                            ?>
                            <div class="popular-card-item">
                                <a href="<?= base_url('opac/detail/' . $book->ID) ?>" class="sd-card text-decoration-none">
                                    <div class="sd-cover-wrap mb-3">
                                        <img src="<?= $thumbUrl ?>"
                                             class="sd-cover"
                                             alt="<?= esc($book->Title) ?>"
                                             width="300"
                                             height="450"
                                             loading="lazy"
                                             decoding="async"
                                             onerror="this.onerror=null; this.src='<?= $defaultCover ?>';">
                                        <?php if ($total > 0): ?>
                                            <span class="sd-badge<?= $tersedia === 0 ? ' is-empty' : '' ?>">Tersedia <?= $tersedia ?> dari <?= $total ?> item</span>
                                        <?php endif; ?>
                                    </div>
                                    <h4 class="fs-6 fw-bold mb-2 line-clamp-2 text-dark" title="<?= esc($book->Title) ?>"><?= esc($book->Title) ?></h4>
                                    <p class="text-secondary small mb-2 text-truncate" title="<?= esc($book->Author) ?>"><?= esc($book->Author ?: 'Anonim') ?></p>
                                    <span class="text-secondary small mt-auto"><?= esc($book->PublishYear ?: '-') ?></span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (setting_enabled('is_berita', true)): ?>
<section class="py-5 section-tone-1" id="berita">
    <div class="container my-4">
        <h3 class="fw-bold mb-4 d-flex align-items-center section-title">
            <div class="bg-brand rounded-pill me-3" style="width: 5px; height: 30px;"></div>
            Berita & Pengumuman
        </h3>

        <?php if (!empty($news)): ?>
            <div class="row g-4">
                <div class="col-lg-6">
                    <?php
                    $highlight = $news[0];
                    $newsFallback = base_url('assets/img/default-cover.webp');
                    $highlightCover = basename((string) ($highlight['file_cover'] ?? ''));
                    $highlightImg = $highlightCover !== '' && is_file(FCPATH . 'uploads/berita/' . $highlightCover)
                        ? base_url('uploads/berita/' . $highlightCover)
                        : $newsFallback;
                    ?>
                    <a href="<?= base_url('news/detail/' . $highlight['id'] . '/' . $highlight['slug']) ?>" class="card border-0 rounded-2xl overflow-hidden text-decoration-none shadow-sm hover-card position-relative text-white h-100">
                        <img src="<?= $highlightImg ?>" onerror="this.onerror=null;this.src='<?= $newsFallback ?>'" class="news-main-cover" alt="<?= esc($highlight['title']) ?>" width="700" height="380" loading="lazy" decoding="async">
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to top, rgba(15,23,42,0.95) 0%, rgba(15,23,42,0.4) 50%, transparent 100%);"></div>

                        <div class="position-absolute bottom-0 start-0 p-4 w-100">
                            <span class="badge bg-brand mb-2 py-1 px-3 rounded-pill"><?= date('d M Y', strtotime($highlight['created_at'])) ?></span>
                            <h4 class="fw-bold mb-2 line-clamp-2"><?= esc($highlight['title']) ?></h4>
                            <p class="small text-light text-opacity-75 mb-0 line-clamp-2"><?= strip_tags($highlight['content']) ?></p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-6">
                    <div class="d-flex flex-column h-100 justify-content-between">
                        <?php for ($i = 1; $i < count($news) && $i <= 3; $i++):
                            $article = $news[$i];
                            $articleCover = basename((string) ($article['file_cover'] ?? ''));
                            $imgFile = $articleCover !== '' && is_file(FCPATH . 'uploads/berita/' . $articleCover)
                                ? base_url('uploads/berita/' . $articleCover)
                                : $newsFallback;
                        ?>
                            <a href="<?= base_url('news/detail/' . $article['id'] . '/' . $article['slug']) ?>" class="d-flex gap-3 text-decoration-none text-dark hover-card p-3 rounded-xl border border-light mb-3 bg-white shadow-sm h-100 align-items-center">
                                <div class="flex-shrink-0" style="width: 120px;">
                                    <img src="<?= $imgFile ?>" onerror="this.onerror=null;this.src='<?= $newsFallback ?>'" class="news-cover" alt="<?= esc($article['title']) ?>" width="120" height="100" loading="lazy" decoding="async">
                                </div>
                                <div>
                                    <small class="text-secondary fw-semibold d-block mb-1"><i class="fa-regular fa-calendar me-1"></i> <?= date('d M Y', strtotime($article['created_at'])) ?></small>
                                    <h4 class="fs-6 fw-bold mb-1 line-clamp-2 text-dark" style="line-height: 1.4;"><?= esc($article['title']) ?></h4>
                                    <span class="text-brand small fw-bold mt-2 d-inline-block">Baca Selengkapnya</span>
                                </div>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="<?= base_url('news') ?>" class="btn btn-brand rounded-pill px-5 py-2 fw-semibold shadow-sm">
                    Lihat Semua Berita <i class="fa-solid fa-arrow-right ms-2"></i>
                </a>
            </div>
        <?php else: ?>
            <div class="text-center text-secondary py-5 bg-white rounded-2xl border border-light shadow-sm">
                <i class="fa-regular fa-newspaper fa-3x mb-3 text-secondary opacity-50"></i>
                <p class="mb-0">Belum ada berita yang dipublikasikan.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    (() => {
        function initCardSlider(sliderId, prevId, nextId) {
            const slider = document.getElementById(sliderId);
            const prev = document.getElementById(prevId);
            const next = document.getElementById(nextId);

            if (!slider || !prev || !next) return;

            const scrollStep = () => (slider.querySelector('.popular-card-item')?.offsetWidth || 200) * 2;

            prev.addEventListener('click', () => {
                slider.scrollBy({ left: -scrollStep(), behavior: 'smooth' });
            });

            next.addEventListener('click', () => {
                slider.scrollBy({ left: scrollStep(), behavior: 'smooth' });
            });

            const toggleNavButtons = () => {
                const isScrollable = slider.scrollWidth > slider.clientWidth + 5;
                prev.style.display = isScrollable ? 'flex' : 'none';
                next.style.display = isScrollable ? 'flex' : 'none';
            };

            toggleNavButtons();
            window.addEventListener('resize', toggleNavButtons);
        }

        // Koleksi Unggulan: tombol selalu tampil (redup di ujung), geser otomatis & kembali ke awal
        function initUnggulanSlider(sliderId, prevId, nextId, interval = 5000) {
            const slider = document.getElementById(sliderId);
            const prev = document.getElementById(prevId);
            const next = document.getElementById(nextId);

            if (!slider || !prev || !next) return;

            const step = () => {
                const item = slider.querySelector('.popular-card-item');
                const gap = parseFloat(getComputedStyle(slider).columnGap) || 0;
                return item ? item.offsetWidth + gap : 200;
            };
            const atStart = () => slider.scrollLeft <= 5;
            const atEnd = () => slider.scrollLeft + slider.clientWidth >= slider.scrollWidth - 5;

            const updateNav = () => {
                prev.disabled = atStart();
                next.disabled = atEnd();
                prev.style.opacity = prev.disabled ? 0.4 : 1;
                next.style.opacity = next.disabled ? 0.4 : 1;
            };

            const goNext = () => {
                if (atEnd()) {
                    slider.scrollTo({ left: 0, behavior: 'smooth' });
                } else {
                    slider.scrollBy({ left: step(), behavior: 'smooth' });
                }
            };

            prev.addEventListener('click', () => slider.scrollBy({ left: -step(), behavior: 'smooth' }));
            next.addEventListener('click', () => slider.scrollBy({ left: step(), behavior: 'smooth' }));
            slider.addEventListener('scroll', updateNav, { passive: true });
            window.addEventListener('resize', updateNav);
            updateNav();

            let timer = null;
            const start = () => {
                stop();
                if (slider.scrollWidth > slider.clientWidth + 5) timer = setInterval(goNext, interval);
            };
            const stop = () => {
                if (timer) clearInterval(timer);
                timer = null;
            };
            const panel = slider.closest('section') || slider;
            panel.addEventListener('mouseenter', stop);
            panel.addEventListener('mouseleave', start);
            panel.addEventListener('touchstart', stop, { passive: true });
            window.addEventListener('resize', start);
            start();
        }

        initUnggulanSlider('koleksiUnggulanSlider', 'koleksiUnggulanSliderPrev', 'koleksiUnggulanSliderNext');
        initUnggulanSlider('seringDipinjamSlider', 'seringDipinjamSliderPrev', 'seringDipinjamSliderNext');
        initCardSlider('seringDibacaSlider', 'seringDibacaSliderPrev', 'seringDibacaSliderNext');
        initUnggulanSlider('koleksiTerbaruSlider', 'koleksiTerbaruSliderPrev', 'koleksiTerbaruSliderNext');

        function animateCounters() {
            document.querySelectorAll('.stat-number').forEach((element) => {
                const target = Number.parseInt(element.textContent.replace(/[,.]/g, ''), 10);

                if (Number.isNaN(target) || target === 0) return;

                const increment = Math.ceil(target / 40);
                let current = 0;

                const timer = window.setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        window.clearInterval(timer);
                    }
                    element.textContent = current.toLocaleString('id-ID');
                }, 40);
            });
        }

        if ('IntersectionObserver' in window) {
            const statsObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounters();
                        statsObserver.unobserve(entry.target);
                    }
                });
            });

            const statElement = document.querySelector('.stat-number');
            if (statElement) {
                statsObserver.observe(statElement);
            }
        } else {
            animateCounters();
        }

        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        const searchGroup = document.querySelector('.search-input-group');
        let searchTimeout;
        let searchRequest;

        const removeSuggestions = () => {
            document.querySelector('.search-suggestions')?.remove();
        };

        searchInput?.addEventListener('input', () => {
            const query = searchInput.value.trim();
            window.clearTimeout(searchTimeout);

            if (query.length >= 3) {
                searchTimeout = window.setTimeout(() => performAutoComplete(query), 300);
            } else {
                searchRequest?.abort();
                removeSuggestions();
            }
        });

        async function performAutoComplete(query) {
            searchRequest?.abort();
            searchRequest = new AbortController();

            try {
                const url = new URL('<?= base_url('opac/searchBooks') ?>');
                url.searchParams.set('q', query);
                const response = await fetch(url, {
                    signal: searchRequest.signal,
                    headers: { Accept: 'application/json' }
                });
                if (!response.ok) return;

                const result = await response.json();
                if (result.status === 'success') {
                    showSearchSuggestions(result.data || []);
                }
            } catch (error) {
                if (error.name !== 'AbortError') console.error(error);
            }
        }

        function showSearchSuggestions(books) {
            removeSuggestions();
            if (!books.length || !searchGroup) return;

            const list = document.createElement('div');
            list.className = 'search-suggestions position-absolute w-100 bg-white border shadow-lg z-3 mt-1';
            list.setAttribute('role', 'listbox');

            books.forEach((book) => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'suggestion-item d-block w-100 p-3 border-0 border-bottom text-start bg-white';
                item.setAttribute('role', 'option');

                const title = document.createElement('span');
                title.className = 'd-block fw-bold small text-dark';
                title.textContent = book.Title || 'Tanpa judul';

                const author = document.createElement('span');
                author.className = 'd-block text-muted';
                author.style.fontSize = '0.75rem';
                author.textContent = book.Author || 'Penulis tidak diketahui';

                item.append(title, author);
                item.addEventListener('click', () => {
                    searchInput.value = book.Title || '';
                    removeSuggestions();
                    searchForm?.requestSubmit();
                });
                list.appendChild(item);
            });

            searchGroup.appendChild(list);
        }

        document.addEventListener('click', (event) => {
            if (!event.target.closest('.search-input-group')) removeSuggestions();
        });
    })();
</script>
<?= $this->endSection() ?>
