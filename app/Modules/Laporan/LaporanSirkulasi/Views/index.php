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
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}
@media (max-width: 768px) {
    .filters-container {
        grid-template-columns: 1fr;
    }
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
            <p class="text-muted mb-0">Pilih kolom dan filter yang diinginkan. Anda dapat mengkombinasikan beberapa filter sekaligus.</p>
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
                    <!-- Filter Tanggal Peminjaman -->
                    <div class="filter-section">
                        <h6><i class="fas fa-calendar-alt"></i> Filter Berdasarkan Tanggal Peminjaman</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="start_date" id="start_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Tanggal Akhir</label>
                                <input type="date" name="end_date" id="end_date" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Filter Status Peminjaman -->
                    <div class="filter-section">
                        <h6><i class="fas fa-info-circle"></i> Filter Berdasarkan Status Peminjaman</h6>
                        <label>Status Peminjaman</label>
                        <select name="loan_status" id="loan_status" class="form-control">
                            <option value="">-- Semua Status --</option>
                            <option value="">Dipinjam</option>
                            <option value="Return">Dikembalikan</option>
                        </select>
                    </div>

                    <!-- Filter Nama Anggota -->
                    <div class="filter-section">
                        <h6><i class="fas fa-user"></i> Filter Berdasarkan Nama Anggota</h6>
                        <label>Nama Anggota</label>
                        <input type="text" name="member_name" id="member_name" class="form-control" placeholder="Masukkan nama anggota...">
                    </div>

                    <!-- Filter Judul Buku -->
                    <div class="filter-section">
                        <h6><i class="fas fa-book"></i> Filter Berdasarkan Judul Buku</h6>
                        <label>Judul Buku</label>
                        <input type="text" name="book_title" id="book_title" class="form-control" placeholder="Masukkan judul buku...">
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
<script>
$(document).ready(function() {
    let currentColumns = [];
    
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
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                loan_status: $('#loan_status').val(),
                member_name: $('#member_name').val(),
                book_title: $('#book_title').val()
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
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'start_date',
            'value': $('#start_date').val()
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'end_date',
            'value': $('#end_date').val()
        }));
        
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
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'book_title',
            'value': $('#book_title').val()
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

        form.append($('<input>', { 'type': 'hidden', 'name': 'start_date',  'value': $('#start_date').val() }));
        form.append($('<input>', { 'type': 'hidden', 'name': 'end_date',    'value': $('#end_date').val() }));
        form.append($('<input>', { 'type': 'hidden', 'name': 'loan_status', 'value': $('#loan_status').val() }));
        form.append($('<input>', { 'type': 'hidden', 'name': 'member_name', 'value': $('#member_name').val() }));
        form.append($('<input>', { 'type': 'hidden', 'name': 'book_title',  'value': $('#book_title').val() }));

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
            'nama_anggota': 'Nama Anggota',
            'MemberNo': 'Nomor Anggota',
            'NomorBarcode': 'Nomor Barcode',
            'judul_buku': 'Judul Buku',
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
        $('input[type="date"], input[type="text"]').val('');
        $('select').prop('selectedIndex', 0);
    }
}
</script>
<?= $this->endSection('script'); ?>