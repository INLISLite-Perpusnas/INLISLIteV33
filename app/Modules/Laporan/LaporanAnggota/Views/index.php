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
                <div>Laporan Kunjungan
                    <div class="page-title-subheading">Daftar Semua Kunjungan</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('auth') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Laporan</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Laporan Kunjungan </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Export Data Anggota</h4>
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

                <div class="form-group mb-3">
                    <label><strong>Pilih Kolom yang akan diekspor</strong></label>
                    <div class="row">
                        <?php foreach ($columns as $key => $label) : ?>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="columns[]" value="<?= $key ?>" id="<?= $key ?>">
                                    <label class="form-check-label" for="<?= $key ?>">
                                        <?= $label ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>

                <!-- Gender Filter Section -->
                <div class="form-group mb-3">
                    <label><strong>Filter Berdasarkan Jenis Kelamin</strong></label>
                    <select class="form-control" name="gender_id" id="gender_id">
                        <option value="">-- Semua Jenis Kelamin --</option>
                        <?php if (isset($genderOptions)) : ?>
                            <?php foreach ($genderOptions as $gender) : ?>
                                <option value="<?= $gender->id ?>"><?= $gender->Name ?></option>
                            <?php endforeach ?>
                        <?php endif ?>
                    </select>
                </div>

                <!-- Member Type Filter Section -->
                <div class="form-group mb-3">
                    <label><strong>Filter Berdasarkan Jenis Anggota</strong></label>
                    <select class="form-control" name="member_type_id" id="member_type_id">
                        <option value="">-- Semua Jenis Anggota --</option>
                        <?php if (isset($memberTypeOptions)) : ?>
                            <?php foreach ($memberTypeOptions as $memberType) : ?>
                                <option value="<?= $memberType->id ?>"><?= $memberType->jenisanggota ?></option>
                            <?php endforeach ?>
                        <?php endif ?>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label><strong>Filter Berdasarkan Tanggal</strong></label>
                    <select class="form-control" name="filter_type" id="filter_type">
                        <option value="date">Tanggal</option>
                        <option value="month">Bulan</option>
                        <option value="year">Tahun</option>
                    </select>
                </div>

                <div id="date_filter" class="filter-section mb-3">
                    <h6 class="mb-3"><i class="fas fa-calendar-alt"></i> Filter Berdasarkan Tanggal</h6>
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

                <div id="month_filter" class="filter-section mb-3" style="display: none;">
                    <h6 class="mb-3"><i class="fas fa-calendar"></i> Filter Berdasarkan Bulan</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Bulan</label>
                            <select name="month" class="form-control">
                                <?php for ($i = 1; $i <= 12; $i++) : ?>
                                    <option value="<?= $i ?>"><?= date('F', mktime(0, 0, 0, $i, 1)) ?></option>
                                <?php endfor ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Tahun</label>
                            <select name="year" class="form-control">
                                <?php for ($i = date('Y'); $i >= 2020; $i--) : ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="year_filter" class="filter-section mb-3" style="display: none;">
                    <h6 class="mb-3"><i class="fas fa-calendar-year"></i> Filter Berdasarkan Tahun</h6>
                    <label>Tahun</label>
                    <select name="year" class="form-control">
                        <?php for ($i = date('Y'); $i >= 2020; $i--) : ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor ?>
                    </select>
                </div>

                <div class="text-center mb-3">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-download"></i> Export to Excel
                    </button>
                </div>
            </form>

            <!-- Preview Section -->
            <div class="preview-container">
                <h5><i class="fas fa-eye"></i> Preview Data (20 Baris Pertama)</h5>
                <div class="preview-table" id="preview-table">
                    <div class="text-center text-muted">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <p>Pilih kolom dan filter untuk melihat preview data</p>
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
        const genderId = $('#gender_id').val(); // Get selected gender
        const memberTypeId = $('#member_type_id').val(); // Get selected member type
        
        const formData = new FormData();
        formData.append('columns', JSON.stringify(selectedColumns));
        formData.append('filter_type', filterType);
        formData.append('gender_id', genderId); // Add gender filter to form data
        formData.append('member_type_id', memberTypeId); // Add member type filter to form data

        // Add appropriate date filters based on filter type
        if (filterType === 'date') {
            formData.append('start_date', $('input[name="start_date"]').val());
            formData.append('end_date', $('input[name="end_date"]').val());
        } else if (filterType === 'month') {
            formData.append('month', $('select[name="month"]').val());
            formData.append('year', $('#month_filter select[name="year"]').val());
        } else if (filterType === 'year') {
            formData.append('year', $('#year_filter select[name="year"]').val());
        }

        // Show loading indicator
        $('#preview-table').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Memuat preview data...</p></div>');

        // Make AJAX call to get preview data
        $.ajax({
            url: '<?= base_url('laporan-anggota/preview') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#preview-table').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching preview:', error);
                $('#preview-table').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat preview data</div>');
            }
        });
    }

    // Event listeners for form changes
    $('input[name="columns[]"], #filter_type, #gender_id, #member_type_id').change(updatePreview);
    $('input[name="start_date"], input[name="end_date"]').change(updatePreview);
    $('select[name="month"], select[name="year"]').change(updatePreview);

    // Initial preview load
    updatePreview();

    // Show/hide filter sections
    $('#filter_type').change(function() {
        $('.filter-section').hide();
        $('#' + $(this).val() + '_filter').show();
        updatePreview();
    });

    // Add some visual feedback when checkboxes are changed
    $('input[name="columns[]"]').change(function() {
        const checkedCount = $('input[name="columns[]"]:checked').length;
        if (checkedCount === 0) {
            $(this).closest('.form-group').find('label').first().addClass('text-danger');
        } else {
            $(this).closest('.form-group').find('label').first().removeClass('text-danger');
        }
    });
});
</script>
<?= $this->endSection('script'); ?>