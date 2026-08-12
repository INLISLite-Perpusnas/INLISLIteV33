<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
    .scanner-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        color: white;
    }

    .barcode-input {
        font-size: 1.2rem;
        padding: 12px;
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .barcode-input:focus {
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
        border: 2px solid #667eea;
    }

    .scan-btn {
        background: linear-gradient(45deg, #28a745, #20c997);
        border: none;
        padding: 12px 25px;
        border-radius: 10px;
        color: white;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .scan-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
    }

    .collection-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .collection-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .status-badge {
        font-size: 0.8rem;
        padding: 5px 10px;
        border-radius: 15px;
    }

    .change-indicator {
        background: linear-gradient(45deg, #ffc107, #ff8c00);
        color: white;
        font-size: 0.7rem;
        padding: 2px 8px;
        border-radius: 10px;
        margin-left: 5px;
    }

    .summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .table-container {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    #detailTable .form-select-sm {
        min-width: 120px;
        font-size: 0.8rem;
    }

    #detailTable .select-saving {
        opacity: 0.5;
        pointer-events: none;
    }

    .table th,
    #detailTable thead tr th {
        background-color: #667eea !important;
        color: white !important;
        font-weight: 600;
        border: none;
    }

    .not-scanned-list {
        max-height: 400px;
        overflow-y: auto;
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
    }

    .not-scanned-item {
        background: white;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 8px;
        border-left: 4px solid #dc3545;
        transition: all 0.3s ease;
    }

    .not-scanned-item:hover {
        transform: translateX(5px);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .modal-content {
        border-radius: 15px;
        border: none;
    }

    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px 15px 0 0;
    }

    .btn-update {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
        border-radius: 8px;
        padding: 8px 15px;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-update:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
    }

    .btn-delete {
        background: linear-gradient(45deg, #dc3545, #c82333);
        border: none;
        border-radius: 8px;
        padding: 8px 15px;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-delete:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    }

    /* Custom styling for CI4 pager links, since we no longer use DataTables' own pagination UI */
    .pagination {
        margin-bottom: 0;
        flex-wrap: wrap;
    }

    .pagination .page-link {
        border-radius: 8px;
        margin: 0 3px;
        border: none;
        color: #667eea;
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
    }

    @media (max-width: 768px) {
        .table-responsive {
            border-radius: 15px;
        }

        .collection-card {
            margin-bottom: 10px;
        }

        .scanner-container {
            padding: 15px;
        }
    }
</style>
<style media="print">
    .scanner-container,
    .not-scanned-list,
    .btn,
    .modal,
    .pagination {
        display: none !important;
    }

    .table {
        font-size: 12px;
    }

    .summary-card {
        background: white !important;
        color: black !important;
        border: 1px solid #ccc;
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-note icon-gradient bg-strong-bliss"></i>
                </div>
                <div>
                    <h2><?= esc($stockopname->ProjectName) ?></h2>
                    <div class="page-title-subheading">
                        Detail data stock opname
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="ms-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('dashboard') ?>">
                                <i class="fa fa-home"></i> Home
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('stockopname') ?>">Stock Opname</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">
                            <?= esc($stockopname->ProjectName) ?>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Project Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="summary-card">
                <div class="row">
                    <div class="col-md-6">
                        <h4><i class="fas fa-project-diagram"></i> <?= $stockopname->ProjectName ?></h4>
                        <p class="mb-1"><strong>Tahun:</strong> <?= $stockopname->Tahun ?></p>
                        <p class="mb-1"><strong>Koordinator:</strong> <?= $stockopname->Koordinator ?></p>
                        <p class="mb-0"><strong>Tanggal Mulai:</strong> <?= date('d/m/Y', strtotime($stockopname->TglMulai)) ?></p>
                        <div class="mt-3">
                            <a href="<?= base_url('stockopname/exportexcel/' . $stockopname->ID) ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <?php
                        $summary = $stockopnamedetailModel->getStockopnameSummary($stockopname->ID);
                        ?>
                        <div class="row text-center">
                            <div class="col-3">
                                <h3 class="mb-0"><?= $summary['total_items'] ?></h3>
                                <small>Total Item</small>
                            </div>
                            <div class="col-3">
                                <h3 class="mb-0"><?= $summary['location_changes'] ?></h3>
                                <small>Pindah Lokasi</small>
                            </div>
                            <div class="col-3">
                                <h3 class="mb-0"><?= $summary['status_changes'] ?></h3>
                                <small>Ganti Status</small>
                            </div>
                            <div class="col-3">
                                <h3 class="mb-0"><?= $summary['rule_changes'] ?></h3>
                                <small>Ganti Aturan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barcode Scanner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="scanner-container">
                <h5><i class="fas fa-barcode"></i> Scan Barcode Koleksi</h5>
                <form id="scanForm" class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <input type="text"
                            id="barcodeInput"
                            class="form-control barcode-input"
                            placeholder="Scan atau ketik nomor barcode..."
                            autocomplete="off"
                            autofocus>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn scan-btn w-100">
                            <i class="fas fa-plus-circle"></i> Tambah ke Stockopname
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Stockopname Details Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Detail Stockopname</h5>
                    <span class="badge bg-light text-dark"><?= number_format($totalDetails ?? count($details)) ?> total data</span>
                </div>
                <div class="card-body p-0">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 pt-3">
                        <div class="d-flex align-items-center gap-2">
                            <label for="perPageSelect" class="mb-0 small text-muted">Tampilkan</label>
                            <select id="perPageSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="25" <?= ($perPageDetails ?? 25) == 25 ? 'selected' : '' ?>>25</option>
                                <option value="50" <?= ($perPageDetails ?? 25) == 50 ? 'selected' : '' ?>>50</option>
                                <option value="100" <?= ($perPageDetails ?? 25) == 100 ? 'selected' : '' ?>>100</option>
                            </select>
                            <span class="small text-muted">data per halaman</span>
                        </div>
                        <div class="small text-muted">
                            Menampilkan <?= ($offsetDetails ?? 0) + 1 ?>–<?= min(($offsetDetails ?? 0) + ($perPageDetails ?? 25), $totalDetails ?? count($details)) ?>
                            dari <?= number_format($totalDetails ?? count($details)) ?> data
                        </div>
                    </div>
                    <div class="table-container">
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
                                            <tr id="row-<?= $detail['ID'] ?>">
                                                <td><?= $no++ ?></td>
                                                <td>
                                                    <strong><?= $detail['NomorBarcode'] ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?= $detail['CallNumber'] ?></small>
                                                </td>
                                                <td>
                                                    <strong><?= $detail['Title'] ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?= $detail['Publisher'] ?></small>
                                                </td>
                                                <td><?= $detail['Author'] ?></td>
                                                <td>
                                                    <select class="form-select form-select-sm select-lokasi"
                                                            data-detail-id="<?= $detail['ID'] ?>"
                                                            data-field="current_location_id">
                                                        <?php foreach ($locations as $loc): ?>
                                                            <option value="<?= $loc->ID ?>"
                                                                <?= $loc->ID == $detail['CurrentLocationID'] ? 'selected' : '' ?>>
                                                                <?= esc($loc->Name) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <?php if ($detail['PrevLocationID'] != $detail['CurrentLocationID']): ?>
                                                        <small class="text-muted d-block mt-1">Dari: <?= $detail['PrevLocationName'] ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm select-status"
                                                            data-detail-id="<?= $detail['ID'] ?>"
                                                            data-field="current_status_id">
                                                        <?php foreach ($statuses as $st): ?>
                                                            <option value="<?= $st->ID ?>"
                                                                <?= $st->ID == $detail['CurrentStatusID'] ? 'selected' : '' ?>>
                                                                <?= esc($st->Name) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <?php if ($detail['PrevStatusID'] != $detail['CurrentStatusID']): ?>
                                                        <small class="text-muted d-block mt-1">Dari: <?= $detail['PrevStatusName'] ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm select-rule"
                                                            data-detail-id="<?= $detail['ID'] ?>"
                                                            data-field="current_collection_rule_id">
                                                        <?php foreach ($rules as $rule): ?>
                                                            <option value="<?= $rule->ID ?>"
                                                                <?= $rule->ID == $detail['CurrentCollectionRuleID'] ? 'selected' : '' ?>>
                                                                <?= esc($rule->Name) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <?php if ($detail['PrevCollectionRuleID'] != $detail['CurrentCollectionRuleID']): ?>
                                                        <small class="text-muted d-block mt-1">Dari: <?= $detail['PrevRuleName'] ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?= date('d/m/Y H:i', strtotime($detail['CreateDate'])) ?>
                                                    <br>
                                                    <small class="text-muted">oleh User <?= $detail['CreateBy'] ?></small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <br>
                                                Belum ada koleksi yang di-scan
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php if (!empty($details)): ?>
                    <div class="card-footer d-flex justify-content-center">
                        <?= $detailsPager ?? '' ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Location & Status Summary Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Ringkasan Berdasarkan Lokasi & Status</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <?php
                                    if (!empty($locationSummary)) {
                                        $statusHeaders = array_keys($locationSummary[0]);
                                        foreach ($statusHeaders as $header) {
                                            echo "<th>{$header}</th>";
                                        }
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($locationSummary)): ?>
                                    <?php foreach ($locationSummary as $row): ?>
                                        <tr>
                                            <?php foreach ($row as $value): ?>
                                                <td><?= $value ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="100%" class="text-center py-4">Data ringkasan tidak tersedia.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Collections Not in Stockopname -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle"></i>
                        Koleksi Belum Di-Stockopname
                        <span class="badge bg-danger"><?= $totalNotInStockopname ?></span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-container">
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
                                        <th>Aksi</th>
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
                                                    <strong><?= $collection['NomorBarcode'] ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?= $collection['CallNumber'] ?></small>
                                                </td>
                                                <td>
                                                    <strong><?= $collection['Title'] ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?= $collection['Publisher'] ?></small>
                                                </td>
                                                <td><?= $collection['Author'] ?></td>
                                                <td>
                                                    <select class="form-select form-select-sm select-lokasi-not-in"
                                                            data-collection-id="<?= $collection['id'] ?>"
                                                            data-field="current_location_id">
                                                        <?php foreach ($locations as $loc): ?>
                                                            <option value="<?= $loc->ID ?>"
                                                                <?= $loc->ID == $collection['CurrentLocationID'] ? 'selected' : '' ?>>
                                                                <?= esc($loc->Name) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm select-status-not-in"
                                                            data-collection-id="<?= $collection['id'] ?>"
                                                            data-field="current_status_id">
                                                        <?php foreach ($statuses as $st): ?>
                                                            <option value="<?= $st->ID ?>"
                                                                <?= $st->ID == $collection['CurrentStatusID'] ? 'selected' : '' ?>>
                                                                <?= esc($st->Name) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm select-rule-not-in"
                                                            data-collection-id="<?= $collection['id'] ?>"
                                                            data-field="current_collection_rule_id">
                                                        <?php foreach ($rules as $rule): ?>
                                                            <option value="<?= $rule->ID ?>"
                                                                <?= $rule->ID == $collection['CurrentCollectionRuleID'] ? 'selected' : '' ?>>
                                                                <?= esc($rule->Name) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-success" onclick="quickAdd('<?= $collection['NomorBarcode'] ?>')">
                                                        <i class="fas fa-plus"></i> Scan
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                                <br>
                                                <strong>Semua koleksi sudah di-stockopname!</strong>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-center">
                    <?= $notInPager ?? '' ?>
                </div>
            </div>
        </div>
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