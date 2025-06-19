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
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="stats-card">
                    <i class="fas fa-books fa-3x mb-3"></i>
                    <h3><?= number_format($total_records ?? 0) ?></h3>
                    <p class="mb-0">Total Koleksi</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stats-card">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h3><?= isset($search) && $search ? count($catalogs ?? []) : 0 ?></h3>
                    <p class="mb-0">Hasil Pencarian</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stats-card">
                    <i class="fas fa-eye fa-3x mb-3"></i>
                    <h3><?= number_format(rand(1000, 9999)) ?></h3>
                    <p class="mb-0">Pengunjung Hari Ini</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stats-card">
                    <i class="fas fa-download fa-3x mb-3"></i>
                    <h3><?= number_format(rand(100, 999)) ?></h3>
                    <p class="mb-0">Download Bulan Ini</p>
                </div>
            </div>
        </div>
    </div>
</section>

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
                    <div class="card-header bg-primary text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-book me-2"></i>
                            <?= esc($catalog->ControlNumber ?? 'N/A') ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-primary">
                            <?= esc(substr($catalog->Title ?? 'Tanpa Judul', 0, 60)) ?>
                            <?= strlen($catalog->Title ?? '') > 60 ? '...' : '' ?>
                        </h5>

                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>
                                <strong>Pengarang:</strong> <?= esc($catalog->Author ?? 'N/A') ?>
                            </small><br>
                            <small class="text-muted">
                                <i class="fas fa-building me-1"></i>
                                <strong>Penerbit:</strong> <?= esc($catalog->Publisher ?? 'N/A') ?>
                            </small><br>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                <strong>Tahun:</strong> <?= esc($catalog->PublishYear ?? 'N/A') ?>
                            </small>
                        </div>

                        <?php if (!empty($catalog->Subject)): ?>
                        <div class="mb-3">
                            <span class="badge bg-secondary">
                                <i class="fas fa-tag me-1"></i>
                                <?= esc(substr($catalog->Subject, 0, 30)) ?>
                                <?= strlen($catalog->Subject) > 30 ? '...' : '' ?>
                            </span>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($catalog->ISBN)): ?>
                        <div class="mb-3">
                            <small class="text-info">
                                <i class="fas fa-barcode me-1"></i>
                                ISBN: <?= esc($catalog->ISBN) ?>
                            </small>
                        </div>
                        <?php endif; ?>
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
<?= $this->endSection() ?>