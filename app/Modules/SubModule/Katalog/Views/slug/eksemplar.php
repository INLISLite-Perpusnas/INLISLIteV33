<?php
$request = service('request');
$slug = $request->getGet('slug');
$branch_id = $request->getGet('branch_id');

/**
 * Daftar aksi massal.
 * Aksi cetak-label memunculkan panel tambahan (jenis kertas + model).
 */
$actions = [
    'cetak-label'         => 'Cetak Label',
    'tampil-opac'         => 'Tampilkan di Opac',
    'karantina-eksemplar' => 'Karantina Eksemplar',
];

/**
 * Konfigurasi lengkap semua jenis kertas beserta model-model label-nya.
 *
 * Format:
 *   'paper_size_key' => [
 *       'label'  => 'Nama tampilan di dropdown',
 *       'group'  => 'Nama group optgroup',
 *       'models' => [
 *           'template_key' => 'Label model',
 *       ]
 *   ]
 *
 * template_key = nama file view tanpa prefix path dan tanpa .php
 * Contoh: 'cetak-label-a4-1' → Views/template/cetak-label-a4-1.php
 */
$paper_size_config = [
    // ── Kertas A4 ──────────────────────────────────────────────────────────
    'a4' => [
        'label'  => 'Kertas A4',
        'group'  => 'Kertas A4',
        'models' => [
            'cetak-label-a4-1'       => 'Model A4-1 (No. Panggil + Barcode)',
            'cetak-label-a4-2'       => 'Model A4-2 (No. Panggil + Barcode)',
            'cetak-label-a4-3'       => 'Model A4-3 (No. Panggil + Barcode + 1 Warna)',
            'cetak-label-a4-4'       => 'Model A4-4 (No. Panggil + Barcode + 1 Warna)',
            'cetak-label-a4-5'       => 'Model A4-5 (No. Panggil + Barcode)',
            // QR Code – fitur baru yang dipertahankan
            'cetak-label-a4-4-qrcode' => 'Model A4-QR (QR Code + No. Panggil)',
        ],
    ],
    // ── Kertas Label Roll ──────────────────────────────────────────────────
    'label-roll' => [
        'label'  => 'Kertas Label Roll',
        'group'  => 'Kertas Label Roll',
        'models' => [
            'cetak-label-lr1' => 'Model LR1 (No. Panggil + Barcode)',
            'cetak-label-lr2' => 'Model LR2 (No. Panggil + Barcode)',
            'cetak-label-lr3' => 'Model LR3 (No. Panggil + Barcode + 1 Warna)',
            'cetak-label-lr4' => 'Model LR4 (No. Panggil + Barcode + 1 Warna)',
            'cetak-label-lr5' => 'Model LR5 (No. Panggil Tanpa Barcode)',
            'cetak-label-lr6' => 'Model LR6 (No. Panggil Tanpa Barcode + 1 Warna)',
        ],
    ],
    // ── Kertas Barcode Roll ────────────────────────────────────────────────
    'barcode-roll' => [
        'label'  => 'Kertas Barcode Roll',
        'group'  => 'Kertas Label Roll',
        'models' => [
            'cetak-label-br1' => 'Model BR1 (Barcode)',
            'cetak-label-br2' => 'Model BR2 (Barcode + Judul)',
        ],
    ],
    // ── Kertas Label Tom & Jerry 107 ───────────────────────────────────────
    'label-tj107' => [
        'label'  => 'Kertas Label Tom & Jerry 107',
        'group'  => 'Kertas Label Stiker',
        'models' => [
            'cetak-label-tj107-1' => 'Model TJ107-1 (Hanya Barcode)',
        ],
    ],
    // ── Kertas Label Tom & Jerry 121 ───────────────────────────────────────
    'label-tj121' => [
        'label'  => 'Kertas Label Tom & Jerry 121',
        'group'  => 'Kertas Label Stiker',
        'models' => [
            'cetak-label-tj121-1' => 'Model TJ121-1 (No. Panggil + Barcode)',
            'cetak-label-tj121-2' => 'Model TJ121-2 (No. Panggil + Barcode)',
        ],
    ],
    // ── Kertas Label Golden Cock 121 ───────────────────────────────────────
    'label-gc121' => [
        'label'  => 'Kertas Label Golden Cock 121',
        'group'  => 'Kertas Label Stiker',
        'models' => [
            'cetak-label-gc121-1' => 'Model GC121-1 (No. Panggil + Barcode)',
            'cetak-label-gc121-2' => 'Model GC121-2 (No. Panggil + Barcode)',
            'cetak-label-gc121-3' => 'Model GC121-3 (No. Panggil + Barcode + 1 Warna)',
            'cetak-label-gc121-4' => 'Model GC121-4 (No. Panggil + Barcode + 1 Warna)',
        ],
    ],
];

