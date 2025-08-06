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
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-notebook icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Laporan Eksemplar
                    <div class="page-title-subheading">Export Data Eksemplar</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('auth') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Laporan</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Laporan Eksemplar</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Export Data Eksemplar</h4>
        </div>
        <div class="card-body">
            <?php if (session('errors')) : ?>
                <div class="alert alert-danger">
                    <?php foreach (session('errors') as $error) : ?>
                        <?= $error ?><br>
                    <?php endforeach ?>
                </div>
            <?php endif ?>

            <form action="<?= base_url('laporan-eksemplar/export') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="form-group mb-3">
                    <label>Pilih Kolom yang akan diekspor</label>
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

                <div class="form-group mb-3">
                    <label>Filter Berdasarkan</label>
                    <select class="form-control" name="filter_type" id="filter_type">
                        <option value="date">Tanggal</option>
                        <option value="month">Bulan</option>
                        <option value="year">Tahun</option>
                        <option value="location">Lokasi Perpustakaan</option>
                        <option value="tanggalpengadaan">Tanggal Pengadaan</option>
                        <option value="author">Pengarang</option>
                        <option value="publishlocation">Tempat Terbit</option>
                        <option value="subject">Subjek</option>
                        <option value="publisher">Penerbit</option>
                    </select>
                </div>

                <div id="date_filter" class="filter-section mb-3">
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
                    <label>Tahun</label>
                    <select name="year" class="form-control">
                        <?php for ($i = date('Y'); $i >= 2020; $i--) : ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor ?>
                    </select>
                </div>

                 <div id="location_filter" class="filter-section mb-3" style="display: none;">
                   <label>Lokasi Perpustakaan</label>
                        <div class="select-wrapper">
                            <select class="form-control" name="location">
                                <option value="">-Pilih-</option>
                                <?php foreach (get_ref_table('location_library', 'ID, Name', 'Branch_id = ' . user()->branch_id ?? '', 'data') as $row) : ?>
                                    <option value="<?= $row->ID ?>"><?= $row->Name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                </div>

                  <div id="tanggalpengadaan_filter" class="filter-section mb-3" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="tp_start_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Tanggal Akhir</label>
                            <input type="date" name="tp_end_date" class="form-control">
                        </div>
                    </div>
                </div>

                 <div id="publishlocation_filter" class="filter-section mb-3" style="display: none;">
                    <label>Tempat Terbit</label>
                    <input type="text" name="publishlocation" class="form-control">
                </div>

                <div id="author_filter" class="filter-section mb-3" style="display: none;">
                    <label>Pengarang</label>
                    <input type="text" name="author" class="form-control">
                </div>

                <div id="subject_filter" class="filter-section mb-3" style="display: none;">
                    <label>Subjek</label>
                    <input type="text" name="subject" class="form-control">
                </div>

                <div id="publisher_filter" class="filter-section mb-3" style="display: none;">
                    <label>Penerbit</label>
                    <input type="text" name="publisher" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Export to Excel</button>
            </form>

            <!-- Preview Section -->
            <div class="preview-container">
                <h5>Preview (20 Baris Pertama)</h5>
                <div class="preview-table" id="preview-table">
                    <div class="text-center">
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
        const formData = new FormData();
        formData.append('columns', JSON.stringify(selectedColumns));
        formData.append('filter_type', filterType);

        // Add appropriate date filters based on filter type
        if (filterType === 'date') {
            formData.append('start_date', $('input[name="start_date"]').val());
            formData.append('end_date', $('input[name="end_date"]').val());
        } else if (filterType === 'month') {
            formData.append('month', $('select[name="month"]').val());
            formData.append('year', $('#month_filter select[name="year"]').val());
        } else if (filterType === 'year') {
            formData.append('year', $('#year_filter select[name="year"]').val());
        } else if (filterType === 'location') {
            formData.append('location', $('#location_filter select[name="location"]').val());
        } else if (filterType === 'tanggalpengadaan') {
            formData.append('tp_start_date', $('#tanggalpengadaan_filter input[name="tp_start_date"]').val());
            formData.append('tp_end_date', $('#tanggalpengadaan_filter input[name="tp_end_date"]').val());
        } else if (filterType === 'author') {
            formData.append('author', $('#author_filter input[name="author"]').val());
        } else if (filterType === 'subject') {
            formData.append('subject', $('#subject_filter input[name="subject"]').val());
        } else if (filterType === 'publisher') {
            formData.append('publisher', $('#publisher_filter input[name="publisher"]').val());
        } else if (filterType === 'publishlocation') {
            formData.append('publishlocation', $('#publishlocation_filter input[name="publishlocation"]').val());
        }

        // Make AJAX call to get preview data
        $.ajax({
            url: '<?= base_url('laporan-eksemplar/preview') ?>',
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

    // Event listeners for form changes
    $('input[name="columns[]"], #filter_type').change(updatePreview);
    $('input[name="start_date"], input[name="end_date"]').change(updatePreview);
    $('select[name="month"], select[name="year"]').change(updatePreview);
    $('select[name="location"]').change(updatePreview);
    $('input[name="tp_start_date"], input[name="tp_end_date"]').change(updatePreview);
    $('input[name="author"]').change(updatePreview);
    $('input[name="subject"]').change(updatePreview);
    $('input[name="publisher"]').change(updatePreview); 
    $('input[name="publishlocation"]').change(updatePreview);

    // Initial preview load
    updatePreview();

    // Show/hide filter sections
    $('#filter_type').change(function() {
        $('.filter-section').hide();
        $('#' + $(this).val() + '_filter').show();
    });
});
</script>
<?= $this->endSection('script'); ?>