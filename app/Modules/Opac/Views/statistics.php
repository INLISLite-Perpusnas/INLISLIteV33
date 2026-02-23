<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>
<section class="hero-section" style="padding-top: 80px !important; padding-bottom: 40px !important;">
<div class="container py-5">
    <!-- Page Header -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="text-center">
                <h1 class="display-5 fw-bold text-primary mb-3">
                    <i class="fas fa-chart-bar me-3"></i>
                    Statistik Katalog
                </h1>
                <p class="lead text-muted">
                    Analisis dan statistik koleksi perpustakaan
                </p>
            </div>
        </div>
    </div>

    <!-- Overview Statistics -->
    <div class="row mb-5">
        <div class="col-md-3 mb-4">
            <div class="card text-center border-0 shadow">
                <div class="card-body bg-primary text-white rounded">
                    <i class="fas fa-books fa-3x mb-3"></i>
                    <h3 class="fw-bold"><?= number_format($total_catalogs ?? 0) ?></h3>
                    <p class="mb-0">Total Katalog</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-center border-0 shadow">
                <div class="card-body bg-success text-white rounded">
                    <i class="fas fa-calendar fa-3x mb-3"></i>
                    <h3 class="fw-bold"><?= count($by_year ?? []) ?></h3>
                    <p class="mb-0">Rentang Tahun</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-center border-0 shadow">
                <div class="card-body bg-info text-white rounded">
                    <i class="fas fa-globe fa-3x mb-3"></i>
                    <h3 class="fw-bold"><?= count($by_language ?? []) ?></h3>
                    <p class="mb-0">Bahasa</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-center border-0 shadow">
                <div class="card-body bg-warning text-white rounded">
                    <i class="fas fa-building fa-3x mb-3"></i>
                    <h3 class="fw-bold"><?= count($by_publisher ?? []) ?></h3>
                    <p class="mb-0">Penerbit Aktif</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Charts -->
    <div class="row">
        <!-- Year Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Distribusi Berdasarkan Tahun Terbit
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($by_year)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tahun</th>
                                        <th>Jumlah</th>
                                        <th>Persentase</th>
                                        <th>Grafik</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $maxCount = max(array_column($by_year, 'total'));
                                    foreach (array_slice($by_year, 0, 15) as $year): 
                                        $percentage = ($year->total / $total_catalogs) * 100;
                                        $barWidth = ($year->total / $maxCount) * 100;
                                    ?>
                                        <tr>
                                            <td><strong><?= esc($year->PublishYear) ?></strong></td>
                                            <td><?= number_format($year->total) ?></td>
                                            <td><?= number_format($percentage, 1) ?>%</td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-primary" 
                                                         style="width: <?= $barWidth ?>%"
                                                         title="<?= $year->total ?> katalog">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if (count($by_year) > 15): ?>
                            <div class="text-center">
                                <button class="btn btn-outline-primary btn-sm" onclick="showAllYears()">
                                    <i class="fas fa-eye me-1"></i>
                                    Lihat Semua (<?= count($by_year) ?> tahun)
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-chart-line fa-3x mb-3"></i>
                            <p>Tidak ada data tahun tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Language Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Distribusi Berdasarkan Bahasa
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($by_language)): ?>
                        <div class="row">
                            <?php 
                            $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                            foreach ($by_language as $index => $language): 
                                $percentage = ($language->total / $total_catalogs) * 100;
                                $color = $colors[$index % count($colors)];
                            ?>
                                <div class="col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold"><?= esc($language->Languages) ?></span>
                                        <span class="badge bg-<?= $color ?>"><?= number_format($language->total) ?></span>
                                    </div>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-<?= $color ?>" 
                                             style="width: <?= $percentage ?>%"
                                             title="<?= number_format($percentage, 1) ?>%">
                                            <?= number_format($percentage, 1) ?>%
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-globe fa-3x mb-3"></i>
                            <p>Tidak ada data bahasa tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Top Publishers -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        Top 10 Penerbit
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($by_publisher)): ?>
                        <div class="row">
                            <?php 
                            $maxPublisherCount = $by_publisher->total ?? 1;
                            foreach ($by_publisher as $index => $publisher): 
                                $percentage = ($publisher->total / $maxPublisherCount) * 100;
                            ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0 text-truncate" title="<?= esc($publisher->Publisher) ?>">
                                                    <span class="badge bg-info me-2">#<?= $index + 1 ?></span>
                                                    <?= esc(substr($publisher->Publisher, 0, 30)) ?>
                                                    <?= strlen($publisher->Publisher) > 30 ? '...' : '' ?>
                                                </h6>
                                                <span class="badge bg-primary"><?= number_format($publisher->total) ?></span>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-info" 
                                                     style="width: <?= $percentage ?>%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-building fa-3x mb-3"></i>
                            <p>Tidak ada data penerbit tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="row">
        <!-- Quick Stats -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Statistik Cepat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border-end">
                                <h4 class="text-primary"><?= number_format(($total_catalogs ?? 0) / max(1, count($by_year ?? []))) ?></h4>
                                <small class="text-muted">Rata-rata per Tahun</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="text-success"><?= !empty($by_year) ? $by_year[0]->PublishYear : 'N/A' ?></h4>
                            <small class="text-muted">Tahun Terbanyak</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-info"><?= !empty($by_language) ? $by_language[0]->Languages : 'N/A' ?></h4>
                            <small class="text-muted">Bahasa Utama</small>
                        </div>
                        <div class="col-6">
                           <h4 class="text-warning">
                            <?= (int)date('Y') - (!empty($by_year) ? (int)min(array_map(fn($item) => $item->PublishYear, $by_year)) : (int)date('Y')) ?>
                            </h4>

                            <small class="text-muted">Rentang Tahun</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-download me-2"></i>
                        Export Statistik
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-success" onclick="exportStatistics('excel')">
                            <i class="fas fa-file-excel me-2"></i>
                            Export ke Excel
                        </button>
                        <button class="btn btn-info" onclick="exportStatistics('csv')">
                            <i class="fas fa-file-csv me-2"></i>
                            Export ke CSV
                        </button>
                        <button class="btn btn-warning" onclick="exportStatistics('pdf')">
                            <i class="fas fa-file-pdf me-2"></i>
                            Export ke PDF
                        </button>
                        <button class="btn btn-primary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>
                            Cetak Laporan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Growth Trend -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-area me-2"></i>
                        Tren Pertumbuhan
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($by_year) && count($by_year) >= 2): ?>
                        <?php 
                        $recentYears = array_slice($by_year, 0, 5);
                        $oldestCount = end($recentYears)->total;
                        $newestCount = $recentYears[0]->total;
                        $growthRate = (($newestCount - $oldestCount) / max(1, $oldestCount)) * 100;
                        ?>
                        
                        <div class="text-center mb-3">
                            <h3 class="<?= $growthRate >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= $growthRate >= 0 ? '+' : '' ?><?= number_format($growthRate, 1) ?>%
                            </h3>
                            <small class="text-muted">Pertumbuhan 5 Tahun Terakhir</small>
                        </div>
                        
                        <div class="mini-chart">
                            <?php foreach (array_reverse($recentYears) as $year): ?>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small><?= $year->PublishYear ?></small>
                                    <small><?= $year->total ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted">
                            <i class="fas fa-chart-area fa-2x mb-2"></i>
                            <p class="mb-0">Butuh minimal 2 tahun data untuk analisis tren</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Insights -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header bg-gradient" style="background: linear-gradient(45deg, #667eea, #764ba2);">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-lightbulb me-2"></i>
                        Insights & Rekomendasi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-chart-line me-2"></i>Analisis Data:</h6>
                            <ul class="list-unstyled">
                                <?php if (!empty($by_year)): ?>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Tahun <?= $by_year[0]->PublishYear ?> memiliki koleksi terbanyak (<?= number_format($by_year[0]->total) ?> item)
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (!empty($by_language)): ?>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Bahasa <?= $by_language[0]->Languages ?> mendominasi koleksi (<?= number_format(($by_language[0]->total / $total_catalogs) * 100, 1) ?>%)
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (!empty($by_publisher)): ?>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <?= $by_publisher[0]->Publisher ?> adalah penerbit terbesar (<?= $by_publisher[0]->total ?> buku)
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-warning"><i class="fas fa-recommendations me-2"></i>Rekomendasi:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-arrow-right text-warning me-2"></i>
                                    Diversifikasi koleksi dari berbagai penerbit
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-arrow-right text-warning me-2"></i>
                                    Tambah koleksi dalam bahasa lain untuk keseimbangan
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-arrow-right text-warning me-2"></i>
                                    Focus pada publikasi tahun terbaru untuk update koleksi
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Export statistics functionality
function exportStatistics(format) {
    const params = new URLSearchParams({
        'export': 'statistics',
        'format': format
    });
    
    const url = `<?= base_url('opac/export') ?>?${params.toString()}`;
    window.open(url, '_blank');
    
    showToast(`Export statistik ${format.toUpperCase()} dimulai...`, 'info');
}

// Show all years functionality
function showAllYears() {
    // This would typically load more data via AJAX
    // For now, we'll just show a message
    showToast('Fitur ini akan menampilkan semua tahun. Implementasi via AJAX.', 'info');
}

// Toast notification
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

// Print styles
window.addEventListener('beforeprint', function() {
    document.querySelectorAll('.btn').forEach(el => el.style.display = 'none');
    document.body.classList.add('printing');
});

window.addEventListener('afterprint', function() {
    document.querySelectorAll('.btn').forEach(el => el.style.display = '');
    document.body.classList.remove('printing');
});

// Auto-refresh statistics (every 5 minutes)
setInterval(function() {
    // This would typically refresh the statistics via AJAX
    console.log('Auto-refresh statistics...');
}, 300000);

// Interactive charts (using Chart.js if available)
document.addEventListener('DOMContentLoaded', function() {
    // Year distribution chart
    const yearData = <?= json_encode(array_slice($by_year ?? [], 0, 10)) ?>;
    if (yearData.length > 0 && typeof Chart !== 'undefined') {
        createYearChart(yearData);
    }
    
    // Language pie chart
    const languageData = <?= json_encode($by_language ?? []) ?>;
    if (languageData.length > 0 && typeof Chart !== 'undefined') {
        createLanguageChart(languageData);
    }
});

function createYearChart(data) {
    const ctx = document.getElementById('yearChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(item => item.PublishYear),
            datasets: [{
                label: 'Jumlah Katalog',
                data: data.map(item => item.total),
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Distribusi Katalog per Tahun'
                }
            }
        }
    });
}

