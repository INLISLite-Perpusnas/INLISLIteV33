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
        --op-shadow-lg: 0 20px 45px -18px rgba(30, 58, 138, 0.28);
    }

    body { background: var(--op-bg); }

    .op-container { max-width: 1600px; margin-left: auto; margin-right: auto; }
    @media (min-width: 1400px) {
        .op-container { padding-left: 2.5rem; padding-right: 2.5rem; }
    }

    /* Breadcrumb */
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

    /* Main card */
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
    .op-hero-header h4 { font-weight: 700; letter-spacing: -.01em; }
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

    /* Cover */
    .op-cover-wrap {
        border-radius: var(--op-radius-md);
        overflow: hidden;
        box-shadow: var(--op-shadow-md);
        position: relative;
    }
    .book-cover-large {
        width: 100%;
        display: block;
        max-height: 420px;
        object-fit: cover;
        transition: transform .35s ease;
        cursor: zoom-in;
    }
    .op-cover-wrap:hover .book-cover-large { transform: scale(1.035); }
    .op-cover-overlay-btn {
        position: absolute;
        bottom: .75rem;
        right: .75rem;
        border: none;
        background: rgba(17,24,39,.65);
        color: #fff;
        backdrop-filter: blur(4px);
        border-radius: 999px;
        padding: .4rem .85rem;
        font-size: .8rem;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        opacity: 0;
        transform: translateY(6px);
        transition: all .25s ease;
    }
    .op-cover-wrap:hover .op-cover-overlay-btn { opacity: 1; transform: translateY(0); }

    .no-cover-large {
        border-radius: var(--op-radius-md) !important;
        border: 2px dashed #d1d5db !important;
        background: linear-gradient(180deg, #fafbff, #f1f3fb) !important;
        box-shadow: none !important;
    }

    /* Title */
    .op-title { font-weight: 800; letter-spacing: -.02em; color: var(--op-ink); line-height: 1.25; }
    .op-edition-chip {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        background: var(--op-primary-soft);
        color: var(--op-primary);
        border-radius: 999px;
        padding: .3rem .75rem;
        font-size: .82rem;
        font-weight: 600;
    }

    /* Info grid */
    .op-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .85rem 1.25rem;
    }
    @media (max-width: 767px) { .op-info-grid { grid-template-columns: 1fr; } }

    .op-info-item {
        display: flex;
        gap: .75rem;
        padding: .85rem;
        background: #fafbfd;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-sm);
        transition: border-color .2s ease, background .2s ease;
    }
    .op-info-item:hover { border-color: #c7d2fe; background: #f7f8ff; }
    .op-info-icon {
        flex: 0 0 auto;
        width: 36px; height: 36px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 10px;
        background: var(--op-primary-soft);
        color: var(--op-primary);
        font-size: .9rem;
    }
    .op-info-label { font-size: .74rem; text-transform: uppercase; letter-spacing: .04em; color: var(--op-muted); font-weight: 600; margin-bottom: .1rem; }
    .op-info-value { font-size: .93rem; color: var(--op-ink); font-weight: 500; word-break: break-word; }
    .op-info-value.mono { font-family: 'JetBrains Mono', Consolas, monospace; }

    /* Section titles */
    .op-section-title {
        font-weight: 700;
        color: var(--op-ink);
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: .5rem;
        margin-bottom: .9rem;
    }
    .op-section-title .op-info-icon { width: 30px; height: 30px; font-size: .8rem; }

    /* Subject chips */
    .op-subject-panel {
        background: #fafbfd;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-md);
        padding: 1rem 1.1rem;
        display: flex; flex-wrap: wrap; gap: .5rem;
    }
    .op-chip {
        background: #fff;
        border: 1px solid #dbe2ff;
        color: var(--op-primary);
        font-weight: 600;
        font-size: .8rem;
        padding: .4rem .8rem;
        border-radius: 999px;
        display: inline-flex; align-items: center; gap: .35rem;
        transition: all .2s ease;
    }
    .op-chip:hover { background: var(--op-primary); color: #fff; border-color: var(--op-primary); }

    /* Status badges */
    .op-status-badge {
        border-radius: 999px;
        padding: .5rem 1rem;
        font-weight: 600;
        font-size: .82rem;
        display: inline-flex; align-items: center; gap: .4rem;
        border: 1px solid transparent;
    }
    .op-status-success { background: #ecfdf3; color: #027a48; border-color: #abefc6; }
    .op-status-info     { background: #eff8ff; color: #175cd3; border-color: #b2ddff; }
    .op-status-warning  { background: #fffaeb; color: #b54708; border-color: #fedf89; }
    .op-status-secondary{ background: #f4f4f5; color: #3f3f46; border-color: #e4e4e7; }

    /* Note panel */
    .op-note-panel {
        background: #fffdf5;
        border: 1px solid #fde68a;
        border-left: 4px solid #f59e0b;
        border-radius: var(--op-radius-sm);
        padding: 1rem 1.1rem;
        color: #78350f;
        font-size: .92rem;
    }

    /* Action buttons */
    .op-actions .btn {
        border-radius: 999px;
        font-weight: 600;
        font-size: .875rem;
        padding: .55rem 1.1rem;
    }
    .btn-op-primary {
        background: linear-gradient(135deg, var(--op-primary), var(--op-primary-light));
        border: none; color: #fff;
        box-shadow: 0 6px 16px -6px rgba(30,58,138,.5);
    }
    .btn-op-primary:hover { color: #fff; filter: brightness(1.05); }
    .btn-op-soft { background: #fff; border: 1px solid var(--op-border); color: var(--op-ink); }
    .btn-op-soft:hover { border-color: var(--op-primary-light); color: var(--op-primary); }

    .op-dropdown-menu {
        border-radius: var(--op-radius-sm);
        border: 1px solid var(--op-border);
        box-shadow: var(--op-shadow-md);
        padding: .4rem;
        min-width: 190px;
    }
    .op-dropdown-menu .dropdown-item { border-radius: 8px; padding: .5rem .65rem; font-size: .88rem; }
    .op-dropdown-menu .dropdown-item:hover { background: var(--op-primary-soft); color: var(--op-primary); }

    /* Secondary card (eksemplar/marc) */
    .op-tabs .nav-tabs { border: none; gap: .4rem; }
    #bookTab .nav-link {
        color: var(--op-muted);
        font-weight: 600;
        font-size: .875rem;
        border: 1px solid var(--op-border);
        border-radius: 999px;
        padding: .5rem 1.1rem;
        background: #fff;
        transition: all .2s ease;
    }
    #bookTab .nav-link:hover { color: var(--op-primary); border-color: #c7d2fe; }
    #bookTab .nav-link.active {
        background: var(--op-primary);
        color: #fff;
        border-color: var(--op-primary);
        box-shadow: 0 6px 14px -6px rgba(30,58,138,.45);
    }

    .op-table-wrap {
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-md);
        overflow: hidden;
    }
    .op-table-wrap table { margin-bottom: 0; }
    .op-table-wrap thead th {
        background: #f8f9fc !important;
        color: var(--op-ink) !important;
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        font-weight: 700;
        border: none !important;
        padding: .8rem 1rem;
    }
    .op-table-wrap tbody td { padding: .75rem 1rem; vertical-align: middle; font-size: .875rem; border-color: #f0f1f5; }
    .op-table-wrap tbody tr:hover { background: #fafbff; }

    .op-marc-legend {
        background: #fafbfd;
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-md);
    }

    /* Sidebar */
    .op-side-card {
        border: 1px solid var(--op-border);
        border-radius: var(--op-radius-md);
        box-shadow: var(--op-shadow-sm);
        overflow: hidden;
    }
    .op-side-card .card-header {
        background: #fff;
        border-bottom: 1px solid var(--op-border);
        font-weight: 700;
        font-size: .92rem;
        color: var(--op-ink);
        padding: .95rem 1.1rem;
    }
    .op-side-card .card-body { padding: 1.1rem; }
    .op-side-card .btn { border-radius: var(--op-radius-sm); text-align: left; font-weight: 600; font-size: .875rem; padding: .6rem .9rem; }

    .op-meta-line { font-size: .85rem; color: var(--op-muted); display: flex; gap: .55rem; align-items: flex-start; margin-bottom: .5rem; }
    .op-meta-line i { color: var(--op-primary-light); margin-top: .15rem; }

    .op-sticky { position: sticky; top: 1.25rem; }

    @media print {
        .op-actions, .op-side-card, .op-dropdown-menu, nav[aria-label="breadcrumb"] { display: none !important; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid op-container py-5">
    <div class="row">
        <div class="col-12 mb-4 op-breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('opac') ?>">
                            <i class="fas fa-home me-1"></i>Beranda
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Detail Katalog</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="op-card mb-4">
                <div class="op-hero-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-white">
                        <i class="fas fa-book-open me-2"></i>Detail Katalog
                    </h4>
                    <span class="op-id-badge">ID: <?= esc($catalog['ID']) ?></span>
                </div>

                <div class="op-body">
                    <!-- Cover + Basic Info -->
                    <div class="row mb-4 g-4">
                        <div class="col-md-4">
                            <?php
                            $coverUrl = '';
                            if (!empty($catalog['CoverURL'])) {
                                if (filter_var($catalog['CoverURL'], FILTER_VALIDATE_URL)) {
                                    $coverUrl = $catalog['CoverURL'];
                                } else {
                                    $coverUrl = base_url('uploads/katalog/' . $catalog['CoverURL']);
                                }
                            }
                            ?>

                            <?php if ($coverUrl): ?>
                                <div class="op-cover-wrap" onclick="showCoverModal('<?= $coverUrl ?>', '<?= esc($catalog['Title']) ?>')">
                                    <img src="<?= $coverUrl ?>"
                                        alt="Cover <?= esc($catalog['Title']) ?>"
                                        class="book-cover-large"
                                        onerror="handleImageError(this)">
                                    <button type="button" class="op-cover-overlay-btn">
                                        <i class="fas fa-search-plus"></i> Perbesar
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="no-cover-large d-flex align-items-center justify-content-center"
                                    style="height: 380px;">
                                    <div class="text-center text-muted">
                                        <i class="fas fa-book fa-3x mb-3" style="color:#c7cbe0;"></i>
                                        <h6 class="fw-semibold">Cover Tidak Tersedia</h6>
                                        <p class="mb-0 small">Gambar cover belum diupload</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-8">
                            <div class="mb-3">
                                <h2 class="op-title fs-3 mb-2"><?= esc($catalog['Title']) ?></h2>
                                <?php if (!empty($catalog['Edition'])): ?>
                                    <span class="op-edition-chip">
                                        <i class="fas fa-bookmark"></i> Edisi: <?= esc($catalog['Edition']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="op-info-grid">
                                <div class="op-info-item">
                                    <div class="op-info-icon"><i class="fas fa-user"></i></div>
                                    <div>
                                        <div class="op-info-label">Pengarang</div>
                                        <div class="op-info-value"><?= esc($catalog['Author'] ?? 'Tidak tersedia') ?></div>
                                    </div>
                                </div>

                                <div class="op-info-item">
                                    <div class="op-info-icon"><i class="fas fa-building"></i></div>
                                    <div>
                                        <div class="op-info-label">Penerbit</div>
                                        <div class="op-info-value"><?= esc($catalog['Publisher'] ?? 'Tidak tersedia') ?></div>
                                    </div>
                                </div>

                                <?php if (!empty($catalog['PublishLocation'])): ?>
                                    <div class="op-info-item">
                                        <div class="op-info-icon"><i class="fas fa-map-marker-alt"></i></div>
                                        <div>
                                            <div class="op-info-label">Tempat Terbit</div>
                                            <div class="op-info-value"><?= esc($catalog['PublishLocation']) ?></div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="op-info-item">
                                    <div class="op-info-icon"><i class="fas fa-calendar"></i></div>
                                    <div>
                                        <div class="op-info-label">Tahun Terbit</div>
                                        <div class="op-info-value"><?= esc($catalog['PublishYear'] ?? 'Tidak tersedia') ?></div>
                                    </div>
                                </div>

                                <?php if (!empty($catalog['Languages'])): ?>
                                    <div class="op-info-item">
                                        <div class="op-info-icon"><i class="fas fa-globe"></i></div>
                                        <div>
                                            <div class="op-info-label">Bahasa</div>
                                            <div class="op-info-value"><?= esc($catalog['Languages']) ?></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Subject -->
                    <?php if (!empty($catalog['Subject'])): ?>
                        <div class="mb-4">
                            <div class="op-section-title">
                                <span class="op-info-icon"><i class="fas fa-tags"></i></span>Subjek
                            </div>
                            <div class="op-subject-panel">
                                <?php
                                $subjects = explode(';', $catalog['Subject']);
                                foreach ($subjects as $subject):
                                    $subject = trim($subject);
                                    if ($subject):
                                ?>
                                        <span class="op-chip"><i class="fas fa-tag"></i><?= esc($subject) ?></span>
                                <?php endif;
                                endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Technical Details -->
                    <div class="mb-4">
                        <div class="op-section-title">
                            <span class="op-info-icon"><i class="fas fa-microchip"></i></span>Detail Teknis
                        </div>
                        <div class="op-info-grid">
                            <?php if (!empty($catalog['PhysicalDescription'])): ?>
                                <div class="op-info-item">
                                    <div class="op-info-icon"><i class="fas fa-file-alt"></i></div>
                                    <div>
                                        <div class="op-info-label">Deskripsi Fisik</div>
                                        <div class="op-info-value"><?= esc($catalog['PhysicalDescription']) ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($catalog['ISBN'])): ?>
                                <div class="op-info-item">
                                    <div class="op-info-icon"><i class="fas fa-barcode"></i></div>
                                    <div>
                                        <div class="op-info-label">ISBN</div>
                                        <div class="op-info-value mono"><?= esc($catalog['ISBN']) ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($catalog['CallNumber'])): ?>
                                <div class="op-info-item">
                                    <div class="op-info-icon"><i class="fas fa-hashtag"></i></div>
                                    <div>
                                        <div class="op-info-label">Nomor Panggil</div>
                                        <div class="op-info-value mono"><?= esc($catalog['CallNumber']) ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($catalog['ControlNumber'])): ?>
                                <div class="op-info-item">
                                    <div class="op-info-icon"><i class="fas fa-id-card"></i></div>
                                    <div>
                                        <div class="op-info-label">Control Number</div>
                                        <div class="op-info-value mono"><?= esc($catalog['ControlNumber']) ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($catalog['BIBID'])): ?>
                                <div class="op-info-item">
                                    <div class="op-info-icon"><i class="fas fa-database"></i></div>
                                    <div>
                                        <div class="op-info-label">BIB ID</div>
                                        <div class="op-info-value mono"><?= esc($catalog['BIBID']) ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Note -->
                    <?php if (!empty($catalog['Note'])): ?>
                        <div class="mb-4">
                            <div class="op-section-title">
                                <span class="op-info-icon"><i class="fas fa-sticky-note"></i></span>Catatan
                            </div>
                            <div class="op-note-panel"><?= nl2br(esc($catalog['Note'])) ?></div>
                        </div>
                    <?php endif; ?>

                    <!-- Status -->
                    <div class="mb-4">
                        <div class="op-section-title">
                            <span class="op-info-icon"><i class="fas fa-info-circle"></i></span>Status
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <?php if ($catalog['IsOPAC'] ?? false): ?>
                                <span class="op-status-badge op-status-success"><i class="fas fa-check"></i>Tersedia di OPAC</span>
                            <?php endif; ?>

                            <?php if ($catalog['IsBNI'] ?? false): ?>
                                <span class="op-status-badge op-status-info"><i class="fas fa-flag"></i>Bibliografi Nasional Indonesia</span>
                            <?php endif; ?>

                            <?php if ($catalog['IsKIN'] ?? false): ?>
                                <span class="op-status-badge op-status-warning"><i class="fas fa-star"></i>Karya Tulis Ilmiah Nasional</span>
                            <?php endif; ?>

                            <?php if ($catalog['IsRDA'] ?? false): ?>
                                <span class="op-status-badge op-status-secondary"><i class="fas fa-bookmark"></i>RDA Cataloging</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex gap-2 flex-wrap op-actions">
                        <button class="btn btn-op-primary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Cetak
                        </button>

                        <button class="btn btn-op-soft" onclick="copyToClipboard()">
                            <i class="fas fa-copy me-2"></i>Salin Detail
                        </button>

                        <?php if (!empty($catalog['CoverURL'])): ?>
                            <a href="<?= $coverUrl ?>" target="_blank" class="btn btn-op-soft">
                                <i class="fas fa-external-link-alt me-2"></i>Lihat Cover Original
                            </a>
                        <?php endif; ?>

                        <div class="dropdown" style="position: relative;" id="bagikanDropdownWrapper">
                            <button class="btn btn-op-soft" type="button" id="bagikanDropdownBtn"
                                style="display:inline-flex;align-items:center;gap:6px;">
                                <i class="fas fa-share-alt"></i>Bagikan
                                <i class="fas fa-caret-down" id="bagikanCaretIcon"></i>
                            </button>
                            <ul class="dropdown-menu op-dropdown-menu" id="bagikanDropdownMenu"
                                style="z-index: 9999; display:none; position:absolute; left:0; top:100%; margin-top:6px;">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="shareViaEmail(); document.getElementById('bagikanDropdownMenu').style.display='none'; document.getElementById('bagikanCaretIcon').className='fas fa-caret-down';">
                                        <i class="fas fa-envelope me-2"></i>Email
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="shareViaWhatsapp(); document.getElementById('bagikanDropdownMenu').style.display='none'; document.getElementById('bagikanCaretIcon').className='fas fa-caret-down';">
                                        <i class="fab fa-whatsapp me-2"></i>WhatsApp
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="copyLink(); document.getElementById('bagikanDropdownMenu').style.display='none'; document.getElementById('bagikanCaretIcon').className='fas fa-caret-down';">
                                        <i class="fas fa-link me-2"></i>Salin Link
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($roweksemplar) || !empty($roweksemplar_drm) || !empty($marc)): ?>
                <div class="op-card mb-4">
                    <div class="op-hero-header">
                        <h5 class="mb-0 text-white"><i class="fas fa-book me-2"></i>Informasi Eksemplar & Metadata</h5>
                    </div>
                    <div class="op-body">

                        <!-- Tab Navigation + Download Button -->
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 op-tabs">
                            <ul class="nav nav-tabs" id="bookTab" role="tablist">
                                <?php if (!empty($roweksemplar)): ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="fisik-tab" data-bs-toggle="tab" data-bs-target="#fisik" type="button" role="tab" aria-controls="fisik" aria-selected="true">
                                            <i class="fas fa-book me-1"></i>Buku Fisik
                                        </button>
                                    </li>
                                <?php endif; ?>

                                <?php if (!empty($roweksemplar_drm)): ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link <?= empty($roweksemplar) ? 'active' : '' ?>" id="digital-tab" data-bs-toggle="tab" data-bs-target="#digital" type="button" role="tab" aria-controls="digital" aria-selected="false">
                                            <i class="fas fa-tablet-alt me-1"></i>Buku Digital
                                        </button>
                                    </li>
                                <?php endif; ?>

                                <?php if (!empty($marc)): ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link <?= empty($roweksemplar) && empty($roweksemplar_drm) ? 'active' : '' ?>" id="marc-tab" data-bs-toggle="tab" data-bs-target="#marc" type="button" role="tab" aria-controls="marc" aria-selected="false">
                                            <i class="fas fa-database me-1"></i>MARC
                                        </button>
                                    </li>
                                <?php endif; ?>
                            </ul>

                            <?php if (!empty($marc)): ?>
                                <div class="dropdown" style="position: relative;" id="marcDropdownWrapper">
                                    <button class="btn btn-op-soft btn-sm" type="button" id="downloadMarcDropdown"
                                        style="border-radius:999px;display:inline-flex;align-items:center;gap:6px;">
                                        <i class="fas fa-download"></i>Unduh Katalog MARC
                                        <i class="fas fa-caret-down" id="marcCaretIcon"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end op-dropdown-menu" id="marcDropdownMenu"
                                        style="z-index: 9999; display:none; position:absolute; right:0; top:100%; margin-top:6px;">
                                        <li><a class="dropdown-item" href="<?= base_url("opac/downloadMarcUtf8/{$catalog['ID']}") ?>" target="_blank">Format MARC Unicode/UTF-8</a></li>
                                        <li><a class="dropdown-item" href="<?= base_url("opac/downloadMarcXml/{$catalog['ID']}") ?>" target="_blank">Format MARC XML</a></li>
                                        <li><a class="dropdown-item" href="<?= base_url("opac/downloadMarcMods/{$catalog['ID']}") ?>" target="_blank">Format MODS</a></li>
                                        <li><a class="dropdown-item" href="<?= base_url("opac/downloadMarcRdf/{$catalog['ID']}") ?>" target="_blank">Format Dublin Core (RDF)</a></li>
                                        <li><a class="dropdown-item" href="<?= base_url("opac/downloadMarcOai/{$catalog['ID']}") ?>" target="_blank">Format Dublin Core (OAI)</a></li>
                                        <li><a class="dropdown-item" href="<?= base_url("opac/downloadMarcSrw/{$catalog['ID']}") ?>" target="_blank">Format Dublin Core (SRW)</a></li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Tab Content -->
                        <div class="tab-content" id="bookTabContent">
                            <!-- Buku Fisik -->
                            <?php if (!empty($roweksemplar)): ?>
                                <div class="tab-pane fade show active" id="fisik" role="tabpanel" aria-labelledby="fisik-tab">
                                    <div class="op-table-wrap">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0" id="tbl_eksemplars_fisik">
                                                <thead>
                                                    <tr>
                                                        <th>Nomor Barcode</th>
                                                        <th>Nomor Panggil</th>
                                                        <th>Akses</th>
                                                        <th>Lokasi</th>
                                                        <th>Ketersediaan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($roweksemplar as $eksemplar): ?>
                                                        <tr>
                                                            <td><code><?= esc($eksemplar->NomorBarcode) ?></code></td>
                                                            <td><code><?= esc($eksemplar->CallNumber) ?></code></td>
                                                            <td><span class="op-status-badge op-status-info"><?= esc($eksemplar->RuleName) ?></span></td>
                                                            <td><i class="fas fa-building me-1 text-muted"></i><?= esc($eksemplar->LocationName) ?></td>
                                                            <td><span class="op-status-badge op-status-success"><?= esc($eksemplar->StatusName) ?></span></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Buku Digital -->
                            <?php if (!empty($roweksemplar_drm)): ?>
                                <div class="tab-pane fade <?= empty($roweksemplar) ? 'show active' : '' ?>" id="digital" role="tabpanel" aria-labelledby="digital-tab">
                                    <div class="op-table-wrap">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0" id="tbl_eksemplars_digital">
                                                <thead>
                                                    <tr>
                                                        <th>Nomor Barcode</th>
                                                        <th>Nomor Panggil</th>
                                                        <th>Akses</th>
                                                        <th>Lokasi</th>
                                                        <th>Ketersediaan</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($roweksemplar_drm as $eksemplar_drm): ?>
                                                        <tr>
                                                            <td><code><?= esc($eksemplar_drm->NomorBarcode) ?></code></td>
                                                            <td><code><?= esc($eksemplar_drm->CallNumber) ?></code></td>
                                                            <td><span class="op-status-badge op-status-info"><?= esc($eksemplar_drm->RuleName) ?></span></td>
                                                            <td><i class="fas fa-cloud me-1 text-muted"></i><?= esc($eksemplar_drm->LocationName) ?></td>
                                                            <td><span class="op-status-badge op-status-success"><?= esc($eksemplar_drm->StatusName) ?></span></td>
                                                            <td>
                                                                <?php
                                                                $canRead = $eksemplar_drm->Status_id == 1;
                                                                $bacaDigitalUrl = base_url('opac/baca-digital/' . $catalog['ID']);
                                                                ?>
                                                                <?php if ($canRead): ?>
                                                                    <?php if (logged_in()): ?>
                                                                        <a href="<?= $bacaDigitalUrl ?>"
                                                                            target="_blank"
                                                                            class="btn btn-op-primary btn-sm" style="border-radius:999px;">
                                                                            <i class="fas fa-file-pdf me-1"></i>Baca PDF
                                                                        </a>
                                                                    <?php else: ?>
                                                                        <button type="button"
                                                                            onclick="showMemberLoginModal('<?= $bacaDigitalUrl ?>')"
                                                                            class="btn btn-op-primary btn-sm" style="border-radius:999px;">
                                                                            <i class="fas fa-file-pdf me-1"></i>Baca PDF
                                                                        </button>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- MARC -->
                            <?php if (!empty($marc)): ?>
                                <div class="tab-pane fade <?= empty($roweksemplar) && empty($roweksemplar_drm) ? 'show active' : '' ?>" id="marc" role="tabpanel" aria-labelledby="marc-tab">
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Format MARC21 - Total <?= count($marc) ?> field
                                        </small>
                                    </div>
                                    <div class="op-table-wrap mb-3">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover mb-0" id="marcTable">
                                                <thead>
                                                    <tr>
                                                        <th width="10%">Tag</th>
                                                        <th width="8%">Ind1</th>
                                                        <th width="8%">Ind2</th>
                                                        <th width="64%">Nilai</th>
                                                        <th width="10%" class="text-center">Urutan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($marc as $field): ?>
                                                        <tr class="marc-row">
                                                            <td><span class="op-status-badge op-status-info"><?= esc($field->Tag) ?></span></td>
                                                            <td><code><?= esc($field->Indicator1 ?: '_') ?></code></td>
                                                            <td><code><?= esc($field->Indicator2 ?: '_') ?></code></td>
                                                            <td><span><?= esc($field->Value) ?></span></td>
                                                            <td class="text-center"><span class="op-status-badge op-status-secondary"><?= esc($field->Sequence) ?></span></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="op-marc-legend p-3">
                                        <h6 class="fw-bold mb-2" style="color: var(--op-primary);">
                                            <i class="fas fa-lightbulb me-1"></i>Penjelasan Field MARC21:
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul class="list-unstyled small mb-0">
                                                    <li class="mb-1"><strong>001:</strong> Control Number</li>
                                                    <li class="mb-1"><strong>005:</strong> Date and Time of Latest Transaction</li>
                                                    <li class="mb-1"><strong>020:</strong> ISBN</li>
                                                    <li class="mb-1"><strong>100:</strong> Main Entry - Personal Name</li>
                                                    <li class="mb-0"><strong>245:</strong> Title Statement</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-unstyled small mb-0">
                                                    <li class="mb-1"><strong>250:</strong> Edition Statement</li>
                                                    <li class="mb-1"><strong>260:</strong> Publication Information</li>
                                                    <li class="mb-1"><strong>300:</strong> Physical Description</li>
                                                    <li class="mb-1"><strong>650:</strong> Subject</li>
                                                    <li class="mb-0"><strong>700:</strong> Added Entry - Personal Name</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="op-sticky">
                <!-- Quick Actions -->
                <div class="op-side-card mb-4">
                    <div class="card-header"><i class="fas fa-tools me-2 text-primary"></i>Aksi Cepat</div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('opac') ?>" class="btn btn-op-soft">
                                <i class="fas fa-search me-2"></i>Cari Lagi
                            </a>
                            <a href="<?= base_url('opac/browse') ?>" class="btn btn-op-soft">
                                <i class="fas fa-list me-2"></i>Browse Katalog
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Catalog Information -->
                <div class="op-side-card mb-4">
                    <div class="card-header"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Katalog</div>
                    <div class="card-body">
                        <div class="op-meta-line">
                            <i class="fas fa-calendar-plus"></i>
                            <div><strong>Ditambahkan:</strong><br><?= date('d M Y', strtotime($catalog['CreateDate'] ?? 'now')) ?></div>
                        </div>

                        <?php if (!empty($catalog['ApproveDateOPAC'])): ?>
                            <div class="op-meta-line mb-0">
                                <i class="fas fa-check-circle"></i>
                                <div><strong>Disetujui OPAC:</strong><br><?= date('d M Y', strtotime($catalog['ApproveDateOPAC'])) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Related Search -->
                <div class="op-side-card mb-4">
                    <div class="card-header"><i class="fas fa-search me-2 text-primary"></i>Pencarian Terkait</div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <?php if (!empty($catalog['Author'])): ?>
                                <a href="<?= base_url('opac?search=' . urlencode($catalog['Author']) . '&search_by=Author') ?>"
                                    class="btn btn-op-soft btn-sm">
                                    <i class="fas fa-user me-1 text-primary"></i>
                                    Karya <?= esc(explode(',', $catalog['Author'])[0]) ?>
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($catalog['Publisher'])): ?>
                                <a href="<?= base_url('opac?search=' . urlencode($catalog['Publisher']) . '&search_by=Publisher') ?>"
                                    class="btn btn-op-soft btn-sm">
                                    <i class="fas fa-building me-1 text-success"></i>
                                    Dari <?= esc($catalog['Publisher']) ?>
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($catalog['Subject'])): ?>
                                <?php
                                $firstSubject = trim(explode(';', $catalog['Subject'])[0]);
                                if ($firstSubject):
                                ?>
                                    <a href="<?= base_url('opac?search=' . urlencode($firstSubject) . '&search_by=Subject') ?>"
                                        class="btn btn-op-soft btn-sm">
                                        <i class="fas fa-tag me-1 text-info"></i>
                                        Subjek: <?= esc(substr($firstSubject, 0, 20)) ?>...
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if (!empty($catalog['PublishYear'])): ?>
                                <a href="<?= base_url('opac?PublishYear=' . $catalog['PublishYear'] . '&search_by=') ?>"
                                    class="btn btn-op-soft btn-sm">
                                    <i class="fas fa-calendar me-1 text-warning"></i>
                                    Tahun <?= esc($catalog['PublishYear']) ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Export Options -->
                <div class="op-side-card">
                    <div class="card-header"><i class="fas fa-download me-2 text-primary"></i>Export</div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-op-soft btn-sm" onclick="exportBibTeX()">
                                <i class="fas fa-file-code me-2 text-success"></i>BibTeX
                            </button>
                            <button class="btn btn-op-soft btn-sm" onclick="exportRIS()">
                                <i class="fas fa-file-alt me-2 text-info"></i>RIS Format
                            </button>
                            <button class="btn btn-op-soft btn-sm" onclick="exportCSV()">
                                <i class="fas fa-file-csv me-2 text-warning"></i>CSV
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cover Modal -->
<div class="modal fade" id="coverModal" tabindex="-1" aria-labelledby="coverModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: var(--op-radius-md); overflow:hidden; border:none;">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--op-primary), var(--op-primary-light)); border:none;">
                <h5 class="modal-title text-white" id="coverModalLabel">Cover</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0" style="background:#111827;">
                <img id="modalCoverImage" src="" alt="Cover" class="img-fluid" style="max-height: 70vh;">
            </div>
            <div class="modal-footer" style="border:none;">
                <a id="downloadCoverBtn" href="#" class="btn btn-op-primary btn-sm" download>
                    <i class="fas fa-download me-1"></i>Unduh
                </a>
                <button type="button" class="btn btn-op-soft btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- PDF Modal for Digital Books -->
