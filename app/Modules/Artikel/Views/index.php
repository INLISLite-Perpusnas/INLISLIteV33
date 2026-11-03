<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<style>
    /* Artikel - gaya INA Digital (Inter, skala 28/36, 18/26, 16/24, 14/20, 12/16; biru #2d659e) */
    :root {
        --op-primary: #2d659e;
        --op-primary-light: #2d659e;
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
        --op-shadow-md: 0 8px 24px rgba(16, 24, 40, .12);
        --op-focus: 0 0 0 3px rgba(45, 101, 158, .2);
    }

    body { background: var(--op-bg); }

    .op-container { max-width: 1600px; margin-left: auto; margin-right: auto; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: var(--op-ink); }
    @media (min-width: 1400px) {
        .op-container { padding-left: 2rem; padding-right: 2rem; }
    }

    /* Header halaman: rata kiri, H4 */
    .op-page-header { text-align: left; margin-bottom: 24px; }
    .op-page-header .op-icon-badge { display: none; }
    .op-page-header h1 { font-size: 28px; line-height: 36px; font-weight: 700; letter-spacing: 0; color: var(--op-ink); margin: 0 0 4px; }
    .op-page-header p { font-size: 16px; line-height: 24px; color: var(--op-muted); }

    /* Breadcrumb INA: teks polos */
    .op-breadcrumb .breadcrumb {
        background: transparent;
        padding: 0;
        border-radius: 0;
        box-shadow: none;
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 0;
        font-size: 14px;
        line-height: 20px;
    }
    .op-breadcrumb .breadcrumb-item a { color: var(--op-primary); text-decoration: none; font-weight: 500; }
    .op-breadcrumb .breadcrumb-item a:hover { text-decoration: underline; }
    .op-breadcrumb .breadcrumb-item.active { color: var(--op-ink); font-weight: 600; }

    /* Tombol: utama solid, sekunder bergaris, sudut 8px */
    .btn-op-primary,
    .btn-op-soft {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 40px;
        padding: 8px 16px;
        font-size: 14px;
        line-height: 20px;
        font-weight: 600;
        border-radius: var(--op-radius-sm) !important;
        box-shadow: none;
    }
    .btn-op-primary.btn-sm,
    .btn-op-soft.btn-sm { min-height: 32px; padding: 4px 12px; }
    .btn-op-primary.btn-lg,
    .btn-op-soft.btn-lg { min-height: 44px; padding: 10px 20px; font-size: 16px; line-height: 24px; }
    .btn-op-primary { background: var(--op-primary); border: 1px solid var(--op-primary); color: #fff; }
    .btn-op-primary:hover { background: var(--op-primary-hover); border-color: var(--op-primary-hover); color: #fff; filter: none; }
    .btn-op-soft { background: #fff; border: 1px solid var(--op-primary); color: var(--op-primary); }
    .btn-op-soft:hover { background: var(--op-primary-soft); border-color: var(--op-primary-hover); color: var(--op-primary-hover); }
    .btn-op-primary:focus-visible,
    .btn-op-soft:focus-visible { outline: none; box-shadow: var(--op-focus); }

    /* Pencarian: Text Input INA + tombol */
    .op-search-card {
        background: #fff;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-lg);
        box-shadow: var(--op-shadow-sm);
        padding: 16px 24px;
        margin-bottom: 24px;
    }
    .op-search-card .input-group { gap: 8px; flex-wrap: nowrap; }
    .op-search-card .op-search-field { position: relative; flex: 1; min-width: 0; }
    .op-search-card .op-search-field i {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: var(--op-muted); pointer-events: none;
    }
    .op-search-card .form-control {
        width: 100%;
        min-height: 44px;
        padding: 10px 14px 10px 40px;
        font-size: 16px;
        line-height: 24px;
        color: var(--op-ink);
        border: 1px solid var(--op-border-strong);
        border-radius: var(--op-radius-sm) !important;
        box-shadow: none;
    }
    .op-search-card .form-control:hover { border-color: #9ca3af; }
    .op-search-card .form-control:focus { border-color: var(--op-primary); box-shadow: var(--op-focus); }
    .op-search-card .btn-op-primary,
    .op-search-card .btn-op-soft { min-height: 44px; }

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

    /* Kartu artikel */
    .article-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        background: #fff;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-md);
        box-shadow: var(--op-shadow-sm);
        overflow: hidden;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .article-card:hover { border-color: var(--op-primary); box-shadow: 0 4px 12px rgba(16, 24, 40, .08); }
    .article-card .ac-header {
        padding: 12px 16px 0;
        display: flex; align-items: center; gap: 6px;
        font-size: 12px; line-height: 16px; font-weight: 600;
        color: var(--op-muted);
        background: transparent;
    }
    .article-card .ac-header i { color: var(--op-primary); }
    .article-card .ac-body { flex: 1; padding: 8px 16px 16px; }
    .article-card .ac-title {
        font-size: 16px; line-height: 24px; font-weight: 600; color: var(--op-ink);
        margin-bottom: 12px;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .article-card .ac-meta { display: flex; align-items: flex-start; gap: 8px; margin-bottom: 4px; font-size: 14px; line-height: 20px; color: var(--op-muted); }
    .article-card .ac-meta i { width: 14px; margin-top: 3px; color: #9ca3af; }
    .article-card .ac-footer {
        display: flex; justify-content: space-between; align-items: center; gap: 8px;
        padding: 12px 16px;
        border-top: 1px solid var(--op-border);
        background: #fff;
    }

    /* Badge ketersediaan PDF */
    .op-chip-pdf,
    .op-chip-nopdf {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 10px;
        font-size: 12px; line-height: 16px; font-weight: 600;
        border-radius: 999px;
        border: 1px solid transparent;
    }
    .op-chip-pdf { background: #e9f6ec; color: #1f7a36; border-color: #a7d9b3; }
    .op-chip-nopdf { background: #f3f4f6; color: var(--op-muted); border-color: var(--op-border); }

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
    .op-empty-panel p { font-size: 14px; line-height: 20px; }

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

    @media (max-width: 576px) {
        .op-page-header h1 { font-size: 24px; line-height: 32px; }
        .op-search-card { padding: 16px; }
        .op-search-card .input-group { flex-wrap: wrap; }
        .op-search-card .op-search-field { flex-basis: 100%; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid op-container py-5" style="padding-top: 110px !important;">

    <!-- Page Header -->
    <div class="op-page-header">
        <div class="op-icon-badge"><i class="fas fa-newspaper"></i></div>
        <h1>Artikel</h1>
        <p class="mb-0">Telusuri artikel dari terbitan berkala perpustakaan</p>
    </div>

    <!-- Search -->
    <div class="op-search-card">
        <form method="get" action="<?= base_url('artikel') ?>">
            <div class="input-group">
                <div class="op-search-field">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <input type="text" name="search" class="form-control" placeholder="Cari judul artikel, penulis, atau subjek..." aria-label="Cari artikel" value="<?= esc($search ?? '') ?>">
                </div>
                <button type="submit" class="btn btn-op-primary px-4">Cari</button>
                <?php if (!empty($search)) : ?>
                    <a href="<?= base_url('artikel') ?>" class="btn btn-op-soft px-3" aria-label="Hapus pencarian">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Result Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="op-results-title mb-0">
            <i class="fas fa-list-ul me-2 text-primary"></i>
            <?= !empty($search) ? 'Hasil pencarian "' . esc($search) . '"' : 'Semua Artikel' ?>
            <span class="op-count-badge ms-2">
                <?= number_format($pager->getTotal('artikel')) ?> artikel
            </span>
        </h3>
    </div>

    <?php if (!empty($articles)) : ?>
        <div class="row">
            <?php foreach ($articles as $article) : ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="article-card">
                        <div class="ac-header">
                            <i class="fas fa-book-open"></i>
                            <span class="text-truncate"><?= esc($article->CatalogTitle ?? 'Terbitan Berkala') ?></span>
                        </div>
                        <div class="ac-body">
                            <div class="ac-title" title="<?= esc($article->Title) ?>"><?= esc($article->Title) ?></div>

                            <?php if (!empty($article->Creator)) : ?>
                                <div class="ac-meta">
                                    <i class="fas fa-user"></i>
                                    <span><?= esc($article->Creator) ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($article->EDISISERIAL)) : ?>
                                <div class="ac-meta">
                                    <i class="fas fa-hashtag"></i>
                                    <span>Edisi <?= esc($article->EDISISERIAL) ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($article->Subject)) : ?>
                                <div class="ac-meta">
                                    <i class="fas fa-tag"></i>
                                    <span><?= esc(mb_strimwidth($article->Subject, 0, 60, '...')) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="ac-footer">
                            <?php if (!empty($article->FileId)) : ?>
                                <span class="op-chip-pdf"><i class="fas fa-file-pdf"></i>PDF Tersedia</span>
                            <?php else : ?>
                                <span class="op-chip-nopdf"><i class="fas fa-file"></i>Belum Ada PDF</span>
                            <?php endif; ?>
                            <a href="<?= base_url('artikel/detail/' . $article->id) ?>" class="btn btn-op-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>Detail
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="row mt-4">
            <div class="col-12 d-flex flex-column align-items-center">
                <div class="custom-pagination">
                    <?= $pager->links('artikel', 'opac_pagination') ?>
                </div>
                <div class="text-muted mt-2">
                    <small>
                        <i class="fas fa-list text-primary me-1"></i>
                        Halaman <strong><?= $pager->getCurrentPage('artikel') ?></strong>
                        dari <strong><?= max(1, $pager->getPageCount('artikel')) ?></strong>
                        &nbsp;|&nbsp;
                        Total <strong><?= number_format($pager->getTotal('artikel')) ?></strong> artikel
                    </small>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="op-empty-panel">
            <i class="fas fa-newspaper fa-3x mb-3"></i>
            <h4 class="text-muted fw-bold">
                <?= !empty($search) ? 'Artikel tidak ditemukan' : 'Belum ada artikel' ?>
            </h4>
            <p class="text-muted mb-4">
                <?= !empty($search) ? 'Coba kata kunci lain.' : 'Artikel akan muncul di sini setelah ditambahkan dan ditandai "Tampilkan di OPAC".' ?>
            </p>
            <?php if (!empty($search)) : ?>
                <a href="<?= base_url('artikel') ?>" class="btn btn-op-primary">
                    <i class="fas fa-arrow-left me-2"></i>Lihat Semua Artikel
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
