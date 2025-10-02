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
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
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
                        <li class="active breadcrumb-item" aria-current="page">Laporan Baca Ditempat </li>
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

            <form action="<?= base_url('laporan-baca-ditempat/export') ?>" method="post">
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
                    <!-- Filter Tanggal Kunjungan -->
                    <div class="filter-section">
                        <h6><i class="fas fa-calendar-alt"></i> Filter Berdasarkan Tanggal Kunjungan</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Tanggal Mulai</label>
                                <input type="date" id="start_date" name="start_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Tanggal Akhir</label>
                                <input type="date" id="end_date" name="end_date" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Filter Bulan & Tahun Kunjungan -->
                    <div class="filter-section">
                        <h6><i class="fas fa-calendar-alt"></i> Filter Berdasarkan Bulan & Tahun Kunjungan</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Bulan</label>
                                <select name="month" class="form-control">
                                    <option value="">-- Pilih Bulan --</option>
                                    <?php for ($i = 1; $i <= 12; $i++) : ?>
                                        <option value="<?= $i ?>"><?= date('F', mktime(0, 0, 0, $i, 1)) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Tahun</label>
                                <select name="year" class="form-control">
                                    <option value="">-- Pilih Tahun --</option>
                                    <?php for ($i = date('Y'); $i >= 2020; $i--) : ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Tahun Kunjungan Saja -->
                    <div class="filter-section">
                        <h6><i class="fas fa-calendar"></i> Filter Berdasarkan Tahun Kunjungan Saja</h6>
                        <label>Tahun</label>
                        <select name="year_only" id="year_only" class="form-control">
                            <option value="">-- Pilih Tahun --</option>
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
                        <label>Penerbit</label>
                        <input type="text" name="penerbit" class="form-control" placeholder="Masukkan nama penerbit...">
                    </div>
                    
                </div>


              <div class="text-center mb-4">
                    <button type="submit" class="btn btn-success btn-lg px-5" id="exportBtn">
                        <i class="fas fa-download"></i> Export to Excel
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
    // Function to update preview table
    function updatePreview() {
        const selectedColumns = [];
        $('input[name="columns[]"]:checked').each(function() {
            selectedColumns.push($(this).val());
        });

        const formData = new FormData();
        formData.append('start_date', $('input[name="start_date"]').val());
        formData.append('end_date', $('input[name="end_date"]').val());
        formData.append('month', $('select[name="month"]').val());
        formData.append('year', $('select[name="year"]').val());
        formData.append('year_only', $('select[name="year_only"]').val());
        formData.append('member_type_id', $('select[name="member_type_id"]').val());
        formData.append('location_library_id', $('select[name="location_library_id"]').val());
        formData.append('noinduk', $('input[name="noinduk"]').val());
        formData.append('penerbit', $('input[name="penerbit"]').val());
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

// Function to clear all filters
function clearAllFilters() {
    if (confirm('Apakah Anda yakin ingin menghapus semua filter?')) {
        // Clear all input fields
        $('input[type="date"], input[type="text"], input[type="email"]').val('');
        $('select').prop('selectedIndex', 0);
        
        // Update preview
        const selectedColumns = [];
        $('input[name="columns[]"]:checked').each(function() {
            selectedColumns.push($(this).val());
        });
        
        if (selectedColumns.length > 0) {
            updatePreview();
        }
    }
}
</script>
<?= $this->endSection('script'); ?>
