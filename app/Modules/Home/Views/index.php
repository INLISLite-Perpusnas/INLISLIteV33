<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<?php if (!empty($banners[0]->file_cover)): ?>
<link rel="preload" as="image" href="<?= base_url('uploads/banner/' . rawurlencode($banners[0]->file_cover)) ?>" fetchpriority="high">
<?php endif; ?>
<style>
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
        height: 240px;
        object-fit: contain;
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
        background: #ffffff;
        color: var(--brand-500);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .popular-slider-nav:hover {
        background: var(--brand-500);
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

    /* Koleksi Populer - highlight + list, mengikuti gaya section Berita */
    .popular-highlight-card {
        height: 380px;
    }

    .popular-highlight-bg {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, #1b3878, #0f172a);
    }

    .popular-highlight-cover {
        position: relative;
        z-index: 1;
        max-height: 86%;
        max-width: 58%;
        object-fit: contain;
        border-radius: 0.5rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    }

    .popular-list-cover {
        height: 120px;
        width: 90px;
        object-fit: contain;
        background-color: #e2e8f0;
        border-radius: 0.5rem;
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

<?php if (!empty($popular_books)): ?>
    <section class="py-5 section-tone-2" id="koleksi-populer">
        <div class="container my-4">
            <h3 class="fw-bold mb-4 d-flex align-items-center section-title">
                <div class="bg-brand rounded-pill me-3" style="width: 5px; height: 30px;"></div>
                Koleksi Populer
            </h3>

            <?php
            $defaultCover  = base_url('assets/img/default-cover.webp');
            $highlightBook = $popular_books[0];
            $highlightThumb = get_catalog_thumb_url($highlightBook->CoverURL ?: '', 400, 600);
            ?>
            <div class="row g-4">
                <div class="col-lg-6">
                    <a href="<?= base_url('opac/detail/' . $highlightBook->ID) ?>" class="card border-0 rounded-2xl overflow-hidden text-decoration-none shadow-sm hover-card position-relative text-white h-100 popular-highlight-card">
                        <div class="popular-highlight-bg"></div>
                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                            <img src="<?= $highlightThumb ?>"
                                 class="popular-highlight-cover"
                                 alt="<?= esc($highlightBook->Title) ?>"
                                 width="400"
                                 height="600"
                                 loading="lazy"
                                 decoding="async"
                                 onerror="this.onerror=null; this.src='<?= $defaultCover ?>';">
                        </div>
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to top, rgba(15,23,42,0.9) 0%, rgba(15,23,42,0.15) 55%, rgba(15,23,42,0.35) 100%); z-index: 1;"></div>

                        <div class="position-absolute bottom-0 start-0 p-4 w-100" style="z-index: 2;">
                            <span class="badge bg-danger mb-2 py-1 px-3 rounded-pill"><i class="fa-solid fa-fire me-1"></i>Paling Populer</span>
                            <h4 class="fw-bold mb-2 line-clamp-2"><?= esc($highlightBook->Title) ?></h4>
                            <p class="small text-light text-opacity-75 mb-0 line-clamp-2"><?= esc($highlightBook->Author ?: 'Anonim') ?></p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-6">
                    <div class="d-flex flex-column h-100 justify-content-between">
                        <?php for ($i = 1; $i < count($popular_books) && $i <= 3; $i++):
                            $book     = $popular_books[$i];
                            $thumbUrl = get_catalog_thumb_url($book->CoverURL ?: '', 200, 340);
                        ?>
                            <a href="<?= base_url('opac/detail/' . $book->ID) ?>" class="d-flex gap-3 text-decoration-none text-dark hover-card p-3 rounded-xl border border-light mb-3 bg-white shadow-sm h-100 align-items-center">
                                <div class="flex-shrink-0">
                                    <img src="<?= $thumbUrl ?>" onerror="this.onerror=null; this.src='<?= $defaultCover ?>';" class="popular-list-cover" alt="Sampul <?= esc($book->Title) ?>" width="90" height="120" loading="lazy" decoding="async">
                                </div>
                                <div>
                                    <span class="badge bg-danger-subtle text-danger fw-semibold small mb-1"><i class="fa-solid fa-fire me-1"></i>Populer</span>
                                    <h4 class="fs-6 fw-bold mb-1 line-clamp-2 text-dark" style="line-height: 1.4;"><?= esc($book->Title) ?></h4>
                                    <span class="text-secondary small d-block"><?= esc($book->Author ?: 'Anonim') ?></span>
                                </div>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (!empty($sering_dipinjam_books)): ?>
    <section class="py-5 section-tone-1" id="koleksi-sering-dipinjam">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h3 class="fw-bold mb-1 section-title"><i class="fa-solid fa-hand-holding-heart text-primary me-2"></i>Sering Dipinjam</h3>
                    <p class="text-secondary mb-0">Buku dengan jumlah peminjaman terbanyak.</p>
                </div>
            </div>

            <div class="popular-slider-wrapper">
                <button type="button" class="popular-slider-nav prev" id="seringDipinjamSliderPrev" aria-label="Sebelumnya">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <div class="popular-slider" id="seringDipinjamSlider">
                    <?php foreach ($sering_dipinjam_books as $i => $book): ?>
                        <?php
                        $defaultCover = base_url('assets/img/default-cover.webp');
                        $thumbUrl     = get_catalog_thumb_url($book->CoverURL ?: '', 200, 340);
                        ?>
                        <div class="popular-card-item">
                            <div class="card h-100 border-0 shadow-sm hover-card rounded-xl overflow-hidden bg-white position-relative">
                                <span class="badge bg-primary position-absolute" style="top: 10px; left: 10px; z-index: 2;">
                                    <i class="fa-solid fa-hand-holding-heart me-1"></i>Sering Dipinjam
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

                <button type="button" class="popular-slider-nav next" id="seringDipinjamSliderNext" aria-label="Berikutnya">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
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

<section class="py-5 section-tone-1" id="koleksi">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h3 class="fw-bold mb-1 section-title">Koleksi Terbaru</h3>
                <p class="text-secondary mb-0">Buku-buku yang baru saja ditambahkan.</p>
            </div>
        </div>

        <div class="row g-4">
            <?php if (!empty($featured_books)): ?>
                <?php foreach (array_slice($featured_books, 0, 5) as $i => $book): ?>
                    <?php
                    $defaultCover = base_url('assets/img/default-cover.webp');
                    $thumbUrl     = get_catalog_thumb_url($book->CoverURL ?: '', 200, 340);
                    ?>
                    <div class="col-6 col-md-4 col-lg-2-4" style="width: 20%; min-width: 160px;">
                        <div class="card h-100 border-0 shadow-sm hover-card rounded-xl overflow-hidden bg-white">
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
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fa-solid fa-book-open fa-3x text-secondary opacity-50 mb-3"></i>
                    <p class="text-secondary">Koleksi buku masih belum tersedia</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-5">
            <a href="<?= base_url('opac') ?>" class="btn btn-brand rounded-pill px-5 py-2 fw-semibold shadow-sm">
                Lihat Semua Koleksi <i class="fa-solid fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<section class="py-5 section-tone-2" id="berita">
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

        initCardSlider('seringDipinjamSlider', 'seringDipinjamSliderPrev', 'seringDipinjamSliderNext');
        initCardSlider('seringDibacaSlider', 'seringDibacaSliderPrev', 'seringDibacaSliderNext');

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
