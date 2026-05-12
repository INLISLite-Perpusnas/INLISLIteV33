<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/opac_style.css') ?>">
<style>
    /* Global Styles */
    body {
        background-color: #f8f9fa;
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding-top: 140px;
        padding-bottom: 80px;
        border-bottom-left-radius: 40px;
        border-bottom-right-radius: 40px;
        margin-bottom: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .hero-section p.lead {
        color: #d1e0ff;
    }

    /* Carousel sebagai background hero */
    .hero-bg-carousel {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 0;
    }

    .hero-bg-carousel .carousel-inner,
    .hero-bg-carousel .carousel-item {
        height: 100%;
    }

    .hero-bg-carousel .carousel-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    .hero-bg-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(20, 45, 100, 0.65);
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    /* Glass Search & Recommendation Card */
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        backdrop-filter: blur(10px);
    }

    .custom-input {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 12px 15px;
    }

    .custom-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(42, 82, 152, 0.25);
        border-color: #2a5298;
    }

    .btn-primary-custom {
        background: #2a5298;
        border: none;
        border-radius: 10px;
        padding: 12px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary-custom:hover {
        background: #1e3c72;
        transform: translateY(-2px);
    }

    /* Sidebar Styles */
    .sidebar-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        background: #ffffff;
    }

    .sidebar-card .card-header {
        /* Gradasi biru senada dengan hero section agar tulisan putih terbaca */
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border-bottom: none;
        padding: 15px 20px;
        border-radius: 16px 16px 0 0;
    }

    .sidebar-card .card-title {
        color: #ffffff;
        /* Mengubah tulisan menjadi putih */
        font-size: 1.1rem;
        font-weight: 600;
    }

    .sidebar-card .card-title i {
        color: #ffffff;
        /* Mengubah ikon menjadi putih */
    }

    .stat-list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px dashed #f0f0f0;
    }

    .stat-list-item:last-child {
        border-bottom: none;
    }

    .stat-list-item a {
        color: #555;
        transition: color 0.3s;
        font-size: 0.9rem;
    }

    .stat-list-item a:hover {
        color: #2a5298;
        font-weight: 500;
    }

    .stat-badge {
        background: #edf2f7;
        color: #4a5568;
        border-radius: 20px;
        padding: 4px 10px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Catalog Cards */
    .catalog-card {
        border: none;
        border-radius: 16px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        background: #ffffff;
        height: 100%;
        overflow: hidden;
    }

    .catalog-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .book-cover-wrapper {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .book-cover {
        width: 100%;
        height: 180px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .catalog-card:hover .book-cover {
        transform: scale(1.05);
    }

    .book-overlay {
        background: rgba(42, 82, 152, 0.8);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .catalog-card:hover .book-overlay {
        opacity: 1;
    }

    .book-meta {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 6px;
    }

    .book-meta i {
        width: 16px;
        color: #a0aec0;
    }

    .badge-floating {
        position: absolute;
        top: 10px;
        left: -5px;
        z-index: 10;
        box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="hero-section">
    <?php if (!empty($opac_banners)): ?>
        <div id="opacBannerCarousel" class="carousel slide hero-bg-carousel" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-inner">
                <?php foreach ($opac_banners as $i => $b): ?>
                    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                        <img src="<?= base_url('uploads/banner/' . esc($b->file_cover)) ?>" alt="Banner <?= $i + 1 ?>">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($opac_banners) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#opacBannerCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#opacBannerCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            <?php endif; ?>
        </div>
        <div class="hero-bg-overlay"></div>
    <?php endif; ?>

    <div class="container hero-content">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <h1 class="display-5 fw-bold mb-3">
                        <i class="fas fa-book-reader me-3"></i>
                        Katalog Perpustakaan
                    </h1>
                    <p class="lead">Temukan referensi, buku, dan koleksi literatur dengan mudah dan cepat.</p>
                </div>

                <div class="glass-card">
                    <form method="GET" action="<?= base_url('opac') ?>" id="recommendationForm" class="mb-4 pb-4 border-bottom">
                        <?= csrf_field() ?>
                        <div class="row align-items-center g-3">
                            <div class="col-md-3">
                                <h6 class="mb-0 text-dark fw-bold">
                                    <i class="fas fa-magic text-warning me-2"></i>Rekomendasi
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control custom-input" name="member_no"
                                    placeholder="Nomor Anggota..."
                                    value="<?= esc($member_no ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-warning w-100 fw-bold" style="border-radius: 10px;">
                                    Cari
                                </button>
                            </div>
                        </div>
                        <?php if (!empty($member_no)): ?>
                            <div class="mt-3 text-center">
                                <a href="<?= base_url('opac') ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                    <i class="fas fa-times me-1"></i>Reset Rekomendasi
                                </a>
                            </div>
                        <?php endif; ?>
                    </form>

                    <form method="GET" action="<?= base_url('opac') ?>">
                        <?= csrf_field() ?>
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <select class="form-select custom-input" name="search_by">
                                    <option value="" <?= ($search_by ?? '') == '' ? 'selected' : '' ?>>Semua Kriteria</option>
                                    <option value="Title" <?= ($search_by ?? '') == 'Title' ? 'selected' : '' ?>>Judul</option>
                                    <option value="Author" <?= ($search_by ?? '') == 'Author' ? 'selected' : '' ?>>Pengarang</option>
                                    <option value="Subject" <?= ($search_by ?? '') == 'Subject' ? 'selected' : '' ?>>Subjek</option>
                                    <option value="Publisher" <?= ($search_by ?? '') == 'Publisher' ? 'selected' : '' ?>>Penerbit</option>
                                    <option value="ISBN" <?= ($search_by ?? '') == 'ISBN' ? 'selected' : '' ?>>ISBN</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control custom-input" name="search"
                                    placeholder="Masukkan kata kunci pencarian..." value="<?= esc($search ?? '') ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary-custom w-100 text-white">
                                    <i class="fas fa-search me-1"></i>Cari
                                </button>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <input type="text" class="form-control custom-input form-control-sm" name="Author" placeholder="Filter Pengarang..." value="<?= esc(request()->getVar('Author')) ?>">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control custom-input form-control-sm" name="Publisher" placeholder="Filter Penerbit..." value="<?= esc(request()->getVar('Publisher')) ?>">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control custom-input form-control-sm" name="Subject" placeholder="Filter Subjek..." value="<?= esc(request()->getVar('Subject')) ?>">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control custom-input form-control-sm" name="PublishYear" placeholder="Filter Tahun..." value="<?= esc(request()->getVar('PublishYear')) ?>">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content-section mb-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 sidebar-section">
                <div class="sidebar-container position-sticky" style="top: 20px;">

                    <div class="card sidebar-card">
                        <div class="card-header d-flex align-items-center">
                            <h5 class="card-title mb-0"><i class="fas fa-chart-pie me-2"></i>Statistik Koleksi</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="me-3">
                                    <i class="fas fa-books fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 fw-bold text-dark"><?= isset($total_records) ? number_format($total_records) : '0' ?></h4>
                                    <small class="text-muted">Total Katalog</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($publisher_counts) && !empty($publisher_counts)): ?>
                        <div class="card sidebar-card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="fas fa-building me-2"></i>Penerbit Teratas</h5>
                            </div>
                            <div class="card-body pt-2">
                                <div class="stat-list">
                                    <?php
                                    arsort($publisher_counts);
                                    $top_publishers = array_slice($publisher_counts, 0, 5, true);
                                    foreach ($top_publishers as $publisher => $count):
                                        if (!empty(trim($publisher))):
                                    ?>
                                            <div class="stat-list-item">
                                                <a href="<?= esc(buildFilterUrl('Publisher', $publisher)) ?>" class="text-decoration-none text-truncate pe-2">
                                                    <?= esc($publisher) ?>
                                                </a>
                                                <span class="stat-badge"><?= $count ?></span>
                                            </div>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($author_counts) && !empty($author_counts)): ?>
                        <div class="card sidebar-card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="fas fa-user-edit me-2"></i>Pengarang Teratas</h5>
                            </div>
                            <div class="card-body pt-2">
                                <div class="stat-list">
                                    <?php
                                    arsort($author_counts);
                                    $top_authors = array_slice($author_counts, 0, 5, true);
                                    foreach ($top_authors as $author => $count):
                                        if (!empty(trim($author))):
                                    ?>
                                            <div class="stat-list-item">
                                                <a href="<?= esc(buildFilterUrl('Author', $author)) ?>" class="text-decoration-none text-truncate pe-2">
                                                    <?= esc($author) ?>
                                                </a>
                                                <span class="stat-badge"><?= $count ?></span>
                                            </div>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($subject_counts) && !empty($subject_counts)): ?>
                        <div class="card sidebar-card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="fas fa-tags me-2"></i>Subjek Populer</h5>
                            </div>
                            <div class="card-body pt-2">
                                <div class="stat-list">
                                    <?php
                                    arsort($subject_counts);
                                    $top_subjects = array_slice($subject_counts, 0, 5, true);
                                    foreach ($top_subjects as $subject => $count):
                                        if (!empty(trim($subject))):
                                    ?>
                                            <div class="stat-list-item">
                                                <a href="<?= esc(buildFilterUrl('Subject', $subject)) ?>" class="text-decoration-none text-truncate pe-2">
                                                    <?= esc($subject) ?>
                                                </a>
                                                <span class="stat-badge"><?= $count ?></span>
                                            </div>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($year_counts) && !empty($year_counts)): ?>
                        <div class="card sidebar-card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="fas fa-calendar-alt me-2"></i>Tahun Terbit</h5>
                            </div>
                            <div class="card-body pt-2">
                                <div class="stat-list">
                                    <?php
                                    arsort($year_counts);
                                    $top_years = array_slice($year_counts, 0, 5, true);
                                    foreach ($top_years as $year => $count):
                                        if (!empty(trim($year)) && $year != '1970'):
                                    ?>
                                            <div class="stat-list-item">
                                                <a href="<?= esc(buildFilterUrl('PublishYear', $year)) ?>" class="text-decoration-none text-truncate pe-2">
                                                    <?= esc($year) ?>
                                                </a>
                                                <span class="stat-badge"><?= $count ?></span>
                                            </div>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($publish_location_counts) && !empty($publish_location_counts)): ?>
                        <div class="card sidebar-card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="fas fa-map-marker-alt me-2"></i>Kota Terbit</h5>
                            </div>
                            <div class="card-body pt-2">
                                <div class="stat-list">
                                    <?php
                                    arsort($publish_location_counts);
                                    $top_locations = array_slice($publish_location_counts, 0, 8, true);
                                    foreach ($top_locations as $location => $count):
                                        if (!empty(trim($location))):
                                    ?>
                                            <div class="stat-list-item">
                                                <a href="<?= esc(buildFilterUrl('PublishLocation', $location)) ?>" class="text-decoration-none text-truncate pe-2">
                                                    <?= esc($location) ?>
                                                </a>
                                                <span class="stat-badge"><?= $count ?></span>
                                            </div>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-9 catalog-section">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <h4 class="mb-0 fw-bold text-dark">
                        <?= !empty($member_no) ? '<i class="fas fa-star text-warning me-2"></i>Rekomendasi Untuk Anda' : '<i class="fas fa-stream text-primary me-2"></i>Koleksi Terbaru' ?>
                    </h4>
                </div>

                <div class="row g-4">
                    <?php
                    $displayCatalogs = (!empty($member_no) && isset($recommendations)) ? $recommendations : ($catalogs ?? []);
                    ?>

                    <?php if (!empty($displayCatalogs)): ?>
                        <?php foreach ($displayCatalogs as $catalog): ?>
                            <div class="col-md-6">
                                <div class="card catalog-card p-3">
                                    <div class="row g-0 h-100">

                                        <div class="col-4 position-relative">
                                            <?php if (!empty($member_no)): ?>
                                                <span class="badge bg-<?= $is_cold_start ? 'warning text-dark' : 'success' ?> badge-floating">
                                                    <i class="fas <?= $is_cold_start ? 'fa-fire' : 'fa-magic' ?> me-1"></i>
                                                    <?= $is_cold_start ? 'Populer' : 'Cocok' ?>
                                                </span>
                                            <?php elseif (isset($catalog->ISDRM) && $catalog->ISDRM == 1): ?>
                                                <span class="badge bg-info text-white badge-floating">
                                                    <i class="fas fa-globe me-1"></i>E-Book
                                                </span>
                                            <?php endif; ?>

                                            <div class="book-cover-wrapper h-100">
                                                <?php
                                                $coverURL = !empty($member_no) ? ($catalog['CoverURL'] ?? '') : ($catalog->CoverURL ?? '');
                                                $title = !empty($member_no) ? ($catalog['Title'] ?? 'Book') : ($catalog->Title ?? 'Book');
                                                $coverPath = base_url('uploads/katalog/' . ($coverURL ?: 'default-cover.jpg'));
                                                $defaultCover = base_url('assets/img/default-cover.png');
                                                ?>
                                                <img src="<?= $coverPath ?>" alt="Cover <?= esc($title) ?>" class="book-cover" onerror="this.src='<?= $defaultCover ?>'">
                                                <div class="book-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                                    <?php $bookID = !empty($member_no) ? ($catalog['ID'] ?? 0) : ($catalog->ID ?? 0); ?>
                                                    <a href="<?= base_url('opac/detail/' . $bookID) ?>" class="btn btn-light btn-sm rounded-pill fw-bold">
                                                        <i class="fas fa-eye me-1"></i>Lihat
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-8 ps-3 d-flex flex-column">
                                            <?php
                                            if (!empty($member_no)) {
                                                $bookTitle = $catalog['Title'] ?? 'Tanpa Judul';
                                                $bookAuthor = $catalog['Author'] ?? '-';
                                                $bookPublisher = $catalog['Publisher'] ?? '-';
                                                $bookYear = $catalog['PublishYear'] ?? '-';
                                                $bookSubject = $catalog['Subject'] ?? '';
                                            } else {
                                                $bookTitle = $catalog->Title ?? 'Tanpa Judul';
                                                $bookAuthor = $catalog->Author ?? '-';
                                                $bookPublisher = $catalog->Publisher ?? '-';
                                                $bookYear = $catalog->PublishYear ?? '-';
                                                $bookSubject = $catalog->Subject ?? '';
                                            }
                                            ?>

                                            <h5 class="card-title text-dark fw-bold mb-2" style="font-size: 1.05rem; line-height: 1.4;">
                                                <a href="<?= base_url('opac/detail/' . $bookID) ?>" class="text-decoration-none text-dark">
                                                    <?= esc(substr($bookTitle, 0, 65)) ?><?= strlen($bookTitle) > 65 ? '...' : '' ?>
                                                </a>
                                            </h5>

                                            <div class="book-meta mt-1">
                                                <span class="d-block mb-1 text-truncate" title="<?= esc($bookAuthor) ?>">
                                                    <i class="fas fa-user-edit"></i> <?= esc($bookAuthor) ?>
                                                </span>
                                                <span class="d-block mb-1 text-truncate" title="<?= esc($bookPublisher) ?>">
                                                    <i class="fas fa-building"></i> <?= esc($bookPublisher) ?>
                                                </span>
                                                <span class="d-block">
                                                    <i class="fas fa-calendar-alt"></i> <?= esc($bookYear) ?>
                                                </span>
                                            </div>

                                            <div class="mt-auto pt-2 border-top">
                                                <?php if (!empty($bookSubject)): ?>
                                                    <span class="badge bg-light text-secondary border text-truncate d-inline-block" style="max-width: 100%;">
                                                        <?= esc($bookSubject) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-secondary border">Umum</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="text-center py-5 bg-white rounded-3 shadow-sm">
                                <div class="mb-4">
                                    <i class="fas fa-search-minus fa-4x text-muted opacity-50"></i>
                                </div>
                                <h4 class="text-dark fw-bold">
                                    <?php if (!empty($member_no)): ?>
                                        Tidak ada rekomendasi untuk anggota ini
                                    <?php elseif (isset($search) && $search): ?>
                                        Koleksi tidak ditemukan
                                    <?php else: ?>
                                        Mulai temukan koleksi
                                    <?php endif; ?>
                                </h4>
                                <p class="text-muted mb-4 px-3">
                                    <?php if (!empty($member_no)): ?>
                                        Anggota mungkin belum memiliki riwayat peminjaman yang cukup untuk menampilkan rekomendasi personal.
                                    <?php elseif (isset($search) && $search): ?>
                                        Coba gunakan kata kunci yang lebih umum atau periksa ejaan Anda.
                                    <?php endif; ?>
                                </p>
                                <a href="<?= base_url('opac') ?>" class="btn btn-primary-custom text-white px-4">
                                    <i class="fas fa-sync-alt me-2"></i>Muat Ulang Katalog
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12 d-flex flex-column align-items-center">
                <?php if (empty($member_no) && !empty($pager)): ?>
                    <div class="custom-pagination">
                        <?php
                        // ✅ Tangani dua kemungkinan: objek (non-cache) atau string HTML (cache hit)
                        if (is_string($pager)) {
                            echo $pager;
                        } else {
                            echo $pager->links('default', 'opac_pagination');
                        }
                        ?>
                    </div>
                    <div class="text-muted mt-2">
                        <small>
                            <i class="fas fa-list text-primary me-1"></i>
                            Total <strong><?= number_format($total_records ?? 0) ?></strong> koleksi ditemukan
                        </small>
                    </div>
                <?php endif; ?>

                <?php if (isset($execution_time)): ?>
                    <div class="text-muted mt-3">
                        <small>
                            <i class="fas fa-bolt text-warning me-1"></i>
                            Waktu muat: <strong><?= number_format($execution_time, 4) ?></strong> detik
                        </small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput && !searchInput.value) {
            searchInput.focus();
        }
    });
</script>
<?= $this->endSection() ?>