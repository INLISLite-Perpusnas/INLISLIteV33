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

    .op-search-card {
        background: #fff;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-lg);
        box-shadow: var(--op-shadow-sm);
        padding: 1.25rem 1.5rem;
        margin-bottom: 2rem;
    }
    .op-search-card .input-group-text {
        background: #fff; border-right: none; color: var(--op-muted);
    }
    .op-search-card .form-control {
        border-left: none; box-shadow: none;
    }
    .op-search-card .form-control:focus { border-color: var(--op-border); box-shadow: none; }

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

    .op-results-title { font-weight: 800; color: var(--op-ink); font-size: 1.35rem; }
    .op-count-badge {
        background: var(--op-primary-soft); color: var(--op-primary);
        border-radius: 999px; padding: .35rem .85rem; font-weight: 700; font-size: .82rem;
        display: inline-flex; align-items: center; gap: .3rem;
    }

    .article-card {
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-md);
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color .25s ease;
        box-shadow: var(--op-shadow-sm);
        overflow: hidden;
        background: #fff;
        height: 100%;
    }
    .article-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--op-shadow-md);
        border-color: #c7d2fe;
    }
    .article-card .ac-header {
        padding: .7rem 1rem;
        color: #fff;
        display: flex; align-items: center; gap: .5rem;
        font-size: .78rem; font-weight: 700;
        background: linear-gradient(135deg, #1e3a8a, #3b5bdb);
    }
    .article-card .ac-body { padding: 1.1rem; }
    .article-card .ac-title {
        font-weight: 700; color: var(--op-ink); font-size: 1rem;
        margin-bottom: .6rem; line-height: 1.35;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .article-card .ac-meta { font-size: .8rem; color: var(--op-muted); display: flex; align-items: flex-start; gap: .45rem; margin-bottom: .3rem; }
    .article-card .ac-meta i { color: var(--op-primary-light); margin-top: .15rem; width: 14px; }
    .article-card .ac-footer { padding: .8rem 1.1rem; border-top: 1px solid var(--op-border); background: #fafbfd; display: flex; justify-content: space-between; align-items: center; gap: .5rem; }

    .op-chip-pdf {
        background: #ecfdf3; color: #027a48; border: 1px solid #abefc6;
        border-radius: 999px; padding: .3rem .7rem; font-weight: 700; font-size: .74rem;
        display: inline-flex; align-items: center; gap: .3rem;
    }
    .op-chip-nopdf {
        background: #f4f4f5; color: #71717a; border: 1px solid #e4e4e7;
        border-radius: 999px; padding: .3rem .7rem; font-weight: 600; font-size: .74rem;
        display: inline-flex; align-items: center; gap: .3rem;
    }

    .op-empty-panel {
        text-align: center;
        padding: 3.5rem 1.5rem;
        background: #fff;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-lg);
        box-shadow: var(--op-shadow-sm);
    }
    .op-empty-panel i { color: #d1d5db; }

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
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid op-container py-5" style="padding-top: 100px !important;">

    <!-- Page Header -->
    <div class="op-page-header">
        <div class="op-icon-badge"><i class="fas fa-newspaper"></i></div>
        <h1>Artikel</h1>
        <p class="mb-0">Telusuri artikel dari terbitan berkala perpustakaan</p>
    </div>

    <!-- Search -->
    <div class="op-search-card">
        <form method="get" action="<?= base_url('artikel') ?>">
            <div class="input-group input-group-lg">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Cari judul artikel, penulis, atau subjek..." value="<?= esc($search ?? '') ?>">
                <button type="submit" class="btn btn-op-primary px-4">Cari</button>
                <?php if (!empty($search)) : ?>
                    <a href="<?= base_url('artikel') ?>" class="btn btn-op-soft px-3">
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
