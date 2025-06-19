<?= $this->extend('App\Views\layout\opac\layout'); ?>



<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h1 class="display-4 fw-bold mb-3" style="color: #028548;">
                        <i class="fas fa-book-reader me-3"></i>
                        Selamat Datang di OPAC
                    </h1>
                    <p class="lead" style="color: #028548;">Online Public Access Catalog - Temukan koleksi perpustakaan
                        dengan mudah</p>
                </div>

                <!-- Search Box -->
                <div class="search-box">
                    <form method="GET" action="<?= base_url('opac') ?>">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <select class="form-select" name="search_by">
                                    <option value="">Semua Field</option>
                                    <option value="Title" <?= ($search_by ?? '') == 'Title' ? 'selected' : '' ?>>Judul
                                    </option>
                                    <option value="Author" <?= ($search_by ?? '') == 'Author' ? 'selected' : '' ?>>
                                        Pengarang</option>
                                    <option value="Subject" <?= ($search_by ?? '') == 'Subject' ? 'selected' : '' ?>>
                                        Subjek</option>
                                    <option value="Publisher"
                                        <?= ($search_by ?? '') == 'Publisher' ? 'selected' : '' ?>>Penerbit</option>
                                    <option value="ISBN" <?= ($search_by ?? '') == 'ISBN' ? 'selected' : '' ?>>ISBN
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control form-control-lg" name="search"
                                    placeholder="Masukkan kata kunci pencarian..." value="<?= $search ?? '' ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-search me-2"></i>Cari
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h3><i class="fas fa-rocket me-2"></i>Akses Cepat</h3>
            <p class="text-muted">Jelajahi koleksi dengan cara yang berbeda</p>
        </div>

        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-search-plus fa-3x text-primary mb-3"></i>
                        <h5>Pencarian Lanjutan</h5>
                        <p class="text-muted">Cari dengan filter yang lebih detail</p>
                        <a href="<?= base_url('opac/search') ?>" class="btn btn-primary">
                            Mulai Pencarian
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-list fa-3x text-success mb-3"></i>
                        <h5>Browse Katalog</h5>
                        <p class="text-muted">Jelajahi berdasarkan huruf awal</p>
                        <a href="<?= base_url('opac/browse') ?>" class="btn btn-success">
                            Mulai Browse
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-chart-bar fa-3x text-info mb-3"></i>
                        <h5>Statistik</h5>
                        <p class="text-muted">Lihat statistik koleksi perpustakaan</p>
                        <a href="<?= base_url('opac/statistics') ?>" class="btn btn-info">
                            Lihat Statistik
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-download fa-3x text-warning mb-3"></i>
                        <h5>Export Data</h5>
                        <p class="text-muted">Download data dalam format Excel/CSV</p>
                        <div class="btn-group">
                            <a href="<?= base_url('opac/export?format=excel') ?>"
                                class="btn btn-warning btn-sm">Excel</a>
                            <a href="<?= base_url('opac/export?format=csv') ?>" class="btn btn-warning btn-sm">CSV</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Catalog Results -->
