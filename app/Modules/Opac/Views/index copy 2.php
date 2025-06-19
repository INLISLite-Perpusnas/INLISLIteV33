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
                        <span class="badge" style="background-color: #028548;"><?= count($catalogs ?? []) ?> dari <?= $total_records ?></span>
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
            <h3><i class="fas fa-star me-2" style="color: #028548;"></i>Koleksi Terbaru</h3>
            <p class="text-muted">Temukan buku-buku terbaru dalam koleksi kami</p>
        </div>
        <?php endif; ?>

        <!-- Enhanced Catalog Cards -->
        <div class="row">
            <?php if (!empty($catalogs)): ?>
            <?php foreach ($catalogs as $catalog): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card catalog-card-enhanced h-100 shadow-sm border-0">
                    <!-- Card Header with Green Theme -->
                    <div class="card-header-enhanced">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-book text-white me-2"></i>
                            <h6 class="card-title mb-0 text-white fw-bold">
                                <!-- <?= esc($catalog->ControlNumber ?? 'N/A') ?> -->
                               
                            </h6>
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        <!-- Book Cover and Info Section -->
                        <div class="row g-0">
                            <!-- Book Cover Column -->
                            <div class="col-4">
                                <div class="book-cover-container">
                                    <?php 
                                    $coverPath = base_url('uploads/katalog/' . ($catalog->CoverURL ?? 'default-cover.jpg'));
                                    $defaultCover = base_url('uploads/katalog/1726759290_e8330f6fbe57a88d3108.png');
                                    ?>
                                    <img src="<?= $coverPath ?>" 
                                      style="max-width: 100px; max-height: 150px;"
                                         alt="Cover <?= esc($catalog->Title ?? 'Book') ?>" 
                                         class="book-cover"
                                         onerror="this.src='<?= $defaultCover ?>'">
                                    <div class="book-overlay">
                                        <i class="fas fa-eye text-white"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Book Info Column -->
                            <div class="col-8">
                                <div class="book-info p-3">
                                    <h5 class="book-title mb-2">
                                        <?= esc(substr($catalog->Title ?? 'Tanpa Judul', 0, 50)) ?>
                                        <?= strlen($catalog->Title ?? '') > 50 ? '...' : '' ?>
                                    </h5>

                                    <div class="book-details mb-3">
                                        <div class="detail-item">
                                            <i class="fas fa-user detail-icon"></i>
                                            <span class="detail-text"><?= esc(substr($catalog->Author ?? 'N/A', 0, 25)) ?><?= strlen($catalog->Author ?? '') > 25 ? '...' : '' ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-building detail-icon"></i>
                                            <span class="detail-text"><?= esc(substr($catalog->Publisher ?? 'N/A', 0, 20)) ?><?= strlen($catalog->Publisher ?? '') > 20 ? '...' : '' ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-calendar detail-icon"></i>
                                            <span class="detail-text"><?= esc($catalog->PublishYear ?? 'N/A') ?></span>
                                        </div>
                                    </div>

                                    <?php if (!empty($catalog->Subject)): ?>
                                    <div class="mb-2">
                                        <span class="subject-badge">
                                            <i class="fas fa-tag me-1"></i>
                                            <?= esc(substr($catalog->Subject, 0, 25)) ?>
                                            <?= strlen($catalog->Subject) > 25 ? '...' : '' ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ISBN Section -->
                        <?php if (!empty($catalog->ISBN)): ?>
                        <div class="isbn-section px-3 py-2">
                            <small class="isbn-text">
                                <i class="fas fa-barcode me-1"></i>
                                ISBN: <?= esc($catalog->ISBN) ?>
                            </small>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Card Footer -->
                    <div class="card-footer-enhanced">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="<?= base_url('opac/detail/' . $catalog->ID) ?>" class="btn btn-detail">
                                <i class="fas fa-eye me-1"></i>Detail
                            </a>
                            <div class="availability-status">
                                <?php if ($catalog->IsOPAC ?? false): ?>
                                <span class="badge-available">
                                    <i class="fas fa-check-circle me-1"></i>Online
                                </span>
                                <?php else: ?>
                                <span class="badge-offline">
                                    <i class="fas fa-building me-1"></i>Perpustakaan
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
                        <a href="<?= base_url('opac/search') ?>" style="color: #028548;">pencarian lanjutan</a>
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
            <h3><i class="fas fa-rocket me-2" style="color: #028548;"></i>Akses Cepat</h3>
            <p class="text-muted">Jelajahi koleksi dengan cara yang berbeda</p>
        </div>

        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card text-center h-100 border-0 shadow-sm quick-link-card">
                    <div class="card-body">
                        <i class="fas fa-search-plus fa-3x mb-3" style="color: #028548;"></i>
                        <h5>Pencarian Lanjutan</h5>
                        <p class="text-muted">Cari dengan filter yang lebih detail</p>
                        <a href="<?= base_url('opac/search') ?>" class="btn btn-outline-primary">
                            Mulai Pencarian
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card text-center h-100 border-0 shadow-sm quick-link-card">
                    <div class="card-body">
                        <i class="fas fa-list fa-3x mb-3" style="color: #20c997;"></i>
                        <h5>Browse Katalog</h5>
                        <p class="text-muted">Jelajahi berdasarkan huruf awal</p>
                        <a href="<?= base_url('opac/browse') ?>" class="btn btn-outline-success">
                            Mulai Browse
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card text-center h-100 border-0 shadow-sm quick-link-card">
                    <div class="card-body">
                        <i class="fas fa-chart-bar fa-3x mb-3" style="color: #17a2b8;"></i>
                        <h5>Statistik</h5>
                        <p class="text-muted">Lihat statistik koleksi perpustakaan</p>
                        <a href="<?= base_url('opac/statistics') ?>" class="btn btn-outline-info">
                            Lihat Statistik
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card text-center h-100 border-0 shadow-sm quick-link-card">
                    <div class="card-body">
                        <i class="fas fa-download fa-3x mb-3" style="color: #ffc107;"></i>
                        <h5>Export Data</h5>
                        <p class="text-muted">Download data dalam format Excel/CSV</p>
                        <div class="btn-group">
                            <a href="<?= base_url('opac/export?format=excel') ?>"
                                class="btn btn-outline-warning btn-sm">Excel</a>
                            <a href="<?= base_url('opac/export?format=csv') ?>" class="btn btn-outline-warning btn-sm">CSV</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* Enhanced Catalog Card Styles */
