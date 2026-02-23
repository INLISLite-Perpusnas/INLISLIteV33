<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>

<div class="container py-5" style="padding-top: 80px !important; padding-bottom: 40px !important;">
    <!-- Page Header -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="text-center">
                <h1 class="display-5 fw-bold text-primary mb-3">
                    <i class="fas fa-list me-3"></i>
                    Browse Katalog
                </h1>
                <p class="lead text-muted">
                    Jelajahi koleksi perpustakaan berdasarkan huruf awal
                </p>
            </div>
        </div>
    </div>

    <!-- Browse Options -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>
                        Pilih Kategori Browse
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="bg-primary text-white p-3 rounded">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <h5 class="mb-0"><?= rand(500, 2000) ?></h5>
                                <small>Total Pengarang</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="bg-success text-white p-3 rounded">
                                <i class="fas fa-books fa-2x mb-2"></i>
                                <h5 class="mb-0"><?= rand(1000, 5000) ?></h5>
                                <small>Total Judul</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="bg-info text-white p-3 rounded">
                                <i class="fas fa-tags fa-2x mb-2"></i>
                                <h5 class="mb-0"><?= rand(200, 800) ?></h5>
                                <small>Total Subjek</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="bg-warning text-white p-3 rounded">
                                <i class="fas fa-globe fa-2x mb-2"></i>
                                <h5 class="mb-0"><?= rand(5, 15) ?></h5>
                                <small>Bahasa</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="<?= base_url('opac/browse?type=author&letter=' . ($letter ?? 'A')) ?>" 
                               class="btn <?= ($browse_type ?? '') == 'author' ? 'btn-primary' : 'btn-outline-primary' ?> btn-lg w-100">
                                <i class="fas fa-user fa-2x mb-2 d-block"></i>
                                <strong>Pengarang</strong><br>
                                <small>Browse berdasarkan nama pengarang</small>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="<?= base_url('opac/browse?type=title&letter=' . ($letter ?? 'A')) ?>" 
                               class="btn <?= ($browse_type ?? '') == 'title' ? 'btn-success' : 'btn-outline-success' ?> btn-lg w-100">
                                <i class="fas fa-book fa-2x mb-2 d-block"></i>
                                <strong>Judul</strong><br>
                                <small>Browse berdasarkan judul buku</small>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="<?= base_url('opac/browse?type=subject&letter=' . ($letter ?? 'A')) ?>" 
                               class="btn <?= ($browse_type ?? '') == 'subject' ? 'btn-info' : 'btn-outline-info' ?> btn-lg w-100">
                                <i class="fas fa-tags fa-2x mb-2 d-block"></i>
                                <strong>Subjek</strong><br>
                                <small>Browse berdasarkan subjek</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($browse_type)): ?>
        <!-- Alphabet Navigation -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-sort-alpha-down me-2"></i>
                            Pilih Huruf Awal
                            <?php
                            $typeNames = [
                                'author' => 'Pengarang',
                                'title' => 'Judul', 
                                'subject' => 'Subjek'
                            ];
                            ?>
                            <span class="text-primary">(<?= $typeNames[$browse_type] ?? $browse_type ?>)</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alphabet-nav text-center">
                            <?php foreach ($alphabet ?? range('A', 'Z') as $char): ?>
                                <a href="<?= base_url('opac/browse?type=' . $browse_type . '&letter=' . $char) ?>" 
                                   class="browse-letter <?= ($letter ?? 'A') == $char ? 'active' : '' ?>">
                                    <?= $char ?>
                                </a>
                            <?php endforeach; ?>
                            
                            <!-- Numbers -->
                            <div class="mt-2">
                                <?php for ($i = 0; $i <= 9; $i++): ?>
                                    <a href="<?= base_url('opac/browse?type=' . $browse_type . '&letter=' . $i) ?>" 
                                       class="browse-letter <?= ($letter ?? '') == $i ? 'active' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Browse Results -->
        <div class="row">
            <div class="col-12">
                <?php if (!empty($catalogs)): ?>
                    <!-- Results Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="text-primary">
                            <i class="fas fa-list-ul me-2"></i>
                            <?= ucfirst($typeNames[$browse_type] ?? $browse_type) ?> dimulai dengan "<?= esc($letter) ?>"
                            <span class="badge bg-primary"><?= count($catalogs) ?> ditemukan</span>
                        </h3>
                        
                        <div class="btn-group">
                            <button class="btn btn-outline-secondary dropdown-toggle" 
                                    type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-eye me-1"></i>Tampilan
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="switchView('grid')">
                                        <i class="fas fa-th me-2"></i>Grid
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="switchView('list')">
                                        <i class="fas fa-list me-2"></i>List
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="switchView('table')">
                                        <i class="fas fa-table me-2"></i>Tabel
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Grid View (Default) -->
                    <div id="gridView" class="view-container">
                        <div class="row">
                            <?php foreach ($catalogs as $catalog): ?>
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="card catalog-card h-100">
                                        <div class="card-header bg-<?= $browse_type == 'author' ? 'primary' : ($browse_type == 'title' ? 'success' : 'info') ?> text-white">
                                            <h6 class="card-title mb-0">
                                                <i class="fas fa-<?= $browse_type == 'author' ? 'user' : ($browse_type == 'title' ? 'book' : 'tag') ?> me-2"></i>
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
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <a href="<?= base_url('opac/detail/' . $catalog->ID) ?>" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- List View -->
                    <div id="listView" class="view-container" style="display: none;">
                        <?php foreach ($catalogs as $catalog): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h5 class="mb-1 text-primary">
                                                <?= esc($catalog->Title ?? 'Tanpa Judul') ?>
                                            </h5>
                                            <p class="mb-1">
                                                <strong>Pengarang:</strong> <?= esc($catalog->Author ?? 'N/A') ?> | 
                                                <strong>Penerbit:</strong> <?= esc($catalog->Publisher ?? 'N/A') ?> | 
                                                <strong>Tahun:</strong> <?= esc($catalog->PublishYear ?? 'N/A') ?>
                                            </p>
                                            <?php if (!empty($catalog->Subject)): ?>
                                                <small class="text-muted">
                                                    <i class="fas fa-tags me-1"></i>
                                                    <?= esc($catalog->Subject) ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <span class="badge bg-secondary mb-2">
                                                <?= esc($catalog->ControlNumber ?? 'N/A') ?>
                                            </span><br>
                                            <a href="<?= base_url('opac/detail/' . $catalog->ID) ?>" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Table View -->
                    <div id="tableView" class="view-container" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Control Number</th>
                                        <th>Judul</th>
                                        <th>Pengarang</th>
                                        <th>Penerbit</th>
                                        <th>Tahun</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($catalogs as $index => $catalog): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= esc($catalog->ControlNumber ?? 'N/A') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong><?= esc(substr($catalog->Title ?? 'Tanpa Judul', 0, 50)) ?></strong>
                                                <?= strlen($catalog->Title ?? '') > 50 ? '...' : '' ?>
                                            </td>
                                            <td><?= esc($catalog->Author ?? 'N/A') ?></td>
                                            <td><?= esc($catalog->Publisher ?? 'N/A') ?></td>
                                            <td><?= esc($catalog->PublishYear ?? 'N/A') ?></td>
                                            <td>
                                                <a href="<?= base_url('opac/detail/' . $catalog->ID) ?>" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Export Options -->
                    <div class="text-center mt-4">
                        <div class="btn-group">
                            <button class="btn btn-success" onclick="exportBrowseResults('excel')">
                                <i class="fas fa-file-excel me-2"></i>Export Excel
                            </button>
                            <button class="btn btn-info" onclick="exportBrowseResults('csv')">
                                <i class="fas fa-file-csv me-2"></i>Export CSV
                            </button>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- No Results -->
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">
                            Tidak ada <?= $typeNames[$browse_type] ?? $browse_type ?> yang dimulai dengan "<?= esc($letter) ?>"
                        </h4>
                        <p class="text-muted mb-4">
                            Coba pilih huruf yang lain atau ubah kategori browse
                        </p>
                        
                        <div class="btn-group">
                            <a href="<?= base_url('opac/browse?type=' . $browse_type . '&letter=A') ?>" 
                               class="btn btn-primary">
                                <i class="fas fa-refresh me-2"></i>Reset ke A
                            </a>
                            <a href="<?= base_url('opac/browse') ?>" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Pilih Kategori Lain
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>




