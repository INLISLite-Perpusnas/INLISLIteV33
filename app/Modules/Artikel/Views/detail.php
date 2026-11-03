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

    /* Kartu utama */
    .op-card {
        background: #fff;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-lg);
        box-shadow: var(--op-shadow-sm);
        overflow: hidden;
    }
    .op-hero-header {
        background: #fff;
        padding: 16px 24px;
        border-bottom: 1px solid var(--op-border);
    }
    .op-hero-header::after { display: none; }
    .op-hero-header h4 { font-size: 18px; line-height: 26px; font-weight: 600; color: var(--op-ink) !important; }
    .op-hero-header h4 i { color: var(--op-primary); }
    .op-id-badge {
        background: #f3f4f6;
        color: var(--op-ink-subtle);
        font-size: 12px;
        line-height: 16px;
        font-weight: 600;
        border: 0;
        padding: 2px 10px;
        border-radius: 999px;
    }
    .op-body { padding: 24px; }
    .op-title { font-size: 28px !important; line-height: 36px; font-weight: 700; letter-spacing: 0; color: var(--op-ink); }

    /* Grid informasi */
    .op-info-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px 16px; }
    @media (max-width: 991px) { .op-info-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 767px) { .op-info-grid { grid-template-columns: 1fr; } }
    .op-info-item {
        display: flex; gap: 12px; padding: 12px 16px;
        background: #fff; border: 1px solid var(--op-border); border-radius: var(--op-radius-sm);
    }
    .op-info-icon {
        flex: 0 0 auto; width: 36px; height: 36px;
        display: flex; align-items: center; justify-content: center;
        border-radius: var(--op-radius-sm); background: var(--op-primary-soft); color: var(--op-primary); font-size: 14px;
    }
    .op-info-label { font-size: 12px; line-height: 16px; text-transform: none; letter-spacing: 0; color: var(--op-muted); font-weight: 500; margin-bottom: 2px; }
    .op-info-value { font-size: 16px; line-height: 24px; color: var(--op-ink); font-weight: 500; word-break: break-word; }

    /* Judul bagian */
    .op-section-title { font-size: 18px; line-height: 26px; font-weight: 600; color: var(--op-ink); display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }
    .op-section-title .op-info-icon { width: 28px; height: 28px; font-size: 12px; }

    /* Subjek */
    .op-subject-panel { background: transparent; border: 0; border-radius: 0; padding: 0; display: flex; flex-wrap: wrap; gap: 8px; }
    .op-chip {
        background: var(--op-primary-soft); border: 1px solid #cfdeee; color: var(--op-primary);
        font-size: 14px; line-height: 20px; font-weight: 500;
        padding: 4px 12px; border-radius: 999px;
        display: inline-flex; align-items: center; gap: 6px;
    }

    /* Abstrak: teks bacaan */
    .op-abstract-panel {
        background: #f9fafb;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-sm);
        padding: 16px;
        color: var(--op-ink-subtle);
        font-size: 16px;
        line-height: 26px;
    }

    /* Terbitan induk */
    .op-catalog-panel {
        background: #fff;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-sm);
        padding: 12px 16px;
        display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
    }
    .op-catalog-panel .op-catalog-title { font-size: 16px; line-height: 24px; font-weight: 600; color: var(--op-ink); }
    .op-catalog-panel small { font-size: 14px; line-height: 20px; }

    /* Konten digital: alert INA (sukses / netral) */
    .op-read-panel {
        display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;
        padding: 16px 20px;
        background: #e9f6ec;
        border: 1px solid #a7d9b3;
        border-radius: var(--op-radius-sm);
        color: #1f7a36;
    }
    .op-read-panel .op-read-title { font-size: 16px; line-height: 24px; font-weight: 600; }
    .op-read-panel .op-read-desc { font-size: 14px; line-height: 20px; color: var(--op-ink-subtle); }
    .op-read-panel.op-read-empty {
        background: #f9fafb;
        border: 1px dashed var(--op-border-strong);
        color: var(--op-ink-subtle);
    }

    @media (max-width: 768px) {
        .op-body { padding: 16px; }
        .op-hero-header { padding: 16px; }
        .op-title { font-size: 24px !important; line-height: 32px; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid op-container py-5" style="padding-top: 110px !important;">

    <!-- Breadcrumb -->
    <nav class="op-breadcrumb mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('artikel') ?>"><i class="fas fa-newspaper me-1"></i>Artikel</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= esc(mb_strimwidth($article->Title, 0, 40, '...')) ?></li>
        </ol>
    </nav>

    <div class="op-card mb-4">
        <div class="op-hero-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="mb-0"><i class="fas fa-newspaper me-2"></i>Detail Artikel</h4>
            <span class="op-id-badge">ID #<?= esc($article->id) ?></span>
        </div>

        <div class="op-body">
            <h1 class="op-title h3 mb-3"><?= esc($article->Title) ?></h1>

            <!-- Info Grid -->
            <div class="op-info-grid mb-4">
                <?php if (!empty($article->Creator)) : ?>
                    <div class="op-info-item">
                        <div class="op-info-icon"><i class="fas fa-user"></i></div>
                        <div>
                            <div class="op-info-label">Kreator</div>
                            <div class="op-info-value"><?= esc($article->Creator) ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($article->Contributor)) : ?>
                    <div class="op-info-item">
                        <div class="op-info-icon"><i class="fas fa-users"></i></div>
                        <div>
                            <div class="op-info-label">Kontributor</div>
                            <div class="op-info-value"><?= esc($article->Contributor) ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($article->EDISISERIAL)) : ?>
                    <div class="op-info-item">
                        <div class="op-info-icon"><i class="fas fa-hashtag"></i></div>
                        <div>
                            <div class="op-info-label">Edisi Serial</div>
                            <div class="op-info-value"><?= esc($article->EDISISERIAL) ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($article->TANGGAL_TERBIT_EDISI_SERIAL)) : ?>
                    <div class="op-info-item">
                        <div class="op-info-icon"><i class="fas fa-calendar"></i></div>
                        <div>
                            <div class="op-info-label">Tanggal Terbit Edisi</div>
                            <div class="op-info-value"><?= date('d M Y', strtotime($article->TANGGAL_TERBIT_EDISI_SERIAL)) ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($article->StartPage)) : ?>
                    <div class="op-info-item">
                        <div class="op-info-icon"><i class="fas fa-file-alt"></i></div>
                        <div>
                            <div class="op-info-label">Halaman Awal</div>
                            <div class="op-info-value"><?= esc($article->StartPage) ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($article->Pages)) : ?>
                    <div class="op-info-item">
                        <div class="op-info-icon"><i class="fas fa-copy"></i></div>
                        <div>
                            <div class="op-info-label">Jumlah Halaman</div>
                            <div class="op-info-value"><?= esc($article->Pages) ?></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Subject -->
            <?php if (!empty($article->Subject)) : ?>
                <div class="mb-4">
                    <div class="op-section-title">
                        <span class="op-info-icon"><i class="fas fa-tags"></i></span>Subjek
                    </div>
                    <div class="op-subject-panel">
                        <?php foreach (explode(';', $article->Subject) as $subject) :
                            $subject = trim($subject);
                            if ($subject) : ?>
                                <span class="op-chip"><i class="fas fa-tag"></i><?= esc($subject) ?></span>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Abstract -->
            <?php if (!empty($article->Abstract)) : ?>
                <div class="mb-4">
                    <div class="op-section-title">
                        <span class="op-info-icon"><i class="fas fa-align-left"></i></span>Abstrak
                    </div>
                    <div class="op-abstract-panel"><?= nl2br(esc($article->Abstract)) ?></div>
                </div>
            <?php endif; ?>

            <!-- Terbitan Induk -->
            <?php if (!empty($article->CatalogId)) : ?>
                <div class="mb-4">
                    <div class="op-section-title">
                        <span class="op-info-icon"><i class="fas fa-book"></i></span>Terbitan Berkala Induk
                    </div>
                    <div class="op-catalog-panel">
                        <div class="flex-grow-1">
                            <div class="op-catalog-title"><?= esc($article->CatalogTitle ?? '-') ?></div>
                            <small class="text-muted">
                                <?= esc($article->CatalogAuthor ?? '') ?>
                                <?= !empty($article->Publisher) ? ' &bull; ' . esc($article->Publisher) : '' ?>
                                <?= !empty($article->PublishYear) ? ' &bull; ' . esc($article->PublishYear) : '' ?>
                            </small>
                        </div>
                        <a href="<?= base_url('opac/detail/' . $article->CatalogId) ?>" class="btn btn-op-soft btn-sm">
                            <i class="fas fa-external-link-alt me-1"></i>Lihat Katalog
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Baca PDF -->
            <div class="mb-1">
                <div class="op-section-title">
                    <span class="op-info-icon"><i class="fas fa-file-pdf"></i></span>Konten Digital
                </div>

                <?php if (!empty($article->FileId)) : ?>
                    <div class="op-read-panel">
                        <div>
                            <div class="op-read-title"><i class="fas fa-check-circle me-2"></i>Konten digital tersedia</div>
                            <div class="op-read-desc">Dapat dibaca langsung, tidak perlu login.</div>
                        </div>
                        <a href="<?= base_url('katalog/view_decrypted_article/' . $article->FileId) ?>" target="_blank" class="btn btn-op-primary btn-lg" rel="noopener">
                            <i class="fas fa-book-open me-2"></i>Baca Artikel
                        </a>
                    </div>
                <?php else : ?>
                    <div class="op-read-panel op-read-empty">
                        <div>
                            <div class="op-read-title"><i class="fas fa-info-circle me-2"></i>Konten digital belum tersedia</div>
                            <div class="op-read-desc">Artikel ini belum memiliki file PDF yang bisa dibaca.</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <a href="<?= base_url('artikel') ?>" class="btn btn-op-soft">
        <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Artikel
    </a>
</div>

<?= $this->endSection() ?>
