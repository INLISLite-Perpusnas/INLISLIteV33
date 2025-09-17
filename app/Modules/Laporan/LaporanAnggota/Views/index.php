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
.user-filter-section {
    background-color: #e3f2fd;
    border: 1px solid #1976d2;
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
                <div>Laporan Anggota
                    <div class="page-title-subheading">Daftar Semua Anggota dengan Filter Multi-Database</div>
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
            <h4><i class="fas fa-file-export"></i> Export Data Anggota</h4>
            <small class="text-muted">Pilih kolom dan filter yang diinginkan untuk export data anggota</small>
        </div>
        <div class="card-body">
            <?php if (session('errors')) : ?>
                <div class="alert alert-danger">
                    <?php foreach (session('errors') as $error) : ?>
                        <?= $error ?><br>
                    <?php endforeach ?>
                </div>
            <?php endif ?>

            <form action="<?= base_url('laporan-anggota/export') ?>" method="post">
                <?= csrf_field() ?>

                <!-- Column Selection Section -->
                <div class="form-group mb-4">
                    <label><strong><i class="fas fa-columns"></i> Pilih Kolom yang akan diekspor</strong></label>
                    <div class="row">
                        <?php foreach ($columns as $key => $label) : ?>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="columns[]" value="<?= $key ?>" id="<?= $key ?>">
                                    <label class="form-check-label" for="<?= $key ?>">
                                        <?= $label ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <small class="text-muted mt-2">
                        <i class="fas fa-info-circle"></i> Pilih minimal satu kolom untuk melakukan export
                    </small>
                </div>

                <!-- User Filter Section -->
                <div class="filter-section user-filter-section mb-3">
                    <h6 class="mb-3"><i class="fas fa-users"></i> Filter Berdasarkan User (Database Terpisah)</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <label><strong>Filter Berdasarkan Pembuat (CreatedBy)</strong></label>
                            <select class="form-control" name="created_by_id" id="created_by_id">
                                <option value="">-- Semua Pembuat --</option>
                                <?php if (isset($userOptions) && is_array($userOptions)) : ?>
                                    <?php foreach ($userOptions as $user) : ?>
                                        <option value="<?= $user->id ?>"><?= esc($user->username) ?></option>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </select>
                            <small class="text-muted">Filter berdasarkan user yang membuat data anggota</small>
                        </div>
                        <div class="col-md-6">
                            <label><strong>Filter Berdasarkan Pengubah (UpdatedBy)</strong></label>
                            <select class="form-control" name="updated_by_id" id="updated_by_id">
                                <option value="">-- Semua Pengubah --</option>
                                <?php if (isset($userOptions) && is_array($userOptions)) : ?>
                                    <?php foreach ($userOptions as $user) : ?>
                                        <option value="<?= $user->id ?>"><?= esc($user->username) ?></option>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </select>
                            <small class="text-muted">Filter berdasarkan user yang terakhir mengubah data anggota</small>
                        </div>
                    </div>
                </div>

                <!-- Gender Filter Section -->
                <div class="form-group mb-3">
                    <label><strong><i class="fas fa-venus-mars"></i> Filter Berdasarkan Jenis Kelamin</strong></label>
                    <select class="form-control" name="gender_id" id="gender_id">
                        <option value="">-- Semua Jenis Kelamin --</option>
                        <?php if (isset($genderOptions)) : ?>
                            <?php foreach ($genderOptions as $gender) : ?>
                                <option value="<?= $gender->id ?>"><?= esc($gender->Name) ?></option>
                            <?php endforeach ?>
                        <?php endif ?>
                    </select>
                </div>

                <!-- Member Type Filter Section -->
                <div class="form-group mb-3">
                    <label><strong><i class="fas fa-user-tag"></i> Filter Berdasarkan Jenis Anggota</strong></label>
                    <select class="form-control" name="member_type_id" id="member_type_id">
                        <option value="">-- Semua Jenis Anggota --</option>
                        <?php if (isset($memberTypeOptions)) : ?>
                            <?php foreach ($memberTypeOptions as $memberType) : ?>
                                <option value="<?= $memberType->id ?>"><?= esc($memberType->jenisanggota) ?></option>
                            <?php endforeach ?>
                        <?php endif ?>
                    </select>
                </div>

                <!-- Date Filter Type Selection -->
                <div class="form-group mb-3">
                    <label><strong><i class="fas fa-filter"></i> Jenis Filter Tanggal</strong></label>
                    <select class="form-control" name="filter_type" id="filter_type">
                        <option value="date">Filter berdasarkan Range Tanggal</option>
                        <option value="month">Filter berdasarkan Bulan</option>
                        <option value="year">Filter berdasarkan Tahun</option>
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div id="date_filter" class="filter-section mb-3">
                    <h6 class="mb-3"><i class="fas fa-calendar-alt"></i> Filter Berdasarkan Range Tanggal</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6">
                            <label>Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control" max="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                </div>

                <!-- Month Filter -->
                <div id="month_filter" class="filter-section mb-3" style="display: none;">
                    <h6 class="mb-3"><i class="fas fa-calendar"></i> Filter Berdasarkan Bulan</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Bulan</label>
                            <select name="year" class="form-control">
                                <option value="">-- Pilih Tahun --</option>
                                <?php for ($i = date('Y'); $i >= 2020; $i--) : ?>
                                    <option value="<?= $i ?>" <?= ($i == date('Y')) ? 'selected' : '' ?>>
                                        <?= $i ?>
                                    </option>
                                <?php endfor ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Year Filter -->
                <div id="year_filter" class="filter-section mb-3" style="display: none;">
                    <h6 class="mb-3"><i class="fas fa-calendar-year"></i> Filter Berdasarkan Tahun</h6>
                    <label>Tahun</label>
                    <select name="year" class="form-control">
                        <option value="">-- Pilih Tahun --</option>
                        <?php for ($i = date('Y'); $i >= 2020; $i--) : ?>
                            <option value="<?= $i ?>" <?= ($i == date('Y')) ? 'selected' : '' ?>>
                                <?= $i ?>
                            </option>
                        <?php endfor ?>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="text-center mb-4">
                    <button type="button" id="select-all-columns" class="btn btn-info btn-sm">
                        <i class="fas fa-check-double"></i> Pilih Semua Kolom
                    </button>
                    <button type="button" id="clear-all-columns" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times"></i> Hapus Semua Pilihan
                    </button>
                </div>

                <div class="text-center mb-3">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-download"></i> Export ke Excel
                    </button>
                </div>
            </form>

            <!-- Preview Section -->
            <div class="preview-container">
                <h5><i class="fas fa-eye"></i> Preview Data (Maksimal 20 Baris Pertama)</h5>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Informasi:</strong> Preview menampilkan maksimal 20 baris pertama sesuai dengan filter yang dipilih. 
                    File export akan berisi semua data yang sesuai dengan filter.
                </div>
                <div class="preview-table" id="preview-table">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <p class="h5">Pilih kolom dan filter untuk melihat preview data</p>
                        <small>Data akan dimuat secara otomatis ketika Anda memilih kolom atau mengubah filter</small>
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

        const filterType = $('#filter_type').val();
        const genderId = $('#gender_id').val();
        const memberTypeId = $('#member_type_id').val();
        const createdById = $('#created_by_id').val(); // New filter
        const updatedById = $('#updated_by_id').val(); // New filter
        
        const formData = new FormData();
        formData.append('columns', JSON.stringify(selectedColumns));
        formData.append('filter_type', filterType);
        formData.append('gender_id', genderId);
        formData.append('member_type_id', memberTypeId);
        formData.append('created_by_id', createdById); // Add to form data
        formData.append('updated_by_id', updatedById); // Add to form data

        // Add appropriate date filters based on filter type
        if (filterType === 'date') {
            formData.append('start_date', $('input[name="start_date"]').val());
            formData.append('end_date', $('input[name="end_date"]').val());
        } else if (filterType === 'month') {
            formData.append('month', $('#month_filter select[name="month"]').val());
            formData.append('year', $('#month_filter select[name="year"]').val());
        } else if (filterType === 'year') {
            formData.append('year', $('#year_filter select[name="year"]').val());
        }

        // Show loading indicator
        $('#preview-table').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-3">Memuat preview data...</p>
            </div>
        `);

        // Make AJAX call to get preview data
        $.ajax({
            url: '<?= base_url('laporan-anggota/preview') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#preview-table').html(response);
                
                // Add some styling to the preview table
                $('#preview-table table').addClass('table-hover');
                
                // Show success message if data found
                if (response.includes('<table')) {
                    const rowCount = $(response).find('tbody tr').length;
                    const successMsg = `<div class="alert alert-success alert-dismissible fade show mb-3">
                        <i class="fas fa-check-circle"></i>
                        <strong>Berhasil!</strong> Menampilkan ${rowCount} baris data untuk preview.
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>`;
                    $('#preview-table').prepend(successMsg);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching preview:', error);
                $('#preview-table').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Error!</strong> Terjadi kesalahan saat memuat preview data.
                        <br><small>Error: ${error}</small>
                    </div>
                `);
            }
        });
    }

    // Event listeners for form changes
    $('input[name="columns[]"], #filter_type, #gender_id, #member_type_id, #created_by_id, #updated_by_id').change(function() {
        updatePreview();
    });
    
    $('input[name="start_date"], input[name="end_date"]').change(function() {
        updatePreview();
    });
    
    $('select[name="month"], select[name="year"]').change(function() {
        updatePreview();
    });

    // Initial preview load
    updatePreview();

    // Show/hide filter sections based on filter type
    $('#filter_type').change(function() {
        $('.filter-section:not(.user-filter-section)').hide();
        const selectedFilter = $(this).val();
        $('#' + selectedFilter + '_filter').show();
        updatePreview();
    });

    // Select all columns functionality
    $('#select-all-columns').click(function() {
        $('input[name="columns[]"]').prop('checked', true);
        updatePreview();
        $(this).removeClass('btn-info').addClass('btn-success');
        setTimeout(() => {
            $(this).removeClass('btn-success').addClass('btn-info');
        }, 1000);
    });

    // Clear all columns functionality
    $('#clear-all-columns').click(function() {
        $('input[name="columns[]"]').prop('checked', false);
        updatePreview();
        $(this).removeClass('btn-secondary').addClass('btn-warning');
        setTimeout(() => {
            $(this).removeClass('btn-warning').addClass('btn-secondary');
        }, 1000);
    });

    // Add visual feedback when checkboxes are changed
    $('input[name="columns[]"]').change(function() {
        const checkedCount = $('input[name="columns[]"]:checked').length;
        const label = $(this).closest('.form-group').find('label').first();
        
        if (checkedCount === 0) {
            label.addClass('text-danger').removeClass('text-success');
        } else {
            label.addClass('text-success').removeClass('text-danger');
        }
        
        // Update column counter
        const counterText = `(${checkedCount} kolom dipilih)`;
        label.find('.column-counter').remove();
        label.append(` <small class="column-counter text-muted">${counterText}</small>`);
    });

    // Form validation before submit
    $('form').on('submit', function(e) {
        const selectedColumns = $('input[name="columns[]"]:checked').length;
        
        if (selectedColumns === 0) {
            e.preventDefault();
            alert('Pilih minimal satu kolom untuk melakukan export!');
            $('input[name="columns[]"]').first().focus();
            return false;
        }
        
        // Show loading state on submit button
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Mengekspor...').prop('disabled', true);
        
        // Re-enable button after 5 seconds (in case of error)
        setTimeout(() => {
            submitBtn.html(originalText).prop('disabled', false);
        }, 5000);
    });

    // Add tooltips to filter sections
    $('[data-toggle="tooltip"]').tooltip();

    // Enhance date inputs
    $('input[type="date"]').on('change', function() {
        const startDate = $('input[name="start_date"]').val();
        const endDate = $('input[name="end_date"]').val();
        
        if (startDate && endDate && startDate > endDate) {
            alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir!');
            $(this).val('');
            return;
        }
    });
});
</script>
<?= $this->endSection('script'); ?>