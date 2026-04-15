<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row">
        <!-- Breadcrumb -->
        <div class="col-12 mb-4">
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

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Detail Katalog Card -->
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-book-open me-2"></i>
                            Detail Katalog
                        </h4>
                        <span class="badge bg-light text-dark">
                            ID: <?= esc($catalog['ID']) ?>
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Book Cover and Basic Info -->
                    <div class="row mb-4">
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

                            <div class="book-cover-detail text-center">
                                <?php if ($coverUrl): ?>
                                    <img src="<?= $coverUrl ?>"
                                        alt="Cover <?= esc($catalog['Title']) ?>"
                                        class="img-fluid rounded shadow book-cover-large"
                                        style="max-height: 400px; cursor: pointer;"
                                        onclick="showCoverModal('<?= $coverUrl ?>', '<?= esc($catalog['Title']) ?>')"
                                        onerror="handleImageError(this)">
                                    <div class="mt-2">
                                        <button class="btn btn-outline-primary btn-sm"
                                            onclick="showCoverModal('<?= $coverUrl ?>', '<?= esc($catalog['Title']) ?>')">
                                            <i class="fas fa-search-plus me-1"></i>Lihat Cover
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div class="no-cover-large d-flex align-items-center justify-content-center bg-light rounded shadow"
                                        style="height: 400px; border: 3px dashed #dee2e6;">
                                        <div class="text-center text-muted">
                                            <i class="fas fa-book fa-4x mb-3"></i>
                                            <h5>Cover Tidak Tersedia</h5>
                                            <p class="mb-0">Gambar cover belum diupload</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <!-- Title -->
                            <div class="mb-4">
                                <h2 class="text-primary mb-3">
                                    <?= esc($catalog['Title']) ?>
                                </h2>

                                <?php if (!empty($catalog['Edition'])): ?>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-bookmark me-2"></i>
                                        <strong>Edisi:</strong> <?= esc($catalog['Edition']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Author & Basic Info -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="info-item mb-3">
                                        <i class="fas fa-user text-primary me-2"></i>
                                        <strong>Pengarang:</strong><br>
                                        <span class="ms-4"><?= esc($catalog['Author'] ?? 'Tidak tersedia') ?></span>
                                    </div>

                                    <div class="info-item mb-3">
                                        <i class="fas fa-building text-primary me-2"></i>
                                        <strong>Penerbit:</strong><br>
                                        <span class="ms-4"><?= esc($catalog['Publisher'] ?? 'Tidak tersedia') ?></span>
                                    </div>

                                    <?php if (!empty($catalog['PublishLocation'])): ?>
                                        <div class="info-item mb-3">
                                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                            <strong>Tempat Terbit:</strong><br>
                                            <span class="ms-4"><?= esc($catalog['PublishLocation']) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="info-item mb-3">
                                        <i class="fas fa-calendar text-primary me-2"></i>
                                        <strong>Tahun Terbit:</strong><br>
                                        <span class="ms-4"><?= esc($catalog['PublishYear'] ?? 'Tidak tersedia') ?></span>
                                    </div>

                                    <?php if (!empty($catalog['Languages'])): ?>
                                        <div class="info-item mb-3">
                                            <i class="fas fa-globe text-primary me-2"></i>
                                            <strong>Bahasa:</strong><br>
                                            <span class="ms-4"><?= esc($catalog['Languages']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subject & Classification -->
                    <?php if (!empty($catalog['Subject'])): ?>
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-tags me-2"></i>Subjek
                            </h5>
                            <div class="bg-light p-3 rounded">
                                <?php
                                $subjects = explode(';', $catalog['Subject']);
                                foreach ($subjects as $subject):
                                    $subject = trim($subject);
                                    if ($subject):
                                ?>
                                        <span class="badge bg-primary me-2 mb-2">
                                            <i class="fas fa-tag me-1"></i><?= esc($subject) ?>
                                        </span>
                                <?php endif;
                                endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Technical Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <?php if (!empty($catalog['PhysicalDescription'])): ?>
                                <div class="info-item mb-3">
                                    <i class="fas fa-file-alt text-primary me-2"></i>
                                    <strong>Deskripsi Fisik:</strong><br>
                                    <span class="ms-4"><?= esc($catalog['PhysicalDescription']) ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($catalog['ISBN'])): ?>
                                <div class="info-item mb-3">
                                    <i class="fas fa-barcode text-primary me-2"></i>
                                    <strong>ISBN:</strong><br>
                                    <span class="ms-4 font-monospace"><?= esc($catalog['ISBN']) ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($catalog['CallNumber'])): ?>
                                <div class="info-item mb-3">
                                    <i class="fas fa-hashtag text-primary me-2"></i>
                                    <strong>Nomor Panggil:</strong><br>
                                    <span class="ms-4 font-monospace"><?= esc($catalog['CallNumber']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <?php if (!empty($catalog['ControlNumber'])): ?>
                                <div class="info-item mb-3">
                                    <i class="fas fa-id-card text-primary me-2"></i>
                                    <strong>Control Number:</strong><br>
                                    <span class="ms-4 font-monospace"><?= esc($catalog['ControlNumber']) ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($catalog['BIBID'])): ?>
                                <div class="info-item mb-3">
                                    <i class="fas fa-database text-primary me-2"></i>
                                    <strong>BIB ID:</strong><br>
                                    <span class="ms-4 font-monospace"><?= esc($catalog['BIBID']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <?php if (!empty($catalog['Note'])): ?>
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-sticky-note me-2"></i>Catatan
                            </h5>
                            <div class="bg-light p-3 rounded">
                                <?= nl2br(esc($catalog['Note'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Status Badges -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Status
                        </h5>
                        <div class="d-flex flex-wrap gap-2">
                            <?php if ($catalog['IsOPAC'] ?? false): ?>
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-check me-1"></i>Tersedia di OPAC
                                </span>
                            <?php endif; ?>

                            <?php if ($catalog['IsBNI'] ?? false): ?>
                                <span class="badge bg-info fs-6">
                                    <i class="fas fa-flag me-1"></i>Bibliografi Nasional Indonesia
                                </span>
                            <?php endif; ?>

                            <?php if ($catalog['IsKIN'] ?? false): ?>
                                <span class="badge bg-warning fs-6">
                                    <i class="fas fa-star me-1"></i>Karya Tulis Ilmiah Nasional
                                </span>
                            <?php endif; ?>

                            <?php if ($catalog['IsRDA'] ?? false): ?>
                                <span class="badge bg-secondary fs-6">
                                    <i class="fas fa-bookmark me-1"></i>RDA Cataloging
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-primary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Cetak
                        </button>

                        <button class="btn btn-success" onclick="copyToClipboard()">
                            <i class="fas fa-copy me-2"></i>Salin Detail
                        </button>

                        <?php if (!empty($catalog['CoverURL'])): ?>
                            <a href="<?= $coverUrl ?>" target="_blank" class="btn btn-info">
                                <i class="fas fa-external-link-alt me-2"></i>Lihat Cover Original
                            </a>
                        <?php endif; ?>

                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="fas fa-share-alt me-2"></i>Bagikan
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="shareViaEmail()">
                                        <i class="fas fa-envelope me-2"></i>Email
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="shareViaWhatsapp()">
                                        <i class="fab fa-whatsapp me-2"></i>WhatsApp
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="copyLink()">
                                        <i class="fas fa-link me-2"></i>Salin Link
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($roweksemplar) || !empty($roweksemplar_drm) || !empty($marc)): ?>
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-book me-2"></i>Informasi Eksemplar & Metadata
                        </h5>
                    </div>
                    <div class="card-body">

                        <!-- Tab Navigation + Download Button -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
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
                                <div class="dropdown ms-3">
                                    <button class="btn btn-outline-primary dropdown-toggle btn-sm" type="button" id="downloadMarcDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-download me-1"></i>Unduh Katalog MARC
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="downloadMarcDropdown">
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
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped table-bordered mb-0" id="tbl_eksemplars_fisik">
                                            <thead class="table-dark">
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
                                                        <td><span class="badge bg-primary"><?= esc($eksemplar->RuleName) ?></span></td>
                                                        <td><i class="fas fa-building me-1"></i><?= esc($eksemplar->LocationName) ?></td>
                                                        <td><span class="badge bg-success"><?= esc($eksemplar->StatusName) ?></span></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Buku Digital -->
                            <?php if (!empty($roweksemplar_drm)): ?>
                                <div class="tab-pane fade <?= empty($roweksemplar) ? 'show active' : '' ?>" id="digital" role="tabpanel" aria-labelledby="digital-tab">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped table-bordered mb-0" id="tbl_eksemplars_digital">
                                            <thead class="table-dark">
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
        <td><span class="badge bg-primary"><?= esc($eksemplar_drm->RuleName) ?></span></td>
        <td><i class="fas fa-cloud me-1"></i><?= esc($eksemplar_drm->LocationName) ?></td>
        <td><span class="badge bg-success"><?= esc($eksemplar_drm->StatusName) ?></span></td>
        <td>
            <a href="<?= base_url('katalog/view_decrypted/' . encData($catalog['ID'])) ?>" 
               target="_blank" 
               class="btn btn-primary btn-sm view-decrypted" 
               data-id="<?= $catalog['ID'] ?>">
               
                <i class="fas fa-file-pdf me-1"></i>Baca PDF
            </a>
        </td>
    </tr>
<?php endforeach; ?>
</tbody>
                                        </table>
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
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover" id="marcTable">
                                            <thead class="table-dark">
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
                                                        <td><span class="badge bg-primary"><?= esc($field->Tag) ?></span></td>
                                                        <td><code><?= esc($field->Indicator1 ?: '_') ?></code></td>
                                                        <td><code><?= esc($field->Indicator2 ?: '_') ?></code></td>
                                                        <td><span><?= esc($field->Value) ?></span></td>
                                                        <td class="text-center"><span class="badge bg-secondary"><?= esc($field->Sequence) ?></span></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-3 p-3 bg-light rounded">
                                        <h6 class="fw-bold text-primary mb-2">
                                            <i class="fas fa-lightbulb me-1"></i>Penjelasan Field MARC21:
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul class="list-unstyled small">
                                                    <li><strong>001:</strong> Control Number</li>
                                                    <li><strong>005:</strong> Date and Time of Latest Transaction</li>
                                                    <li><strong>020:</strong> ISBN</li>
                                                    <li><strong>100:</strong> Main Entry - Personal Name</li>
                                                    <li><strong>245:</strong> Title Statement</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-unstyled small">
                                                    <li><strong>250:</strong> Edition Statement</li>
                                                    <li><strong>260:</strong> Publication Information</li>
                                                    <li><strong>300:</strong> Physical Description</li>
                                                    <li><strong>650:</strong> Subject</li>
                                                    <li><strong>700:</strong> Added Entry - Personal Name</li>
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
            <!-- Quick Actions -->
            <div class="card mb-4 border-0 shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-tools me-2"></i>Aksi Cepat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('opac') ?>" class="btn btn-outline-primary">
                            <i class="fas fa-search me-2"></i>Cari Lagi
                        </a>
                        <a href="<?= base_url('opac/browse') ?>" class="btn btn-outline-info">
                            <i class="fas fa-list me-2"></i>Browse Katalog
                        </a>
                    </div>
                </div>
            </div>

            <!-- Catalog Information -->
            <div class="card mb-4 border-0 shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informasi Katalog
                    </h5>
                </div>
                <div class="card-body">


                    <hr>

                    <div class="small text-muted">
                        <i class="fas fa-calendar-plus me-2"></i>
                        <strong>Ditambahkan:</strong> <?= date('d M Y', strtotime($catalog['CreateDate'] ?? 'now')) ?><br>

                        <?php if (!empty($catalog['ApproveDateOPAC'])): ?>
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Disetujui OPAC:</strong> <?= date('d M Y', strtotime($catalog['ApproveDateOPAC'])) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Related Search -->
            <div class="card mb-4 border-0 shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-search me-2"></i>Pencarian Terkait
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if (!empty($catalog['Author'])): ?>
                            <a href="<?= base_url('opac?search=' . urlencode($catalog['Author']) . '&search_by=Author') ?>"
                                class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-user me-1"></i>
                                Karya <?= esc(explode(',', $catalog['Author'])[0]) ?>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($catalog['Publisher'])): ?>
                                <a href="<?= base_url('opac?search=' . urlencode($catalog['Publisher']) . '&search_by=Publisher') ?>"
                                class="btn btn-outline-success btn-sm">
                                <i class="fas fa-building me-1"></i>
                                Dari <?= esc($catalog['Publisher']) ?>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($catalog['Subject'])): ?>
                            <?php
                            $firstSubject = trim(explode(';', $catalog['Subject'])[0]);
                            if ($firstSubject):
                            ?>
                                <a href="<?= base_url('opac?search=' . urlencode($firstSubject) . '&search_by=Subject') ?>"
                                    class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-tag me-1"></i>
                                    Subjek: <?= esc(substr($firstSubject, 0, 20)) ?>...
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (!empty($catalog['PublishYear'])): ?>
                            <a href="<?= base_url('opac?PublishYear=' . $catalog['PublishYear'] . '&search_by=') ?>"
                                class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-calendar me-1"></i>
                                Tahun <?= esc($catalog['PublishYear']) ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Export Options -->
            <div class="card border-0 shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-download me-2"></i>Export
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-success btn-sm" onclick="exportBibTeX()">
                            <i class="fas fa-file-code me-2"></i>BibTeX
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="exportRIS()">
                            <i class="fas fa-file-alt me-2"></i>RIS Format
                        </button>
                        <button class="btn btn-outline-warning btn-sm" onclick="exportCSV()">
                            <i class="fas fa-file-csv me-2"></i>CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PDF Modal for Digital Books -->
<?php if (!empty($roweksemplar_drm)): ?>
    <div class="modal fade" id="pdfModal<?= $catalog['ID'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-pdf me-2"></i>Baca Buku Digital: <?= esc($catalog['Title']) ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">

                    <div class="text-center">
                        <i class="fas fa-file-pdf fa-4x text-primary mb-3"></i>
                        <h5>PDF Viewer</h5>
                        <iframe
                            src="<?= base_url('katalog/view_decrypted/' . $ID) ?>"
                            width="100%" height="500px" style="border: none;"></iframe>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                  
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>



<script>
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
        // Remove existing toasts
        const existingToasts = document.querySelectorAll('.custom-toast');
        existingToasts.forEach(toast => toast.remove());

        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show position-fixed custom-toast`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 350px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';

        const iconClass = type === 'success' ? 'check-circle' :
            type === 'danger' ? 'exclamation-circle' :
            type === 'warning' ? 'exclamation-triangle' : 'info-circle';

        toast.innerHTML = `
        <i class="fas fa-${iconClass} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

        document.body.appendChild(toast);

        // Auto remove after 4 seconds
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

    // Print styles
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
</script>

<?= $this->endsection() ?>