<?php if (!empty($roweksemplar_drm)): ?>
    <div class="modal fade" id="pdfModal<?= $catalog['ID'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content" style="border-radius: var(--op-radius-md); overflow:hidden; border:none;">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--op-primary), var(--op-primary-light)); border:none;">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-file-pdf me-2"></i>Baca Buku Digital: <?= esc($catalog['Title']) ?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="text-center">
                        <iframe
                            src="<?= base_url('opac/baca-digital/' . $catalog['ID']) ?>"
                            width="100%" height="600px" style="border: none;"></iframe>
                    </div>
                </div>
                <div class="modal-footer" style="border:none;">
                    <button type="button" class="btn btn-op-soft btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Member Login Modal for Digital Books -->
<?php if (!empty($roweksemplar_drm) && !logged_in()): ?>
    <div class="modal fade" id="memberLoginModal" tabindex="-1" aria-labelledby="memberLoginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: var(--op-radius-md); overflow:hidden; border:none;">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--op-primary), var(--op-primary-light)); border:none;">
                    <h5 class="modal-title text-white" id="memberLoginModalLabel">
                        <i class="fas fa-lock me-2"></i>Login Anggota
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="memberLoginForm">
                    <div class="modal-body">
                        <p class="text-muted small mb-3">Silakan login sebagai anggota perpustakaan untuk membaca buku digital ini.</p>

                        <div id="memberLoginAlert" class="alert alert-danger d-none" role="alert"></div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="memberLoginUsername">Nomor Anggota</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                <input type="text" class="form-control" id="memberLoginUsername" name="username" required autocomplete="username" placeholder="Masukkan nomor anggota">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="memberLoginPassword">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="memberLoginPassword" name="password" required autocomplete="current-password" placeholder="Masukkan password">
                            </div>
                        </div>

                        <?php if (!empty($hcaptcha_site_key)): ?>
                            <div class="d-flex justify-content-center my-3">
                                <div class="h-captcha" data-sitekey="<?= esc($hcaptcha_site_key) ?>"></div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer" style="border:none;">
                        <button type="button" class="btn btn-op-soft btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-op-primary btn-sm" id="memberLoginSubmitBtn">
                            <i class="fas fa-sign-in-alt me-1"></i>Masuk &amp; Baca
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php if (!empty($hcaptcha_site_key)): ?>
        <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
    <?php endif; ?>
