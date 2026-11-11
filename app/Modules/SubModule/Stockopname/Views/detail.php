<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/ina-pagination.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-pagination.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/ina-stockopname.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-stockopname.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<?php
$summary       = $stockopnamedetailModel->getStockopnameSummary($stockopname->ID);
$totalRows     = (int) ($totalDetails ?? count($details));
$offsetDetails = (int) ($offsetDetails ?? 0);
$perPage       = (int) ($perPageDetails ?? 25);
$fromRow       = $totalRows > 0 ? $offsetDetails + 1 : 0;
$toRow         = min($offsetDetails + $perPage, $totalRows);
?>
<div class="app-main__inner so-page">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-note icon-gradient bg-strong-bliss"></i>
                </div>
                <div>
                    <?= esc($stockopname->ProjectName) ?>
                    <div class="page-title-subheading">Detail data stock opname</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="ms-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('stockopname') ?>">Stock Opname</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"><?= esc($stockopname->ProjectName) ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Info proyek -->
    <div class="card mb-4">
        <div class="card-header">
            <div>
                <h2 class="so-card-title"><i class="fas fa-project-diagram" aria-hidden="true"></i> Informasi Proyek</h2>
                <p class="so-card-subtitle">Ringkasan hasil stock opname sejauh ini</p>
            </div>
            <div class="so-header-end">
                <a href="<?= base_url('stockopname/exportexcel/' . (int) $stockopname->ID) ?>" class="btn btn-outline-primary">
                    <i class="fas fa-file-excel" aria-hidden="true"></i> Export Excel
                </a>
            </div>
        </div>
        <div class="card-body">
            <dl class="so-meta">
                <div>
                    <dt>Tahun</dt>
                    <dd><?= esc($stockopname->Tahun ?: '-') ?></dd>
                </div>
                <div>
                    <dt>Koordinator</dt>
                    <dd><?= esc($stockopname->Koordinator ?: '-') ?></dd>
                </div>
                <div>
                    <dt>Tanggal Mulai</dt>
                    <dd><?= $stockopname->TglMulai ? date('d/m/Y', strtotime($stockopname->TglMulai)) : '-' ?></dd>
                </div>
            </dl>
            <div class="so-stats">
                <div class="so-stat so-stat--primary">
                    <span class="so-stat__label">Total Item</span>
                    <span class="so-stat__value" data-stat="total_items"><?= number_format((int) $summary['total_items']) ?></span>
                </div>
                <div class="so-stat">
                    <span class="so-stat__label">Pindah Lokasi</span>
                    <span class="so-stat__value" data-stat="location_changes"><?= number_format((int) $summary['location_changes']) ?></span>
                </div>
                <div class="so-stat">
                    <span class="so-stat__label">Ganti Status</span>
                    <span class="so-stat__value" data-stat="status_changes"><?= number_format((int) $summary['status_changes']) ?></span>
                </div>
                <div class="so-stat">
                    <span class="so-stat__label">Ganti Aturan</span>
                    <span class="so-stat__value" data-stat="rule_changes"><?= number_format((int) $summary['rule_changes']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Pemindai barcode -->
    <div class="card mb-4 so-no-print">
        <div class="card-header">
            <h2 class="so-card-title"><i class="fas fa-barcode" aria-hidden="true"></i> Scan Barcode Koleksi</h2>
        </div>
        <div class="card-body">
            <form id="scanForm">
                <label for="barcodeInput">Nomor Barcode</label>
                <div class="so-scan">
                    <input type="text"
                        id="barcodeInput"
                        class="form-control barcode-input"
                        placeholder="Scan atau ketik nomor barcode"
                        autocomplete="off"
                        autofocus>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus-circle" aria-hidden="true"></i> Tambah ke Stock Opname
                    </button>
                </div>
                <small class="so-hint">Tekan Enter setelah memindai. Tekan Esc untuk kembali ke kolom ini.</small>
            </form>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4 so-no-print">
        <div class="card-body">
            <form method="get" action="<?= current_url() ?>" class="so-filter">
                <div>
                    <label for="location_library_id">Lokasi</label>
                    <select id="location_library_id" name="location_library_id" class="form-select">
                        <option value="">Semua Lokasi</option>
                        <?php foreach ($locationLibraries as $library): ?>
                            <option value="<?= (int) $library->ID ?>" <?= $library->ID == ($selectedLocationLibraryId ?? 0) ? 'selected' : '' ?>>
                                <?= esc($library->Name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="location_id">Lokasi Ruang</label>
                    <select id="location_id" name="location_id" class="form-select">
                        <option value="">Semua Lokasi Ruang</option>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?= (int) $loc->ID ?>" data-location-library-id="<?= (int) $loc->LocationLibrary_id ?>"
                                <?= $loc->ID == ($selectedLocationId ?? 0) ? 'selected' : '' ?>>
                                <?= esc($loc->Name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="so-filter__actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter" aria-hidden="true"></i> Filter</button>
                    <a href="<?= current_url() ?>" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Detail stock opname -->
    <div class="card mb-4">
        <div class="card-header">
            <h2 class="so-card-title"><i class="fas fa-list" aria-hidden="true"></i> Detail Stock Opname</h2>
            <div class="so-header-end">
                <span class="so-count"><?= number_format($totalRows) ?> data</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="detailTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Barcode</th>
                            <th>Judul</th>
                            <th>Pengarang</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Aturan</th>
                            <th>Tanggal Scan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($details)): ?>
                            <?php
                            // Nomor urut menyesuaikan halaman aktif, bukan selalu mulai dari 1
                            $no = $offsetDetails + 1;
                            foreach ($details as $detail): ?>
                                <tr id="row-<?= (int) $detail['ID'] ?>">
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <span class="so-cell-main"><?= esc($detail['NomorBarcode']) ?></span>
                                        <span class="so-cell-sub"><?= esc($detail['CallNumber']) ?></span>
                                    </td>
                                    <td>
                                        <span class="so-cell-main"><?= esc($detail['Title']) ?></span>
                                        <span class="so-cell-sub"><?= esc($detail['Publisher']) ?></span>
                                    </td>
                                    <td><?= esc($detail['Author']) ?></td>
                                    <td>
                                        <select class="form-select form-select-sm select-lokasi"
                                                data-prev-id="<?= (int) $detail['PrevLocationID'] ?>"
                                                data-prev-name="<?= esc($detail['PrevLocationName'] ?? '', 'attr') ?>"
                                                aria-label="Lokasi"
                                                data-detail-id="<?= (int) $detail['ID'] ?>"
                                                data-field="current_location_id">
                                            <?php foreach ($locations as $loc): ?>
                                                <option value="<?= (int) $loc->ID ?>" <?= $loc->ID == $detail['CurrentLocationID'] ? 'selected' : '' ?>>
                                                    <?= esc($loc->Name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="so-changed"<?= $detail['PrevLocationID'] != $detail['CurrentLocationID'] ? '' : ' hidden' ?>>Dari: <?= esc($detail['PrevLocationName']) ?></span>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm select-status"
                                                data-prev-id="<?= (int) $detail['PrevStatusID'] ?>"
                                                data-prev-name="<?= esc($detail['PrevStatusName'] ?? '', 'attr') ?>"
                                                aria-label="Status"
                                                data-detail-id="<?= (int) $detail['ID'] ?>"
                                                data-field="current_status_id">
                                            <?php foreach ($statuses as $st): ?>
                                                <option value="<?= (int) $st->ID ?>" <?= $st->ID == $detail['CurrentStatusID'] ? 'selected' : '' ?>>
                                                    <?= esc($st->Name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="so-changed"<?= $detail['PrevStatusID'] != $detail['CurrentStatusID'] ? '' : ' hidden' ?>>Dari: <?= esc($detail['PrevStatusName']) ?></span>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm select-rule"
                                                data-prev-id="<?= (int) $detail['PrevCollectionRuleID'] ?>"
                                                data-prev-name="<?= esc($detail['PrevRuleName'] ?? '', 'attr') ?>"
                                                aria-label="Aturan"
                                                data-detail-id="<?= (int) $detail['ID'] ?>"
                                                data-field="current_collection_rule_id">
                                            <?php foreach ($rules as $rule): ?>
                                                <option value="<?= (int) $rule->ID ?>" <?= $rule->ID == $detail['CurrentCollectionRuleID'] ? 'selected' : '' ?>>
                                                    <?= esc($rule->Name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="so-changed"<?= $detail['PrevCollectionRuleID'] != $detail['CurrentCollectionRuleID'] ? '' : ' hidden' ?>>Dari: <?= esc($detail['PrevRuleName']) ?></span>
                                    </td>
                                    <td>
                                        <span class="so-cell-main"><?= date('d/m/Y H:i', strtotime($detail['CreateDate'])) ?></span>
                                        <span class="so-cell-sub">oleh User <?= esc($detail['CreateBy']) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="so-empty">
                                    <i class="fas fa-inbox" aria-hidden="true"></i>
                                    <strong>Belum ada koleksi yang di-scan</strong>
                                    Scan barcode koleksi untuk memulai.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!empty($details)): ?>
            <div class="ina-table-pagination">
                <div class="ina-table-pagination__rows">
                    <label for="perPageSelect">Baris per halaman</label>
                    <select id="perPageSelect" class="form-select">
                        <?php foreach ([25, 50, 100] as $opt): ?>
                            <option value="<?= $opt ?>" <?= $perPage === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span>Menampilkan <?= number_format($fromRow) ?>–<?= number_format($toRow) ?> dari <?= number_format($totalRows) ?></span>
                </div>
                <div class="ina-table-pagination__end"><?= $detailsPager ?? '' ?></div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Ringkasan per lokasi & status -->
    <div class="card mb-4">
        <div class="card-header">
            <h2 class="so-card-title"><i class="fas fa-map-marker-alt" aria-hidden="true"></i> Ringkasan Berdasarkan Lokasi &amp; Status</h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="locationSummaryTable">
                    <thead>
                        <tr>
                            <?php if (!empty($locationSummary)): ?>
                                <?php foreach (array_keys($locationSummary[0]) as $header): ?>
                                    <th><?= esc($header) ?></th>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($locationSummary)): ?>
                            <?php foreach ($locationSummary as $row): ?>
                                <tr>
                                    <?php foreach ($row as $value): ?>
                                        <td><?= esc($value) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="100%" class="so-empty">Data ringkasan tidak tersedia.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Koleksi belum di-stock opname -->
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="so-card-title"><i class="fas fa-exclamation-triangle" aria-hidden="true"></i> Koleksi Belum Di-Stock Opname</h2>
                <p class="so-card-subtitle">Klik Scan untuk memasukkan koleksi tanpa memindai barcode</p>
            </div>
            <div class="so-header-end">
                <span class="so-count so-count--danger"><?= number_format((int) $totalNotInStockopname) ?> koleksi</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="notInTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Barcode</th>
                            <th>Judul</th>
                            <th>Pengarang</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Aturan</th>
                            <th class="so-no-print">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($collectionsNotInStockopname)): ?>
                            <?php
                            $no = ($offsetNotIn ?? 0) + 1;
                            foreach ($collectionsNotInStockopname as $collection): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <span class="so-cell-main"><?= esc($collection['NomorBarcode']) ?></span>
                                        <span class="so-cell-sub"><?= esc($collection['CallNumber']) ?></span>
                                    </td>
                                    <td>
                                        <span class="so-cell-main"><?= esc($collection['Title']) ?></span>
                                        <span class="so-cell-sub"><?= esc($collection['Publisher']) ?></span>
                                    </td>
                                    <td><?= esc($collection['Author']) ?></td>
                                    <td>
                                        <select class="form-select form-select-sm select-lokasi-not-in"
                                                aria-label="Lokasi"
                                                data-collection-id="<?= (int) $collection['id'] ?>"
                                                data-field="current_location_id">
                                            <?php foreach ($locations as $loc): ?>
                                                <option value="<?= (int) $loc->ID ?>" <?= $loc->ID == $collection['CurrentLocationID'] ? 'selected' : '' ?>>
                                                    <?= esc($loc->Name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm select-status-not-in"
                                                aria-label="Status"
                                                data-collection-id="<?= (int) $collection['id'] ?>"
                                                data-field="current_status_id">
                                            <?php foreach ($statuses as $st): ?>
                                                <option value="<?= (int) $st->ID ?>" <?= $st->ID == $collection['CurrentStatusID'] ? 'selected' : '' ?>>
                                                    <?= esc($st->Name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm select-rule-not-in"
                                                aria-label="Aturan"
                                                data-collection-id="<?= (int) $collection['id'] ?>"
                                                data-field="current_collection_rule_id">
                                            <?php foreach ($rules as $rule): ?>
                                                <option value="<?= (int) $rule->ID ?>" <?= $rule->ID == $collection['CurrentCollectionRuleID'] ? 'selected' : '' ?>>
                                                    <?= esc($rule->Name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="so-no-print">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="quickAdd(<?= esc(json_encode((string) $collection['NomorBarcode']), 'attr') ?>)">
                                            <i class="fas fa-plus" aria-hidden="true"></i> Scan
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="so-empty so-empty--success">
                                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                                    <strong>Semua koleksi sudah di-stock opname</strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!empty($collectionsNotInStockopname)): ?>
            <div class="ina-table-pagination">
                <div class="ina-table-pagination__rows">10 baris per halaman</div>
                <div class="ina-table-pagination__end"><?= $notInPager ?? '' ?></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha384-NXgwF8Kv9SSAr+jemKKcbvQsz+teULH/a5UNJvZc6kP47hZgl62M1vGnw6gHQhb1" crossorigin="anonymous"></script>
<!-- toastr.js/latest sengaja TANPA integrity: URL "latest" memang selalu berubah, SRI di sini justru akan mematahkan skrip tiap ada rilis baru -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>

<script>
    // Configuration
    const stockopnameId = <?= $stockopname->ID ?>;
    let currentEditingDetail = null;

    // Toastr configuration
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    $(document).ready(function() {
        const $locationLibrary = $('#location_library_id');
        const $location = $('#location_id');

        function filterLocations() {
            const libraryId = $locationLibrary.val();
            $location.find('option').each(function() {
                const $option = $(this);
                const optionLibraryId = $option.data('location-library-id');
                $option.toggle(!$option.val() || !libraryId || String(optionLibraryId) === String(libraryId));
            });
            if ($location.find('option:selected').is(':hidden')) {
                $location.val('');
            }
        }

        filterLocations();
        $locationLibrary.on('change', function() {
            $location.val('');
            filterLocations();
        });

        // Catatan: DataTables tidak lagi dipakai di tabel ini.
        // Pagination sekarang murni ditangani server-side oleh pager bawaan CI4
        // (lihat $detailsPager di controller & view), supaya kuat untuk data
        // puluhan ribu baris tanpa menumpuk semuanya di DOM sekaligus.

        // Inline select — update lokasi / status / aturan
        $(document).on('change', '.select-lokasi, .select-status, .select-rule', function() {
            const $sel      = $(this);
            const detailId  = $sel.data('detail-id');
            const field     = $sel.data('field');
            const value     = $sel.val();

            $sel.addClass('select-saving');

            const payload = { detail_id: detailId };
            payload[field] = value;

            $.ajax({
                url: '<?= base_url('stockopname/updateDetail') ?>',
                method: 'POST',
                data: payload,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Berhasil diperbarui', timer: 1500, showConfirmButton: false });
                        $sel.data('prev-val', value);
                        updateChangeNote($sel);
                        renderSummary(res.summary);
                        renderLocationSummary(res.location_summary);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Gagal memperbarui' });
                        $sel.val($sel.data('prev-val'));
                    }
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan jaringan' });
                    $sel.val($sel.data('prev-val'));
                },
                complete: function() {
                    $sel.removeClass('select-saving');
                },
            });
        });

        // Simpan nilai sebelumnya agar bisa di-rollback jika gagal
        $(document).on('focus', '.select-lokasi, .select-status, .select-rule, .select-lokasi-not-in, .select-status-not-in, .select-rule-not-in', function() {
            $(this).data('prev-val', $(this).val());
        });

        // Inline select — update lokasi / status / aturan for NOT IN stockopname
        $(document).on('change', '.select-lokasi-not-in, .select-status-not-in, .select-rule-not-in', function() {
            const $sel      = $(this);
            const collectionId = $sel.data('collection-id');
            const field     = $sel.data('field');
            const value     = $sel.val();

            $sel.addClass('select-saving');

            const payload = { collection_id: collectionId };
            payload[field] = value;

            $.ajax({
                url: '<?= base_url('stockopname/updateCollection') ?>',
                method: 'POST',
                data: payload,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Koleksi berhasil diperbarui', timer: 1500, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Gagal memperbarui' });
                        $sel.val($sel.data('prev-val'));
                    }
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan jaringan' });
                    $sel.val($sel.data('prev-val'));
                },
                complete: function() {
                    $sel.removeClass('select-saving');
                },
            });
        });

        // Ganti jumlah data per halaman — reload dengan parameter per_page baru,
        // reset ke halaman 1 karena jumlah baris per halaman berubah
        $('#perPageSelect').on('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', $(this).val());
            url.searchParams.set('page_details', 1);
            window.location.href = url.toString();
        });

        // Focus on barcode input
        $('#barcodeInput').focus();

        // Handle scan form submission
        $('#scanForm').on('submit', function(e) {
            e.preventDefault();
            scanBarcode();
        });

        // Auto-submit when barcode is scanned (typically ends with Enter)
        $('#barcodeInput').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                scanBarcode();
            }
        });

        // Auto-focus back to barcode input after any action
        // Kecualikan klik pada elemen interaktif dan link pagination
        $(document).on('click', function(e) {
            const $target = $(e.target);
            const isInteractiveControl = $target.closest(
                'select, input, button, a, .modal, .pagination'
            ).length > 0;

            if (!isInteractiveControl && !$('#editModal').hasClass('show')) {
                setTimeout(() => $('#barcodeInput').focus(), 100);
            }
        });
    });

    // Scan barcode function
    function scanBarcode() {
        const barcode = $('#barcodeInput').val().trim();

        if (!barcode) {
            Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Mohon masukkan nomor barcode' });
            return;
        }

        const submitBtn = $('#scanForm button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Memproses...').prop('disabled', true);

        $.ajax({
            url: '<?= base_url('stockopname/scanBarcode') ?>',
            type: 'POST',
            data: {
                barcode: barcode,
                stockopname_id: stockopnameId
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message, timer: 1500, showConfirmButton: false })
                        .then(function() {
                            // Reload halaman penuh supaya data baru + pager tetap konsisten
                            // dengan yang ada di server (item baru biasanya masuk ke halaman awal).
                            location.reload();
                        });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
                    $('#barcodeInput').select();
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan saat memproses barcode' });
                console.error('Error:', error);
            },
            complete: function() {
                submitBtn.html(originalText).prop('disabled', false);
                $('#barcodeInput').focus();
            }
        });
    }

    // Keterangan "Dari: ..." mengikuti nilai baru tanpa memuat ulang halaman
    function updateChangeNote($sel) {
        const $note = $sel.siblings('.so-changed');
        const changed = String($sel.val()) !== String($sel.data('prev-id'));
        $note.text('Dari: ' + ($sel.data('prev-name') || '-')).prop('hidden', !changed);
    }

    // Kotak angka ringkasan proyek
    function renderSummary(summary) {
        if (!summary) return;
        $('[data-stat]').each(function() {
            const key = $(this).data('stat');
            if (summary[key] !== undefined) {
                $(this).text(Number(summary[key]).toLocaleString('en-US'));
            }
        });
    }

    // Tabel "Ringkasan Berdasarkan Lokasi & Status" digambar ulang dari data server
    function renderLocationSummary(rows) {
        if (!Array.isArray(rows)) return;
        const $table = $('#locationSummaryTable');
        const $headRow = $('<tr>');
        const $body = $('<tbody>');
        if (rows.length) {
            const headers = Object.keys(rows[0]);
            headers.forEach(function(h) { $headRow.append($('<th>').text(h)); });
            rows.forEach(function(row) {
                const $tr = $('<tr>');
                headers.forEach(function(h) { $tr.append($('<td>').text(row[h] ?? '')); });
                $body.append($tr);
            });
        } else {
            $body.append($('<tr>').append($('<td colspan="100%" class="so-empty">').text('Data ringkasan tidak tersedia.')));
        }
        $table.find('thead').empty().append($headRow);
        $table.find('tbody').replaceWith($body);
    }

    // Quick add function for collections not in stockopname
    function quickAdd(barcode) {
        $('#barcodeInput').val(barcode);
        scanBarcode();
    }

    // Edit detail function
    function editDetail(detailId) {
        currentEditingDetail = detailId;
        $('#editDetailId').val(detailId);

        const row = $(`#row-${detailId}`);
        const barcode = row.find('td:eq(1) strong').text();

        loadCollectionInfo(barcode);
        $('#editModal').modal('show');
    }

    // Load collection info for edit modal
    function loadCollectionInfo(barcode) {
        $.ajax({
            url: '<?= base_url('stockopname/getCollectionInfo') ?>',
            type: 'GET',
            data: {
                barcode: barcode
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const collection = response.data;
                    $('#collectionInfo').html(`
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Barcode:</strong> ${collection.NomorBarcode}<br>
                                    <strong>Call Number:</strong> ${collection.CallNumber || '-'}<br>
                                </div>
                                <div class="col-md-6">
                                    <strong>Judul:</strong> ${collection.Title}<br>
                                    <strong>Pengarang:</strong> ${collection.Author || '-'}
                                </div>
                            </div>
                        `);

                    $('#editCurrentLocation').val(collection.location_id);
                    $('#editCurrentStatus').val(collection.status_id);
                    $('#editCurrentRule').val(collection.collection_rule_id);
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal memuat informasi koleksi' });
            }
        });
    }

    // Update detail function
    function updateDetail() {
        const formData = $('#editForm').serialize();

        $.ajax({
            url: '<?= base_url('stockopname/updateDetail') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message, timer: 1500, showConfirmButton: false })
                        .then(() => { location.reload(); });
                    $('#editModal').modal('hide');
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan saat memperbarui data' });
            }
        });
    }

    // Delete detail function
    function deleteDetail(detailId) {
        Swal.fire({
            icon: 'warning',
            title: 'Hapus Data',
            text: 'Yakin ingin menghapus detail stockopname ini?',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= base_url('stockopname/deleteDetail') ?>/${detailId}`,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message, timer: 1500, showConfirmButton: false })
                                .then(function() {
                                    // Reload penuh: dengan pagination server-side,
                                    // nomor urut & halaman perlu dihitung ulang oleh server.
                                    location.reload();
                                });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
                        }
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan saat menghapus data' });
                    }
                });
            }
        });
    }

    // Handle modal events
    $('#editModal').on('hidden.bs.modal', function() {
        currentEditingDetail = null;
        $('#barcodeInput').focus();
    });

    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && !$('#editModal').hasClass('show')) {
            $('#barcodeInput').focus().select();
        }
    });

    // Print function (bonus)
    function printStockopname() {
        window.print();
    }
</script>
<?= $this->endSection('script'); ?>