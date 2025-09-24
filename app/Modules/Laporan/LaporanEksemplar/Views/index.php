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
                    <i class="pe-7s-notebook icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Laporan Eksemplar
                    <div class="page-title-subheading">Export Data Eksemplar dengan Multiple Filter</div>
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
            <h5><strong>Export Data Eksemplar</strong></h5>
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

            <form action="<?= base_url('laporan-eksemplar/export') ?>" method="post">
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
                    <!-- Filter Tanggal Dibuat -->
                    <div class="filter-section">
                        <h6><i class="fas fa-calendar-alt"></i> Filter Berdasarkan Tanggal Dibuat</h6>
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

                    <!-- Filter Bulan & Tahun Dibuat -->
                    <div class="filter-section">
                        <h6><i class="fas fa-calendar-alt"></i> Filter Berdasarkan Bulan & Tahun Dibuat</h6>
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

                    <!-- Filter Tahun Saja -->
                    <div class="filter-section">
                        <h6><i class="fas fa-calendar"></i> Filter Berdasarkan Tahun Dibuat Saja</h6>
                        <label>Tahun</label>
                        <select name="year_only" class="form-control">
                            <option value="">-- Pilih Tahun --</option>
                            <?php for ($i = date('Y'); $i >= 2020; $i--) : ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor ?>
                        </select>
                    </div>

                    <!-- Filter Tanggal Pengadaan -->
                    <div class="filter-section">
                        <h6><i class="fas fa-calendar-plus"></i> Filter Berdasarkan Tanggal Pengadaan</h6>
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

                    <!-- Filter Lokasi -->
                    <div class="filter-section">
                        <h6><i class="fas fa-map-marker-alt"></i> Filter Berdasarkan Lokasi</h6>
                        <label>Lokasi Perpustakaan</label>
                        <div class="select-wrapper mb-3">
                            <select class="form-control" name="location">
                                <option value="">-- Pilih Lokasi --</option>
                                <?php foreach (get_ref_table('location_library', 'ID, Name') as $row) : ?>
                                    <option value="<?= $row->ID ?>"><?= $row->Name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <label>Lokasi Ruang</label>
                        <div class="select-wrapper">
                            <select class="form-control" name="location_ruang">
                                <option value="">-- Pilih Ruang --</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filter Pengarang -->
                    <div class="filter-section">
                        <h6><i class="fas fa-user-edit"></i> Filter Berdasarkan Pengarang</h6>
                        <label>Pengarang</label>
                        <input type="text" name="author" class="form-control" placeholder="Masukkan nama pengarang...">
                    </div>

                    <!-- Filter Tempat Terbit -->
                    <div class="filter-section">
                        <h6><i class="fas fa-globe"></i> Filter Berdasarkan Tempat Terbit</h6>
                        <label>Tempat Terbit</label>
                        <input type="text" name="publishlocation" class="form-control" placeholder="Masukkan tempat terbit...">
                    </div>

                    <!-- Filter Subjek -->
                    <div class="filter-section">
                        <h6><i class="fas fa-tags"></i> Filter Berdasarkan Subjek</h6>
                        <label>Subjek</label>
                        <input type="text" name="subject" class="form-control" placeholder="Masukkan subjek...">
                    </div>

                    <!-- Filter Penerbit -->
                    <div class="filter-section">
                        <h6><i class="fas fa-building"></i> Filter Berdasarkan Penerbit</h6>
                        <label>Penerbit</label>
                        <input type="text" name="publisher" class="form-control" placeholder="Masukkan nama penerbit...">
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
                    <button type="submit" class="btn btn-success btn-lg px-5" id="exportBtn">
                        <i class="fas fa-download"></i> Export to Excel
                    </button>
                    <button type="button" class="btn btn-secondary btn-lg px-5 ml-2" onclick="clearAllFilters()">
                        <i class="fas fa-eraser"></i> Clear All Filters
                    </button>
                </div>

                <!-- Export Warning -->
                <div class="alert alert-warning" style="display: none;" id="exportWarning">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
                        <div>
                            <strong>Perhatian:</strong> Export data dalam jumlah besar membutuhkan waktu lebih lama.<br>
                            <small>Maksimum export: <strong>50,000 records</strong>. Gunakan filter untuk mengurangi jumlah data jika diperlukan.</small>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Preview Section -->
            <div class="preview-container">
                <h5><i class="fas fa-eye"></i> Preview Data (20 Baris Pertama)</h5>
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
        formData.append('tp_start_date', $('input[name="tp_start_date"]').val());
        formData.append('tp_end_date', $('input[name="tp_end_date"]').val());
        formData.append('location', $('select[name="location"]').val());
        formData.append('location_ruang', $('select[name="location_ruang"]').val());
        formData.append('author', $('input[name="author"]').val());
        formData.append('publishlocation', $('input[name="publishlocation"]').val());
        formData.append('subject', $('input[name="subject"]').val());
        formData.append('publisher', $('input[name="publisher"]').val());
        formData.append('createby', $('select[name="createby"]').val());
        formData.append('updateby', $('select[name="updateby"]').val());

        // Show loading indicator
        $('#preview-table').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p>Memuat preview data...</p></div>');

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
                $('#preview-table').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan saat memuat preview data. Silakan coba lagi.</div>');
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
    $('input[type="date"], input[type="text"], select').on('change keyup', debounce(updatePreview, 500));

    // Initial setup
    updateSelectAllStatus();
    updatePreview();

    // Handle location dropdown change for loading rooms
    $('select[name="location"]').change(function() {
        let locationId = $(this).val();
        let ruangSelect = $('select[name="location_ruang"]');
        
        // Clear first
        ruangSelect.html('<option value="">-- Pilih Ruang --</option>');

        if (locationId) {
            $.ajax({
                url: "<?= base_url('laporan-eksemplar/get-ruang') ?>", 
                type: "POST",
                data: { location_id: locationId },
                dataType: "json",
                success: function(data) {
                    if (data.length > 0) {
                        $.each(data, function(i, item) {
                            ruangSelect.append('<option value="'+item.ID+'">'+item.Name+'</option>');
                        });
                    } else {
                        ruangSelect.append('<option value="">Tidak ada ruang</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    ruangSelect.append('<option value="">Error loading data</option>');
                }
            });
        }
    });

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
        $('input[type="date"], input[type="text"]').val('');
        $('select').prop('selectedIndex', 0);
        $('select[name="location_ruang"]').html('<option value="">-- Pilih Ruang --</option>');
        
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