<?php endif; ?>

<script>
    <?php if (session()->getFlashdata('digital_error')): ?>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'warning',
            title: 'Masa Peminjaman Berakhir',
            text: <?= json_encode(session()->getFlashdata('digital_error')) ?>,
            confirmButtonText: 'Oke',
        });
    });
    <?php endif; ?>
    <?php if (session()->getFlashdata('digital_info')): ?>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'info',
            title: 'Sedang Dipinjam',
            text: <?= json_encode(session()->getFlashdata('digital_info')) ?>,
            confirmButtonText: 'Oke',
        });
    });
    <?php endif; ?>

    // Data dari PHP yang sudah di-escape
    const catalogData = {
        id: <?= json_encode($catalog['ID']) ?>,
        controlNumber: <?= json_encode($catalog['ControlNumber'] ?? '') ?>,
        title: <?= json_encode($catalog['Title']) ?>,
        author: <?= json_encode($catalog['Author'] ?? '') ?>,
        publisher: <?= json_encode($catalog['Publisher'] ?? '') ?>,
        publishLocation: <?= json_encode($catalog['PublishLocation'] ?? '') ?>,
        publishYear: <?= json_encode($catalog['PublishYear'] ?? '') ?>,
        isbn: <?= json_encode($catalog['ISBN'] ?? '') ?>,
        callNumber: <?= json_encode($catalog['CallNumber'] ?? '') ?>,
        subject: <?= json_encode($catalog['Subject'] ?? '') ?>,
        physicalDescription: <?= json_encode($catalog['PhysicalDescription'] ?? '') ?>,
        languages: <?= json_encode($catalog['Languages'] ?? '') ?>,
        note: <?= json_encode($catalog['Note'] ?? '') ?>,
        currentUrl: <?= json_encode(current_url()) ?>
    };

    function showCoverModal(coverUrl, title) {
        const modal = new bootstrap.Modal(document.getElementById('coverModal'));
        const modalImage = document.getElementById('modalCoverImage');
        const modalTitle = document.getElementById('coverModalLabel');
        const downloadBtn = document.getElementById('downloadCoverBtn');

        modalImage.src = coverUrl;
        modalImage.alt = 'Cover: ' + title;
        modalTitle.textContent = 'Cover: ' + title;
        downloadBtn.href = coverUrl;
        downloadBtn.download = 'cover-' + title.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.jpg';

        modal.show();
    }

    function handleImageError(img) {
        img.onerror = null;
        img.src = '<?= base_url("assets/images/no-cover.png") ?>';
        img.alt = 'Cover tidak tersedia';
    }

    function copyToClipboard() {
        const textData = `Judul: ${catalogData.title}
Pengarang: ${catalogData.author || 'N/A'}
Penerbit: ${catalogData.publisher || 'N/A'}
Tahun: ${catalogData.publishYear || 'N/A'}
ISBN: ${catalogData.isbn || 'N/A'}
Link: ${catalogData.currentUrl}`;

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(textData).then(function() {
                showToast('Detail katalog telah disalin ke clipboard!', 'success');
            }).catch(function(err) {
                fallbackCopyTextToClipboard(textData);
            });
        } else {
            fallbackCopyTextToClipboard(textData);
        }
    }

    function fallbackCopyTextToClipboard(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.top = "0";
        textArea.style.left = "0";
        textArea.style.position = "fixed";
        textArea.style.opacity = "0";

        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            const successful = document.execCommand('copy');
            if (successful) {
                showToast('Detail katalog telah disalin ke clipboard!', 'success');
            } else {
                showToast('Gagal menyalin ke clipboard', 'danger');
            }
        } catch (err) {
            showToast('Browser tidak mendukung copy ke clipboard', 'warning');
        }

        document.body.removeChild(textArea);
    }

    function shareViaEmail() {
        const subject = encodeURIComponent('Katalog: ' + catalogData.title);
        const body = encodeURIComponent(`Saya ingin berbagi katalog ini dengan Anda:

Judul: ${catalogData.title}
Pengarang: ${catalogData.author || 'N/A'}
Penerbit: ${catalogData.publisher || 'N/A'}
Tahun: ${catalogData.publishYear || 'N/A'}

Lihat detail lengkap di: ${catalogData.currentUrl}`);

        window.open(`mailto:?subject=${subject}&body=${body}`);
    }

    function shareViaWhatsapp() {
        const text = encodeURIComponent(`*Katalog Perpustakaan*

📚 *Judul:* ${catalogData.title}
✍️ *Pengarang:* ${catalogData.author || 'N/A'}
🏢 *Penerbit:* ${catalogData.publisher || 'N/A'}
📅 *Tahun:* ${catalogData.publishYear || 'N/A'}

🔗 Link: ${catalogData.currentUrl}`);

        window.open(`https://wa.me/?text=${text}`, '_blank');
    }

    function copyLink() {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(catalogData.currentUrl).then(function() {
                showToast('Link telah disalin ke clipboard!', 'success');
            }).catch(function(err) {
                fallbackCopyTextToClipboard(catalogData.currentUrl);
            });
        } else {
            fallbackCopyTextToClipboard(catalogData.currentUrl);
        }
    }

    function exportBibTeX() {
        const controlNumber = catalogData.controlNumber.replace(/[^a-zA-Z0-9]/g, '') || 'catalog' + catalogData.id;
        const bibtex = `@book{${controlNumber},
    title = {${catalogData.title}},
    author = {${catalogData.author}},
    publisher = {${catalogData.publisher}},
    year = {${catalogData.publishYear}},
    isbn = {${catalogData.isbn}},
    address = {${catalogData.publishLocation}},
    note = {Available at: ${catalogData.currentUrl}}
}`;

        downloadFile(`catalog_${catalogData.id}.bib`, bibtex, 'text/plain');
    }

    function exportRIS() {
        const ris = `TY  - BOOK
TI  - ${catalogData.title}
AU  - ${catalogData.author}
PB  - ${catalogData.publisher}
PY  - ${catalogData.publishYear}
SN  - ${catalogData.isbn}
CY  - ${catalogData.publishLocation}
UR  - ${catalogData.currentUrl}
N2  - ${catalogData.note.substring(0, 100)}
KW  - ${catalogData.subject}
ER  - 

`;

        downloadFile(`catalog_${catalogData.id}.ris`, ris, 'application/x-research-info-systems');
    }

    function exportCSV() {
        const csvContent = [
            ['Field', 'Value'],
            ['ID', catalogData.id],
            ['Control Number', catalogData.controlNumber],
            ['Title', catalogData.title],
            ['Author', catalogData.author],
            ['Publisher', catalogData.publisher],
            ['Publish Location', catalogData.publishLocation],
            ['Publish Year', catalogData.publishYear],
            ['ISBN', catalogData.isbn],
            ['Call Number', catalogData.callNumber],
            ['Subject', catalogData.subject],
            ['Physical Description', catalogData.physicalDescription],
            ['Languages', catalogData.languages],
            ['Note', catalogData.note],
            ['URL', catalogData.currentUrl]
        ];

        const csv = csvContent.map(row =>
            row.map(cell => `"${String(cell || '').replace(/"/g, '""')}"`).join(',')
        ).join('\n');

        downloadFile(`catalog_${catalogData.id}.csv`, csv, 'text/csv');
    }

    function exportJSON() {
        const jsonData = {
            ...catalogData,
            exportedAt: new Date().toISOString()
        };

        const json = JSON.stringify(jsonData, null, 2);
        downloadFile(`catalog_${catalogData.id}.json`, json, 'application/json');
    }

    function downloadFile(filename, content, mimeType = 'text/plain') {
        try {
            const blob = new Blob([content], {
                type: mimeType + ';charset=utf-8'
            });

            if (window.navigator && window.navigator.msSaveOrOpenBlob) {
                window.navigator.msSaveOrOpenBlob(blob, filename);
            } else {
                const url = window.URL.createObjectURL(blob);
                const element = document.createElement('a');
                element.setAttribute('href', url);
                element.setAttribute('download', filename);
                element.style.display = 'none';

                document.body.appendChild(element);
                element.click();
                document.body.removeChild(element);

                setTimeout(() => {
                    window.URL.revokeObjectURL(url);
                }, 100);
            }

            showToast(`File ${filename} berhasil didownload!`, 'success');

        } catch (error) {
            console.error('Download error:', error);
            showToast('Gagal mendownload file. Silakan coba lagi.', 'danger');
        }
    }

    function printCatalog() {
        window.print();
    }

    function showToast(message, type = 'info') {
        const existingToasts = document.querySelectorAll('.custom-toast');
        existingToasts.forEach(toast => toast.remove());

        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show position-fixed custom-toast`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 350px; border-radius: 12px; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.25); border:none;';

        const iconClass = type === 'success' ? 'check-circle' :
            type === 'danger' ? 'exclamation-circle' :
            type === 'warning' ? 'exclamation-triangle' : 'info-circle';

        toast.innerHTML = `
        <i class="fas fa-${iconClass} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

        document.body.appendChild(toast);

        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 150);
            }
        }, 4000);
    }

    window.addEventListener('beforeprint', function() {
        document.querySelectorAll('.btn, .dropdown').forEach(el => {
            el.style.display = 'none';
        });
    });

    window.addEventListener('afterprint', function() {
        document.querySelectorAll('.btn, .dropdown').forEach(el => {
            el.style.display = '';
        });
    });

    // Manual Bootstrap 5 Dropdown initialization (fallback jika jQuery 4 konflik)
    document.addEventListener('DOMContentLoaded', function () {
        var btn  = document.getElementById('downloadMarcDropdown');
        var menu = document.getElementById('marcDropdownMenu');
        var caret = document.getElementById('marcCaretIcon');

        if (btn && menu) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                var isOpen = menu.style.display === 'block';
                menu.style.display = isOpen ? 'none' : 'block';
                if (caret) {
                    caret.className = isOpen ? 'fas fa-caret-down' : 'fas fa-caret-up';
                }
            });

            document.addEventListener('click', function (e) {
                var wrapper = document.getElementById('marcDropdownWrapper');
                if (wrapper && !wrapper.contains(e.target)) {
                    menu.style.display = 'none';
                    if (caret) caret.className = 'fas fa-caret-down';
                }
            });

            menu.querySelectorAll('.dropdown-item').forEach(function(item) {
                item.addEventListener('click', function() {
                    menu.style.display = 'none';
                    if (caret) caret.className = 'fas fa-caret-down';
                });
            });
        }

        var btnBagikan  = document.getElementById('bagikanDropdownBtn');
        var menuBagikan = document.getElementById('bagikanDropdownMenu');
        var caretBagikan = document.getElementById('bagikanCaretIcon');

        if (btnBagikan && menuBagikan) {
            btnBagikan.addEventListener('click', function (e) {
                e.stopPropagation();
                var isOpen = menuBagikan.style.display === 'block';
                menuBagikan.style.display = isOpen ? 'none' : 'block';
                if (caretBagikan) {
                    caretBagikan.className = isOpen ? 'fas fa-caret-down' : 'fas fa-caret-up';
                }
            });

            document.addEventListener('click', function (e) {
                var wrapper = document.getElementById('bagikanDropdownWrapper');
                if (wrapper && !wrapper.contains(e.target)) {
                    menuBagikan.style.display = 'none';
                    if (caretBagikan) caretBagikan.className = 'fas fa-caret-down';
                }
            });
        }
    });

    // ===== Member Login Modal (Baca Buku Digital) =====
    let pendingBacaDigitalUrl = null;

    function showMemberLoginModal(url) {
        pendingBacaDigitalUrl = url;
        const alertBox = document.getElementById('memberLoginAlert');
        if (alertBox) {
            alertBox.classList.add('d-none');
            alertBox.textContent = '';
        }
        const form = document.getElementById('memberLoginForm');
        if (form) form.reset();
        if (typeof hcaptcha !== 'undefined') {
            try { hcaptcha.reset(); } catch (e) {}
        }
        const modalEl = document.getElementById('memberLoginModal');
        if (modalEl) {
            new bootstrap.Modal(modalEl).show();
        }
    }

    (function () {
        const memberLoginForm = document.getElementById('memberLoginForm');
        if (!memberLoginForm) return;

        memberLoginForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const alertBox = document.getElementById('memberLoginAlert');
            const submitBtn = document.getElementById('memberLoginSubmitBtn');
            const originalBtnHtml = submitBtn.innerHTML;

            // Buka tab kosong sekarang (dalam gesture klik user) supaya tidak diblokir popup blocker,
            // lalu arahkan setelah login berhasil.
            const pdfTab = pendingBacaDigitalUrl ? window.open('about:blank', '_blank') : null;

            const formData = new FormData();
            formData.append('username', document.getElementById('memberLoginUsername').value.trim());
            formData.append('password', document.getElementById('memberLoginPassword').value);
            if (typeof hcaptcha !== 'undefined') {
                formData.append('h-captcha-response', hcaptcha.getResponse());
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...';
            alertBox.classList.add('d-none');

            fetch('<?= base_url('opac/member-login') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        if (pdfTab) pdfTab.close();
                        alertBox.textContent = data.message || 'Login gagal. Silakan coba lagi.';
                        alertBox.classList.remove('d-none');
                        if (typeof hcaptcha !== 'undefined') {
                            try { hcaptcha.reset(); } catch (e) {}
                        }
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                        return;
                    }

                    const modalEl = document.getElementById('memberLoginModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();

                    showToast('Login berhasil!', 'success');

                    if (pdfTab && pendingBacaDigitalUrl) {
                        pdfTab.location.href = pendingBacaDigitalUrl;
                    } else if (pendingBacaDigitalUrl) {
                        window.open(pendingBacaDigitalUrl, '_blank');
                    }

                    // Muat ulang halaman agar tombol "Baca PDF" langsung tampil sebagai link
                    setTimeout(() => window.location.reload(), 600);
                })
                .catch(() => {
                    if (pdfTab) pdfTab.close();
                    alertBox.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
                    alertBox.classList.remove('d-none');
                    if (typeof hcaptcha !== 'undefined') {
                        try { hcaptcha.reset(); } catch (e) {}
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                });
        });
    })();
</script>

<?= $this->endsection() ?>