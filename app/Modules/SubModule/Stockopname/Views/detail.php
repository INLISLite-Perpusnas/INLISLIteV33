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

    #detailTable_wrapper {
        padding: 10px;
    }

    #detailTable_wrapper .dataTables_length,
    #detailTable_wrapper .dataTables_filter {
        margin-bottom: 4px;
    }

    #detailTable_wrapper .dataTables_length select {
        margin: 0 4px;
    }

    #detailTable_wrapper .row:first-child {
        margin-bottom: 4px !important;
    }

    #detailTable_wrapper .row:last-child {
        margin-top: 4px !important;
    }

    .table th,
    #detailTable thead tr th,
    #detailTable thead tr th div,
    #detailTable thead tr th span {
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
    .modal {
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
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Detail Stockopname</h5>
                </div>
                <div class="card-body p-0">
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
                                        <?php $no = 1;
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
                                                    <?= $detail['CurrentLocationName'] ?>
                                                    <?php if ($detail['PrevLocationID'] != $detail['CurrentLocationID']): ?>
                                                        <span class="change-indicator">PINDAH</span>
                                                        <br>
                                                        <small class="text-muted">Dari: <?= $detail['PrevLocationName'] ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info status-badge"><?= $detail['CurrentStatusName'] ?></span>
                                                    <?php if ($detail['PrevStatusID'] != $detail['CurrentStatusID']): ?>
                                                        <span class="change-indicator">GANTI</span>
                                                        <br>
                                                        <small class="text-muted">Dari: <?= $detail['PrevStatusName'] ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?= $detail['CurrentRuleName'] ?>
                                                    <?php if ($detail['PrevCollectionRuleID'] != $detail['CurrentCollectionRuleID']): ?>
                                                        <span class="change-indicator">GANTI</span>
                                                        <br>
                                                        <small class="text-muted">Dari: <?= $detail['PrevRuleName'] ?></small>
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
                                            <td colspan="9" class="text-center py-4">
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
                                    // Ambil header dari baris pertama data summary
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
                <div class="card-body">
                    <div class="not-scanned-list">
                        <?php if (!empty($collectionsNotInStockopname)): ?>
                            <?php foreach ($collectionsNotInStockopname as $collection): ?>
                                <div class="not-scanned-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <strong><?= $collection['NomorBarcode'] ?></strong>
                                        </div>
                                        <div class="col-md-6">
                                            <strong><?= $collection['Title'] ?></strong>
                                            <br>
                                            <small class="text-muted"><?= $collection['Author'] ?></small>
                                        </div>

                                    </div>
                                </div>
                            <?php endforeach; ?>

                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <br>
                                <strong>Semua koleksi sudah di-stockopname!</strong>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer">
                    <?= $notInPager ?? '' ?>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
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
        // Inisialisasi DataTables untuk tabel detail
        $('#detailTable').DataTable({
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Semua']],
            language: {
                search:           'Cari:',
                lengthMenu:       'Tampilkan _MENU_ data',
                info:             'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                infoEmpty:        'Tidak ada data',
                infoFiltered:     '(difilter dari _MAX_ total data)',
                zeroRecords:      'Data tidak ditemukan',
                paginate: {
                    first:    'Pertama',
                    last:     'Terakhir',
                    next:     'Berikutnya',
                    previous: 'Sebelumnya',
                },
            },
            order: [],
            columnDefs: [
                { orderable: false, targets: 0 },
            ],
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
        // Kecualikan klik pada kontrol DataTables dan elemen interaktif lainnya
        $(document).on('click', function(e) {
            const $target = $(e.target);
            const isDataTablesControl = $target.closest(
                '.dataTables_length, .dataTables_filter, .dataTables_paginate, .dataTables_info, select, input, button, a, .modal'
            ).length > 0;

            if (!isDataTablesControl && !$('#editModal').hasClass('show')) {
                setTimeout(() => $('#barcodeInput').focus(), 100);
            }
        });
    });

    // Scan barcode function
    function scanBarcode() {
        const barcode = $('#barcodeInput').val().trim();

        if (!barcode) {
            toastr.warning('Mohon masukkan nomor barcode');
            return;
        }

        // Show loading
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
                    toastr.success(response.message);
                    addNewRowToTable(response.data);
                    $('#barcodeInput').val('').focus();
                    updateSummary();
                } else {
                    toastr.error(response.message);
                    $('#barcodeInput').select();
                }
            },
            error: function(xhr, status, error) {
                toastr.error('Terjadi kesalahan saat memproses barcode');
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

    // Add new row to table
    function addNewRowToTable(data) {
        // Remove "no data" row if exists
        if ($('#detailTable tbody tr td[colspan="9"]').length > 0) {
            $('#detailTable tbody').empty();
        }

        const newRow = `
                <tr id="row-${data.ID}" class="table-success">
                    <td>${$('#detailTable tbody tr').length + 1}</td>
                    <td>
                        <strong>${data.NomorBarcode}</strong>
                        <br>
                        <small class="text-muted">${data.CallNumber || ''}</small>
                    </td>
                    <td>
                        <strong>${data.Title}</strong>
                        <br>
                        <small class="text-muted">${data.Publisher || ''}</small>
                    </td>
                    <td>${data.Author || ''}</td>
                    <td>${data.CurrentLocationName || ''}</td>
                    <td><span class="badge bg-info status-badge">${data.CurrentStatusName || ''}</span></td>
                    <td>${data.CurrentRuleName || ''}</td>
                    <td>
                        ${new Date().toLocaleDateString('id-ID')} ${new Date().toLocaleTimeString('id-ID')}
                        <br>
                        <small class="text-muted">oleh User ${data.CreateBy}</small>
                    </td>
                    <td>
                        <button class="btn btn-update btn-sm me-1" 
                                onclick="editDetail(${data.ID})"
                                title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-delete btn-sm" 
                                onclick="deleteDetail(${data.ID})"
                                title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

        $('#detailTable tbody').prepend(newRow);

        // Remove highlight after 3 seconds
        setTimeout(() => {
            $(`#row-${data.ID}`).removeClass('table-success');
        }, 3000);
    }

    // Edit detail function
    function editDetail(detailId) {
        currentEditingDetail = detailId;

        // Get detail data from table row
        const row = $(`#row-${detailId}`);
        const barcode = row.find('td:eq(1) strong').text();

        // Set form values
        $('#editDetailId').val(detailId);

        // Load collection info
        loadCollectionInfo(barcode);

        // Show modal
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

                    // Set current values
                    $('#editCurrentLocation').val(collection.location_id);
                    $('#editCurrentStatus').val(collection.status_id);
                    $('#editCurrentRule').val(collection.collection_rule_id);
                }
            },
            error: function() {
                toastr.error('Gagal memuat informasi koleksi');
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
                    toastr.success(response.message);
                    $('#editModal').modal('hide');
                    location.reload(); // Reload to show updated data
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Terjadi kesalahan saat memperbarui data');
            }
        });
    }

    // Delete detail function
    function deleteDetail(detailId) {
        if (confirm('Yakin ingin menghapus detail stockopname ini?')) {
            $.ajax({
                url: `<?= base_url('stockopname/deleteDetail') ?>/${detailId}`,
                type: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        $(`#row-${detailId}`).fadeOut(500, function() {
                            $(this).remove();
                            updateRowNumbers();
                            updateSummary();
                        });
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Terjadi kesalahan saat menghapus data');
                }
            });
        }
    }

    // Update row numbers after deletion
    function updateRowNumbers() {
        $('#detailTable tbody tr').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    }

    // Update summary (you might want to reload specific parts)
    function updateSummary() {
        // This could be implemented to update summary without full page reload
        // For now, we'll just note that summary should be updated
    }

    // Handle modal events
    $('#editModal').on('hidden.bs.modal', function() {
        currentEditingDetail = null;
        $('#barcodeInput').focus();
    });

    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        // ESC to focus on barcode input
        if (e.key === 'Escape' && !$('#editModal').hasClass('show')) {
            $('#barcodeInput').focus().select();
        }

        // F1 to show help (you can implement this)
        if (e.key === 'F1') {
            e.preventDefault();
            // Show help modal or tooltip
        }
    });

    // Auto-refresh not scanned list every 30 seconds
    setInterval(function() {
        // You could implement auto-refresh of the not scanned list here
        // For performance reasons, this is commented out by default
    }, 30000);

    // Print function (bonus)
    function printStockopname() {
        window.print();
    }

    // Show loading overlay function
    function showLoading() {
        $('body').append(`
                <div id="loadingOverlay" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.5);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                ">
                    <div class="spinner-border text-light" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `);
    }

    function hideLoading() {
        $('#loadingOverlay').remove();
    }
</script>
<?= $this->endSection('script'); ?>

<!-- Print Styles -->