<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="py-3">
    <div class="container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('opac') ?>" style="color: #028548;">Beranda</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail Katalog</li>
        </ol>
    </div>
</nav>

<!-- Main Content -->
<section class="py-4">
    <div class="container">
        <div class="row">
            <!-- Book Cover & Quick Info -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                    <div class="card-body text-center">
                        <?php 
                        $coverPath = base_url('uploads/katalog/' . ($catalog['CoverURL'] ?? 'default-cover.jpg'));
                        $defaultCover = base_url('uploads/katalog/1726759290_e8330f6fbe57a88d3108.png');
                        ?>
                        <img src="<?= $coverPath ?>" 
                             alt="Cover <?= esc($catalog['Title']) ?>" 
                             class="img-fluid mb-3 book-cover-detail"
                             style="max-height: 300px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);"
                             onerror="this.src='<?= $defaultCover ?>'">
                        
                        <h4 class="mb-3" style="color: #028548;"><?= esc($catalog['Title']) ?></h4>
                        
                        <!-- Quick Actions -->
                        <div class="d-grid gap-2">
                            <?php if (isset($ID)): ?>
                            <a href="<?= base_url('opac/read/' . $ID) ?>" class="btn btn-success btn-lg">
                                <i class="fas fa-book-open me-2"></i>Baca Online
                            </a>
                            <?php endif; ?>
                            
                            <button class="btn btn-outline-primary" onclick="printCatalog()">
                                <i class="fas fa-print me-2"></i>Cetak Detail
                            </button>
                            
                            <button class="btn btn-outline-info" onclick="shareCatalog()">
                                <i class="fas fa-share-alt me-2"></i>Bagikan
                            </button>
                        </div>
                        
                        <!-- Availability Status -->
                        <div class="mt-3">
                            <?php if (!empty($roweksemplar) || !empty($roweksemplar_drm)): ?>
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-check-circle me-1"></i>Tersedia
                            </span>
                            <?php else: ?>
                            <span class="badge bg-secondary fs-6">
                                <i class="fas fa-times-circle me-1"></i>Tidak Tersedia
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Book Details -->
            <div class="col-lg-8">
                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs mb-4" id="detailTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                            <i class="fas fa-info-circle me-1"></i>Ringkasan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="marc-tab" data-bs-toggle="tab" data-bs-target="#marc" type="button" role="tab">
                            <i class="fas fa-database me-1"></i>Data MARC
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="copies-tab" data-bs-toggle="tab" data-bs-target="#copies" type="button" role="tab">
                            <i class="fas fa-books me-1"></i>Eksemplar
                        </button>
                    </li>
                </ul>
                
                <!-- Tab Content -->
                <div class="tab-content" id="detailTabsContent">
                    <!-- Overview Tab -->
                    <div class="tab-pane fade show active" id="overview" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header" style="background: linear-gradient(135deg, #028548 0%, #20c997 100%); color: white;">
                                <h5 class="mb-0"><i class="fas fa-book me-2"></i>Informasi Katalog</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td width="30%" class="fw-bold text-muted">Judul:</td>
                                                <td><?= esc($catalog['Title'] ?? 'N/A') ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">Pengarang:</td>
                                                <td><?= esc($catalog['Author'] ?? 'N/A') ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">Penerbit:</td>
                                                <td><?= esc($catalog['Publisher'] ?? 'N/A') ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">Tahun Terbit:</td>
                                                <td><?= esc($catalog['PublishYear'] ?? 'N/A') ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">Tempat Terbit:</td>
                                                <td><?= esc($catalog['PublishLocation'] ?? 'N/A') ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td width="30%" class="fw-bold text-muted">ISBN:</td>
                                                <td><?= esc($catalog['ISBN'] ?? 'N/A') ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">Subjek:</td>
                                                <td><?= esc($catalog['Subject'] ?? 'N/A') ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">Bahasa:</td>
                                                <td><?= esc($catalog['Language'] ?? 'N/A') ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">Klasifikasi:</td>
                                                <td><?= esc($catalog['Classification'] ?? 'N/A') ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">Halaman:</td>
                                                <td><?= esc($catalog['PhysicalDescription'] ?? 'N/A') ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                
                                <?php if (!empty($catalog['Notes'])): ?>
                                <div class="mt-3">
                                    <h6 class="fw-bold text-muted">Catatan:</h6>
                                    <p class="text-muted"><?= esc($catalog['Notes']) ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- MARC Tab -->
                    <div class="tab-pane fade" id="marc" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%); color: white;">
                                <h5 class="mb-0"><i class="fas fa-database me-2"></i>Data MARC21</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($marc)): ?>
                                <div class="marc-container">
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Format MARC21 (Machine-Readable Cataloging) - Total <?= count($marc) ?> field
                                        </small>
                                    </div>
                                    
                                    <!-- Search MARC -->
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            <input type="text" class="form-control" id="marcSearch" placeholder="Cari tag atau nilai MARC...">
                                        </div>
                                    </div>
                                    
                                    <!-- MARC Table -->
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover" id="marcTable">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th width="10%">Tag</th>
                                                    <th width="8%">Ind1</th>
                                                    <th width="8%">Ind2</th>
                                                    <th width="64%">Nilai</th>
                                                    <th width="10%" class="text-center">Urutan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($marc as $field): ?>
                                                <tr class="marc-row">
                                                    <td>
                                                        <span class="badge bg-primary marc-tag"><?= esc($field->Tag) ?></span>
                                                    </td>
                                                    <td>
                                                        <code class="marc-indicator"><?= esc($field->Indicator1 ?: '_') ?></code>
                                                    </td>
                                                    <td>
                                                        <code class="marc-indicator"><?= esc($field->Indicator2 ?: '_') ?></code>
                                                    </td>
                                                    <td>
                                                        <span class="marc-value"><?= esc($field->Value) ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-secondary"><?= esc($field->Sequence) ?></span>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- MARC Info -->
                                    <div class="mt-3 p-3 bg-light rounded">
                                        <h6 class="fw-bold text-primary mb-2">
                                            <i class="fas fa-lightbulb me-1"></i>Penjelasan Field MARC21:
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul class="list-unstyled small">
                                                    <li><strong>001:</strong> Control Number</li>
                                                    <li><strong>005:</strong> Date and Time of Latest Transaction</li>
                                                    <li><strong>020:</strong> ISBN</li>
                                                    <li><strong>100:</strong> Main Entry - Personal Name</li>
                                                    <li><strong>245:</strong> Title Statement</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-unstyled small">
                                                    <li><strong>250:</strong> Edition Statement</li>
                                                    <li><strong>260:</strong> Publication Information</li>
                                                    <li><strong>300:</strong> Physical Description</li>
                                                    <li><strong>650:</strong> Subject</li>
                                                    <li><strong>700:</strong> Added Entry - Personal Name</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-database fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Data MARC Tidak Tersedia</h5>
                                    <p class="text-muted mb-0">Belum ada data MARC21 untuk katalog ini</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Copies Tab -->
                    <div class="tab-pane fade" id="copies" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header" style="background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%); color: white;">
                                <h5 class="mb-0"><i class="fas fa-books me-2"></i>Daftar Eksemplar</h5>
                            </div>
                            <div class="card-body">
                                <!-- Physical Copies -->
                                <?php if (!empty($roweksemplar)): ?>
                                <h6 class="mb-3"><i class="fas fa-book me-2 text-primary"></i>Koleksi Fisik</h6>
                                <div class="table-responsive mb-4">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Barcode</th>
                                                <th>Call Number</th>
                                                <th>Lokasi</th>
                                                <th>Status</th>
                                                <th>Jenis</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($roweksemplar as $eksemplar): ?>
                                            <tr>
                                                <td><code><?= esc($eksemplar['NomorBarcode']) ?></code></td>
                                                <td><?= esc($eksemplar['CallNumber']) ?></td>
                                                <td><?= esc($eksemplar['LocationName']) ?></td>
                                                <td>
                                                    <span class="badge bg-success"><?= esc($eksemplar['StatusName']) ?></span>
                                                </td>
                                                <td><?= esc($eksemplar['RuleName']) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Digital Copies -->
                                <?php if (!empty($roweksemplar_drm)): ?>
                                <h6 class="mb-3"><i class="fas fa-tablet-alt me-2 text-success"></i>Koleksi Digital (DRM)</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Barcode</th>
                                                <th>Call Number</th>
                                                <th>Lokasi</th>
                                                <th>Status</th>
                                                <th>Jenis</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($roweksemplar_drm as $eksemplar): ?>
                                            <tr>
                                                <td><code><?= esc($eksemplar['NomorBarcode']) ?></code></td>
                                                <td><?= esc($eksemplar['CallNumber']) ?></td>
                                                <td><?= esc($eksemplar['LocationName']) ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?= esc($eksemplar['StatusName']) ?></span>
                                                </td>
                                                <td><?= esc($eksemplar['RuleName']) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (empty($roweksemplar) && empty($roweksemplar_drm)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Tidak Ada Eksemplar</h5>
                                    <p class="text-muted mb-0">Belum ada eksemplar yang tersedia untuk katalog ini</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Back Button -->
        <div class="row mt-4">
            <div class="col-12">
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* Detail Page Styles */
.book-cover-detail {
    transition: transform 0.3s ease;
}

.book-cover-detail:hover {
    transform: scale(1.05);
}

/* MARC Styles */
.marc-container {
    max-height: 600px;
    overflow-y: auto;
}

.marc-tag {
    font-family: 'Courier New', monospace;
    font-weight: bold;
}

.marc-indicator {
    background-color: #f8f9fa;
    padding: 2px 4px;
    border-radius: 3px;
    font-size: 0.85em;
    color: #6c757d;
}

.marc-value {
    word-break: break-word;
    line-height: 1.4;
}

.marc-row:hover {
    background-color: #f8f9fa;
}

/* Tab Styles */
.nav-tabs .nav-link {
    color: #6c757d;
    border: 1px solid transparent;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link.active {
    color: #028548;
    border-color: #028548 #028548 transparent;
    font-weight: 600;
}

.nav-tabs .nav-link:hover {
    color: #028548;
    border-color: #e9ecef #e9ecef #dee2e6;
}

/* Table Styles */
.table-hover tbody tr:hover {
    background-color: rgba(2, 133, 72, 0.05);
}

/* Badge Styles */
.badge {
    font-size: 0.75em;
}

/* Responsive */
@media (max-width: 768px) {
    .sticky-top {
        position: relative !important;
        top: auto !important;
    }
    
    .nav-tabs {
        flex-wrap: nowrap;
        overflow-x: auto;
    }
    
    .nav-tabs .nav-item {
        white-space: nowrap;
    }
}

/* Print Styles */
@media print {
    .btn, .nav-tabs, .breadcrumb {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// MARC Search functionality
document.getElementById('marcSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#marcTable tbody tr');
    
    rows.forEach(row => {
        const tag = row.querySelector('.marc-tag').textContent.toLowerCase();
        const value = row.querySelector('.marc-value').textContent.toLowerCase();
        
        if (tag.includes(searchTerm) || value.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Print function
function printCatalog() {
    window.print();
}

// Share function
function shareCatalog() {
    if (navigator.share) {
        navigator.share({
            title: '<?= esc($catalog['Title']) ?>',
            text: 'Lihat detail katalog: <?= esc($catalog['Title']) ?>',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link telah disalin ke clipboard!');
        });
    }
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    // Focus on MARC search when tab is activated
    document.getElementById('marc-tab').addEventListener('shown.bs.tab', function() {
        document.getElementById('marcSearch').focus();
    });
    
    // Auto-resize MARC container based on content
    const marcContainer = document.querySelector('.marc-container');
    if (marcContainer) {
        const tableHeight = document.querySelector('#marcTable').offsetHeight;
        if (tableHeight < 400) {
            marcContainer.style.maxHeight = 'none';
        }
    }
});

// Keyboard navigation for tabs
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey) {
        switch(e.key) {
            case '1':
                e.preventDefault();
                document.getElementById('overview-tab').click();
                break;
            case '2':
                e.preventDefault();
                document.getElementById('marc-tab').click();
                break;
            case '3':
                e.preventDefault();
                document.getElementById('copies-tab').click();
                break;
        }
    }
});
</script>
<?= $this->endSection() ?>