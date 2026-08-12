<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
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
    }

    body { background: var(--op-bg); }

    .op-container { max-width: 1600px; margin-left: auto; margin-right: auto; }
    @media (min-width: 1400px) {
        .op-container { padding-left: 2.5rem; padding-right: 2.5rem; }
    }

    /* Page header */
    .op-page-header { text-align: center; margin-bottom: 2.5rem; }
    .op-page-header .op-icon-badge {
        width: 64px; height: 64px;
        border-radius: 18px;
        background: linear-gradient(135deg, var(--op-primary), var(--op-primary-light));
        display: inline-flex; align-items: center; justify-content: center;
        color: #fff; font-size: 1.5rem;
        box-shadow: var(--op-shadow-md);
        margin-bottom: 1rem;
    }
    .op-page-header h1 { font-weight: 800; letter-spacing: -0.02em; color: var(--op-ink); font-size: 2.15rem; }
    .op-page-header p { color: var(--op-muted); font-size: 1.02rem; }

    /* Card shells */
    .op-card {
        background: #fff;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-lg);
        box-shadow: var(--op-shadow-sm);
        overflow: hidden;
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
        flex: 0 0 auto;
    }
    .op-card-body { padding: 1.5rem; }

    /* Overview mini stats */
    .op-mini-stat-card {
        border-radius: var(--op-radius-md);
        padding: 1.1rem;
        text-align: center;
        color: #fff;
        height: 100%;
    }
    .op-mini-stat-card i { opacity: .9; }
    .op-mini-stat-card h5 { font-weight: 800; margin-top: .35rem; }
    .op-mini-stat-card small { opacity: .85; font-weight: 500; }
    .op-mg-primary { background: linear-gradient(135deg, #1e3a8a, #3b5bdb); }
    .op-mg-success { background: linear-gradient(135deg, #059669, #34d399); }
    .op-mg-info    { background: linear-gradient(135deg, #0891b2, #38bdf8); }
    .op-mg-warning { background: linear-gradient(135deg, #d97706, #fbbf24); }

    /* Browse type selector cards */
    .op-type-btn {
        display: block;
        text-align: center;
        padding: 1.5rem 1rem;
        border-radius: var(--op-radius-md);
        border: 1.5px solid var(--op-border);
        background: #fff;
        color: var(--op-ink);
        text-decoration: none;
        transition: all .2s ease;
        height: 100%;
    }
    .op-type-btn:hover { border-color: #c7d2fe; background: #f7f8ff; color: var(--op-ink); transform: translateY(-2px); }
    .op-type-btn.active {
        border-color: transparent;
        color: #fff;
        box-shadow: var(--op-shadow-md);
    }
    .op-type-btn.active.t-author { background: linear-gradient(135deg, #1e3a8a, #3b5bdb); }
    .op-type-btn.active.t-title  { background: linear-gradient(135deg, #059669, #34d399); }
    .op-type-btn.active.t-subject{ background: linear-gradient(135deg, #0891b2, #38bdf8); }
    .op-type-btn .op-type-icon { font-size: 1.8rem; margin-bottom: .5rem; display: block; }
    .op-type-btn strong { display: block; font-size: 1.05rem; margin-bottom: .2rem; }
    .op-type-btn small { opacity: .85; }

    /* Alphabet nav */
    .op-alpha-nav { text-align: center; }
    .browse-letter {
        display: inline-flex;
        align-items: center; justify-content: center;
        width: 40px;
        height: 40px;
        margin: 3px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.92rem;
        text-decoration: none;
        color: var(--op-primary);
        background: #f8f9fc;
        border: 1px solid var(--op-border);
        transition: all 0.2s ease;
    }
    .browse-letter:hover {
        background: var(--op-primary);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 14px -6px rgba(30,58,138,.45);
    }
    .browse-letter.active {
        background: linear-gradient(135deg, var(--op-primary), var(--op-primary-light));
        color: white;
        border-color: transparent;
        box-shadow: 0 6px 14px -6px rgba(30,58,138,.45);
    }
    .op-alpha-divider { border-top: 1px dashed var(--op-border); margin: .9rem auto; max-width: 90%; }

    /* Results header */
    .op-results-title { font-weight: 800; color: var(--op-ink); font-size: 1.35rem; }
    .op-count-badge {
        background: var(--op-primary-soft); color: var(--op-primary);
        border-radius: 999px; padding: .35rem .85rem; font-weight: 700; font-size: .82rem;
        display: inline-flex; align-items: center; gap: .3rem;
    }
    .op-view-toggle-btn {
        border-radius: 999px !important;
        border: 1px solid var(--op-border) !important;
        color: var(--op-ink) !important;
        background: #fff !important;
        font-weight: 600;
        font-size: .875rem;
    }
    .op-view-toggle-btn:hover { border-color: #c7d2fe !important; color: var(--op-primary) !important; }
    .op-dropdown-menu { border-radius: var(--op-radius-sm); border: 1px solid var(--op-border); box-shadow: var(--op-shadow-md); padding: .4rem; }
    .op-dropdown-menu .dropdown-item { border-radius: 8px; padding: .5rem .65rem; font-size: .88rem; }
    .op-dropdown-menu .dropdown-item:hover { background: var(--op-primary-soft); color: var(--op-primary); }

    /* Catalog grid cards */
    .catalog-card {
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-md);
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color .25s ease;
        box-shadow: var(--op-shadow-sm);
        overflow: hidden;
        background: #fff;
    }
    .catalog-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--op-shadow-md);
        border-color: #c7d2fe;
    }
    .catalog-card .cc-header {
        padding: .7rem 1rem;
        color: #fff;
        display: flex; align-items: center; gap: .5rem;
        font-size: .82rem; font-weight: 700;
    }
    .cc-header.t-author { background: linear-gradient(135deg, #1e3a8a, #3b5bdb); }
    .cc-header.t-title  { background: linear-gradient(135deg, #059669, #34d399); }
    .cc-header.t-subject{ background: linear-gradient(135deg, #0891b2, #38bdf8); }
    .catalog-card .cc-body { padding: 1.1rem; }
    .catalog-card .cc-title { font-weight: 700; color: var(--op-ink); font-size: 1rem; margin-bottom: .7rem; line-height: 1.35; }
    .catalog-card .cc-meta { font-size: .8rem; color: var(--op-muted); display: flex; align-items: flex-start; gap: .45rem; margin-bottom: .3rem; }
    .catalog-card .cc-meta i { color: var(--op-primary-light); margin-top: .15rem; }
    .catalog-card .cc-footer { padding: .8rem 1.1rem; border-top: 1px solid var(--op-border); background: #fafbfd; }

    .op-chip-subject {
        background: var(--op-primary-soft); color: var(--op-primary);
        border-radius: 999px; padding: .3rem .7rem; font-weight: 600; font-size: .76rem;
        display: inline-flex; align-items: center; gap: .3rem;
    }
    .op-chip-controlnum {
        background: #f4f4f5; color: #3f3f46;
        border-radius: 999px; padding: .25rem .65rem; font-weight: 700; font-size: .74rem;
    }

    .btn-op-primary {
        background: linear-gradient(135deg, var(--op-primary), var(--op-primary-light));
        border: none; color: #fff; font-weight: 600;
        border-radius: 999px;
        box-shadow: 0 6px 16px -6px rgba(30,58,138,.5);
    }
    .btn-op-primary:hover { color: #fff; filter: brightness(1.05); }
    .btn-op-soft {
        background: #fff; border: 1px solid var(--op-border); color: var(--op-ink);
        border-radius: 999px; font-weight: 600;
    }
    .btn-op-soft:hover { border-color: var(--op-primary-light); color: var(--op-primary); }

    /* List view */
    .op-list-item {
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-md);
        padding: 1.1rem 1.3rem;
        margin-bottom: .9rem;
        background: #fff;
        transition: border-color .2s ease, box-shadow .2s ease;
    }
    .op-list-item:hover { border-color: #c7d2fe; box-shadow: var(--op-shadow-sm); }

    /* Table view */
    .op-table-wrap { border: 1px solid var(--op-border); border-radius: var(--op-radius-md); overflow: hidden; }
    .op-table-wrap table { margin-bottom: 0; }
    .op-table-wrap thead th {
        background: #f8f9fc; color: var(--op-ink); font-size: .74rem;
        text-transform: uppercase; letter-spacing: .04em; font-weight: 700;
        border: none; padding: .75rem 1rem;
    }
    .op-table-wrap tbody td { padding: .65rem 1rem; vertical-align: middle; font-size: .875rem; border-color: #f0f1f5; }
    .op-table-wrap tbody tr:hover { background: #fafbff; }

    /* Empty state */
    .op-empty-panel {
        text-align: center;
        padding: 3.5rem 1.5rem;
        background: #fff;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-lg);
        box-shadow: var(--op-shadow-sm);
    }
    .op-empty-panel i { color: #d1d5db; }

    /* Pagination */
    .custom-pagination .pagination { justify-content: center; gap: .25rem; }
    .custom-pagination .page-link {
        border-radius: 10px !important;
        border: 1px solid var(--op-border) !important;
        color: var(--op-ink) !important;
        font-weight: 600;
        margin: 0 2px;
    }
    .custom-pagination .page-item.active .page-link {
        background: linear-gradient(135deg, var(--op-primary), var(--op-primary-light)) !important;
        border-color: transparent !important;
        color: #fff !important;
    }

    mark, .bg-warning.px-1.rounded { background: #fde68a !important; border-radius: 4px; padding: 0 2px; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid op-container py-5" style="padding-top: 100px !important;">

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
                    <span class="op-header-icon" style="background: linear-gradient(135deg,#1e3a8a,#3b5bdb);"><i class="fas fa-filter"></i></span>
                    Pilih Kategori Browse
                </div>
                <div class="op-card-body">

                    <!-- Statistik -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="op-mini-stat-card op-mg-primary">
                                <i class="fas fa-users fa-2x"></i>
                                <h5><?= number_format($total_authors) ?></h5>
                                <small>Total Pengarang</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="op-mini-stat-card op-mg-success">
                                <i class="fas fa-book fa-2x"></i>
                                <h5><?= number_format($total_titles) ?></h5>
                                <small>Total Judul</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="op-mini-stat-card op-mg-info">
                                <i class="fas fa-tags fa-2x"></i>
                                <h5><?= number_format($total_subjects) ?></h5>
                                <small>Total Subjek</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="op-mini-stat-card op-mg-warning">
                                <i class="fas fa-globe fa-2x"></i>
                                <h5><?= number_format($total_languages) ?></h5>
                                <small>Bahasa</small>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Tipe Browse -->
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="<?= base_url('opac/browse?type=author&letter=' . ($letter ?? 'A')) ?>"
                               class="op-type-btn t-author <?= ($browse_type ?? '') == 'author' ? 'active' : '' ?>">
                                <i class="fas fa-user op-type-icon"></i>
                                <strong>Pengarang</strong>
                                <small>Browse berdasarkan nama pengarang</small>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?= base_url('opac/browse?type=title&letter=' . ($letter ?? 'A')) ?>"
                               class="op-type-btn t-title <?= ($browse_type ?? '') == 'title' ? 'active' : '' ?>">
                                <i class="fas fa-book op-type-icon"></i>
                                <strong>Judul</strong>
                                <small>Browse berdasarkan judul buku</small>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?= base_url('opac/browse?type=subject&letter=' . ($letter ?? 'A')) ?>"
                               class="op-type-btn t-subject <?= ($browse_type ?? '') == 'subject' ? 'active' : '' ?>">
                                <i class="fas fa-tags op-type-icon"></i>
                                <strong>Subjek</strong>
                                <small>Browse berdasarkan subjek</small>
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
                        <span class="op-header-icon" style="background: linear-gradient(135deg,#4b5563,#9ca3af);"><i class="fas fa-sort-alpha-down"></i></span>
                        Pilih Huruf Awal
                        <span class="text-primary">(<?= $typeNames[$browse_type] ?? $browse_type ?>)</span>
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
        btn.className = 'btn btn-op-soft btn-sm ms-3 mt-2';
        btn.innerHTML = '<i class="fas fa-search me-1"></i>Jump';
        btn.onclick   = quickJumpToLetter;
        alphabetNav.appendChild(btn);
    }
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>