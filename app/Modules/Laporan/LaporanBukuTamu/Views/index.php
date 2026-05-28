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
                <div>Laporan Buku Tamu
                    <div class="page-title-subheading">Daftar Semua Kunjungan</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('auth') ?>"><i class="fa fa-home"></i>
                                Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Laporan</a></li>
                        <li class="breadcrumb-item" aria-current="page">Laporan Buku Tamu </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5><strong>Export Data Buku Tamu</strong></h5>
        </div>
        <div class="card-body">
            <?php if (session('errors')) : ?>
            <div class="alert alert-danger">
                <?php foreach (session('errors') as $error) : ?>
                <?= $error ?><br>
                <?php endforeach ?>
            </div>
            <?php endif ?>

            <form action="<?= base_url('laporan-buku-tamu/export') ?>" method="post">
                <?= csrf_field() ?>

                <div class="form-group mb-3">
                    <label><b>Pilih Kolom yang akan diekspor</b></label>
                    <div class="mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="select_all_columns">
                            <label class="form-check-label fw-semibold" for="select_all_columns">
                                Pilih Semua Kolom
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <?php foreach ($columns as $key => $label) : ?>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]"
                                    value="<?= $key ?>" id="<?= $key ?>">
                                <label class="form-check-label" for="<?= $key ?>">
                                    <?= $label ?>
                                </label>
                            </div>
                        </div>
                        <?php endforeach ?>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label><b>Filter Berdasarkan</b></label>
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
                    <h6 class="mb-3"><i class="fas fa-calendar-alt"></i> Filter Berdasarkan Bulan</h6>
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
                    <h6 class="mb-3"><i class="fas fa-calendar-alt"></i> Filter Berdasarkan Tahun</h6>
                    <label>Tahun</label>
                    <select name="year" class="form-control">
                        <?php for ($i = date('Y'); $i >= 2020; $i--) : ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label><strong>Filter Berdasarkan Jenis Kelamin</strong></label>
                            <select class="form-control" name="gender_id" id="gender_id">
                                <option value="">-- Semua Jenis Kelamin --</option>
                                <?php if (isset($genderOptions)) : ?>
                                <?php foreach ($genderOptions as $gender) : ?>
                                <option value="<?= $gender->Name ?>"><?= $gender->Name ?></option>
                                <?php endforeach ?>
                                <?php endif ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label><strong>Filter Berdasarkan Kriteria Pengunjung</strong></label>
                            <select class="form-control" name="visitor_type" id="visitor_type">
                                <option value="">-- Semua Kriteria Pengunjung --</option>
                                <option value="anggota">Anggota</option>
                                <option value="non anggota">Non Anggota</option>
                                <option value="rombongan">Rombongan</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label><strong>Filter Berdasarkan Lokasi Perpustakaan</strong></label>
                            <select class="form-control" name="location" id="location">
                                <option value="">-- Semua Lokasi Perpustakaan --</option>
                                <?php if (isset($locationOptions)) : ?>
                                <?php foreach ($locationOptions as $location) : ?>
                                <option value="<?= $location->code ?>"><?= $location->name ?></option>
                                <?php endforeach ?>
                                <?php endif ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label><strong>Filter Berdasarkan Ruang Perpustakaan</strong></label>
                            <select class="form-control" name="room" id="room">
                                <option value="">-- Semua Ruang Perpustakaan --</option>
                                <?php if (isset($roomOptions)) : ?>
                                <?php foreach ($roomOptions as $room) : ?>
                                <option value="<?= $room->Name ?>"><?= $room->Name ?></option>
                                <?php endforeach ?>
                                <?php endif ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label><strong>Filter Berdasarkan Tujuan Kunjungan</strong></label>
                            <select class="form-control" name="destination" id="destination">
                                <option value="">-- Semua Tujuan Kunjungan --</option>
                                <?php if (isset($destinationOptions)) : ?>
                                <?php foreach ($destinationOptions as $destination) : ?>
                                <option value="<?= $destination->name ?>"><?= $destination->name ?></option>
                                <?php endforeach ?>
                                <?php endif ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label><strong>Tampilkan Kop Laporan</strong></label>
                            <select class="form-control" name="kop" id="kop">
                                <option value="">-- Pilih Kop Laporan --</option>
                                <option value="Ya">Ya</option>
                                <option value="Tidak">Tidak</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="text-center mb-3 mt-2">
                    <button type="submit" class="btn btn-success btn-lg" onclick="setExportAction('excel')">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                    <button type="submit" class="btn btn-danger btn-lg ml-2" onclick="setExportAction('pdf')">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>

            </form>

            <!-- Preview Section -->
            <div class="preview-container">
                <h5><i class="fas fa-eye"></i> Preview Data (20 Baris Pertama)</h5>
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
$('#location').change(function(e) {
    var Location_Library_id = $("#location option:selected").val();
    getData(`<?= base_url('api/eksemplar/locations') ?>/${Location_Library_id}`, `#room`, false,
        `-- Semua Ruang Perpustakaan --`);
});

$(document).ready(function() {
    // Function to update preview table
    function updatePreview() {
        const selectedColumns = [];
        $('input[name="columns[]"]:checked').each(function() {
            selectedColumns.push($(this).val());
        });

        const filterType = $('#filter_type').val();
        const genderId = $('#gender_id').val(); // Get selected gender
        const visitor_type = $('#visitor_type').val();
        const location = $('#location').val();
        const room = $('#room').val();
        const destination = $('#destination').val();
        const kop = $('#kop').val();
        const formData = new FormData();

        formData.append('columns', JSON.stringify(selectedColumns));
        formData.append('filter_type', filterType);
        formData.append('gender_id', genderId); // Add gender filter to form data
        formData.append('visitor_type', visitor_type);
        formData.append('location', location);
        formData.append('room', room);
        formData.append('destination', destination);
        formData.append('kop', kop);

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
        $('#preview-table').html(
            '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Memuat preview data...</p></div>'
            );

        // Make AJAX call to get preview data
        $.ajax({
            url: '<?= base_url('laporan-buku-tamu/preview') ?>',
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

    // Select all columns toggle
    $('#select_all_columns').change(function() {
        $('.column-checkbox').prop('checked', $(this).is(':checked'));
        updatePreview();
    });

    // Sync select-all state when individual checkboxes change
    $(document).on('change', '.column-checkbox', function() {
        var total = $('.column-checkbox').length;
        var checked = $('.column-checkbox:checked').length;
        $('#select_all_columns').prop('checked', total === checked);
    });

    // Event listeners for form changes
    $('input[name="columns[]"], #filter_type, #gender_id, #visitor_type, #location, #room, #destination')
        .change(updatePreview);
    $('input[name="start_date"], input[name="end_date"]').change(updatePreview);
    $('select[name="month"], select[name="year"]').change(updatePreview);

    // Initial preview load
    updatePreview();

    // Show/hide filter sections
    $('#filter_type').change(function() {
        $('.filter-section').hide();
        $('#' + $(this).val() + '_filter').show();
    });
});

function setExportAction(type) {
    var form = document.querySelector('form[action*="laporan-buku-tamu"]');
    if (type === 'pdf') {
        form.action = '<?= base_url('laporan-buku-tamu/export_pdf') ?>';
    } else {
        form.action = '<?= base_url('laporan-buku-tamu/export') ?>';
    }
}
</script>
<?= $this->endSection('script'); ?>