function createLanguageChart(data) {
    const ctx = document.getElementById('languageChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(item => item.Languages),
            datasets: [{
                data: data.map(item => item.total),
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB', 
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Distribusi Bahasa'
                }
            }
        }
    });
}

// Download raw data
function downloadRawData(type) {
    let data, filename;
    
    switch(type) {
        case 'year':
            data = <?= json_encode($by_year ?? []) ?>;
            filename = 'statistics_year.json';
            break;
        case 'language':
            data = <?= json_encode($by_language ?? []) ?>;
            filename = 'statistics_language.json';
            break;
        case 'publisher':
            data = <?= json_encode($by_publisher ?? []) ?>;
            filename = 'statistics_publisher.json';
            break;
        default:
            return;
    }
    
    const blob = new Blob([JSON.stringify(data, null, 2)], {type: 'application/json'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    URL.revokeObjectURL(url);
}
</script>

<style>
@media print {
    .btn, .dropdown, .card-header {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        break-inside: avoid;
    }
    
    .container {
        max-width: 100% !important;
    }
}

.mini-chart {
    font-size: 0.8em;
}

.bg-gradient {
    background: linear-gradient(45deg, #667eea, #764ba2) !important;
}
</style>
<?= $this->endSection() ?>
<img style="width: 80px; height: 80px; object-fit: contain; border-radius: 16px; margin-bottom: 20px;" src="<?= !empty($logo) ? base_url('uploads/branch/' . $logo) : base_url('assets/img/default-perpus.png') ?>" alt="Logo">