<script>
// View switching functionality
function switchView(viewType) {
    // Hide all views
    document.querySelectorAll('.view-container').forEach(container => {
        container.style.display = 'none';
    });
    
    // Show selected view
    const targetView = document.getElementById(viewType + 'View');
    if (targetView) {
        targetView.style.display = 'block';
    }
    
    // Save preference (removed localStorage due to restrictions)
    showToast(`Tampilan diubah ke ${viewType}`, 'success');
}

// Export browse results
function exportBrowseResults(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('format', format);
    params.set('export', 'browse_results');
    
    const url = `<?= base_url('opac/export') ?>?${params.toString()}`;
    window.open(url, '_blank');
    
    showToast(`Export ${format.toUpperCase()} dimulai...`, 'info');
}

// Toast notification function
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 350px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}

// Keyboard navigation for alphabet
document.addEventListener('keydown', function(e) {
    if (e.altKey && e.key.match(/[a-zA-Z0-9]/)) {
        const letter = e.key.toUpperCase();
        const currentParams = new URLSearchParams(window.location.search);
        const browseType = currentParams.get('type') || 'author';
        
        window.location.href = `<?= base_url('opac/browse') ?>?type=${browseType}&letter=${letter}`;
    }
});

// Highlight matching characters in results
document.addEventListener('DOMContentLoaded', function() {
    const currentLetter = '<?= $letter ?? '' ?>';
    const browseType = '<?= $browse_type ?? '' ?>';
    
    if (currentLetter && browseType) {
        highlightMatchingText(currentLetter, browseType);
    }
});

