<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>
<style>
    :root {
        --op-primary: #1e3a8a;
        --op-primary-light: #3b5bdb;
        --op-primary-soft: #eef2ff;
        --op-ink: #111827;
        --op-muted: #6b7280;
        --op-border: #e5e7eb;
        --op-bg: #f6f7fb;
        --op-radius-lg: 20px;
        --op-radius-md: 14px;
        --op-radius-sm: 10px;
        --op-shadow-sm: 0 1px 3px rgba(17, 24, 39, 0.06), 0 1px 2px rgba(17, 24, 39, 0.04);
        --op-shadow-md: 0 10px 30px -12px rgba(30, 58, 138, 0.18);
        --op-shadow-lg: 0 20px 45px -18px rgba(30, 58, 138, 0.28);
    }

    body { background: var(--op-bg); }

    .op-container { max-width: 1600px; margin-left: auto; margin-right: auto; }
    @media (min-width: 1400px) {
        .op-container { padding-left: 2.5rem; padding-right: 2.5rem; }
    }

    /* Page header */
    .op-page-header {
        text-align: center;
        margin-bottom: 2.75rem;
    }
    .op-page-header .op-icon-badge {
        width: 64px; height: 64px;
        border-radius: 18px;
        background: linear-gradient(135deg, var(--op-primary), var(--op-primary-light));
        display: inline-flex; align-items: center; justify-content: center;
        color: #fff; font-size: 1.5rem;
        box-shadow: var(--op-shadow-md);
        margin-bottom: 1rem;
    }
    .op-page-header h1 {
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--op-ink);
        font-size: 2.15rem;
    }
    .op-page-header p { color: var(--op-muted); font-size: 1.02rem; }

    /* Overview stat cards */
    .op-stat-card {
        background: #fff;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-lg);
        box-shadow: var(--op-shadow-sm);
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        height: 100%;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .op-stat-card:hover { transform: translateY(-3px); box-shadow: var(--op-shadow-md); }
    .op-stat-icon {
        flex: 0 0 auto;
        width: 56px; height: 56px;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.35rem;
        color: #fff;
    }
    .op-stat-icon.i-primary { background: linear-gradient(135deg, #1e3a8a, #3b5bdb); }
    .op-stat-icon.i-success { background: linear-gradient(135deg, #059669, #34d399); }
    .op-stat-icon.i-info    { background: linear-gradient(135deg, #0891b2, #38bdf8); }
    .op-stat-icon.i-warning { background: linear-gradient(135deg, #d97706, #fbbf24); }
    .op-stat-value { font-weight: 800; font-size: 1.6rem; color: var(--op-ink); line-height: 1.1; }
    .op-stat-label { color: var(--op-muted); font-size: .85rem; font-weight: 500; }

    /* Section cards */
    .op-card {
        background: #fff;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-lg);
        box-shadow: var(--op-shadow-sm);
        overflow: hidden;
        height: 100%;
    }
    .op-card-header {
        padding: 1.1rem 1.4rem;
        display: flex; align-items: center; gap: .6rem;
        border-bottom: 1px solid var(--op-border);
        font-weight: 700;
        color: var(--op-ink);
        font-size: 1rem;
    }
    .op-card-header .op-header-icon {
        width: 34px; height: 34px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: .85rem;
    }
    .op-card-body { padding: 1.4rem; }

    /* Table (year distribution) */
    .op-table-wrap { border: 1px solid var(--op-border); border-radius: var(--op-radius-md); overflow: hidden; }
    .op-table-wrap table { margin-bottom: 0; }
    .op-table-wrap thead th {
        background: #f8f9fc;
        color: var(--op-ink);
        font-size: .74rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        font-weight: 700;
        border: none;
        padding: .75rem 1rem;
    }
    .op-table-wrap tbody td { padding: .65rem 1rem; vertical-align: middle; font-size: .875rem; border-color: #f0f1f5; }
    .op-table-wrap tbody tr:hover { background: #fafbff; }

    .op-progress { height: 10px; border-radius: 999px; background: #f1f2f6; overflow: hidden; }
    .op-progress .op-progress-bar {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, var(--op-primary), var(--op-primary-light));
        transition: width .5s ease;
    }
    .op-progress.lg { height: 26px; border-radius: 999px; }
    .op-progress.lg .op-progress-bar {
        display: flex; align-items: center; justify-content: flex-end;
        padding-right: .6rem;
        color: #fff; font-size: .78rem; font-weight: 700;
    }

    .op-palette-1 { background: linear-gradient(90deg,#1e3a8a,#3b5bdb) !important; }
    .op-palette-2 { background: linear-gradient(90deg,#059669,#34d399) !important; }
    .op-palette-3 { background: linear-gradient(90deg,#0891b2,#38bdf8) !important; }
    .op-palette-4 { background: linear-gradient(90deg,#d97706,#fbbf24) !important; }
    .op-palette-5 { background: linear-gradient(90deg,#dc2626,#f87171) !important; }
    .op-palette-6 { background: linear-gradient(90deg,#4b5563,#9ca3af) !important; }

    .op-badge-soft {
        border-radius: 999px;
        padding: .3rem .7rem;
        font-weight: 700;
        font-size: .78rem;
        display: inline-flex; align-items: center; gap: .3rem;
    }
    .op-badge-primary { background: var(--op-primary-soft); color: var(--op-primary); }
    .op-badge-muted { background: #f4f4f5; color: #3f3f46; }

    /* Publisher list */
    .op-publisher-item {
        background: #fafbfd;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-sm);
        padding: .9rem 1rem;
        height: 100%;
        transition: border-color .2s ease, background .2s ease;
    }
    .op-publisher-item:hover { border-color: #c7d2fe; background: #f7f8ff; }
    .op-rank-badge {
        width: 26px; height: 26px;
        border-radius: 8px;
        background: var(--op-primary-soft);
        color: var(--op-primary);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: .72rem; font-weight: 800;
        flex: 0 0 auto;
    }

    /* Quick stats mini */
    .op-mini-stat { text-align: center; padding: .5rem; }
    .op-mini-stat h4 { font-weight: 800; margin-bottom: .15rem; }
    .op-mini-stat small { color: var(--op-muted); font-weight: 500; }
    .op-divider-v { border-right: 1px solid var(--op-border); }

    /* Export buttons */
    .op-export-btn {
        border-radius: var(--op-radius-sm);
        font-weight: 600;
        font-size: .875rem;
        padding: .65rem 1rem;
        text-align: left;
        border: 1px solid var(--op-border);
        background: #fff;
        color: var(--op-ink);
        display: flex; align-items: center; gap: .6rem;
        transition: all .2s ease;
    }
    .op-export-btn:hover { border-color: #c7d2fe; background: #f7f8ff; color: var(--op-primary); }
    .op-export-btn .op-header-icon { width: 30px; height: 30px; border-radius: 8px; font-size: .78rem; }

    /* Growth trend */
    .op-growth-value { font-weight: 800; font-size: 2rem; }
    .op-growth-up { color: #059669; }
    .op-growth-down { color: #dc2626; }
    .op-mini-year-row { display: flex; justify-content: space-between; font-size: .84rem; padding: .35rem 0; border-bottom: 1px dashed var(--op-border); }
    .op-mini-year-row:last-child { border-bottom: none; }

    /* Insights */
    .op-insight-header {
        background: linear-gradient(135deg, #667eea, #764ba2);
        padding: 1.1rem 1.4rem;
    }
    .op-insight-list { list-style: none; padding: 0; margin: 0; }
    .op-insight-list li {
        display: flex; align-items: flex-start; gap: .6rem;
        padding: .55rem 0;
        font-size: .9rem;
        color: var(--op-ink);
    }
    .op-insight-list li i { margin-top: .2rem; }
    .op-insight-title { font-weight: 700; font-size: .95rem; display: flex; align-items: center; gap: .5rem; margin-bottom: .75rem; }

    .op-empty-state { text-align: center; color: var(--op-muted); padding: 2.5rem 1rem; }
    .op-empty-state i { color: #d1d5db; }

    @media print {
        .btn, .dropdown, .op-card-header { display: none !important; }
        .op-card { border: 1px solid #dee2e6 !important; break-inside: avoid; }
        .op-container { max-width: 100% !important; }
    }
</style>

<section class="hero-section" style="padding-top: 80px !important; padding-bottom: 40px !important;">
<div class="container-fluid op-container py-5">

    <!-- Page Header -->
    <div class="op-page-header">
        <div class="op-icon-badge"><i class="fas fa-chart-bar"></i></div>
        <h1>Statistik Katalog</h1>
        <p class="mb-0">Analisis dan statistik koleksi perpustakaan</p>
    </div>

    <!-- Overview Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="op-stat-card">
                <div class="op-stat-icon i-primary"><i class="fas fa-book"></i></div>
                <div>
                    <div class="op-stat-value"><?= number_format($total_catalogs ?? 0) ?></div>
                    <div class="op-stat-label">Total Katalog</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="op-stat-card">
                <div class="op-stat-icon i-success"><i class="fas fa-calendar"></i></div>
                <div>
                    <div class="op-stat-value"><?= count($by_year ?? []) ?></div>
                    <div class="op-stat-label">Rentang Tahun</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="op-stat-card">
                <div class="op-stat-icon i-info"><i class="fas fa-globe"></i></div>
                <div>
                    <div class="op-stat-value"><?= count($by_language ?? []) ?></div>
                    <div class="op-stat-label">Bahasa</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="op-stat-card">
                <div class="op-stat-icon i-warning"><i class="fas fa-building"></i></div>
                <div>
                    <div class="op-stat-value"><?= count($by_publisher ?? []) ?></div>
                    <div class="op-stat-label">Penerbit Aktif</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Charts -->
    <div class="row g-4 mb-4">
        <!-- Year Distribution -->
        <div class="col-lg-6">
            <div class="op-card">
                <div class="op-card-header">
                    <span class="op-header-icon" style="background: linear-gradient(135deg,#1e3a8a,#3b5bdb);"><i class="fas fa-chart-line"></i></span>
                    Distribusi Berdasarkan Tahun Terbit
                </div>
                <div class="op-card-body">
                    <?php if (!empty($by_year)): ?>
                        <div class="op-table-wrap mb-3">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Tahun</th>
                                            <th>Jumlah</th>
                                            <th>Persentase</th>
                                            <th style="width:35%;">Grafik</th>
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
                                                <td><span class="op-badge-soft op-badge-muted"><?= number_format($percentage, 1) ?>%</span></td>
                                                <td>
                                                    <div class="op-progress" title="<?= $year->total ?> katalog">
                                                        <div class="op-progress-bar" style="width: <?= $barWidth ?>%"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <?php if (count($by_year) > 15): ?>
                            <div class="text-center">
                                <button class="btn btn-op-soft btn-sm" style="border-radius:999px;border:1px solid var(--op-border);" onclick="showAllYears()">
                                    <i class="fas fa-eye me-1"></i>
                                    Lihat Semua (<?= count($by_year) ?> tahun)
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="op-empty-state">
                            <i class="fas fa-chart-line fa-3x mb-3"></i>
                            <p class="mb-0">Tidak ada data tahun tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Language Distribution -->
        <div class="col-lg-6">
            <div class="op-card">
                <div class="op-card-header">
                    <span class="op-header-icon" style="background: linear-gradient(135deg,#059669,#34d399);"><i class="fas fa-chart-pie"></i></span>
                    Distribusi Berdasarkan Bahasa
                </div>
                <div class="op-card-body">
                    <?php if (!empty($by_language)): ?>
                        <?php
                        $paletteClasses = ['op-palette-1','op-palette-2','op-palette-3','op-palette-4','op-palette-5','op-palette-6'];
                        foreach ($by_language as $index => $language):
                            $percentage = ($language->total / $total_catalogs) * 100;
                            $paletteClass = $paletteClasses[$index % count($paletteClasses)];
                        ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold"><?= esc($language->Languages) ?></span>
                                    <span class="op-badge-soft op-badge-primary"><?= number_format($language->total) ?></span>
                                </div>
                                <div class="op-progress lg">
                                    <div class="op-progress-bar <?= $paletteClass ?>"
                                         style="width: <?= $percentage ?>%"
                                         title="<?= number_format($percentage, 1) ?>%">
                                        <?= number_format($percentage, 1) ?>%
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="op-empty-state">
                            <i class="fas fa-globe fa-3x mb-3"></i>
                            <p class="mb-0">Tidak ada data bahasa tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Top Publishers -->
        <div class="col-12">
            <div class="op-card">
                <div class="op-card-header">
                    <span class="op-header-icon" style="background: linear-gradient(135deg,#0891b2,#38bdf8);"><i class="fas fa-building"></i></span>
                    Top 10 Penerbit
                </div>
                <div class="op-card-body">
                    <?php if (!empty($by_publisher)): ?>
                        <div class="row g-3">
                            <?php
                            $maxPublisherCount = $by_publisher->total ?? 1;
                            foreach ($by_publisher as $index => $publisher):
                                $percentage = ($publisher->total / $maxPublisherCount) * 100;
                            ?>
                                <div class="col-md-6">
                                    <div class="op-publisher-item">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center gap-2 text-truncate" title="<?= esc($publisher->Publisher) ?>">
                                                <span class="op-rank-badge">#<?= $index + 1 ?></span>
                                                <span class="fw-semibold text-truncate">
                                                    <?= esc(substr($publisher->Publisher, 0, 30)) ?>
                                                    <?= strlen($publisher->Publisher) > 30 ? '...' : '' ?>
                                                </span>
                                            </div>
                                            <span class="op-badge-soft op-badge-primary"><?= number_format($publisher->total) ?></span>
                                        </div>
                                        <div class="op-progress">
                                            <div class="op-progress-bar" style="width: <?= $percentage ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="op-empty-state">
                            <i class="fas fa-building fa-3x mb-3"></i>
                            <p class="mb-0">Tidak ada data penerbit tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="row g-4 mb-4">
        <!-- Quick Stats -->
        <div class="col-lg-4">
            <div class="op-card">
                <div class="op-card-header">
                    <span class="op-header-icon" style="background: linear-gradient(135deg,#d97706,#fbbf24);"><i class="fas fa-tachometer-alt"></i></span>
                    Statistik Cepat
                </div>
                <div class="op-card-body">
                    <div class="row">
                        <div class="col-6 op-divider-v">
                            <div class="op-mini-stat">
                                <h4 class="text-primary"><?= number_format(($total_catalogs ?? 0) / max(1, count($by_year ?? []))) ?></h4>
                                <small>Rata-rata per Tahun</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="op-mini-stat">
                                <h4 class="text-success"><?= !empty($by_year) ? $by_year[0]->PublishYear : 'N/A' ?></h4>
                                <small>Tahun Terbanyak</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-6 op-divider-v">
                            <div class="op-mini-stat">
                                <h4 class="text-info"><?= !empty($by_language) ? $by_language[0]->Languages : 'N/A' ?></h4>
                                <small>Bahasa Utama</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="op-mini-stat">
                                <h4 class="text-warning">
                                    <?= (int)date('Y') - (!empty($by_year) ? (int)min(array_map(fn($item) => $item->PublishYear, $by_year)) : (int)date('Y')) ?>
                                </h4>
                                <small>Rentang Tahun</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div class="col-lg-4">
            <div class="op-card">
                <div class="op-card-header">
                    <span class="op-header-icon" style="background: linear-gradient(135deg,#4b5563,#9ca3af);"><i class="fas fa-download"></i></span>
                    Export Statistik
                </div>
                <div class="op-card-body">
                    <div class="d-grid gap-2">
                        <button class="op-export-btn" onclick="exportStatistics('excel')">
                            <span class="op-header-icon" style="background:#059669;"><i class="fas fa-file-excel"></i></span>
                            Export ke Excel
                        </button>
                        <button class="op-export-btn" onclick="exportStatistics('csv')">
                            <span class="op-header-icon" style="background:#0891b2;"><i class="fas fa-file-csv"></i></span>
                            Export ke CSV
                        </button>
                        <button class="op-export-btn" onclick="exportStatistics('pdf')">
                            <span class="op-header-icon" style="background:#d97706;"><i class="fas fa-file-pdf"></i></span>
                            Export ke PDF
                        </button>
                        <button class="op-export-btn" onclick="window.print()">
                            <span class="op-header-icon" style="background: linear-gradient(135deg,#1e3a8a,#3b5bdb);"><i class="fas fa-print"></i></span>
                            Cetak Laporan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Growth Trend -->
        <div class="col-lg-4">
            <div class="op-card">
                <div class="op-card-header">
                    <span class="op-header-icon" style="background: linear-gradient(135deg,#111827,#374151);"><i class="fas fa-chart-area"></i></span>
                    Tren Pertumbuhan
                </div>
                <div class="op-card-body">
                    <?php if (!empty($by_year) && count($by_year) >= 2): ?>
                        <?php
                        $recentYears = array_slice($by_year, 0, 5);
                        $oldestCount = end($recentYears)->total;
                        $newestCount = $recentYears[0]->total;
                        $growthRate = (($newestCount - $oldestCount) / max(1, $oldestCount)) * 100;
                        ?>

                        <div class="text-center mb-3">
                            <div class="op-growth-value <?= $growthRate >= 0 ? 'op-growth-up' : 'op-growth-down' ?>">
                                <i class="fas fa-arrow-<?= $growthRate >= 0 ? 'up' : 'down' ?> me-1" style="font-size:1.1rem;"></i><?= $growthRate >= 0 ? '+' : '' ?><?= number_format($growthRate, 1) ?>%
                            </div>
                            <small class="text-muted">Pertumbuhan 5 Tahun Terakhir</small>
                        </div>

                        <div class="mini-chart">
                            <?php foreach (array_reverse($recentYears) as $year): ?>
                                <div class="op-mini-year-row">
                                    <span class="text-muted"><?= $year->PublishYear ?></span>
                                    <span class="fw-semibold"><?= $year->total ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="op-empty-state py-3">
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
            <div class="op-card">
                <div class="op-insight-header">
                    <h5 class="mb-0 text-white"><i class="fas fa-lightbulb me-2"></i>Insights & Rekomendasi</h5>
                </div>
                <div class="op-card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="op-insight-title text-primary"><i class="fas fa-chart-line"></i>Analisis Data</div>
                            <ul class="op-insight-list">
                                <?php if (!empty($by_year)): ?>
                                    <li>
                                        <i class="fas fa-check-circle text-success"></i>
                                        Tahun <?= $by_year[0]->PublishYear ?> memiliki koleksi terbanyak (<?= number_format($by_year[0]->total) ?> item)
                                    </li>
                                <?php endif; ?>

                                <?php if (!empty($by_language)): ?>
                                    <li>
                                        <i class="fas fa-check-circle text-success"></i>
                                        Bahasa <?= $by_language[0]->Languages ?> mendominasi koleksi (<?= number_format(($by_language[0]->total / $total_catalogs) * 100, 1) ?>%)
                                    </li>
                                <?php endif; ?>

                                <?php if (!empty($by_publisher)): ?>
                                    <li>
                                        <i class="fas fa-check-circle text-success"></i>
                                        <?= $by_publisher[0]->Publisher ?> adalah penerbit terbesar (<?= $by_publisher[0]->total ?> buku)
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="op-insight-title text-warning"><i class="fas fa-star"></i>Rekomendasi</div>
                            <ul class="op-insight-list">
                                <li>
                                    <i class="fas fa-arrow-right text-warning"></i>
                                    Diversifikasi koleksi dari berbagai penerbit
                                </li>
                                <li>
                                    <i class="fas fa-arrow-right text-warning"></i>
                                    Tambah koleksi dalam bahasa lain untuk keseimbangan
                                </li>
                                <li>
                                    <i class="fas fa-arrow-right text-warning"></i>
                                    Fokus pada publikasi tahun terbaru untuk update koleksi
                                </li>
                            </ul>
                        </div>
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
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 350px; border-radius: 12px; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.25); border:none;';
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
<?= $this->endSection() ?>