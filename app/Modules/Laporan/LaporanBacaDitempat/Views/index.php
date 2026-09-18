<?php
$request = service('request');
$date_from = $request->getGet('date_from') ?? '';
$date_to = $request->getGet('date_to') ?? '';
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
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 15px;
    background-color: #f8f9fa;
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
#report-form .period-selector,
#report-form .period-filter {
    grid-column: 1 / -1;
    margin-bottom: 0;
}
#report-form .filters-container > .filter-section {
    min-width: 0;
    margin-bottom: 0;
}
#report-form .period-filter .row {
    row-gap: 15px;
}
@media (max-width: 767px) {
    #report-form .filters-container {
        grid-template-columns: minmax(0, 1fr);
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
                    <i class="pe-7s-graph2 icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Laporan Baca Ditempat
                    <div class="page-title-subheading">Daftar Semua Baca Ditempat</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('auth') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Laporan</a></li>
                        <li class="breadcrumb-item" aria-current="page">Laporan Baca Ditempat </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5><strong>Export Data Baca Ditempat</strong></h5>
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

            <form id="report-form" action="<?= base_url('laporan-baca-ditempat/export') ?>" method="post">
                <?= csrf_field() ?>
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
                    <div class="form-group mb-3">
                        <label><b>Pilih Kolom yang akan diekspor</b></label>
                        <div class="row">
                            <?php foreach ($columns as $key => $label) : ?>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="<?= $key ?>" id="<?= $key ?>">
                                        <label class="form-check-label" for="<?= $key ?>">
                                            <?= $label ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach ?>
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

                    <!-- Filter Jenis Anggota -->
                    <div class="filter-section">
                        <h6><i class="fas fa-user-tag"></i> Filter Berdasarkan Jenis Anggota</h6>
                        <label>Jenis Anggota</label>
                        <select name="member_type_id" class="form-control">
                            <option value="">-- Semua Jenis Anggota --</option>
                            <?php if (isset($memberTypeOptions)) : ?>
                                <?php foreach ($memberTypeOptions as $memberType) : ?>
                                    <option value="<?= $memberType->id ?>"><?= esc($memberType->jenisanggota) ?></option>
                                <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </div>


                    <!-- Filter Lokasi Perpustakaan -->
                    <div class="filter-section">
                        <h6><i class="fas fa-map"></i> Filter Berdasarkan Lokasi Perpustakaan</h6>
                        <label>Lokasi Perpustakaan</label>
                        <select name="location_library_id" class="form-control">
                            <option value="">-- Semua Lokasi Perpustakaan --</option>
                            <?php if (isset($locationOptions)) : ?>
                                <?php foreach ($locationOptions as $location) : ?>
                                    <option value="<?= $location->code ?>"><?= esc($location->name) ?></option>
                                <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </div>

                    <!-- Filter Ruang Perpustakaan -->
                    <div class="filter-section">
                        <h6><i class="fas fa-door-open"></i> Filter Berdasarkan Lokasi Ruang</h6>
                        <label>Lokasi Ruang</label>
                        <select name="location_library_id" class="form-control">
                            <option value="">-- Semua Lokasi Ruang --</option>
                            <?php if (isset($roomOptions)) : ?>
                                <?php foreach ($roomOptions as $room) : ?>
                                    <option value="<?= $room->code ?>"><?= esc($room->name) ?></option>
                                <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </div>

                    <!-- Filter No Induk -->
                    <div class="filter-section">
                        <h6><i class="fas fas fa-id-card"></i> Filter Berdasarkan Nomor Induk</h6>
                        <label>Nomor Induk</label>
                        <input type="text" name="noinduk" class="form-control" placeholder="Masukkan no induk...">
                    </div>

                    <!-- Filter Penerbit -->
                    <div class="filter-section">
                        <h6><i class="fas fa-book"></i> Filter Berdasarkan Penerbit</h6>
                        <label for="penerbit">Penerbit</label>
                        <select name="penerbit" id="penerbit" class="form-control" style="width: 100%;">
                            <option value="">-- Semua Penerbit --</option>
                            <?php foreach ($publisherOptions ?? [] as $publisher) : ?>
                                <option value="<?= esc($publisher->Publisher, 'attr') ?>"><?= esc($publisher->Publisher) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    
                </div>


              <div class="text-center mb-4">
                    <button type="submit" class="btn btn-success btn-lg px-5" id="exportBtn" onclick="setExportAction('excel')">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                    <button type="submit" class="btn btn-danger btn-lg px-5 ml-2" id="exportPdfBtn" onclick="setExportAction('pdf')">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button type="button" class="btn btn-secondary btn-lg px-5 ml-2" onclick="clearAllFilters()">
                        <i class="fas fa-eraser"></i> Clear All Filters
                    </button>
                </div>

            </form>

            <!-- Preview Section -->
            <div class="preview-container">
                <h5><i class="fas fa-eye"></i> Preview Data (20 Baris Pertama)</h5>
                 <div>
                            <strong>Perhatian:</strong> Export data dalam jumlah besar membutuhkan waktu lebih lama.<br>
                            <small>Maksimum export: <strong>50,000 records</strong>. Gunakan filter untuk mengurangi jumlah data jika diperlukan.</small>
                        </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Preview akan otomatis terupdate setiap kali Anda mengubah pilihan kolom atau filter.
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
    $('#penerbit').select2({
        placeholder: '-- Semua Penerbit --',
        allowClear: true,
        width: '100%'
    });

    function updatePeriodFilter() {
        const activeSection = '#' + $('#filter_type').val() + '_filter';
        $('#report-form .period-filter').hide().find('input, select').prop('disabled', true);
        $(activeSection).show().find('input, select').prop('disabled', false);
    }
    $('#filter_type').on('change', updatePeriodFilter);
    updatePeriodFilter();

    // Function to update preview table
    function updatePreview() {
        const selectedColumns = [];
        $('input[name="columns[]"]:checked').each(function() {
            selectedColumns.push($(this).val());
        });

        // FormData mengabaikan input nonaktif, sama seperti submit Excel/PDF.
        const formData = new FormData(document.getElementById('report-form'));
        formData.delete('columns[]');
        formData.append('columns', JSON.stringify(selectedColumns));

        // Show loading indicator
        $('#preview-table').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Memuat preview data...</p></div>');

        // Make AJAX call to get preview data
        $.ajax({
            url: '<?= base_url('laporan-baca-ditempat/preview') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#preview-table').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching preview:', error);
            }
        });
    }

     // Handle Select All Columns
    $('#select_all_columns').change(function() {
        const isChecked = $(this).is(':checked');
        $('.column-checkbox').prop('checked', isChecked);
        updatePreview();
    });

    
    // Handle individual column checkboxes
    $('.column-checkbox').change(function() {
        updateSelectAllStatus();
        updatePreview();
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

  // Event listeners for filter inputs
    $('input[type="date"], input[type="text"], input[type="email"], select').on('change keyup', debounce(updatePreview, 500));

    // Initial setup
    updateSelectAllStatus();
    updatePreview();

    // Debounce function to limit API calls
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});

// Switch form action between Excel and PDF export
function setExportAction(type) {
    var form = document.querySelector('form[action*="laporan-baca-ditempat"]');
    if (type === 'pdf') {
        form.action = '<?= base_url('laporan-baca-ditempat/export_pdf') ?>';
    } else {
        form.action = '<?= base_url('laporan-baca-ditempat/export') ?>';
    }
}

// Function to clear all filters
function clearAllFilters() {
    if (confirm('Apakah Anda yakin ingin menghapus semua filter?')) {
        // Clear all input fields
        $('#report-form input[type="date"], #report-form input[type="text"], #report-form input[type="email"]').val('');
        $('#report-form select').prop('selectedIndex', 0);
        $('#penerbit').val('').trigger('change.select2');
        // Sinkronkan panel aktif dan jalankan pembaruan preview melalui event.
        $('#filter_type').trigger('change');
    }
}
</script>
<?= $this->endSection('script'); ?>
