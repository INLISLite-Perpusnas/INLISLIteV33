<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<style>
    /* Browse Katalog - gaya INA Digital (Inter, skala 28/36, 18/26, 16/24, 14/20, 12/16; biru #2d659e) */
    :root {
        --op-primary: #2d659e;
        --op-primary-hover: #24527f;
        --op-primary-soft: #eef4fa;
        --op-ink: #1f2937;
        --op-ink-subtle: #374151;
        --op-muted: #6b7280;
        --op-border: #e5e7eb;
        --op-border-strong: #d1d5db;
        --op-bg: #f7f7f8;
        --op-radius-lg: 12px;
        --op-radius-md: 12px;
        --op-radius-sm: 8px;
        --op-shadow-sm: 0 1px 3px rgba(16, 24, 40, .08), 0 1px 2px rgba(16, 24, 40, .04);
        --op-focus: 0 0 0 3px rgba(45, 101, 158, .2);
    }

    body { background: var(--op-bg); }

    .op-container { max-width: 1280px; margin-left: auto; margin-right: auto; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: var(--op-ink); }
    @media (min-width: 1400px) {
        .op-container { padding-left: 2rem; padding-right: 2rem; }
    }

    /* Header halaman: rata kiri, H4 */
    .op-page-header { text-align: left; margin-bottom: 24px; }
    .op-page-header .op-icon-badge { display: none; }
    .op-page-header h1 { font-size: 28px; line-height: 36px; font-weight: 700; letter-spacing: 0; color: var(--op-ink); margin: 0 0 4px; }
    .op-page-header p { font-size: 16px; line-height: 24px; color: var(--op-muted); }

    /* Kartu */
    .op-card {
        background: #fff;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-lg);
        box-shadow: var(--op-shadow-sm);
        overflow: hidden;
    }
    .op-card-header {
        padding: 16px 24px;
        display: flex; align-items: center; gap: 8px;
        border-bottom: 1px solid var(--op-border);
        font-size: 18px; line-height: 26px; font-weight: 600;
        color: var(--op-ink);
    }
    .op-card-header .op-header-icon { display: none; }
    .op-card-header .op-header-meta { font-size: 14px; line-height: 20px; font-weight: 500; color: var(--op-muted); }
    .op-card-body { padding: 24px; }

    /* Card Metrics: label, angka besar, ikon di kotak lembut */
    .op-mini-stat-card {
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        height: 100%;
        padding: 16px 20px;
        text-align: left;
        background: #fff;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-sm);
    }
    .op-mini-stat-card small { display: block; font-size: 14px; line-height: 20px; font-weight: 500; color: var(--op-muted); }
    .op-mini-stat-card h5 { margin: 0; font-size: 28px; line-height: 36px; font-weight: 700; color: var(--op-ink); font-variant-numeric: tabular-nums; }
    .op-mini-stat-card i {
        flex-shrink: 0;
        width: 44px; height: 44px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 18px;
        color: var(--op-primary);
        background: var(--op-primary-soft);
        border-radius: var(--op-radius-sm);
    }

    /* Pemilih kategori */
    .op-type-btn {
        display: flex; align-items: center; gap: 12px;
        height: 100%;
        padding: 16px 20px;
        text-align: left;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-sm);
        background: #fff;
        color: var(--op-ink);
        text-decoration: none;
        transition: border-color .15s ease, background-color .15s ease;
    }
    .op-type-btn:hover { border-color: var(--op-primary); background: var(--op-primary-soft); color: var(--op-ink); }
    .op-type-btn:focus-visible { outline: none; box-shadow: var(--op-focus); }
    .op-type-btn.active,
    .op-type-btn.active:hover {
        background: var(--op-primary);
        border-color: var(--op-primary);
        color: #fff;
    }
    .op-type-btn .op-type-icon { flex-shrink: 0; font-size: 20px; margin: 0; color: var(--op-primary); }
    .op-type-btn.active .op-type-icon { color: #fff; }
    .op-type-btn strong { display: block; font-size: 16px; line-height: 24px; font-weight: 600; }
    .op-type-btn small { display: block; font-size: 14px; line-height: 20px; color: var(--op-muted); }
    .op-type-btn.active small { color: rgba(255, 255, 255, .85); }

    /* Navigasi huruf */
    .op-alpha-nav { text-align: left; }
    .browse-letter {
        display: inline-flex;
        align-items: center; justify-content: center;
        width: 40px; height: 40px;
        margin: 0 4px 8px 0;
        border-radius: var(--op-radius-sm);
        font-size: 14px; line-height: 20px; font-weight: 600;
        text-decoration: none;
        color: var(--op-ink-subtle);
        background: #fff;
        border: 1px solid var(--op-border-strong);
        transition: border-color .15s ease, background-color .15s ease, color .15s ease;
    }
    .browse-letter:hover { border-color: var(--op-primary); color: var(--op-primary); background: var(--op-primary-soft); }
    .browse-letter:focus-visible { outline: none; box-shadow: var(--op-focus); }
    .browse-letter.active,
    .browse-letter.active:hover { background: var(--op-primary); border-color: var(--op-primary); color: #fff; }
    .op-alpha-divider { border-top: 1px solid var(--op-border); margin: 8px 0 16px; max-width: none; }

    /* Header hasil */
    .op-results-title { font-size: 18px; line-height: 26px; font-weight: 600; color: var(--op-ink); }
    .op-results-title > i { display: none; }
    .op-count-badge {
        background: var(--op-primary-soft); color: var(--op-primary);
        border-radius: 999px; padding: 2px 10px;
        font-size: 12px; line-height: 16px; font-weight: 600;
        display: inline-flex; align-items: center; gap: 4px;
        vertical-align: middle;
    }
    .op-view-toggle-btn {
        min-height: 40px;
        border-radius: var(--op-radius-sm) !important;
        border: 1px solid var(--op-border-strong) !important;
        color: var(--op-ink) !important;
        background: #fff !important;
        font-size: 14px; line-height: 20px; font-weight: 600;
    }
    .op-view-toggle-btn:hover { border-color: var(--op-primary) !important; color: var(--op-primary) !important; }
    .op-dropdown-menu { border-radius: var(--op-radius-sm); border: 1px solid var(--op-border); box-shadow: 0 8px 24px rgba(16, 24, 40, .12); padding: 4px; }
    .op-dropdown-menu .dropdown-item { border-radius: 6px; padding: 8px 12px; font-size: 14px; line-height: 20px; }
    .op-dropdown-menu .dropdown-item:hover { background: var(--op-primary-soft); color: var(--op-primary); }

    /* Kartu katalog (grid) */
    .catalog-card {
        border: 1px solid var(--op-border) !important;
        border-radius: var(--op-radius-md);
        box-shadow: var(--op-shadow-sm);
        overflow: hidden;
        background: #fff;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .catalog-card:hover { border-color: var(--op-primary) !important; box-shadow: 0 4px 12px rgba(16, 24, 40, .08); }
    .catalog-card .cc-header {
        padding: 12px 16px 0;
        display: flex; align-items: center; gap: 6px;
        font-size: 12px; line-height: 16px; font-weight: 600;
        color: var(--op-muted);
        background: transparent;
    }
    .catalog-card .cc-header i { color: var(--op-primary); }
    .catalog-card .cc-body { padding: 8px 16px 16px; }
    .catalog-card .cc-title { font-size: 16px; line-height: 24px; font-weight: 600; color: var(--op-ink); margin-bottom: 12px; }
    .catalog-card .cc-meta { display: flex; align-items: flex-start; gap: 8px; margin-bottom: 4px; font-size: 14px; line-height: 20px; color: var(--op-muted); }
    .catalog-card .cc-meta strong { font-weight: 500; color: var(--op-ink-subtle); }
    .catalog-card .cc-meta i { width: 14px; margin-top: 3px; color: #9ca3af; }
    .catalog-card .cc-footer { padding: 12px 16px; border-top: 1px solid var(--op-border); background: #fff; }

    .op-chip-subject {
        background: var(--op-primary-soft); color: var(--op-primary);
        border-radius: 999px; padding: 2px 10px;
        font-size: 12px; line-height: 16px; font-weight: 600;
        display: inline-flex; align-items: center; gap: 4px;
    }
    .op-chip-controlnum {
        background: #f3f4f6; color: var(--op-ink-subtle);
        border-radius: 999px; padding: 2px 10px;
        font-size: 12px; line-height: 16px; font-weight: 600;
    }

    /* Tombol: utama solid, sekunder bergaris */
    .btn-op-primary {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        min-height: 36px;
        padding: 6px 14px;
        font-size: 14px; line-height: 20px; font-weight: 600;
        color: #fff;
        background: var(--op-primary);
        border: 1px solid var(--op-primary);
        border-radius: var(--op-radius-sm);
        box-shadow: none;
    }
    .btn-op-primary:hover { color: #fff; background: var(--op-primary-hover); border-color: var(--op-primary-hover); filter: none; }
    .btn-op-soft {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        min-height: 40px;
        padding: 8px 16px;
        font-size: 14px; line-height: 20px; font-weight: 600;
        color: var(--op-primary);
        background: #fff;
        border: 1px solid var(--op-primary);
        border-radius: var(--op-radius-sm);
    }
    .btn-op-soft:hover { color: var(--op-primary-hover); background: var(--op-primary-soft); border-color: var(--op-primary-hover); }
    .btn-op-primary:focus-visible,
    .btn-op-soft:focus-visible { outline: none; box-shadow: var(--op-focus); }
    .btn-op-soft .text-success,
    .btn-op-soft .text-info { color: inherit !important; }
    .btn-group > .btn-op-soft + .btn-op-soft { margin-left: 8px; }
    .btn-group > .btn-op-soft { border-radius: var(--op-radius-sm) !important; }

    /* List view */
    .op-list-item {
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-md);
        padding: 16px 20px;
        margin-bottom: 12px;
        background: #fff;
        transition: border-color .15s ease;
    }
    .op-list-item:hover { border-color: var(--op-primary); }
    .op-list-item h5 { font-size: 16px; line-height: 24px; font-weight: 600 !important; color: var(--op-ink) !important; }
    .op-list-item p,
    .op-list-item small { font-size: 14px; line-height: 20px; }

    /* Tabel INA */
    .op-table-wrap { background: #fff; border: 1px solid var(--op-border); border-radius: var(--op-radius-md); overflow: hidden; }
    .op-table-wrap table { margin-bottom: 0; }
    .op-table-wrap thead th {
        padding: 12px 16px;
        font-size: 14px; line-height: 20px; font-weight: 600;
        text-transform: none; letter-spacing: 0;
        color: var(--op-ink-subtle);
        background: #f9fafb;
        border-bottom: 1px solid var(--op-border);
        white-space: nowrap;
    }
    .op-table-wrap tbody td { padding: 12px 16px; vertical-align: top; font-size: 14px; line-height: 20px; border-color: var(--op-border); }
    .op-table-wrap tbody tr:hover { background: #f9fafb; }
    .op-table-wrap td:first-child,
    .op-table-wrap th:first-child { text-align: right; width: 56px; font-variant-numeric: tabular-nums; }

    /* Empty state */
    .op-empty-panel {
        text-align: center;
        padding: 48px 24px;
        background: #fff;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-lg);
        box-shadow: var(--op-shadow-sm);
    }
    .op-empty-panel i { color: #9ca3af; }
    .op-empty-panel h4 { font-size: 18px; line-height: 26px; color: var(--op-ink) !important; }
    .op-empty-panel .btn-group { gap: 8px; }

    /* Pagination INA */
    .custom-pagination .pagination { justify-content: center; gap: 4px; }
    .custom-pagination .page-link {
        min-width: 40px; height: 40px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: var(--op-radius-sm) !important;
        border: 1px solid var(--op-border-strong) !important;
        color: var(--op-ink-subtle) !important;
        background: #fff !important;
        font-size: 14px; font-weight: 600;
        margin: 0;
    }
    .custom-pagination .page-link:hover { border-color: var(--op-primary) !important; color: var(--op-primary) !important; }
    .custom-pagination .page-item.active .page-link {
        background: var(--op-primary) !important;
        border-color: var(--op-primary) !important;
        color: #fff !important;
    }

    mark, .bg-warning.px-1.rounded { background: #fde68a !important; border-radius: 4px; padding: 0 2px; }

    @media (max-width: 768px) {
        .op-page-header h1 { font-size: 24px; line-height: 32px; }
        .op-card-body { padding: 16px; }
        .op-card-header { padding: 16px; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid op-container py-5" style="padding-top: 110px !important;">

    <!-- Page Header -->
    <div class="op-page-header">
        <div class="op-icon-badge"><i class="fas fa-list"></i></div>
        <h1>Browse Katalog</h1>
        <p class="mb-0">Jelajahi koleksi perpustakaan berdasarkan huruf awal</p>
    </div>

    <!-- Statistik & Pilih Tipe -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="op-card">
                <div class="op-card-header">
                    <span class="op-header-icon"><i class="fas fa-filter"></i></span>
                    Pilih Kategori Browse
                </div>
                <div class="op-card-body">

                    <!-- Statistik -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="op-mini-stat-card">
                                <div><small>Total Pengarang</small><h5><?= number_format($total_authors) ?></h5></div>
                                <i class="fas fa-users" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="op-mini-stat-card">
                                <div><small>Total Judul</small><h5><?= number_format($total_titles) ?></h5></div>
                                <i class="fas fa-book" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="op-mini-stat-card">
                                <div><small>Total Subjek</small><h5><?= number_format($total_subjects) ?></h5></div>
                                <i class="fas fa-tags" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="op-mini-stat-card">
                                <div><small>Bahasa</small><h5><?= number_format($total_languages) ?></h5></div>
                                <i class="fas fa-globe" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Tipe Browse -->
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="<?= base_url('opac/browse?type=author&letter=' . ($letter ?? 'A')) ?>"
                               class="op-type-btn t-author <?= ($browse_type ?? '') == 'author' ? 'active' : '' ?>">
                                <i class="fas fa-user op-type-icon"></i>
                                <span><strong>Pengarang</strong><small>Browse berdasarkan nama pengarang</small></span>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?= base_url('opac/browse?type=title&letter=' . ($letter ?? 'A')) ?>"
                               class="op-type-btn t-title <?= ($browse_type ?? '') == 'title' ? 'active' : '' ?>">
                                <i class="fas fa-book op-type-icon"></i>
                                <span><strong>Judul</strong><small>Browse berdasarkan judul buku</small></span>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?= base_url('opac/browse?type=subject&letter=' . ($letter ?? 'A')) ?>"
                               class="op-type-btn t-subject <?= ($browse_type ?? '') == 'subject' ? 'active' : '' ?>">
                                <i class="fas fa-tags op-type-icon"></i>
                                <span><strong>Subjek</strong><small>Browse berdasarkan subjek</small></span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <?php
    $typeNames = [
        'author'  => 'Pengarang',
        'title'   => 'Judul',
        'subject' => 'Subjek',
    ];
    if (isset($browse_type)):
    ?>

        <!-- Navigasi Alfabet -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="op-card">
                    <div class="op-card-header">
                        <span class="op-header-icon"><i class="fas fa-sort-alpha-down"></i></span>
                        Pilih Huruf Awal
                        <span class="op-header-meta">&middot; <?= esc($typeNames[$browse_type] ?? $browse_type) ?></span>
                    </div>
                    <div class="op-card-body">
                        <div class="alphabet-nav op-alpha-nav">
                            <?php foreach ($alphabet ?? range('A', 'Z') as $char): ?>
                                <a href="<?= base_url('opac/browse?type=' . $browse_type . '&letter=' . $char) ?>"
                                   class="browse-letter <?= ($letter ?? 'A') == $char ? 'active' : '' ?>">
                                    <?= $char ?>
                                </a>
                            <?php endforeach; ?>

                            <div class="op-alpha-divider"></div>

                            <div>
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

        <!-- Hasil Browse -->
        <div class="row">
            <div class="col-12">
                <?php if (!empty($catalogs)): ?>

                    <!-- Header Hasil -->
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <h3 class="op-results-title mb-0">
                            <i class="fas fa-list-ul me-2 text-primary"></i>
                            <?= ucfirst($typeNames[$browse_type] ?? $browse_type) ?> dimulai dengan
                            "<strong><?= esc($letter) ?></strong>"
                            <span class="op-count-badge ms-2">
                                <?= number_format($pager->getTotal('browse')) ?> ditemukan
                            </span>
                        </h3>

                        <div class="btn-group">
                            <button class="btn op-view-toggle-btn dropdown-toggle"
                                    type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-eye me-1"></i>Tampilan
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end op-dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="switchView('grid'); return false;">
                                        <i class="fas fa-th me-2"></i>Grid
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="switchView('list'); return false;">
                                        <i class="fas fa-list me-2"></i>List
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="switchView('table'); return false;">
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
                                    <div class="card catalog-card h-100 border-0">
                                        <div class="cc-header t-<?= $browse_type ?>">
                                            <i class="fas fa-<?= $browse_type == 'author' ? 'user' : ($browse_type == 'title' ? 'book' : 'tag') ?>"></i>
                                            <span class="text-truncate"><?= esc($catalog->ControlNumber ?? 'N/A') ?></span>
                                        </div>
                                        <div class="cc-body">
                                            <div class="cc-title">
                                                <?= esc(substr($catalog->Title ?? 'Tanpa Judul', 0, 60)) ?>
                                                <?= strlen($catalog->Title ?? '') > 60 ? '...' : '' ?>
                                            </div>
                                            <div class="mb-3">
                                                <div class="cc-meta">
                                                    <i class="fas fa-user"></i>
                                                    <span><strong>Pengarang:</strong> <?= esc($catalog->Author ?? 'N/A') ?></span>
                                                </div>
                                                <div class="cc-meta">
                                                    <i class="fas fa-building"></i>
                                                    <span><strong>Penerbit:</strong> <?= esc($catalog->Publisher ?? 'N/A') ?></span>
                                                </div>
                                                <div class="cc-meta">
                                                    <i class="fas fa-calendar"></i>
                                                    <span><strong>Tahun:</strong> <?= esc($catalog->PublishYear ?? 'N/A') ?></span>
                                                </div>
                                            </div>
                                            <?php if (!empty($catalog->Subject)): ?>
                                                <span class="op-chip-subject">
                                                    <i class="fas fa-tag"></i>
                                                    <?= esc(substr($catalog->Subject, 0, 30)) ?>
                                                    <?= strlen($catalog->Subject) > 30 ? '...' : '' ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="cc-footer">
                                            <a href="<?= base_url('opac/detail/' . $catalog->ID) ?>"
                                               class="btn btn-op-primary btn-sm">
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
                            <div class="op-list-item">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="mb-1 text-primary fw-bold">
                                            <?= esc($catalog->Title ?? 'Tanpa Judul') ?>
                                        </h5>
                                        <p class="mb-1 text-muted small">
                                            <strong>Pengarang:</strong> <?= esc($catalog->Author ?? 'N/A') ?> &nbsp;|&nbsp;
                                            <strong>Penerbit:</strong> <?= esc($catalog->Publisher ?? 'N/A') ?> &nbsp;|&nbsp;
                                            <strong>Tahun:</strong> <?= esc($catalog->PublishYear ?? 'N/A') ?>
                                        </p>
                                        <?php if (!empty($catalog->Subject)): ?>
                                            <small class="text-muted">
                                                <i class="fas fa-tags me-1"></i><?= esc($catalog->Subject) ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                        <span class="op-chip-controlnum d-inline-block mb-2">
                                            <?= esc($catalog->ControlNumber ?? 'N/A') ?>
                                        </span><br>
                                        <a href="<?= base_url('opac/detail/' . $catalog->ID) ?>"
                                           class="btn btn-op-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Table View -->
                    <div id="tableView" class="view-container" style="display: none;">
                        <div class="op-table-wrap">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
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
                                        <?php
                                        $currentPage = $pager->getCurrentPage('browse');
                                        $startNo     = ($currentPage - 1) * $perPage + 1;
                                        foreach ($catalogs as $index => $catalog):
                                        ?>
                                            <tr>
                                                <td><?= $startNo + $index ?></td>
                                                <td><span class="op-chip-controlnum"><?= esc($catalog->ControlNumber ?? 'N/A') ?></span></td>
                                                <td>
                                                    <strong>
                                                        <?= esc(substr($catalog->Title ?? 'Tanpa Judul', 0, 50)) ?>
                                                        <?= strlen($catalog->Title ?? '') > 50 ? '...' : '' ?>
                                                    </strong>
                                                </td>
                                                <td><?= esc($catalog->Author ?? 'N/A') ?></td>
                                                <td><?= esc($catalog->Publisher ?? 'N/A') ?></td>
                                                <td><?= esc($catalog->PublishYear ?? 'N/A') ?></td>
                                                <td>
                                                    <a href="<?= base_url('opac/detail/' . $catalog->ID) ?>"
                                                       class="btn btn-op-primary btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Export -->
                    <div class="text-center mt-4">
                        <div class="btn-group">
                            <button class="btn btn-op-soft" onclick="exportBrowseResults('excel')">
                                <i class="fas fa-file-excel me-2 text-success"></i>Export Excel
                            </button>
                            <button class="btn btn-op-soft" onclick="exportBrowseResults('csv')">
                                <i class="fas fa-file-csv me-2 text-info"></i>Export CSV
                            </button>
                        </div>
                    </div>

                    <!-- ✅ Pagination — gunakan $pager->links('browse') -->
                    <div class="row mt-5">
                        <div class="col-12 d-flex flex-column align-items-center">
                            <?php if (isset($pager)): ?>
                                <div class="custom-pagination">
                                    <?= $pager->links('browse','opac_pagination') ?>
                                </div>
                                <div class="text-muted mt-2">
                                    <small>
                                        <i class="fas fa-list text-primary me-1"></i>
                                        Halaman <strong><?= $pager->getCurrentPage('browse') ?></strong>
                                        dari <strong><?= $pager->getPageCount('browse') ?></strong>
                                        &nbsp;|&nbsp;
                                        Total <strong><?= number_format($pager->getTotal('browse')) ?></strong> hasil
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php else: ?>

                    <!-- Tidak Ada Hasil -->
                    <div class="op-empty-panel">
                        <i class="fas fa-search fa-3x mb-3"></i>
                        <h4 class="text-muted fw-bold">
                            Tidak ada <?= $typeNames[$browse_type] ?? $browse_type ?>
                            yang dimulai dengan "<?= esc($letter) ?>"
                        </h4>
                        <p class="text-muted mb-4">Coba pilih huruf lain atau ubah kategori browse</p>
                        <div class="btn-group">
                            <a href="<?= base_url('opac/browse?type=' . $browse_type . '&letter=A') ?>"
                               class="btn btn-op-primary">
                                <i class="fas fa-redo me-2"></i>Reset ke A
                            </a>
                            <a href="<?= base_url('opac/browse') ?>" class="btn btn-op-soft">
                                <i class="fas fa-arrow-left me-2"></i>Pilih Kategori Lain
                            </a>
                        </div>
                    </div>

                <?php endif; ?>
            </div>
        </div>

    <?php endif; ?>
</div>

<?= $this->section('script') ?>
<script>
function switchView(viewType) {
    document.querySelectorAll('.view-container').forEach(el => el.style.display = 'none');
    const target = document.getElementById(viewType + 'View');
    if (target) target.style.display = 'block';
    showToast('Tampilan diubah ke ' + viewType, 'success');
}

function exportBrowseResults(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('format', format);
    params.set('export', 'browse_results');
    window.open('<?= base_url('opac/export') ?>?' + params.toString(), '_blank');
    showToast('Export ' + format.toUpperCase() + ' dimulai...', 'info');
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 80px; right: 20px; z-index: 9999; max-width: 350px; border-radius: 12px; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.25); border:none;';
    toast.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    document.body.appendChild(toast);
    setTimeout(() => { if (toast.parentNode) toast.parentNode.removeChild(toast); }, 3000);
}

function quickJumpToLetter() {
    const letter = prompt('Masukkan huruf yang ingin dicari (A-Z, 0-9):');
    if (letter && letter.match(/^[a-zA-Z0-9]$/)) {
        const params   = new URLSearchParams(window.location.search);
        const browseType = params.get('type') || 'author';
        window.location.href = '<?= base_url('opac/browse') ?>?type=' + browseType + '&letter=' + letter.toUpperCase();
    }
}

document.addEventListener('keydown', function(e) {
    if (e.altKey && e.key.match(/^[a-zA-Z0-9]$/)) {
        const letter     = e.key.toUpperCase();
        const params     = new URLSearchParams(window.location.search);
        const browseType = params.get('type') || 'author';
        window.location.href = '<?= base_url('opac/browse') ?>?type=' + browseType + '&letter=' + letter;
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const currentLetter = '<?= $letter ?? '' ?>';
    if (currentLetter) {
        document.querySelectorAll('.catalog-card h5').forEach(function(el) {
            if (el.textContent.trim().toLowerCase().startsWith(currentLetter.toLowerCase())) {
                const first = el.innerHTML.charAt(0);
                el.innerHTML = '<span class="bg-warning px-1 rounded">' + first + '</span>' + el.innerHTML.slice(1);
            }
        });
    }

    // Tombol Quick Jump
    const alphabetNav = document.querySelector('.alphabet-nav');
    if (alphabetNav) {
        const btn    = document.createElement('button');
        btn.className = 'btn btn-op-soft btn-sm mt-2';
        btn.innerHTML = '<i class="fas fa-search me-1"></i>Jump';
        btn.onclick   = quickJumpToLetter;
        alphabetNav.appendChild(btn);
    }
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>