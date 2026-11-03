<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<?php if (!empty($opac_banners)): ?>
    <link rel="preload" as="image" href="<?= base_url('uploads/banner/' . rawurlencode($opac_banners[0]->file_cover)) ?>" fetchpriority="high">
<?php endif; ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
<style>
    :root {
        --op-bg: #ffffff;
        --op-surface: #f8fafc;
        --op-border: #e5e9f0;
        --op-border-strong: #d5dbe5;
        --op-text: #111827;
        --op-text-2: #4b5563;
        --op-muted: #6b7280;
        --op-accent: #2d659e;
        --op-accent-soft: #eef4fa;
        --op-radius: 12px;
    }

    body {
        background-color: var(--op-bg);
        color: var(--op-text);
        font-family: 'Inter', 'Segoe UI', sans-serif;
        font-size: 1rem;
        line-height: 1.5;
    }

    /* ---------- Hero ---------- */
    .op-hero {
        padding: 128px 0 40px;
        background: var(--slate-100);
        border-bottom: 1px solid var(--op-border);
    }

    .op-hero.has-banner {
        padding-top: 85px;
        background: var(--op-bg);
        overflow-x: clip;
    }

    .op-hero.has-banner .col-12 {
        position: relative;
    }

    .op-hero.has-banner .op-hero-heading {
        position: absolute;
        z-index: 2;
        top: 40px;
        left: 52px;
        right: 52px;
        max-width: 40rem;
        text-align: left !important;
    }

    .op-hero.has-banner h1,
    .op-hero.has-banner .op-lead {
        color: #fff;
    }

    .op-hero.has-banner .op-search {
        position: relative;
        z-index: 4;
        margin-top: -96px;
    }

    .op-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: .875rem;
        font-weight: 600;
        line-height: 1.25rem;
        color: var(--op-accent);
        background: var(--op-accent-soft);
        padding: 6px 12px;
        border-radius: 999px;
        margin-bottom: 16px;
    }

    .op-hero h1 {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 3rem;
        color: var(--op-text);
        margin-bottom: 8px;
    }

    .op-hero .op-lead {
        color: var(--op-muted);
        font-size: 1.125rem;
        line-height: 1.625rem;
        margin-bottom: 32px;
    }

    /* ---------- Search ---------- */
    .op-search {
        background: var(--op-bg);
        border: 1px solid var(--op-border);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 1px 2px rgba(16, 24, 40, .04), 0 8px 24px rgba(16, 24, 40, .04);
    }

    .op-input,
    .op-input.form-select,
    .op-input.form-control {
        border: 1px solid var(--op-border);
        border-radius: 10px;
        background-color: var(--op-bg);
        color: var(--op-text);
        padding: 11px 14px;
        font-size: 1rem;
        line-height: 1.5rem;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .op-input.form-control-sm,
    .op-input.form-select-sm {
        padding: 8px 12px;
        font-size: 1rem;
        line-height: 1.5rem;
        background-color: var(--op-surface);
    }

    .op-input::placeholder { color: #9ca3af; }

    .op-input:focus {
        border-color: var(--op-accent);
        background-color: var(--op-bg);
        box-shadow: 0 0 0 3px rgba(45, 101, 158, .12);
    }

    .op-search-main {
        position: relative;
    }

    .op-search-main .fa-search {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        pointer-events: none;
    }

    .op-search-main .op-input { padding-left: 40px; }

    .op-filters { margin-top: 14px; }
    .op-filters-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px 20px;
    }

    .op-filters-toggle {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 8px 4px;
        border: 0;
        background: transparent;
        cursor: pointer;
        color: var(--op-accent);
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.5rem;
    }

    .op-filters-toggle:hover { color: #23517f; }
    .op-filters-toggle:focus-visible { outline: 2px solid var(--op-accent); outline-offset: 3px; }
    .op-filters-chevron { margin-left: 4px; transition: transform .15s ease; }
    .op-filters-toggle[aria-expanded="true"] .op-filters-chevron { transform: rotate(180deg); }
    .op-filter-reset { padding: 7px 12px; font-size: .875rem; }
    .op-filters-content { padding-top: 14px; }
    .op-filter-label {
        display: block;
        margin-bottom: 6px;
        color: var(--op-text-2);
        font-size: .875rem;
        font-weight: 600;
    }

    .btn-op {
        background: var(--op-accent);
        color: #fff;
        border: 1px solid var(--op-accent);
        border-radius: 10px;
        padding: 11px 18px;
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.5rem;
        transition: background .15s ease;
    }

    .btn-op:hover,
    .btn-op:focus {
        background: #23517f;
        border-color: #23517f;
        color: #fff;
    }

    .btn-op-outline {
        background: var(--op-bg);
        color: var(--op-text);
        border: 1px solid var(--op-border-strong);
        border-radius: 10px;
        padding: 9px 18px;
        font-size: 1rem;
        font-weight: 500;
        line-height: 1.5rem;
    }

    .btn-op-outline:hover {
        border-color: var(--op-accent);
        color: var(--op-accent);
    }

    .form-check-input:checked {
        background-color: var(--op-accent);
        border-color: var(--op-accent);
    }

    .op-notice {
        margin-top: 14px;
        padding: 10px 14px;
        border-radius: 10px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
        font-size: .875rem;
        line-height: 1.25rem;
    }

    /* ---------- Banner ---------- */
    .op-banner {
        position: relative;
        width: 100vw;
        height: clamp(320px, 32vw, 540px);
        margin-left: calc(50% - 50vw);
        overflow: hidden;
        background: var(--op-surface);
    }

    .op-banner::after {
        content: "";
        position: absolute;
        inset: 0;
        z-index: 1;
        background: rgba(10, 31, 57, .35);
        pointer-events: none;
    }

    .op-banner .carousel-inner,
    .op-banner .carousel-item { height: 100%; }

    .op-banner .carousel-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .op-banner .carousel-control-prev,
    .op-banner .carousel-control-next {
        z-index: 3;
        width: 44px;
        opacity: 1;
    }

    .op-banner .op-ctrl {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .92);
        color: var(--op-text);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .12);
        font-size: .8rem;
    }

    /* ---------- Layout ---------- */
    .op-content { padding: 40px 0 64px; }

    .op-results {
        scroll-margin-top: 100px;
        display: flex;
        flex-direction: column;
    }

    .op-pagination-footer {
        margin-top: auto;
        padding-top: 3rem;
    }

    .op-section-title {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 2rem;
        color: var(--op-text);
        margin: 0;
    }

    .op-section-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .op-count-pill {
        font-size: .875rem;
        line-height: 1.25rem;
        color: var(--op-muted);
        background: var(--op-surface);
        border: 1px solid var(--op-border);
        border-radius: 999px;
        padding: 4px 12px;
    }

    .op-collection-tabs {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        border-bottom: 1px solid var(--op-border);
        margin-bottom: 24px;
    }

    .op-collection-tab {
        flex: 0 0 auto;
        border: 0;
        border-bottom: 3px solid transparent;
        background: transparent;
        padding: 10px 14px;
        color: var(--op-text-2);
        font-weight: 600;
        white-space: nowrap;
    }

    .op-collection-tab:hover,
    .op-collection-tab.active { color: var(--op-accent); }
    .op-collection-tab.active { border-bottom-color: var(--op-accent); }
    .op-collection-tab:focus-visible { outline: 2px solid var(--op-accent); outline-offset: -3px; }
    .op-results .tab-pane:not(.active) { display: none; }
    /* ---------- Sidebar ---------- */
    .op-side-card {
        background: var(--op-bg);
        border: 1px solid #d6e2ef;
        border-radius: 28px;
        margin-bottom: 20px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(29, 72, 121, .04);
    }

    .op-side-total {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 28px 24px;
    }

    .op-total-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 66px;
        width: 66px;
        height: 66px;
        border-radius: 18px;
        background: #e9f0ff;
        color: #1e64ff;
        font-size: 1.65rem;
    }

    .op-side-heading {
        padding: 22px 24px;
        border-bottom: 1px solid #e4ebf4;
    }

    .op-side-title {
        display: flex;
        align-items: center;
        gap: 14px;
        font-size: 1.25rem;
        font-weight: 700;
        line-height: 1.75rem;
        color: #192e47;
        margin: 0;
    }

    .op-side-title i {
        flex: 0 0 30px;
        color: #1e64ff;
        font-size: 1.4rem;
    }

    .op-total {
        margin-top: 4px;
        font-size: 1.75rem;
        font-weight: 700;
        color: #1c50c5;
        line-height: 2.25rem;
        font-variant-numeric: tabular-nums;
    }

    .op-sidebar .op-side-card:last-child { margin-bottom: 0; }

    .op-side-list { padding: 12px 24px 14px; }

    .op-stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        min-height: 70px;
        padding: 14px 0;
        border-bottom: 1px solid #e9eff5;
        transition: background .15s ease;
    }

    .op-stat-item:hover { background: var(--op-surface); }

    .op-stat-item:last-child { border-bottom: 0; }

    .op-stat-item a {
        color: #4d667f;
        font-size: 1rem;
        line-height: 1.5rem;
        text-decoration: none;
        min-width: 0;
    }

    .op-stat-item:hover a,
    .op-stat-item a:focus-visible { color: var(--op-accent); }

    .op-stat-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        min-width: 36px;
        height: 38px;
        padding: 0 6px;
        border-radius: 12px;
        background: #e9f0ff;
        color: #1c50c5;
        font-size: .875rem;
        font-weight: 700;
        line-height: 1.25rem;
        font-variant-numeric: tabular-nums;
    }

    /* ---------- Catalog card ---------- */
    .op-card {
        display: flex;
        gap: 16px;
        height: 100%;
        padding: 16px;
        background: var(--op-bg);
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius);
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .op-card:hover {
        border-color: var(--op-border-strong);
        box-shadow: 0 6px 20px rgba(16, 24, 40, .06);
    }

    .op-cover {
        position: relative;
        flex: 0 0 104px;
        width: 104px;
        height: 152px;
        border-radius: 8px;
        overflow: hidden;
        background: var(--op-surface);
        border: 1px solid var(--op-border);
    }

    .op-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .op-tag {
        position: absolute;
        top: 6px;
        left: 6px;
        font-size: .75rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .95);
        color: var(--op-text);
        border: 1px solid var(--op-border);
        line-height: 1rem;
    }



    .op-tag.is-ebook { color: var(--op-accent); }

    .op-body {
        display: flex;
        flex-direction: column;
        min-width: 0;
        flex: 1;
    }

    .op-title {
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.5rem;
        margin: 0 0 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .op-title a {
        color: var(--op-text);
        text-decoration: none;
    }

    .op-title a:hover { color: var(--op-accent); }

    /* seluruh kartu dapat diklik lewat link judul */
    .op-title a::after {
        content: "";
        position: absolute;
        inset: 0;
    }

    .op-card { position: relative; }

    .op-meta {
        font-size: .875rem;
        line-height: 1.25rem;
        color: var(--op-text-2);
    }

    .op-meta span {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
        min-width: 0;
    }

    .op-meta span > span.text-truncate { display: block; margin: 0; }

    .op-meta i {
        width: 14px;
        flex-shrink: 0;
        text-align: center;
        color: #9ca3af;
        font-size: .75rem;
    }

    .op-subject {
        margin-top: auto;
        padding-top: 10px;
    }

    .op-chip {
        display: inline-block;
        max-width: 100%;
        font-size: .75rem;
        font-weight: 500;
        line-height: 1rem;
        color: var(--op-text-2);
        background: var(--op-surface);
        border: 1px solid var(--op-border);
        border-radius: 6px;
        padding: 3px 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle;
    }

    /* ---------- Empty state ---------- */
    .op-empty {
        text-align: center;
        padding: 56px 24px;
        border: 1px dashed var(--op-border-strong);
        border-radius: var(--op-radius);
        background: var(--op-bg);
    }

    .op-empty-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: var(--op-surface);
        border: 1px solid var(--op-border);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--op-muted);
        font-size: 1.25rem;
        margin-bottom: 16px;
    }

    .op-empty-title {
        color: var(--op-text);
        font-size: 1.25rem;
        font-weight: 600;
        line-height: 1.75rem;
    }

    /* ---------- Pagination ---------- */
    .pagination { gap: 4px; flex-wrap: wrap; justify-content: center; }

    .pagination li a,
    .pagination li span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 10px;
        color: var(--op-text-2);
        background: var(--op-bg);
        border: 1px solid var(--op-border);
        border-radius: 8px;
        text-decoration: none;
        font-size: .875rem;
        font-weight: 500;
        line-height: 1.25rem;
        transition: all .15s ease;
    }

    .pagination li a:hover {
        border-color: var(--op-accent);
        color: var(--op-accent);
    }

    .pagination li.active span,
    .pagination li.active a {
        background: var(--op-accent);
        border-color: var(--op-accent);
        color: #fff;
    }

    .op-footnote {
        font-size: .875rem;
        line-height: 1.25rem;
        color: var(--op-muted);
    }

    @media (max-width: 991px) {
        .op-sidebar { position: static !important; margin-top: 32px; }
        .op-banner { height: clamp(320px, 50vw, 440px); }
        .op-hero.has-banner .op-hero-heading { top: 32px; }
        .op-hero.has-banner .op-search { margin-top: -80px; }
    }

    @media (max-width: 575px) {
        .op-hero { padding-top: 104px; }
        .op-hero.has-banner { padding-top: 96px; }
        .op-hero h1 { font-size: 2rem; line-height: 2.5rem; }
        .op-search { padding: 16px; }
        .op-banner { height: 280px; }
        .op-hero.has-banner .op-hero-heading { top: 20px; left: 28px; right: 28px; }
        .op-hero.has-banner .op-lead { font-size: 1rem; line-height: 1.5rem; }
        .op-hero.has-banner .op-search { margin-top: -48px; }
        .op-cover { flex-basis: 88px; width: 88px; height: 128px; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="op-hero<?= !empty($opac_banners) ? ' has-banner' : '' ?>">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <?php if (!empty($opac_banners)): ?>
                    <div id="opacBannerCarousel" class="carousel slide op-banner" data-bs-ride="carousel" aria-label="Banner informasi perpustakaan">
                        <div class="carousel-inner">
                            <?php foreach ($opac_banners as $i => $b): ?>
                                <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                                    <img src="<?= base_url('uploads/banner/' . rawurlencode($b->file_cover)) ?>" alt=""
                                        width="1600" height="500" decoding="async"
                                        <?= $i === 0 ? 'fetchpriority="high"' : 'loading="lazy" fetchpriority="low"' ?>>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($opac_banners) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#opacBannerCarousel" data-bs-slide="prev" aria-label="Banner sebelumnya">
                                <span class="op-ctrl"><i class="fas fa-chevron-left"></i></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#opacBannerCarousel" data-bs-slide="next" aria-label="Banner berikutnya">
                                <span class="op-ctrl"><i class="fas fa-chevron-right"></i></span>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="text-center op-hero-heading">

                    <h1>Cari Koleksi</h1>
                    <p class="op-lead">Temukan referensi, buku, dan koleksi literatur dengan mudah dan cepat.</p>
                </div>

                <?php $advanced_filters_open = request()->getVar('Author') || request()->getVar('Publisher') || request()->getVar('Subject') || request()->getVar('PublishYear') || !empty($worksheet_id); ?>
                <div class="op-search">
                    <form method="GET" action="<?= base_url('opac') ?>#hasil-pencarian">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="visually-hidden" for="opac-search-by">Cari berdasarkan</label>
                                <select class="form-select op-input" name="search_by" id="opac-search-by">
                                    <option value="" <?= ($search_by ?? '') == '' ? 'selected' : '' ?>>Semua Kriteria</option>
                                    <option value="Title" <?= ($search_by ?? '') == 'Title' ? 'selected' : '' ?>>Judul</option>
                                    <option value="Author" <?= ($search_by ?? '') == 'Author' ? 'selected' : '' ?>>Pengarang</option>
                                    <option value="Subject" <?= ($search_by ?? '') == 'Subject' ? 'selected' : '' ?>>Subjek</option>
                                    <option value="Publisher" <?= ($search_by ?? '') == 'Publisher' ? 'selected' : '' ?>>Penerbit</option>
                                    <option value="ISBN" <?= ($search_by ?? '') == 'ISBN' ? 'selected' : '' ?>>ISBN</option>
                                </select>
                            </div>
                            <div class="col-md-7 op-search-main">
                                <label class="visually-hidden" for="opac-search">Kata kunci pencarian</label>
                                <i class="fas fa-search" aria-hidden="true"></i>
                                <input type="search" class="form-control op-input" name="search" id="opac-search"
                                    placeholder="Judul, pengarang, subjek, atau ISBN..." value="<?= esc($search ?? '') ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-op w-100">Cari</button>
                            </div>
                        </div>

                        <div class="op-filters">
                            <div class="op-filters-actions">
                                <button type="button" class="op-filters-toggle" data-bs-toggle="collapse" data-bs-target="#opac-advanced-filters" aria-controls="opac-advanced-filters" aria-expanded="<?= $advanced_filters_open ? 'true' : 'false' ?>">
                                    <i class="fas fa-sliders-h" aria-hidden="true"></i>
                                    Filter lanjutan
                                    <i class="fas fa-chevron-down op-filters-chevron" aria-hidden="true"></i>
                                </button>
                                <?php if (!empty($semantic_enabled)) : ?>
                                    <div class="form-check form-switch mb-0" title="Cari berdasarkan makna kata kunci, bukan hanya kata yang sama persis">
                                        <input class="form-check-input" type="checkbox" role="switch" name="semantic" value="1" id="opac-semantic"
                                            <?= request()->getVar('semantic') === '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label small" style="color: var(--op-text-2);" for="opac-semantic">Pencarian semantik</label>
                                    </div>
                                <?php endif; ?>
                                <a href="<?= base_url('opac') ?>" class="btn btn-op-outline op-filter-reset">Reset pencarian</a>
                            </div>
                            <div id="opac-advanced-filters" class="collapse<?= $advanced_filters_open ? ' show' : '' ?>">
                                <div class="op-filters-content row g-3">
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label class="op-filter-label" for="opac-author">Pengarang</label>
                                        <input type="text" class="form-control op-input" name="Author" id="opac-author" placeholder="Nama pengarang" value="<?= esc(request()->getVar('Author')) ?>">
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label class="op-filter-label" for="opac-publisher">Penerbit</label>
                                        <input type="text" class="form-control op-input" name="Publisher" id="opac-publisher" placeholder="Nama penerbit" value="<?= esc(request()->getVar('Publisher')) ?>">
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label class="op-filter-label" for="opac-subject">Subjek</label>
                                        <input type="text" class="form-control op-input" name="Subject" id="opac-subject" placeholder="Subjek koleksi" value="<?= esc(request()->getVar('Subject')) ?>">
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label class="op-filter-label" for="opac-year">Tahun terbit</label>
                                        <input type="text" inputmode="numeric" class="form-control op-input" name="PublishYear" id="opac-year" placeholder="Contoh: 2024" value="<?= esc(request()->getVar('PublishYear')) ?>">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <label class="visually-hidden" for="opac-worksheet">Jenis bahan</label>
                                        <select class="form-select form-select-sm op-input" name="Worksheet_id" id="opac-worksheet">
                                            <option value="">Semua Jenis Bahan</option>
                                            <?php foreach (($worksheets ?? []) as $ws) : ?>
                                                <option value="<?= esc($ws->ID) ?>" <?= (string) ($worksheet_id ?? '') === (string) $ws->ID ? 'selected' : '' ?>>
                                                    <?= esc($ws->Name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($semantic_unavailable)) : ?>
                            <div class="op-notice" role="status">
                                <i class="fas fa-info-circle me-1"></i>Pencarian semantik sedang tidak tersedia; hasil di bawah memakai pencarian kata kunci biasa.
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="op-content">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar -->
            <aside class="col-lg-3 order-2 order-lg-1">
                <div class="op-sidebar position-sticky" style="top: 96px;">
                    <div class="op-side-card op-side-total">
                        <div class="op-total-icon"><i class="fas fa-book-open" aria-hidden="true"></i></div>
                        <div>
                            <h2 class="op-side-title">Total katalog</h2>
                            <div class="op-total"><?= isset($total_records) ? number_format($total_records) : '0' ?></div>
                        </div>
                    </div>

                    <?php
                    $facets = [
                        ['data' => $publisher_counts ?? [],        'field' => 'Publisher',       'label' => 'Penerbit',  'icon' => 'fa-building',       'limit' => 5],
                        ['data' => $author_counts ?? [],           'field' => 'Author',          'label' => 'Pengarang', 'icon' => 'fa-user',      'limit' => 5],
                        ['data' => $subject_counts ?? [],          'field' => 'Subject',         'label' => 'Subjek Populer',    'icon' => 'fa-tags',           'limit' => 5],
                        ['data' => $year_counts ?? [],             'field' => 'PublishYear',     'label' => 'Tahun Terbit',      'icon' => 'fa-calendar-alt',   'limit' => 5],
                        ['data' => $publish_location_counts ?? [], 'field' => 'PublishLocation', 'label' => 'Kota Terbit',       'icon' => 'fa-map-marker-alt', 'limit' => 8],
                    ];
                    foreach ($facets as $facet):
                        if (empty($facet['data'])) continue;
                        $items = $facet['data'];
                        arsort($items);
                        $items = array_slice($items, 0, $facet['limit'], true);
                    ?>
                        <div class="op-side-card">
                            <div class="op-side-heading">
                                <h2 class="op-side-title"><i class="fas <?= $facet['icon'] ?>" aria-hidden="true"></i><?= $facet['label'] ?></h2>
                            </div>
                            <div class="op-side-list">
                                <?php foreach ($items as $name => $count):
                                    $name = (string) $name;
                                    if (trim($name) === '' || ($facet['field'] === 'PublishYear' && $name === '1970')) continue;
                                ?>
                                    <div class="op-stat-item">
                                        <a href="<?= esc(buildFilterUrl($facet['field'], $name) . '#hasil-pencarian') ?>" class="text-truncate" title="<?= esc($name) ?>">
                                            <?= esc($name) ?>
                                        </a>
                                        <span class="op-stat-badge"><?= number_format($count) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </aside>

            <!-- Katalog -->
            <div id="hasil-pencarian" class="col-lg-9 order-1 order-lg-2 op-results">
                <?php
                $defaultCover = base_url('assets/img/default-cover.webp');
                $display_groups = !empty($show_collection_tabs) ? ($collection_tabs ?? []) : [
                    'results' => ['label' => 'Hasil Pencarian', 'books' => $catalogs ?? [], 'count' => $total_records ?? 0],
                ];
                ?>

                <?php if (!empty($show_collection_tabs) && $display_groups): ?>
                    <div class="nav op-collection-tabs" role="tablist" aria-label="Jenis koleksi">
                        <?php foreach ($display_groups as $tab_key => $group): ?>
                            <button type="button" class="op-collection-tab<?= $tab_key === array_key_first($display_groups) ? ' active' : '' ?>"
                                id="opac-tab-<?= esc($tab_key) ?>" data-bs-toggle="tab" data-bs-target="#opac-panel-<?= esc($tab_key) ?>"
                                role="tab" aria-controls="opac-panel-<?= esc($tab_key) ?>" aria-selected="<?= $tab_key === array_key_first($display_groups) ? 'true' : 'false' ?>">
                                <?= esc($group['label']) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($show_collection_tabs) && !$display_groups): ?>
                    <div class="op-empty">Belum ada koleksi yang ditampilkan.</div>
                <?php endif; ?>

                <?php foreach ($display_groups as $tab_key => $group): ?>
                    <div class="<?= !empty($show_collection_tabs) ? 'tab-pane fade' . ($tab_key === array_key_first($display_groups) ? ' show active' : '') : '' ?>"
                        <?= !empty($show_collection_tabs) ? 'id="opac-panel-' . esc($tab_key) . '" role="tabpanel" aria-labelledby="opac-tab-' . esc($tab_key) . '"' : '' ?>>
                        <div class="op-section-head">
                            <h2 class="op-section-title"><?= esc($group['label']) ?></h2>
                            <span class="op-count-pill"><?= number_format($group['count'] ?? count($group['books'])) ?> koleksi</span>
                        </div>
                        <?php if (!empty($group['books'])): ?>
                            <div class="row g-3">
                                <?php foreach ($group['books'] as $catalog):
                                    $c = (object) $catalog;
                                    $bookID        = $c->ID ?? 0;
                                    $bookTitle     = $c->Title ?? 'Tanpa Judul';
                                    $bookAuthor    = $c->Author ?? '-';
                                    $bookPublisher = $c->Publisher ?? '-';
                                    $bookYear      = $c->PublishYear ?? '-';
                                    $bookSubject   = $c->Subject ?? '';

                                    $coverFileName = basename((string) ($c->CoverURL ?? ''));
                                    $hasCover = $coverFileName !== '' && is_file(FCPATH . 'uploads/katalog/' . $coverFileName);
                                    $coverPath = $hasCover ? base_url('uploads/katalog/' . rawurlencode($coverFileName)) : $defaultCover;
                                    $detailUrl = base_url('opac/detail/' . $bookID);
                                ?>
                                    <div class="col-md-6">
                                        <article class="op-card">
                                            <div class="op-cover">
                                                <img src="<?= $coverPath ?>" alt="Sampul <?= esc($bookTitle) ?>"
                                                    width="104" height="152" loading="lazy" decoding="async"
                                                    onerror="this.onerror=null;this.src='<?= $defaultCover ?>'">
                                                <?php if (isset($c->ISDRM) && $c->ISDRM == 1): ?>
                                                    <span class="op-tag is-ebook">E-Book</span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="op-body">
                                                <h3 class="op-title">
                                                    <a href="<?= $detailUrl ?>" title="<?= esc($bookTitle) ?>"><?= esc($bookTitle) ?></a>
                                                </h3>

                                                <div class="op-meta">
                                                    <span title="<?= esc($bookAuthor) ?>"><i class="fas fa-user"></i><span class="text-truncate"><?= esc($bookAuthor) ?></span></span>
                                                    <span title="<?= esc($bookPublisher) ?>"><i class="fas fa-building"></i><span class="text-truncate"><?= esc($bookPublisher) ?></span></span>
                                                    <span><i class="fas fa-calendar"></i><?= esc($bookYear) ?></span>
                                                </div>

                                                <div class="op-subject">
                                                    <span class="op-chip" title="<?= esc($bookSubject ?: 'Umum') ?>"><?= esc($bookSubject ?: 'Umum') ?></span>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="op-empty">
                                <div class="op-empty-icon"><i class="fas fa-search"></i></div>
                                <h3 class="op-empty-title mb-2">
                                    <?php if (!empty($search)): ?>
                                        Koleksi tidak ditemukan
                                    <?php else: ?>
                                        Mulai temukan koleksi
                                    <?php endif; ?>
                                </h3>
                                <p class="mb-4 mx-auto" style="color: var(--op-muted); max-width: 420px;">
                                    <?php if (!empty($search)): ?>
                                        Coba gunakan kata kunci yang lebih umum atau periksa ejaan Anda.
                                    <?php else: ?>
                                        Gunakan kolom pencarian di atas untuk menelusuri katalog.
                                    <?php endif; ?>
                                </p>
                                <a href="<?= base_url('opac') ?>" class="btn btn-op-outline">
                                    <i class="fas fa-redo me-2"></i>Muat ulang katalog
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if ($tab_key === 'latest'): ?>
                            <div class="op-pagination-footer d-flex flex-column align-items-center gap-2">
                                <?php if (!empty($pager)): ?>
                                    <nav class="custom-pagination" aria-label="Navigasi halaman koleksi terbaru">
                                        <?= is_string($pager) ? $pager : $pager->links('default', 'opac_pagination') ?>
                                    </nav>
                                <?php endif; ?>

                                <?php if (isset($execution_time)): ?>
                                    <div class="op-footnote">Dimuat dalam <?= number_format($execution_time, 4) ?> detik</div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($show_collection_tabs)): ?>
                    <div class="op-pagination-footer d-flex flex-column align-items-center gap-2">
                        <?php if (!empty($pager)): ?>
                            <nav class="custom-pagination" aria-label="Navigasi halaman">
                                <?php
                                // Tangani dua kemungkinan: objek (non-cache) atau string HTML (cache hit)
                                echo is_string($pager) ? $pager : $pager->links('default', 'opac_pagination');
                                ?>
                            </nav>
                        <?php endif; ?>

                        <?php if (isset($execution_time)): ?>
                            <div class="op-footnote">Dimuat dalam <?= number_format($execution_time, 4) ?> detik</div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
