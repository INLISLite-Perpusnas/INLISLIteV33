<?php
$request = service('request');
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
.preview-container {
    margin-top: 20px;
    border-top: 1px solid #dee2e6;
    padding-top: 20px;
}
.preview-table {
    max-height: 500px;
    overflow-y: auto;
}
.filter-section {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
    background-color: #f8f9fa;
}
.filter-section h6 {
    color: #495057;
    font-weight: 600;
    margin-bottom: 15px;
}
.columns-section {
    background-color: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}
.filters-container {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}
#filterForm .period-selector,
#filterForm .period-filter {
    grid-column: 1 / -1;
    margin-bottom: 0;
}
#filterForm .filters-container > .filter-section {
    min-width: 0;
    margin-bottom: 0;
}
#filterForm .period-filter .row { row-gap: 15px; }
#filterForm .criteria-section { grid-column: 1 / -1; }
#filterForm .criteria-heading { display: flex; align-items: center; gap: 12px; margin-bottom: 15px; }
#filterForm .criteria-heading h6 { margin: 0; }
#filterForm .criterion-row { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) auto; gap: 15px; align-items: start; margin-top: 12px; }
#filterForm .criterion-control { min-width: 0; }
#filterForm .criterion-control .select2-selection--single { min-height: 38px; border-color: #ced4da; }
#filterForm .criterion-control .select2-selection__rendered { line-height: 36px; }
#filterForm .criterion-control .select2-selection__arrow { height: 36px; }
@media (max-width: 767px) {
    .filters-container {
        grid-template-columns: 1fr;
    }
    #filterForm .criterion-row { grid-template-columns: minmax(0, 1fr) auto; }
    #filterForm .criterion-control:first-child { grid-column: 1 / -1; }
}
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-note2 icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Laporan Peminjaman Buku
                    <div class="page-title-subheading">Export Data Peminjaman dengan Multiple Filter</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('auth') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Laporan</a></li>
                        <li class="breadcrumb-item" aria-current="page">Laporan Peminjaman</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5><strong>Export Data Peminjaman Buku</strong></h5>

        </div>
        <div class="card-body">
            <?php if (session('errors')) : ?>
                <div class="alert alert-danger">
                    <?php foreach (session('errors') as $error) : ?>
                        <?= $error ?><br>
                    <?php endforeach ?>
                </div>
            <?php endif ?>

            <?php if (session('error')) : ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?= session('error') ?>
                </div>
            <?php endif ?>

            <form id="filterForm">
                <?= csrf_field() ?>
                
                <!-- Columns Selection -->
                <div class="columns-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0"><i class="fas fa-columns"></i> Pilih Kolom yang akan diekspor</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="select_all_columns">
                            <label class="form-check-label font-weight-bold text-primary" for="select_all_columns">
                                <i class="fas fa-check-double"></i> Pilih Semua Kolom
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="nama_anggota" id="nama_anggota" checked>
                                <label class="form-check-label" for="nama_anggota">
                                    Nama Anggota
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="MemberNo" id="MemberNo">
                                <label class="form-check-label" for="MemberNo">
                                    Nomor Anggota
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="NomorBarcode" id="NomorBarcode" checked>
                                <label class="form-check-label" for="NomorBarcode">
                                    Nomor Barcode
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="judul_buku" id="judul_buku" checked>
                                <label class="form-check-label" for="judul_buku">
                                    Judul Buku
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="penerbit" id="penerbit">
                                <label class="form-check-label" for="penerbit">Penerbit</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="kelas_ddc" id="kelas_ddc">
                                <label class="form-check-label" for="kelas_ddc">Kelas DDC</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="subjek" id="subjek">
                                <label class="form-check-label" for="subjek">Subjek</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="tanggal_peminjaman" id="tanggal_peminjaman" checked>
                                <label class="form-check-label" for="tanggal_peminjaman">
                                    Tanggal Peminjaman
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo">
                                <label class="form-check-label" for="tanggal_jatuh_tempo">
                                    Tanggal Jatuh Tempo
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="tanggal_pengembalian" id="tanggal_pengembalian" checked>
                                <label class="form-check-label" for="tanggal_pengembalian">
                                    Tanggal Pengembalian
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="petugas_peminjaman" id="petugas_peminjaman">
                                <label class="form-check-label" for="petugas_peminjaman">
                                    Petugas Peminjaman
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="petugas_pengembalian" id="petugas_pengembalian" checked>
                                <label class="form-check-label" for="petugas_pengembalian">
                                    Petugas Pengembalian
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="jenis_kelamin" id="jenis_kelamin" checked>
                                <label class="form-check-label" for="jenis_kelamin">
                                    Jenis Kelamin
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="status_peminjaman" id="status_peminjaman">
                                <label class="form-check-label" for="status_peminjaman">
                                    Status Peminjaman
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Multiple Filters Container -->
                <div class="filters-container">
                    <div class="form-group period-selector">
                        <label for="filter_type"><b>Filter Berdasarkan</b></label>
                        <select class="form-control" name="filter_type" id="filter_type">
                            <option value="date">Tanggal</option>
                            <option value="month">Bulan</option>
                            <option value="year">Tahun</option>
                        </select>
                    </div>
                    <!-- Filter Tanggal Kunjungan -->
                    <div id="date_filter" class="filter-section period-filter">
                        <h6 class="mb-3"><i class="fas fa-calendar-alt" aria-hidden="true"></i> Filter Berdasarkan Tanggal</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="start_date">Tanggal Mulai</label>
                                <input type="date" id="start_date" name="start_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="end_date">Tanggal Akhir</label>
                                <input type="date" id="end_date" name="end_date" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Filter Bulan & Tahun Kunjungan -->
                    <div id="month_filter" class="filter-section period-filter" style="display: none;">
                        <h6 class="mb-3"><i class="fas fa-calendar-alt" aria-hidden="true"></i> Filter Berdasarkan Bulan</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="month">Bulan</label>
                                <select name="month" id="month" class="form-control" disabled>
                                    <?php for ($i = 1; $i <= 12; $i++) : ?>
                                        <option value="<?= $i ?>"><?= date('F', mktime(0, 0, 0, $i, 1)) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="month_year">Tahun</label>
                                <select name="year" id="month_year" class="form-control" disabled>
                                    <?php for ($i = date('Y'); $i >= 2020; $i--) : ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Tahun Kunjungan Saja -->
                    <div id="year_filter" class="filter-section period-filter" style="display: none;">
                        <h6 class="mb-3"><i class="fas fa-calendar-alt" aria-hidden="true"></i> Filter Berdasarkan Tahun</h6>
                        <label for="year_only">Tahun</label>
                        <select name="year" id="year_only" class="form-control" disabled>
                            <?php for ($i = date('Y'); $i >= 2020; $i--) : ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor ?>
                        </select>
                    </div>

                    <?php foreach (['member' => 'Kriteria Anggota Peminjam', 'catalog' => 'Kriteria Koleksi Dipinjam'] as $group => $label): ?>
                    <div class="filter-section criteria-section" aria-labelledby="<?= $group ?>_criteria_heading">
                        <div class="criteria-heading">
                            <h6 id="<?= $group ?>_criteria_heading"><?= esc($label) ?></h6>
                            <button type="button" class="btn btn-success" data-add-criterion="<?= $group ?>" aria-label="Tambah <?= esc($label, 'attr') ?>" title="Tambah kriteria">
                                <i class="fas fa-plus-circle" aria-hidden="true"></i>
                            </button>
                        </div>
                        <p class="text-muted mb-0">Pilih kriteria dan nilainya. Semua kriteria yang diisi harus terpenuhi.</p>
                        <div id="<?= $group ?>_criteria_rows"></div>
                    </div>
                    <?php endforeach; ?>

                    <div class="filter-section">
                        <h6><i class="fas fa-trophy" aria-hidden="true"></i> Anggota Paling Banyak Meminjam</h6>
                        <label for="top_borrowers">Jumlah anggota dalam peringkat</label>
                        <select name="top_borrowers" id="top_borrowers" class="form-control">
                            <option value="">-- Semua Anggota --</option>
                            <option value="5">5 Anggota Teratas</option>
                            <option value="10">10 Anggota Teratas</option>
                            <option value="25">25 Anggota Teratas</option>
                            <option value="50">50 Anggota Teratas</option>
                        </select>
                        <small class="form-text text-muted">Dihitung dari jumlah eksemplar yang dipinjam sesuai periode dan kriteria aktif. Membatasi jumlah anggota, bukan jumlah baris peminjaman. Kolom peringkat dan jumlah buku ditambahkan otomatis.</small>
                    </div>

                    <!-- Filter Status Peminjaman -->
                    <div class="filter-section">
                        <h6><i class="fas fa-info-circle"></i> Filter Berdasarkan Status Peminjaman</h6>
                        <label>Status Peminjaman</label>
                        <select name="loan_status" id="loan_status" class="form-control">
                            <option value="">-- Semua Status --</option>
                            <option value="Loan">Dipinjam</option>
                            <option value="Return">Dikembalikan</option>
                        </select>
                    </div>

                   

                </div>

                <!-- Export Button -->
                <div class="d-flex justify-content-center flex-nowrap mb-4 overflow-auto pb-2">
                    <button type="button" class="btn btn-primary btn-lg px-4 mx-1" id="btnPreview">
                        <i class="fas fa-eye"></i> Preview Data (100 Baris Pertama)
                    </button>
                    <button type="button" class="btn btn-success btn-lg px-4 mx-1" id="btnExport">
                        <i class="fas fa-file-excel"></i> Export ke Excel (Semua Data)
                    </button>
                    <button type="button" class="btn btn-danger btn-lg px-4 mx-1" id="btnExportPdf">
                        <i class="fas fa-file-pdf"></i> Export ke PDF (Semua Data)
                    </button>
                    <button type="button" class="btn btn-secondary btn-lg px-4 mx-1" onclick="clearAllFilters()">
                        <i class="fas fa-eraser"></i> Clear All Filters
                    </button>
                </div>
            </form>

            <!-- Preview Section -->
            <div class="preview-container" id="previewSection" style="display: none;">
                <h5><i class="fas fa-eye"></i> Preview Data (100 Baris Pertama)</h5>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Menampilkan <strong>100 baris pertama</strong> dari data yang akan diexport. 
                    Jika sudah sesuai, klik tombol Export ke Excel untuk mengunduh semua data.
                    <div class="mt-2">
                        <strong>Perhatian:</strong> Export data dalam jumlah besar membutuhkan waktu lebih lama.
                    </div>
                </div>
                <div class="preview-table" id="preview-table">
                    <div class="text-center">
                        <p>Pilih kolom untuk melihat preview data</p>
                    </div>
                </div>
                
                
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script src="<?= base_url('assets/js/circulation-criteria.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/circulation-criteria.js') ?>"></script>
<script>
$(document).ready(function() {
    let currentColumns = [];
    CirculationCriteria.init({
        url: <?= json_encode(base_url('laporan-sirkulasi')) ?>,
        labels: <?= json_encode($criteriaLabels, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
    });

    function updatePeriodFilter() {
        $('#filterForm .period-filter').hide().find('input, select').prop('disabled', true);
        $('#' + $('#filter_type').val() + '_filter').show().find('input, select').prop('disabled', false);
    }
    $('#filter_type').on('change', updatePeriodFilter);
    updatePeriodFilter();

    function periodFilters() {
        const type = $('#filter_type').val();
        const filters = { filter_type: type, top_borrowers: $('#top_borrowers').val(), ...CirculationCriteria.values() };
        if (type === 'date') {
            filters.start_date = $('#start_date').val();
            filters.end_date = $('#end_date').val();
        } else if (type === 'month') {
            filters.month = $('#month').val();
            filters.year = $('#month_year').val();
        } else if (type === 'year') {
            filters.year = $('#year_only').val();
        }
        return filters;
    }

    function appendPeriodFilters(form) {
        Object.entries(periodFilters()).forEach(function ([name, value]) {
            form.append($('<input>', { type: 'hidden', name: name, value: value }));
        });
    }
    
    // Handle Select All Columns
    $('#select_all_columns').change(function() {
        const isChecked = $(this).is(':checked');
        $('.column-checkbox').prop('checked', isChecked);
        updateSelectAllStatus();
    });

    // Handle individual column checkboxes
    $('.column-checkbox').change(function() {
        updateSelectAllStatus();
    });

    // Function to update select all checkbox status
    function updateSelectAllStatus() {
        const totalColumns = $('.column-checkbox').length;
        const checkedColumns = $('.column-checkbox:checked').length;
        
        if (checkedColumns === 0) {
            $('#select_all_columns').prop('indeterminate', false).prop('checked', false);
        } else if (checkedColumns === totalColumns) {
            $('#select_all_columns').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#select_all_columns').prop('indeterminate', true);
        }
    }

    // Preview Data
    $('#btnPreview').click(function() {
        // Ambil kolom yang dipilih
        const selectedColumns = [];
        $('.column-checkbox:checked').each(function() {
            selectedColumns.push($(this).val());
        });
        
        if (selectedColumns.length === 0) {
            alert('Pilih minimal satu kolom untuk ditampilkan!');
            return;
        }
        
        // Show loading
        $(this).html('<i class="fas fa-spinner fa-spin"></i> Loading...').prop('disabled', true);
        $('#preview-table').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p>Memuat preview data...</p></div>');
        
        // AJAX Request
        $.ajax({
            url: '<?= base_url('laporan-sirkulasi/preview') ?>',
            type: 'POST',
            data: {
                columns: selectedColumns,
                ...periodFilters(),
                loan_status: $('#loan_status').val(),
                member_name: $('#member_name').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    currentColumns = response.columns;
                    displayPreview(response.data, response.columns, response.total);
                    $('#previewSection').show();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat mengambil data');
                $('#preview-table').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan saat memuat preview data. Silakan coba lagi.</div>');
            },
            complete: function() {
                $('#btnPreview').html('<i class="fas fa-eye"></i> Preview Data (100 Baris Pertama)').prop('disabled', false);
            }
        });
    });
    
    // Export Excel
    $('#btnExport').click(function() {
        // Ambil kolom yang dipilih
        const selectedColumns = [];
        $('.column-checkbox:checked').each(function() {
            selectedColumns.push($(this).val());
        });
        
        if (selectedColumns.length === 0) {
            alert('Pilih minimal satu kolom untuk diexport!');
            return;
        }
        
        // Show loading
        $(this).html('<i class="fas fa-spinner fa-spin"></i> Mengexport...').prop('disabled', true);
        
        // Buat form untuk submit
        const form = $('<form>', {
            'method': 'POST',
            'action': '<?= base_url('laporan-sirkulasi/export') ?>'
        });
        
        // Tambahkan CSRF token
        form.append($('<input>', {
            'type': 'hidden',
            'name': '<?= csrf_token() ?>',
            'value': '<?= csrf_hash() ?>'
        }));
        
        // Tambahkan kolom
        selectedColumns.forEach(function(col) {
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'columns[]',
                'value': col
            }));
        });
        
        // Tambahkan filter
        appendPeriodFilters(form);

        form.append($('<input>', {
            'type': 'hidden',
            'name': 'loan_status',
            'value': $('#loan_status').val()
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'member_name',
            'value': $('#member_name').val()
        }));
        
        
        // Submit form
        $('body').append(form);
        form.submit();
        form.remove();
        
        // Reset button
        setTimeout(function() {
            $('#btnExport').html('<i class="fas fa-file-excel"></i> Export ke Excel (Semua Data)').prop('disabled', false);
        }, 2000);
    });

    // Export PDF
    $('#btnExportPdf').click(function() {
        const selectedColumns = [];
        $('.column-checkbox:checked').each(function() {
            selectedColumns.push($(this).val());
        });

        if (selectedColumns.length === 0) {
            alert('Pilih minimal satu kolom untuk diexport!');
            return;
        }

        $(this).html('<i class="fas fa-spinner fa-spin"></i> Mengexport...').prop('disabled', true);

        const form = $('<form>', {
            'method': 'POST',
            'action': '<?= base_url('laporan-sirkulasi/export_pdf') ?>'
        });

        form.append($('<input>', {
            'type': 'hidden',
            'name': '<?= csrf_token() ?>',
            'value': '<?= csrf_hash() ?>'
        }));

        selectedColumns.forEach(function(col) {
            form.append($('<input>', { 'type': 'hidden', 'name': 'columns[]', 'value': col }));
        });

        appendPeriodFilters(form);
        form.append($('<input>', { 'type': 'hidden', 'name': 'loan_status', 'value': $('#loan_status').val() }));
        form.append($('<input>', { 'type': 'hidden', 'name': 'member_name', 'value': $('#member_name').val() }));

        $('body').append(form);
        form.submit();
        form.remove();

        setTimeout(function() {
            $('#btnExportPdf').html('<i class="fas fa-file-pdf"></i> Export ke PDF (Semua Data)').prop('disabled', false);
        }, 2000);
    });
    
    // Function to display preview
    function displayPreview(data, columns, total) {
        // Mapping nama kolom
        const columnNames = {
            'peringkat': 'Peringkat',
            'jumlah_peminjaman': 'Jumlah Buku Dipinjam',
            'nama_anggota': 'Nama Anggota',
            'MemberNo': 'Nomor Anggota',
            'NomorBarcode': 'Nomor Barcode',
            'judul_buku': 'Judul Buku',
            'penerbit': 'Penerbit',
            'kelas_ddc': 'Kelas DDC',
            'subjek': 'Subjek',
            'tanggal_peminjaman': 'Tanggal Peminjaman',
            'tanggal_jatuh_tempo': 'Tanggal Jatuh Tempo',
            'tanggal_pengembalian': 'Tanggal Pengembalian',
            'petugas_peminjaman': 'Petugas Peminjaman',
            'petugas_pengembalian': 'Petugas Pengembalian',
            'jenis_kelamin': 'Jenis Kelamin',
            'status_peminjaman': 'Status Peminjaman'
        };
        
        // Build table
        let tableHtml = '<div class="table-responsive"><table class="table table-striped table-hover table-bordered">';
        tableHtml += '<thead class="thead-dark"><tr>';
        
        // Header
        columns.forEach(function(col) {
            tableHtml += '<th>' + (columnNames[col] || col) + '</th>';
        });
        tableHtml += '</tr></thead><tbody>';
        
        // Body
        if (data.length === 0) {
            tableHtml += '<tr><td colspan="' + columns.length + '" class="text-center">Tidak ada data</td></tr>';
        } else {
            data.forEach(function(row) {
                tableHtml += '<tr>';
                columns.forEach(function(col) {
                    let value = row[col] || '-';
                    
                    // Format tanggal
                    if (col.includes('tanggal_') && value !== '-' && value !== null) {
                        const date = new Date(value);
                        if (!isNaN(date.getTime())) {
                            value = date.toLocaleString('id-ID', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        }
                    }
                    
                    tableHtml += '<td>' + value + '</td>';
                });
                tableHtml += '</tr>';
            });
        }
        
        tableHtml += '</tbody></table></div>';
        tableHtml += '<p class="text-muted mt-2"><strong>Total data preview:</strong> ' + total + ' baris (dari 100 baris pertama)</p>';
        
        $('#preview-table').html(tableHtml);
        
        // Scroll to preview
        $('html, body').animate({
            scrollTop: $('#previewSection').offset().top - 100
        }, 500);
    }
    
    // Initial setup
    updateSelectAllStatus();
});

// Function to clear all filters
function clearAllFilters() {
    if (confirm('Apakah Anda yakin ingin menghapus semua filter?')) {
        // Clear all input fields
        $('#filterForm input[type="date"], #filterForm input[type="text"]').val('');
        $('#filterForm select').prop('selectedIndex', 0);
        CirculationCriteria.reset();
        $('#filter_type').trigger('change');
    }
}
</script>
<?= $this->endSection('script'); ?>
