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
                    <i class="pe-7s-users icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Laporan Anggota
                    <div class="page-title-subheading">Export Data Anggota dengan Multiple Filter</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('auth') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Laporan</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Laporan Anggota</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5><strong>Export Data Anggota</strong></h5>
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

            <form action="<?= base_url('laporan-anggota/export') ?>" method="post">
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

                <!-- Multiple Filters Container -->
                <div class="filters-container">
                    <!-- Filter Tanggal Registrasi -->
                    <div class="filter-section">
                        <h6><i class="fas fa-calendar-alt"></i> Filter Berdasarkan Tanggal Registrasi</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Filter Bulan & Tahun Registrasi -->
                    <div class="filter-section">
                        <h6><i class="fas fa-calendar-alt"></i> Filter Berdasarkan Bulan & Tahun Registrasi</h6>
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

                    <!-- Filter Tahun Registrasi Saja -->
                    <div class="filter-section">
                        <h6><i class="fas fa-calendar"></i> Filter Berdasarkan Tahun Registrasi Saja</h6>
                        <label>Tahun</label>
                        <select name="year_only" class="form-control">
                            <option value="">-- Pilih Tahun --</option>
                            <?php for ($i = date('Y'); $i >= 2020; $i--) : ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor ?>
                        </select>
                    </div>

                    <!-- Filter Tanggal Lahir -->
                    <div class="filter-section">
                        <h6><i class="fas fa-birthday-cake"></i> Filter Berdasarkan Tanggal Lahir</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="birth_start_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Tanggal Akhir</label>
                                <input type="date" name="birth_end_date" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Filter Jenis Kelamin -->
                    <div class="filter-section">
                        <h6><i class="fas fa-venus-mars"></i> Filter Berdasarkan Jenis Kelamin</h6>
                        <label>Jenis Kelamin</label>
                        <select name="gender_id" class="form-control">
                            <option value="">-- Semua Jenis Kelamin --</option>
                            <?php if (isset($genderOptions)) : ?>
                                <?php foreach ($genderOptions as $gender) : ?>
                                    <option value="<?= $gender->id ?>"><?= esc($gender->Name) ?></option>
                                <?php endforeach ?>
                            <?php endif ?>
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

                    <!-- Filter Nama Lengkap -->
                    <div class="filter-section">
                        <h6><i class="fas fa-user"></i> Filter Berdasarkan Nama Lengkap</h6>
                        <label>Nama Lengkap</label>
                        <input type="text" name="fullname" class="form-control" placeholder="Masukkan nama lengkap...">
                    </div>

                    <!-- Filter Tempat Lahir -->
                    <div class="filter-section">
                        <h6><i class="fas fa-map-marker-alt"></i> Filter Berdasarkan Tempat Lahir</h6>
                        <label>Tempat Lahir</label>
                        <input type="text" name="place_of_birth" class="form-control" placeholder="Masukkan tempat lahir...">
                    </div>

                    <!-- Filter Alamat -->
                    <div class="filter-section">
                        <h6><i class="fas fa-home"></i> Filter Berdasarkan Alamat</h6>
                        <label>Alamat</label>
                        <input type="text" name="address" class="form-control" placeholder="Masukkan alamat...">
                    </div>

                    <!-- Filter Provinsi -->
                    <div class="filter-section">
                        <h6><i class="fas fa-map"></i> Filter Berdasarkan Provinsi</h6>
                        <label>Provinsi</label>
                        <input type="text" name="province" class="form-control" placeholder="Masukkan nama provinsi...">
                    </div>

                    <!-- Filter Kota -->
                    <div class="filter-section">
                        <h6><i class="fas fa-city"></i> Filter Berdasarkan Kota</h6>
                        <label>Kota</label>
                        <input type="text" name="city" class="form-control" placeholder="Masukkan nama kota...">
                    </div>

                    <!-- Filter Institusi -->
                    <div class="filter-section">
                        <h6><i class="fas fa-university"></i> Filter Berdasarkan Institusi</h6>
                        <label>Nama Institusi</label>
                        <input type="text" name="institution_name" class="form-control" placeholder="Masukkan nama institusi...">
                    </div>

                    <!-- Filter Email -->
                    <div class="filter-section">
                        <h6><i class="fas fa-envelope"></i> Filter Berdasarkan Email</h6>
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Masukkan email...">
                    </div>

                    <!-- Filter CreateBy -->
                    <div class="filter-section">
                        <h6><i class="fas fa-user-plus"></i> Filter Berdasarkan Dibuat Oleh</h6>
                        <label>Dibuat Oleh</label>
                        <select name="createby" class="form-control">
                            <option value="">-- Semua User --</option>
                            <?php if (isset($userOptions)) : ?>
                                <?php foreach ($userOptions as $user) : ?>
                                    <option value="<?= $user->id ?>">
                                        <?= $user->username ?>
                                    </option>
                                <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </div>

                    <!-- Filter UpdateBy -->
                    <div class="filter-section">
                        <h6><i class="fas fa-user-edit"></i> Filter Berdasarkan Diperbarui Oleh</h6>
                        <label>Diperbarui Oleh</label>
                        <select name="updateby" class="form-control">
                            <option value="">-- Semua User --</option>
                            <?php if (isset($userOptions)) : ?>
                                <?php foreach ($userOptions as $user) : ?>
                                    <option value="<?= $user->id ?>">
                                        <?= $user->username ?>
                                    </option>
                                <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </div>
                </div>

                <!-- Export Button -->
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

                <!-- Export Warning -->
               
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

        if (selectedColumns.length === 0) {
            $('#preview-table').html('<div class="text-center"><p>Pilih minimal satu kolom untuk melihat preview data</p></div>');
            $('#exportWarning').hide();
            return;
        }

        const formData = new FormData();
        formData.append('columns', JSON.stringify(selectedColumns));

        // Add all filter values
        formData.append('start_date', $('input[name="start_date"]').val());
        formData.append('end_date', $('input[name="end_date"]').val());
        formData.append('month', $('select[name="month"]').val());
        formData.append('year', $('select[name="year"]').val());
        formData.append('year_only', $('select[name="year_only"]').val());
        formData.append('birth_start_date', $('input[name="birth_start_date"]').val());
        formData.append('birth_end_date', $('input[name="birth_end_date"]').val());
        formData.append('gender_id', $('select[name="gender_id"]').val());
        formData.append('member_type_id', $('select[name="member_type_id"]').val());
        formData.append('fullname', $('input[name="fullname"]').val());
        formData.append('place_of_birth', $('input[name="place_of_birth"]').val());
        formData.append('address', $('input[name="address"]').val());
        formData.append('province', $('input[name="province"]').val());
        formData.append('city', $('input[name="city"]').val());
        formData.append('institution_name', $('input[name="institution_name"]').val());
        formData.append('email', $('input[name="email"]').val());
        formData.append('createby', $('select[name="createby"]').val());
        formData.append('updateby', $('select[name="updateby"]').val());

        // Show loading indicator
        $('#preview-table').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p>Memuat preview data...</p></div>');

        // Make AJAX call to get preview data
        $.ajax({
            url: '<?= base_url('laporan-anggota/preview') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#preview-table').html(response);
                // Show export warning if data exists
                if (response.includes('<table')) {
                    $('#exportWarning').show();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching preview:', error);
                $('#preview-table').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan saat memuat preview data. Silakan coba lagi.</div>');
                $('#exportWarning').hide();
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
    var form = document.querySelector('form[action*="laporan-anggota"]');
    if (type === 'pdf') {
        form.action = '<?= base_url('laporan-anggota/export_pdf') ?>';
    } else {
        form.action = '<?= base_url('laporan-anggota/export') ?>';
    }
}

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