<!-- Catalog Results -->
<section class="py-5 bg-light">
    <div class="container">
        <?php if (isset($search) && $search): ?>
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>
                        <i class="fas fa-search me-2"></i>
                        Hasil Pencarian: "<?= esc($search) ?>"
                        <span class="badge badge-custom"><?= count($catalogs ?? []) ?> dari <?= $total_records ?></span>
                    </h3>
                    <div class="btn-group">
                        <a href="<?= base_url('opac/export?search=' . urlencode($search)) ?>"
                            class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-excel me-1"></i>Export Excel
                        </a>
                        <a href="<?= base_url('opac/search') ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-search-plus me-1"></i>Pencarian Lanjutan
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="text-center mb-5">
            <h3><i class="fas fa-star me-2"></i>Koleksi Terbaru</h3>
            <p class="text-muted">Temukan buku-buku terbaru dalam koleksi kami</p>
        </div>
        <?php endif; ?>

        <!-- Catalog Cards -->
        <div class="row">
            <?php if (!empty($catalogs)): ?>
            <?php foreach ($catalogs as $catalog): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card catalog-card h-100">
                    <div class="card-header bg-success text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-book me-2"></i>
                            <?= esc($catalog->ControlNumber ?? 'N/A') ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Book Cover Column -->
                            <div class="col-4">
                                <div class="book-cover-container position-relative">
                                    <?php 
                                    $coverPath = base_url('uploads/katalog/' . ($catalog->CoverURL ?? 'default-cover.jpg'));
                                    $defaultCover = base_url('uploads/katalog/1726759290_e8330f6fbe57a88d3108.png');
                                    ?>
                                    <img src="<?= $coverPath ?>" 
                                         style="max-width: 100px; max-height: 150px; width: 100%; object-fit: cover;"
                                         alt="Cover <?= esc($catalog->Title ?? 'Book') ?>" 
                                         class="book-cover img-fluid rounded shadow-sm"
                                         onerror="this.src='<?= $defaultCover ?>'">
                                    <div class="book-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center rounded"
                                         style="background: rgba(0,0,0,0.7); opacity: 0; transition: opacity 0.3s ease;">
                                        <i class="fas fa-eye text-white fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Book Information Column -->
                            <div class="col-8">
                                <h5 class="card-title text-primary mb-2" style="font-size: 1rem; line-height: 1.3;">
                                    <?= esc(substr($catalog->Title ?? 'Tanpa Judul', 0, 50)) ?>
                                    <?= strlen($catalog->Title ?? '') > 50 ? '...' : '' ?>
                                </h5>

                                <div class="mb-2">
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                                        <i class="fas fa-user me-1"></i>
                                        <strong>Pengarang:</strong><br>
                                        <?= esc(substr($catalog->Author ?? 'N/A', 0, 35)) ?>
                                        <?= strlen($catalog->Author ?? '') > 35 ? '...' : '' ?>
                                    </small>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                                        <i class="fas fa-building me-1"></i>
                                        <strong>Penerbit:</strong><br>
                                        <?= esc(substr($catalog->Publisher ?? 'N/A', 0, 30)) ?>
                                        <?= strlen($catalog->Publisher ?? '') > 30 ? '...' : '' ?>
                                    </small>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <i class="fas fa-calendar me-1"></i>
                                        <strong>Tahun:</strong> <?= esc($catalog->PublishYear ?? 'N/A') ?>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Subject and ISBN Information -->
                        <div class="mt-3">
                            <?php if (!empty($catalog->Subject)): ?>
                            <div class="mb-2">
                                <span class="badge bg-secondary" style="font-size: 0.7rem;">
                                    <i class="fas fa-tag me-1"></i>
                                    <?= esc(substr($catalog->Subject, 0, 25)) ?>
                                    <?= strlen($catalog->Subject) > 25 ? '...' : '' ?>
                                </span>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($catalog->ISBN)): ?>
                            <div class="mb-2">
                                <small class="text-info" style="font-size: 0.7rem;">
                                    <i class="fas fa-barcode me-1"></i>
                                    ISBN: <?= esc($catalog->ISBN) ?>
                                </small>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="<?= base_url('opac/detail/' . $catalog->ID) ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>Detail
                            </a>
                            <div class="btn-group">
                                <?php if ($catalog->IsOPAC ?? false): ?>
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
                        <?= isset($search) && $search ? 'Tidak ada hasil yang ditemukan' : 'Gunakan form pencarian untuk menemukan koleksi' ?>
                    </h4>
                    <?php if (isset($search) && $search): ?>
                    <p class="text-muted mb-3">
                        Coba gunakan kata kunci yang berbeda atau
                        <a href="<?= base_url('opac/search') ?>">pencarian lanjutan</a>
                    </p>
                    <a href="<?= base_url('opac') ?>" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i>Kembali ke Beranda
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
        <div class="row">
            <div class="col-12">
                <nav aria-label="Pagination">
                    <?= $pager->links() ?>
                </nav>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Quick Links -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h3><i class="fas fa-rocket me-2"></i>Akses Cepat</h3>
            <p class="text-muted">Jelajahi koleksi dengan cara yang berbeda</p>
        </div>

        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-search-plus fa-3x text-primary mb-3"></i>
                        <h5>Pencarian Lanjutan</h5>
                        <p class="text-muted">Cari dengan filter yang lebih detail</p>
                        <a href="<?= base_url('opac/search') ?>" class="btn btn-primary">
                            Mulai Pencarian
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-list fa-3x text-success mb-3"></i>
                        <h5>Browse Katalog</h5>
                        <p class="text-muted">Jelajahi berdasarkan huruf awal</p>
                        <a href="<?= base_url('opac/browse') ?>" class="btn btn-success">
                            Mulai Browse
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-chart-bar fa-3x text-info mb-3"></i>
                        <h5>Statistik</h5>
                        <p class="text-muted">Lihat statistik koleksi perpustakaan</p>
                        <a href="<?= base_url('opac/statistics') ?>" class="btn btn-info">
                            Lihat Statistik
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-download fa-3x text-warning mb-3"></i>
                        <h5>Export Data</h5>
                        <p class="text-muted">Download data dalam format Excel/CSV</p>
                        <div class="btn-group">
                            <a href="<?= base_url('opac/export?format=excel') ?>"
                                class="btn btn-warning btn-sm">Excel</a>
                            <a href="<?= base_url('opac/export?format=csv') ?>" class="btn btn-warning btn-sm">CSV</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
<style>
/* Book Cover Hover Effects */
.book-cover-container:hover .book-overlay {
    opacity: 1 !important;
    cursor: pointer;
}

.book-cover {
    transition: transform 0.3s ease;
    border: 1px solid #e0e0e0;
}

.book-cover-container:hover .book-cover {
    transform: scale(1.05);
}

/* Catalog Card Hover Effects */
.catalog-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.catalog-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .book-cover-container {
        margin-bottom: 15px;
    }
    
    .catalog-card .row {
        flex-direction: column;
    }
    
    .catalog-card .col-4,
    .catalog-card .col-8 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .book-cover-container {
        text-align: center;
    }
    
    .book-cover {
        max-width: 120px !important;
        max-height: 180px !important;
    }
}

/* Badge custom styling */
.badge-custom {
    background-color: #028548;
    color: white;
}

/* Card header improvements */
.catalog-card .card-header {
    border-bottom: 2px solid rgba(255,255,255,0.2);
}

/* Book overlay click effect */
.book-cover-container {
    cursor: pointer;
}

.book-cover-container:active .book-cover {
    transform: scale(0.98);
}
</style>
<?= $this->section('scripts') ?>
<script>
// Auto-focus search input
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput && !searchInput.value) {
        searchInput.focus();
    }
});

// Search suggestions (mock data)
document.querySelector('input[name="search"]').addEventListener('input', function(e) {
    const query = e.target.value;
    if (query.length > 2) {
        // Here you can implement auto-suggestions
        console.log('Search suggestion for:', query);
    }
});
</script>
<?= $this->endsection() ?>