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

    .op-container { max-width: 1100px; margin-left: auto; margin-right: auto; }

    .op-breadcrumb .breadcrumb {
        background: #fff;
        padding: .6rem 1rem;
        border-radius: 999px;
        box-shadow: var(--op-shadow-sm);
        display: inline-flex;
        margin-bottom: 0;
        font-size: .875rem;
    }
    .op-breadcrumb .breadcrumb-item a { color: var(--op-primary-light); text-decoration: none; font-weight: 500; }
    .op-breadcrumb .breadcrumb-item.active { color: var(--op-muted); }

    .op-card {
        background: #fff;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-lg);
        box-shadow: var(--op-shadow-sm);
        overflow: hidden;
    }
    .op-hero-header {
        background: linear-gradient(135deg, var(--op-primary) 0%, var(--op-primary-light) 100%);
        padding: 1.5rem 1.75rem;
        position: relative;
        overflow: hidden;
    }
    .op-hero-header::after {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 85% -20%, rgba(255,255,255,.18), transparent 55%);
        pointer-events: none;
    }
    .op-id-badge {
        background: rgba(255,255,255,.18);
        backdrop-filter: blur(4px);
        color: #fff;
        font-weight: 500;
        border: 1px solid rgba(255,255,255,.28);
        padding: .35rem .75rem;
        border-radius: 999px;
        font-size: .8rem;
    }
    .op-body { padding: 2rem; }
    .op-title { font-weight: 800; letter-spacing: -.02em; color: var(--op-ink); line-height: 1.3; }

    .op-info-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .85rem 1.25rem; }
    @media (max-width: 767px) { .op-info-grid { grid-template-columns: 1fr; } }
    .op-info-item {
        display: flex; gap: .75rem; padding: .85rem;
        background: #fafbfd; border: 1px solid var(--op-border); border-radius: var(--op-radius-sm);
        transition: border-color .2s ease, background .2s ease;
    }
    .op-info-item:hover { border-color: #c7d2fe; background: #f7f8ff; }
    .op-info-icon {
        flex: 0 0 auto; width: 36px; height: 36px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 10px; background: var(--op-primary-soft); color: var(--op-primary); font-size: .9rem;
    }
    .op-info-label { font-size: .74rem; text-transform: uppercase; letter-spacing: .04em; color: var(--op-muted); font-weight: 600; margin-bottom: .1rem; }
    .op-info-value { font-size: .93rem; color: var(--op-ink); font-weight: 500; word-break: break-word; }

    .op-section-title { font-weight: 700; color: var(--op-ink); font-size: 1rem; display: flex; align-items: center; gap: .5rem; margin-bottom: .9rem; }
    .op-section-title .op-info-icon { width: 30px; height: 30px; font-size: .8rem; }

    .op-subject-panel { background: #fafbfd; border: 1px solid var(--op-border); border-radius: var(--op-radius-md); padding: 1rem 1.1rem; display: flex; flex-wrap: wrap; gap: .5rem; }
    .op-chip {
        background: #fff; border: 1px solid #dbe2ff; color: var(--op-primary); font-weight: 600; font-size: .8rem;
        padding: .4rem .8rem; border-radius: 999px; display: inline-flex; align-items: center; gap: .35rem;
        transition: all .2s ease;
    }
    .op-chip:hover { background: var(--op-primary); color: #fff; border-color: var(--op-primary); }

    .op-abstract-panel {
        background: #fffdf5; border: 1px solid #fde68a; border-left: 4px solid #f59e0b;
        border-radius: var(--op-radius-sm); padding: 1rem 1.1rem; color: #78350f; font-size: .92rem; line-height: 1.6;
    }

    .op-catalog-panel {
        background: #fafbfd; border: 1px solid var(--op-border); border-radius: var(--op-radius-md);
        padding: 1rem 1.1rem; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
    }

    .op-read-panel {
        background: linear-gradient(135deg, #059669, #34d399);
        border-radius: var(--op-radius-md);
        padding: 1.5rem;
        color: #fff;
        display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;
    }
    .op-read-panel.op-read-empty {
        background: #f4f4f5; color: #52525b; border: 1px dashed #d4d4d8;
    }

    .btn-op-primary {
        background: linear-gradient(135deg, var(--op-primary), var(--op-primary-light));
        border: none; color: #fff; font-weight: 600; border-radius: 999px;
        box-shadow: 0 6px 16px -6px rgba(30,58,138,.5);
    }
    .btn-op-primary:hover { color: #fff; filter: brightness(1.05); }
    .btn-op-soft { background: #fff; border: 1px solid var(--op-border); color: var(--op-ink); border-radius: 999px; font-weight: 600; }
    .btn-op-soft:hover { border-color: var(--op-primary-light); color: var(--op-primary); }
    .btn-op-white {
        background: #fff; border: none; color: #059669; font-weight: 700; border-radius: 999px;
    }
    .btn-op-white:hover { color: #047857; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid op-container py-5" style="padding-top: 100px !important;">

    <!-- Breadcrumb -->
    <nav class="op-breadcrumb mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('artikel') ?>"><i class="fas fa-newspaper me-1"></i>Artikel</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= esc(mb_strimwidth($article->Title, 0, 40, '...')) ?></li>
        </ol>
    </nav>

    <div class="op-card mb-4">
        <div class="op-hero-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="mb-0 text-white"><i class="fas fa-newspaper me-2"></i>Detail Artikel</h4>
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
                            <div class="fw-bold" style="color: var(--op-ink);"><?= esc($article->CatalogTitle ?? '-') ?></div>
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
                            <div class="fw-bold fs-5 mb-1"><i class="fas fa-book-reader me-2"></i>Konten digital tersedia</div>
                            <div style="opacity:.9;">Dapat dibaca langsung, tidak perlu login.</div>
                        </div>
                        <a href="<?= base_url('katalog/view_decrypted_article/' . $article->FileId) ?>" target="_blank" class="btn btn-op-white btn-lg">
                            <i class="fas fa-book-open me-2"></i>Baca Artikel
                        </a>
                    </div>
                <?php else : ?>
                    <div class="op-read-panel op-read-empty">
                        <div>
                            <div class="fw-bold mb-1"><i class="fas fa-info-circle me-2"></i>Konten digital belum tersedia</div>
                            <div class="small">Artikel ini belum memiliki file PDF yang bisa dibaca.</div>
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