function highlightMatchingText(letter, type) {
    const fieldMap = {
        'author': 'Author',
        'title': 'Title',
        'subject': 'Subject'
    };
    
    document.querySelectorAll('.catalog-card').forEach(card => {
        const textElements = card.querySelectorAll('h5, p, small');
        textElements.forEach(element => {
            if (element.textContent.toLowerCase().startsWith(letter.toLowerCase())) {
                const text = element.textContent;
                const highlighted = `<span class="bg-warning">${text.charAt(0)}</span>${text.slice(1)}`;
                element.innerHTML = highlighted;
            }
        });
    });
}

// Quick jump to letter function
function quickJumpToLetter() {
    const letter = prompt('Masukkan huruf yang ingin dicari (A-Z, 0-9):');
    if (letter && letter.match(/[a-zA-Z0-9]/)) {
        const currentParams = new URLSearchParams(window.location.search);
        const browseType = currentParams.get('type') || 'author';
        
        window.location.href = `<?= base_url('opac/browse') ?>?type=${browseType}&letter=${letter.toUpperCase()}`;
    }
}

// Add quick jump button to alphabet nav
document.addEventListener('DOMContentLoaded', function() {
    const alphabetNav = document.querySelector('.alphabet-nav');
    if (alphabetNav) {
        const quickJumpButton = document.createElement('button');
        quickJumpButton.className = 'btn btn-outline-primary btn-sm ms-3';
        quickJumpButton.innerHTML = '<i class="fas fa-search me-1"></i>Jump';
        quickJumpButton.onclick = quickJumpToLetter;
        alphabetNav.appendChild(quickJumpButton);
    }
});
</script>
<?= $this->endSection() ?>