// Encode ke JSON agar bisa dikonsumsi JavaScript tanpa request AJAX tambahan
$paper_size_json = json_encode($paper_size_config, JSON_UNESCAPED_UNICODE);
?>
<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<style>
    /* Samakan tinggi semua tombol prepend dengan select */
    #cetak_panel .input-group-prepend .btn,
    .aksi-bar .input-group-prepend .btn {
        height     : 38px;
        line-height: 1;
        white-space: nowrap;
        font-size  : 13px;
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>

<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-server icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Eksemplar
                    <div class="page-title-subheading">Daftar semua Eksemplar</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('Eksemplar') ?>">
                                <i class="fa fa-home"></i> Home
                            </a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Eksemplar</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">

        <!-- ── Card Header ──────────────────────────────────────────────── -->
        <div class="card-header">
            <i class="header-icon lnr-list icon-gradient bg-plum-plate"></i>
            Daftar Eksemplar
            <div class="btn-actions-pane-right actions-icon-btn">
                <?php if (is_allowed('eksemplar/create')) : ?>
                    <a href="<?= base_url('eksemplar/create?slug=' . $slug) ?>" class="btn btn-success">
                        <i class="fa fa-plus"></i> Tambah Eksemplar
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- ── Card Footer: Panel Aksi Massal ──────────────────────────── -->
        <div class="card-footer">

            <!-- ── Baris 1: Pilih Aksi + Tombol Proses ── -->
            <div class="d-flex align-items-center flex-wrap" style="gap:6px; padding: 8px 0 4px 0;">
                <div class="input-group" style="width:280px; flex-shrink:0;">
                    <div class="input-group-prepend">
                        <span class="btn btn-secondary">Pilih Aksi</span>
                    </div>
                    <select class="form-control" id="action" name="action">
                        <option value="">-- Pilih Aksi --</option>
                        <?php foreach ($actions as $key => $value) : ?>
                            <option value="<?= esc($key) ?>"><?= esc($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button class="btn btn-primary" id="btnProcess2" type="button" style="height:38px; padding: 0 16px; flex-shrink:0;">
                    <i class="fa fa-check"></i> Proses
                </button>
            </div>

            <!-- ── Baris 2: Opsi Cetak (muncul jika aksi = Cetak Label) ── -->
            <div id="cetak_panel" style="display:none; padding: 8px 0 4px 0;">
                <div class="d-flex align-items-center flex-wrap" style="gap:6px;">

                    <!-- Jenis Kertas -->
                    <div class="input-group" style="width:280px; flex-shrink:0;">
                        <div class="input-group-prepend">
                            <span class="btn btn-secondary">Jenis Kertas</span>
                        </div>
                        <select class="form-control" id="paper_size" name="paper_size">
                            <option value="">-- Pilih Jenis Kertas --</option>
                            <optgroup label="Kertas A4">
                                <option value="a4">Kertas A4</option>
                            </optgroup>
                            <optgroup label="Kertas Label Roll">
                                <option value="label-roll">Kertas Label Roll</option>
                                <option value="barcode-roll">Kertas Barcode Roll</option>
                            </optgroup>
                            <optgroup label="Kertas Label Stiker">
                                <option value="label-tj107">Tom &amp; Jerry 107</option>
                                <option value="label-tj121">Tom &amp; Jerry 121</option>
                                <option value="label-gc121">Golden Cock 121</option>
                            </optgroup>
                        </select>
                    </div>

                    <!-- Model Label (muncul setelah kertas dipilih) -->
                    <div class="input-group" id="label_model_wrapper" style="display:none; width:320px; flex-shrink:0;">
                        <div class="input-group-prepend">
                            <span class="btn btn-secondary">Model Label</span>
                        </div>
                        <select class="form-control" id="label_model" name="label_model">
                            <option value="">-- Pilih Model --</option>
                        </select>
                    </div>

                </div>

                <!-- Info ringkas pilihan aktif -->
                <div id="model_info_row" style="display:none; margin-top:4px;">
                    <small class="text-muted">
                        <i class="fa fa-info-circle text-primary"></i>
                        <span id="model_info_text"></span>
                    </small>
                </div>
            </div>
            <!-- end #cetak_panel -->

        </div>
        <!-- end card-footer -->

        <!-- ── Card Body: Tabel DataTable ─────────────────────────────── -->
        <div class="card-body">
            <form name="form_items" id="form_items">
                <table style="width:100%;" id="tbl_data"
                       class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center" width="35">No</th>
                            <th class="text-center" width="35">
                                <input type="checkbox" class="check_data" title="Pilih Semua">
                            </th>
                            <th class="text-center" width="100">No. Barcode</th>
                            <th class="text-center" width="100">Tanggal Pengadaan</th>
                            <th class="text-center" width="100">No. Induk</th>
                            <th class="text-center" style="min-width: 300px;">Data Bibliografis</th>
                            <th class="text-center">DRM</th>
                            <th class="text-center">Karantina</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="100">Lokasi</th>
                            <th class="text-center" width="80">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </form>
        </div>

    </div>
</div>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
$(document).ready(function () {

    // ── SweetAlert Flashdata ───────────────────────────────────────────────
    <?php if (session()->getFlashdata('swal_icon')) : ?>
        Swal.fire({
            icon            : 'success',
            title           : '<?= session()->getFlashdata('swal_title') ?>',
            html            : '<?= session()->getFlashdata('swal_html') ?? session()->getFlashdata('swal_text') ?>',
            showConfirmButton: false,
            timer           : 3000
        });
    <?php endif; ?>

    // ── Konfigurasi kertas & model (di-inject dari PHP) ────────────────────
    var paperSizeConfig = <?= $paper_size_json ?>;

    // ── DataTable ─────────────────────────────────────────────────────────
    var t = $('#tbl_data').DataTable({
        processing : true,
        serverSide : true,
        scrollX    : true,
        scrollCollapse: true,       
        ajax       : { url: '<?= site_url('api/eksemplar/datatable/0/'.$catalog->ID ) ?>' },
        dom: "<'row mb-2'<'col-md-6 col-sm-12 text-left'l><'col-md-6 col-sm-12 text-right'f>>" +
                   "<'row'<'col-md-12'tr>>" +
                   "<'row mt-2'<'col-md-5 col-sm-12 text-left'i><'col-md-7 col-sm-12 d-flex justify-content-end'p>>",
                   
            "pagingType": "full_numbers",
            "oLanguage": {
                "sSearch": "<i class='fa fa-search'></i> _INPUT_",
                "sLengthMenu": "_MENU_",
                "oPaginate": {
                    "sNext": "<i class='fa fa-chevron-right'></i>",
                    "sPrevious": "<i class='fa fa-chevron-left'></i>",
                    "sLast": "<i class='fa fa-chevron-double-right'></i>",
                    "sFirst": "<i class='fa fa-chevron-double-left'></i>",
                }
            },
        pagingType : 'full_numbers',
        oLanguage  : {
            sSearch    : "<i class='fa fa-search'></i> _INPUT_",
            sLengthMenu: "_MENU_",
            oPaginate  : {
                sNext    : "<i class='fa fa-chevron-right'></i>",
                sPrevious: "<i class='fa fa-chevron-left'></i>",
                sLast    : "<i class='fa fa-chevron-double-right'></i>",
                sFirst   : "<i class='fa fa-chevron-double-left'></i>"
            }
        },
        columns: [
            {     data: 'no',
                    className: 'text-center',
                    searchable: false, // Wajib false
                    orderable: false },
            { data: 'ID',                  className: 'text-center', orderable: false },
            { data: 'NomorBarcode',        className: 'text-left'   },
            { data: 'TanggalPengadaan'                               },
            { data: 'NoInduk'                                        },
            { data: 'Catalog_id'                                     },
            { data: 'ISDRM',               className: 'text-center', orderable: false },
            { data: 'IsQUARANTINE',        className: 'text-center', orderable: false },
            { data: 'StatusName',          className: 'text-center'  },
            { data: 'LocationLibraryName', className: 'text-center'  },
            { data: 'action',              className: 'text-center', orderable: false }
        ],
        drawCallback: function () {
            $('[data-toggle="tooltip"]').tooltip();
            $('.apply-status').bootstrapToggle();
            $('.apply-status').on('change', function () {
                var url       = $(this).attr('data-href');
                var field     = $(this).attr('data-field');
                var value     = $(this).is(':checked');
                $.ajax({ url: url, type: 'POST', data: 'field=' + field + '&value=' + value })
                    .done(function (res) {
                        Swal.fire({
                            title           : res.error == false ? 'Berhasil' : 'Gagal',
                            html            : res.message,
                            icon            : res.error == false ? 'success' : 'error',
                            showConfirmButton: false,
                            timer           : 5000
                        });
                    })
                    .fail(function () {
                        Swal.fire({
                            title           : 'Oups',
                            text            : 'Maaf, terjadi kesalahan. Coba beberapa saat lagi atau hubungi Admin',
                            icon            : 'error',
                            showConfirmButton: false,
                            timer           : 5000
                        });
                    });
            });
        }
    });

    // ── Tampil/Sembunyikan panel cetak ────────────────────────────────────
    $('#action').on('change', function () {
        if ($(this).val() === 'cetak-label') {
            $('#cetak_panel').slideDown(200);
        } else {
            $('#cetak_panel').slideUp(200);
            resetCetakPanel();
        }
    });

    // Reset semua pilihan dalam panel cetak
    function resetCetakPanel() {
        $('#paper_size').val('');
        $('#label_model').html('<option value="">-- Pilih Model --</option>');
        $('#label_model_wrapper').hide();
        $('#model_info_row').hide();
        $('#model_info_text').text('');
    }

    // ── Isi dropdown Model saat Jenis Kertas berubah ──────────────────────
    $('#paper_size').on('change', function () {
        var paperKey  = $(this).val();
        var $modelSel = $('#label_model');

        // Reset model
        $modelSel.html('<option value="">-- Pilih Model --</option>');
        $('#model_info_row').hide();
        $('#model_info_text').text('');

        if (!paperKey || !paperSizeConfig[paperKey]) {
            $('#label_model_wrapper').slideUp(200);
            return;
        }

        var models = paperSizeConfig[paperKey].models;
        $.each(models, function (modelKey, modelLabel) {
            $modelSel.append(
                $('<option>', { value: modelKey, text: modelLabel })
            );
        });

        $('#label_model_wrapper').slideDown(200);
    });

    // ── Info model yang dipilih ───────────────────────────────────────────
    $('#label_model').on('change', function () {
        var modelText = $(this).find('option:selected').text();
        var paperText = $('#paper_size').find('option:selected').text();

        if ($(this).val()) {
            $('#model_info_text').text(paperText + '  →  ' + modelText);
            $('#model_info_row').show();
        } else {
            $('#model_info_row').hide();
        }
    });

    // ── Tombol Proses ─────────────────────────────────────────────────────
    $('#btnProcess2').on('click', function () {
        var action     = $('#action').val();
        var paperSize  = $('#paper_size').val();
        var labelModel = $('#label_model').val();

        // Validasi aksi
        if (!action) {
            Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Silakan pilih aksi terlebih dahulu!', showConfirmButton: true });
            return false;
        }

        // Validasi khusus cetak label
        if (action === 'cetak-label') {
            if (!paperSize) {
                Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Silakan pilih jenis kertas terlebih dahulu!', showConfirmButton: true });
                return false;
            }
            if (!labelModel) {
                Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Silakan pilih model label terlebih dahulu!', showConfirmButton: true });
                return false;
            }
        }

        // Kumpulkan ID eksemplar yang dicentang
        var checkedItems = [];
        $('#tbl_data tbody input.check:checked').each(function () {
            checkedItems.push($(this).val());
        });

        if (checkedItems.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Silakan pilih minimal satu eksemplar!', showConfirmButton: true });
            return false;
        }

        // URL tujuan berdasarkan aksi
        var urlMap = {
            'cetak-label'        : '<?= base_url('eksemplar/print_label') ?>',
            'tampil-opac'        : '<?= base_url('eksemplar/proses_opac') ?>',
            'karantina-eksemplar': '<?= base_url('eksemplar/proses_karantina') ?>'
        };

        var targetUrl = urlMap[action] || '';
        if (!targetUrl) return false;

        // Submit via virtual form
        var form = $('<form>', { method: 'post', action: targetUrl });
        form.append($('<input>', { type: 'hidden', name: 'eksemplar_ids', value: checkedItems.join(',') }));

        if (action === 'cetak-label') {
            // eksemplar_tpl = key template (misal: cetak-label-a4-2, cetak-label-lr3, dst.)
            // Controller akan memetakan key ini ke file view yang sesuai
            form.append($('<input>', { type: 'hidden', name: 'eksemplar_tpl', value: labelModel  }));
            form.append($('<input>', { type: 'hidden', name: 'paper_size',    value: paperSize   }));
        }

        form.appendTo('body').submit();
    });

    // ── Checkbox: Pilih Semua ─────────────────────────────────────────────
    $(document).on('change', '.check_data', function () {
        $('#tbl_data tbody .check').prop('checked', $(this).prop('checked'));
    });

    $('#tbl_data tbody').on('change', '.check', function () {
        if (!$(this).prop('checked')) {
            $('.check_data').prop('checked', false);
        } else {
            var total   = $('#tbl_data tbody .check').length;
            var checked = $('#tbl_data tbody .check:checked').length;
            $('.check_data').prop('checked', total === checked);
        }
    });

    t.on('draw', function () {
        $('.check_data').prop('checked', false);
    });

});
</script>
<?= $this->endSection('script'); ?>