.catalog-card-enhanced {
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: #fff;
}

.catalog-card-enhanced:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(2, 133, 72, 0.15) !important;
}

.card-header-enhanced {
    background: linear-gradient(135deg, #028548 0%, #20c997 100%);
    padding: 12px 15px;
    border: none;
}

.book-cover-container {
    position: relative;
    height: 180px;
    background: #f8f9fa;
    overflow: hidden;
}

.book-cover {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.book-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(2, 133, 72, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.book-cover-container:hover .book-overlay {
    opacity: 1;
}

.book-cover-container:hover .book-cover {
    transform: scale(1.05);
}

.book-info {
    height: 180px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.book-title {
    color: #2c3e50;
    font-size: 1rem;
    font-weight: 600;
    line-height: 1.3;
    margin-bottom: 10px;
}

.book-details {
    flex-grow: 1;
}

.detail-item {
    display: flex;
    align-items: center;
    margin-bottom: 6px;
    font-size: 0.85rem;
}

.detail-icon {
    color: #028548;
    width: 14px;
    margin-right: 8px;
    font-size: 0.8rem;
}

.detail-text {
    color: #6c757d;
    font-weight: 500;
}

.subject-badge {
    background: linear-gradient(135deg, #e3f2fd 0%, #f0f8ff 100%);
    color: #028548;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    border: 1px solid #e1f5fe;
}

.isbn-section {
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.isbn-text {
    color: #6c757d;
    font-weight: 500;
}

.card-footer-enhanced {
    background: #fff;
    border-top: 1px solid #e9ecef;
    padding: 12px 15px;
}

.btn-detail {
    background: linear-gradient(135deg, #028548 0%, #20c997 100%);
    color: white;
    border: none;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-detail:hover {
    background: linear-gradient(135deg, #026940 0%, #1ba085 100%);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(2, 133, 72, 0.3);
}

.availability-status {
    display: flex;
    align-items: center;
}

.badge-available {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.badge-offline {
    background: linear-gradient(135deg, #6c757d 0%, #adb5bd 100%);
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

/* Quick Link Cards */
.quick-link-card {
    transition: all 0.3s ease;
}

.quick-link-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(2, 133, 72, 0.1) !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .book-cover-container {
        height: 120px;
    }
    
    .book-info {
        height: 120px;
        padding: 15px !important;
    }
    
    .book-title {
        font-size: 0.9rem;
    }
    
    .detail-item {
        font-size: 0.8rem;
        margin-bottom: 4px;
    }
}

@media (max-width: 576px) {
    .catalog-card-enhanced .row.g-0 {
        flex-direction: column;
    }
    
    .catalog-card-enhanced .col-4,
    .catalog-card-enhanced .col-8 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .book-cover-container {
        height: 200px;
    }
    
    .book-info {
        height: auto;
        min-height: 140px;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Auto-focus search input
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput && !searchInput.value) {
        searchInput.focus();
    }
    
    // Add loading effect for book covers
    const bookCovers = document.querySelectorAll('.book-cover');
    bookCovers.forEach(cover => {
        cover.addEventListener('load', function() {
            this.style.opacity = '1';
        });
        
        cover.addEventListener('error', function() {
            this.style.opacity = '1';
            this.classList.add('error-cover');
        });
        
        // Set initial opacity
        cover.style.opacity = '0.7';
        cover.style.transition = 'opacity 0.3s ease';
    });
});

// Search suggestions (mock data)
document.querySelector('input[name="search"]').addEventListener('input', function(e) {
    const query = e.target.value;
    if (query.length > 2) {
        // Here you can implement auto-suggestions
        console.log('Search suggestion for:', query);
    }
});

// Add smooth scrolling for pagination
document.addEventListener('click', function(e) {
    if (e.target.closest('.pagination a')) {
        e.preventDefault();
        const link = e.target.closest('.pagination a');
        const href = link.getAttribute('href');
        
        // Smooth scroll to catalog section
        const catalogSection = document.querySelector('.catalog-card-enhanced').closest('section');
        catalogSection.scrollIntoView({ behavior: 'smooth' });
        
        // Navigate after scroll
        setTimeout(() => {
            window.location.href = href;
        }, 500);
    }
});
</script>
<?= $this->endSection() ?>