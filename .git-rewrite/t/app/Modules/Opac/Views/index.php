<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/opac_style.css') ?>">

</head>

<body>
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="text-center mb-5">
                        <h1 class="display-4 fw-bold mb-3">
                            <i class="fas fa-book-reader me-3"></i>
                            Selamat Datang di OPAC
                        </h1>
                        <p class="lead">Online Public Access Catalog - Temukan koleksi perpustakaan dengan mudah</p>
                    </div>
                    <div class="recommendation-search mb-4">
                        <form method="GET" action="<?= base_url('opac') ?>" id="recommendationForm">
                            <?= csrf_field() ?>
                            <div class="card shadow-sm">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="fas fa-magic me-2"></i>Rekomendasi Personal
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="member_no"
                                                placeholder="Masukkan nomor anggota untuk rekomendasi personal..."
                                                value="<?= esc($member_no ?? '') ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-warning w-100">
                                                <i class="fas fa-magic me-2"></i>Dapatkan Rekomendasi
                                            </button>
                                        </div>
                                    </div>
                                    <?php if (!empty($member_no)): ?>
                                        <div class="mt-2">
                                            <a href="<?= base_url('opac') ?>" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-times me-1"></i>Kembali ke Katalog Umum
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="search-box">
                        <form method="GET" action="<?= base_url('opac') ?>">
                            <?= csrf_field() ?>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <select class="form-select" name="search_by">
                                        <option value="" <?= ($search_by ?? '') == '' ? 'selected' : '' ?>>Semua Field</option>
                                        <option value="Title" <?= ($search_by ?? '') == 'Title' ? 'selected' : '' ?>>Judul</option>
                                        <option value="Author" <?= ($search_by ?? '') == 'Author' ? 'selected' : '' ?>>Pengarang</option>
                                        <option value="Subject" <?= ($search_by ?? '') == 'Subject' ? 'selected' : '' ?>>Subjek</option>
                                        <option value="Publisher" <?= ($search_by ?? '') == 'Publisher' ? 'selected' : '' ?>>Penerbit</option>
                                        <option value="ISBN" <?= ($search_by ?? '') == 'ISBN' ? 'selected' : '' ?>>ISBN</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control form-control-lg" name="search"
                                        placeholder="Masukkan kata kunci pencarian..." value="<?= esc($search ?? '') ?>">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-search me-2"></i>Cari
                                    </button>
                                </div>
                            </div>
                            <div class="row g-2 mt-2">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="Author" placeholder="Filter Pengarang..." value="<?= esc(request()->getVar('Author')) ?>">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="Publisher" placeholder="Filter Penerbit..." value="<?= esc(request()->getVar('Publisher')) ?>">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="Subject" placeholder="Filter Subjek..." value="<?= esc(request()->getVar('Subject')) ?>">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="PublishYear" placeholder="Filter Tahun Terbit..." value="<?= esc(request()->getVar('PublishYear')) ?>">
                                </div>
                            </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content-section">
        <div class="main-container">
            <div class="row">
                <div class="col-lg-3 sidebar-section">
                    <div class="sidebar-container">
                        <div class="card sidebar-card">
                            <div class="card-header bg-gradient-primary">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chart-pie me-2"></i>Lebih Lengkap
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-books text-primary"></i>
                                    </div>
                                    <div class="stat-info">
                                        <h6 class="stat-value"><?= isset($total_records) ? number_format($total_records) : '0' ?></h6>
                                        <small class="text-muted">Total Koleksi</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (isset($publisher_counts) && !empty($publisher_counts)): ?>
                            <div class="card sidebar-card">
                                <div class="card-header bg-gradient-success">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-building me-2"></i>Penerbit Teratas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="stat-list">
                                        <?php
                                        arsort($publisher_counts);
                                        $top_publishers = array_slice($publisher_counts, 0, 5, true);
                                        foreach ($top_publishers as $publisher => $count):
                                            if (!empty(trim($publisher))):
                                        ?>
                                                <div class="stat-list-item">
                                                    <a href="<?= esc(buildFilterUrl('Publisher', $publisher)) ?>" class="stat-name text-decoration-none">
                                                        <?= esc(substr($publisher, 0, 25)) ?><?= strlen($publisher) > 25 ? '...' : '' ?>
                                                    </a>
                                                    <span class="badge bg-info text-white"><?= $count ?></span>
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
                                <div class="card-header bg-gradient-info">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-user-edit me-2"></i>Pengarang Teratas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="stat-list">
                                        <?php
                                        arsort($author_counts);
                                        $top_authors = array_slice($author_counts, 0, 5, true);
                                        foreach ($top_authors as $author => $count):
                                            if (!empty(trim($author))):
                                        ?>
                                                <div class="stat-list-item">
                                                    <a href="<?= esc(buildFilterUrl('Author', $author)) ?>" class="stat-name text-decoration-none">
                                                        <?= esc(substr($author, 0, 25)) ?><?= strlen($author) > 25 ? '...' : '' ?>
                                                    </a>

                                                    <span class="badge bg-info text-white"><?= $count ?></span>
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
                                <div class="card-header bg-gradient-warning">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-tags me-2"></i>Subjek Teratas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="stat-list">
                                        <?php
                                        arsort($subject_counts);
                                        $top_subjects = array_slice($subject_counts, 0, 5, true);
                                        foreach ($top_subjects as $subject => $count):
                                            if (!empty(trim($subject))):
                                        ?>
                                                <div class="stat-list-item">
                                                    <a href="<?= esc(buildFilterUrl('Subject', $subject)) ?>" class="stat-name text-decoration-none">
                                                        <?= esc(substr($subject, 0, 25)) ?><?= strlen($subject) > 25 ? '...' : '' ?>
                                                    </a>

                                                    <span class="badge bg-info text-white"><?= $count ?></span>
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
                                <div class="card-header" style="background: linear-gradient(45deg, #fd7e14, #e85d04); color: white;">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-calendar-alt me-2"></i>Tahun Terbit
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="stat-list">
                                        <?php
                                        arsort($year_counts);
                                        $top_years = array_slice($year_counts, 0, 5, true);
                                        foreach ($top_years as $year => $count):
                                            if (!empty(trim($year)) && $year != '1970'):
                                        ?>
                                                <div class="stat-list-item">
                                                    <a href="<?= esc(buildFilterUrl('PublishYear', $year)) ?>" class="stat-name text-decoration-none">
                                                        <?= esc($year) ?>
                                                    </a>

                                                    <span class="badge bg-info text-white"><?= $count ?></span>
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
                                <div class="card-header" style="background: linear-gradient(45deg, #fd7e14, #e85d04); color: white;">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-calendar-alt me-2"></i>KatalogRuasModel Terbit
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="stat-list">
                                        <?php
                                        arsort($publish_location_counts);
                                        $top_locations = array_slice($publish_location_counts, 0, 8, true);
                                        foreach ($top_locations as $location => $count):
                                            if (!empty(trim($location))):
                                        ?>
                                                <div class="stat-list-item">
                                                    <a href="<?= esc(buildFilterUrl('PublishLocation', $location)) ?>" class="stat-name text-decoration-none">
                                                        <?= esc(substr($location, 0, 20)) ?><?= strlen($location) > 20 ? '...' : '' ?>
                                                    </a>

                                                    <span class="badge bg-info text-white"><?= $count ?></span>
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
                    <div class="text-center mb-5">
                        <h3><i class="fas fa-star me-2"></i>Koleksi Terbaru</h3>
                        <p class="text-muted">Temukan buku-buku terbaru dalam koleksi kami</p>
                    </div>

                    <div class="row">
                        <?php
                        // Use recommendations if available, otherwise use regular catalogs
                        $displayCatalogs = [];
                        if (!empty($member_no) && isset($recommendations)) {
                            $displayCatalogs = $recommendations;
                        } else {
                            $displayCatalogs = $catalogs ?? [];
                        }
                        ?>

                        <?php if (!empty($displayCatalogs)): ?>
                            <?php foreach ($displayCatalogs as $catalog): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card catalog-card h-100">
                                        <div class="card-header <?= !empty($member_no) ? 'bg-success text-white' : 'bg-success text-white' ?>">
                                            <h6 class="card-title mb-0">
                                                <i class="fas fa-book me-2"></i>
                                                <?php if (!empty($member_no)): ?>
                                                    <?= $is_cold_start ? 'Populer' : 'Rekomendasi' ?>
                                                    <?php if ($is_cold_start && isset($catalog['LoanCount'])): ?>
                                                        <span class="badge bg-light text-dark ms-2"><?= $catalog['LoanCount'] ?> peminjaman</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <?= esc($catalog->ControlNumber ?? 'N/A') ?>
                                                <?php endif; ?>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="book-cover-container position-relative">
                                                        <?php
                                                        $coverURL = '';
                                                        $title = '';
                                                        if (!empty($member_no)) {
                                                            $coverURL = $catalog['CoverURL'] ?? '';
                                                            $title = $catalog['Title'] ?? 'Book';
                                                        } else {
                                                            $coverURL = $catalog->CoverURL ?? '';
                                                            $title = $catalog->Title ?? 'Book';
                                                        }

                                                        $coverPath = base_url('uploads/katalog/' . ($coverURL ?: 'default-cover.jpg'));
                                                        $defaultCover = base_url('assets/img/default-cover.png');
                                                        ?>
                                                        <img src="<?= $coverPath ?>"
                                                            style="max-width: 100px; max-height: 150px; width: 100%; object-fit: cover;"
                                                            alt="Cover <?= esc($title) ?>"
                                                            class="book-cover img-fluid rounded shadow-sm"
                                                            onerror="this.src='<?= $defaultCover ?>'">
                                                        <div class="book-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center rounded"
                                                            style="background: rgba(0,0,0,0.7); opacity: 0; transition: opacity 0.3s ease;">
                                                            <i class="fas fa-eye text-white fa-2x"></i>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-8">
                                                    <?php
                                                    if (!empty($member_no)) {
                                                        $bookTitle = $catalog['Title'] ?? 'Tanpa Judul';
                                                        $bookPublisher = $catalog['Publisher'] ?? 'N/A';
                                                        $bookAuthor = $catalog['Author'] ?? 'N/A';
                                                        $bookYear = $catalog['PublishYear'] ?? 'N/A';
                                                        $bookISBN = $catalog['ISBN'] ?? '';
                                                        $bookSubject = $catalog['Subject'] ?? '';
                                                        $bookID = $catalog['ID'] ?? 0;
                                                    } else {
                                                        $bookTitle = $catalog->Title ?? 'Tanpa Judul';
                                                        $bookAuthor = $catalog->Author ?? 'N/A';
                                                        $bookPublisher = $catalog->Publisher ?? 'N/A';
                                                        $bookYear = $catalog->PublishYear ?? 'N/A';
                                                        $bookSubject = $catalog->Subject ?? '';
                                                        $bookISBN = $catalog->ISBN ?? '';
                                                        $bookID = $catalog->ID ?? 0;
                                                    }
                                                    ?>

                                                    <h5 class="card-title text-primary mb-2" style="font-size: 1rem; line-height: 1.3;">
                                                        <?= esc(substr($bookTitle, 0, 50)) ?>
                                                        <?= strlen($bookTitle) > 50 ? '...' : '' ?>
                                                    </h5>

                                                    <div class="mb-2">
                                                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                            <i class="fas fa-user me-1"></i>
                                                            <strong>Pengarang:</strong><br>
                                                            <?= esc(substr($bookAuthor, 0, 35)) ?>
                                                            <?= strlen($bookAuthor) > 35 ? '...' : '' ?>
                                                        </small>
                                                    </div>

                                                    <div class="mb-2">
                                                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                            <i class="fas fa-building me-1"></i>
                                                            <strong>Penerbit:</strong><br>
                                                            <?= esc(substr($bookPublisher, 0, 30)) ?>
                                                            <?= strlen($bookPublisher) > 30 ? '...' : '' ?>
                                                        </small>
                                                    </div>

                                                    <div class="mb-2">
                                                        <small class="text-muted" style="font-size: 0.75rem;">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            <strong>Tahun:</strong> <?= esc($bookYear) ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-3">
                                                <?php if (!empty($bookSubject)): ?>
                                                    <div class="mb-2">
                                                        <span class="badge bg-secondary" style="font-size: 0.7rem;">
                                                            <i class="fas fa-tag me-1"></i>
                                                            <?= esc(substr($bookSubject, 0, 25)) ?>
                                                            <?= strlen($bookSubject) > 25 ? '...' : '' ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (empty($member_no) && !empty($bookISBN)): ?>
                                                    <div class="mb-2">
                                                        <small class="text-info" style="font-size: 0.7rem;">
                                                            <i class="fas fa-barcode me-1"></i>
                                                            ISBN: <?= esc($bookISBN) ?>
                                                        </small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="card-footer bg-transparent">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a href="<?= base_url('opac/detail/' . $bookID) ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>Detail
                                                </a>
                                                <div class="btn-group">
                                                    <?php if (!empty($member_no)): ?>
                                                        <span class="badge <?= $is_cold_start ? 'bg-warning text-dark' : 'bg-success' ?>">
                                                            <i class="fas fa-magic me-1"></i><?= $is_cold_start ? 'Populer' : 'Rekomendasi' ?>
                                                        </span>
                                                    <?php elseif (($catalog->ISDRM ==1)): ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>Tersedia Online
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h4 class="text-muted">
                                        <?php if (!empty($member_no)): ?>
                                            Tidak ada rekomendasi yang ditemukan untuk anggota ini
                                        <?php elseif (isset($search) && $search): ?>
                                            Tidak ada hasil yang ditemukan
                                        <?php else: ?>
                                            Gunakan form pencarian untuk menemukan koleksi
                                        <?php endif; ?>
                                    </h4>
                                    <?php if (!empty($member_no)): ?>
                                        <p class="text-muted mb-3">
                                            Anggota mungkin belum terdaftar atau belum memiliki riwayat peminjaman
                                        </p>
                                    <?php elseif (isset($search) && $search): ?>
                                        <p class="text-muted mb-3">
                                            Coba gunakan kata kunci yang berbeda atau
                                            <a href="<?= base_url('opac/search') ?>">pencarian lanjutan</a>
                                        </p>
                                    <?php endif; ?>
                                    <a href="<?= base_url('opac') ?>" class="btn btn-primary">
                                        <i class="fas fa-home me-2"></i>Kembali ke Beranda
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
             <section class="content-section">
        <div class="main-container">
           <?php if (empty($member_no) && isset($pager) && !empty($pager)): ?>
                <div class="row">
                    <div class="col-12">
                        <nav aria-label="Pagination">
                           <?= $pager ?>
                        </nav>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($execution_time)): ?>
            <div class="text-center text-muted mt-4">
                <small>
                    <i class="fas fa-stopwatch me-1"></i>
                    Halaman ini dimuat dalam <strong><?= number_format($execution_time, 4) ?></strong> detik.
                </small>
            </div>
            <?php endif; ?>

        </div>
    </section>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-focus search input
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput && !searchInput.value) {
                searchInput.focus();
            }

            // Add click handlers for stat items
            document.querySelectorAll('.stat-list-item').forEach(item => {
                item.addEventListener('click', function() {
                    const statName = this.querySelector('.stat-name').textContent.trim();
                    console.log('Clicked on:', statName);
                });
            });
        });

        // Search suggestions
        document.querySelector('input[name="search"]').addEventListener('input', function(e) {
            const query = e.target.value;
            if (query.length > 2) {
                console.log('Search suggestion for:', query);
            }
        });
    </script>
<?= $this->endSection() ?>