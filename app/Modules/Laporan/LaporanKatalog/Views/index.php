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
#filterForm .period-selector,
#filterForm .period-filter,
#filterForm .criteria-section {
    grid-column: 1 / -1;
}
#filterForm .criteria-heading {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}
#filterForm .criteria-heading h6 { margin: 0; }
#filterForm .criterion-row {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) auto;
    align-items: start;
    gap: 12px;
    margin-top: 12px;
}
#filterForm .criterion-control { min-width: 0; }
#filterForm .criterion-control .select2-container { width: 100% !important; }
#filterForm .criterion-control .select2-selection--single { min-height: 38px; border-color: #ced4da; }
#filterForm .criterion-control .select2-selection__rendered { line-height: 36px; }
#filterForm .criterion-control .select2-selection__arrow { height: 36px; }
@media (max-width: 768px) {
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
                    <i class="pe-7s-notebook icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Laporan Katalog
                    <div class="page-title-subheading">Export Data Katalog dengan Multiple Filter</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('auth') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Laporan</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Laporan Katalog</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5><strong>Export Data Katalog</strong></h5>
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

            <form id="filterForm" action="<?= base_url('laporan-katalog/export') ?>" method="post">
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
                    <div class="form-group period-selector">
                        <label for="filter_type"><b>Filter Berdasarkan</b></label>
                        <select class="form-control" name="filter_type" id="filter_type">
                            <option value="date">Tanggal</option>
                            <option value="month">Bulan</option>
                            <option value="year">Tahun</option>
                        </select>
                    </div>
                    <!-- Filter Tanggal Dibuat -->
                    <div id="date_filter" class="filter-section period-filter">
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
                    <div id="month_filter" class="filter-section period-filter" style="display: none;">
                        <h6><i class="fas fa-calendar-alt"></i> Filter Berdasarkan Bulan & Tahun Dibuat</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Bulan</label>
                                <select name="month" class="form-control" disabled>
                                    <option value="">-- Pilih Bulan --</option>
                                    <?php for ($i = 1; $i <= 12; $i++) : ?>
                                        <option value="<?= $i ?>"><?= date('F', mktime(0, 0, 0, $i, 1)) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Tahun</label>
                                <select name="year" id="month_year" class="form-control" disabled>
                                    <option value="">-- Pilih Tahun --</option>
                                    <?php for ($i = date('Y'); $i >= 2020; $i--) : ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Tahun Saja -->
                    <div id="year_filter" class="filter-section period-filter" style="display: none;">
                        <h6><i class="fas fa-calendar"></i> Filter Berdasarkan Tahun Dibuat Saja</h6>
                        <label>Tahun</label>
                        <select name="year" id="year_only" class="form-control" disabled>
                            <option value="">-- Pilih Tahun --</option>
                            <?php for ($i = date('Y'); $i >= 2020; $i--) : ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor ?>
                        </select>
                    </div>

                    <div class="filter-section criteria-section">
                        <div class="criteria-heading">
                            <h6><i class="fas fa-filter" aria-hidden="true"></i> Kriteria Katalog</h6>
                            <button type="button" class="btn btn-success" id="addCriterion" title="Tambah kriteria" aria-label="Tambah kriteria">
                                <i class="fas fa-plus-circle" aria-hidden="true"></i>
                            </button>
                        </div>
                        <p class="text-muted mb-0">Pilih kriteria dan nilainya. Semua kriteria yang diisi diterapkan bersama.</p>
                        <div id="criteria_rows"></div>
                    </div>
                    <input type="hidden" name="criteria" id="criteriaPayload" value="[]">
                </div>

                <!-- Export Button -->
                <div class="text-center mb-4">
                    <button type="submit" class="btn btn-success btn-lg px-5" id="exportBtn" onclick="setExportAction('excel')">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                    <button type="submit" class="btn btn-danger btn-lg px-5 ml-2" id="exportPdfBtn" onclick="setExportAction('pdf')">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button type="button" class="btn btn-secondary btn-lg px-5 ml-2" id="clearCatalogFilters">
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
                <h5><i class="fas fa-eye"></i> Preview Data (100 Baris Pertama)</h5>
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
    const criteriaLabels = <?= json_encode($criteriaLabels, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    function criteriaValues() {
        const criteria = [];
        $('#criteria_rows .criterion-row').each(function() {
            const field = $(this).find('.criterion-field').val();
            const value = $(this).find('.criterion-value select').val();
            if (field && value !== null && value !== '') {
                criteria.push({ field: field, value: value });
            }
        });
        return JSON.stringify(criteria);
    }

    function destroyCriterionSelects(container) {
        if ($.fn.select2) {
            container.find('select').each(function() {
                if ($(this).data('select2')) $(this).select2('destroy');
            });
        }
    }

    function addCriterionRow() {
        if ($('#criteria_rows .criterion-row').length >= Object.keys(criteriaLabels).length) return;

        const row = $('<div>', { class: 'criterion-row' });
        const field = $('<select>', { class: 'form-control criterion-field', 'aria-label': 'Pilih kriteria katalog' });
        field.append(new Option('-- Pilih Kriteria --', ''));
        Object.entries(criteriaLabels).forEach(function([key, label]) {
            field.append(new Option(label, key));
        });
        const valueControl = $('<div>', { class: 'criterion-control criterion-value' });
        const remove = $('<button>', { type: 'button', class: 'btn btn-outline-danger', title: 'Hapus kriteria', 'aria-label': 'Hapus kriteria' })
            .append($('<i>', { class: 'fas fa-minus', 'aria-hidden': 'true' }));
        row.append($('<div>', { class: 'criterion-control' }).append(field), valueControl, remove);
        $('#criteria_rows').append(row);
        if ($.fn.select2) field.select2({ width: '100%', placeholder: '-- Pilih Kriteria --' });

        field.on('change', function() {
            destroyCriterionSelects(valueControl);
            valueControl.empty();
            const key = field.val();
            if (!key) {
                updatePreview();
                return;
            }
            const duplicate = $('#criteria_rows .criterion-field').not(field).filter(function() {
                return $(this).val() === key;
            }).length > 0;
            if (duplicate) {
                field.val('').trigger('change.select2');
                alert('Kriteria ini sudah dipilih.');
                updatePreview();
                return;
            }

            const value = $('<select>', { class: 'form-control', 'aria-label': criteriaLabels[key] })
                .append(new Option('-- Semua --', ''));
            valueControl.append(value);
            if ($.fn.select2) {
                value.select2({
                    width: '100%', placeholder: '-- Semua --', allowClear: true,
                    ajax: {
                        url: <?= json_encode(base_url('laporan-katalog')) ?>,
                        dataType: 'json', delay: 300,
                        data: function(params) {
                            return { criterion_options: '1', field: key, q: params.term || '', page: params.page || 1 };
                        },
                        processResults: function(data) { return data; }
                    }
                });
            }
            updatePreview();
        });

        remove.on('click', function() {
            destroyCriterionSelects(row);
            row.remove();
            if (!$('#criteria_rows .criterion-row').length) addCriterionRow();
            updatePreview();
        });
    }

    $('#addCriterion').on('click', addCriterionRow);
    addCriterionRow();

    function updatePeriodFilter() {
        $('.period-filter').hide().find('input, select').prop('disabled', true);
        $('#' + $('#filter_type').val() + '_filter').show().find('input, select').prop('disabled', false);
    }
    $('#filter_type').on('change', updatePeriodFilter);
    updatePeriodFilter();

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

        $('#criteriaPayload').val(criteriaValues());
        const formData = new FormData($('#filterForm')[0]);
        formData.delete('columns[]');
        formData.set('columns', JSON.stringify(selectedColumns));

        // Show loading indicator
        $('#preview-table').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p>Memuat preview data...</p></div>');

        // Make AJAX call to get preview data
        $.ajax({
            url: '<?= base_url('laporan-katalog/preview') ?>',
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
    $('#filterForm .period-selector, #filterForm .period-filter').on('change keyup', 'input, select', debounce(updatePreview, 500));
    $('#criteria_rows').on('change', '.criterion-value select', debounce(updatePreview, 500));

    $('#filterForm').on('submit', function() {
        $('#criteriaPayload').val(criteriaValues());
    });

    $('#clearCatalogFilters').on('click', function() {
        if (!confirm('Apakah Anda yakin ingin menghapus semua filter?')) return;
        $('#filterForm .period-filter input').val('');
        $('#filterForm .period-filter select').prop('selectedIndex', 0);
        $('#filter_type').val('date');
        destroyCriterionSelects($('#criteria_rows'));
        $('#criteria_rows').empty();
        addCriterionRow();
        $('#criteriaPayload').val('[]');
        $('#filter_type').trigger('change');
    });

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
    var form = document.querySelector('form[action*="laporan-katalog"]');
    if (type === 'pdf') {
        form.action = '<?= base_url('laporan-katalog/export_pdf') ?>';
    } else {
        form.action = '<?= base_url('laporan-katalog/export') ?>';
    }
}

</script>
<?= $this->endSection('script'); ?>
