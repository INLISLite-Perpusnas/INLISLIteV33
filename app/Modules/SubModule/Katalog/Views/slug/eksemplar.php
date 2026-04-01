<?php
$request = service('request');
$slug = $request->getGet('slug');
$branch_id = $request->getGet('branch_id');
$actions = array(
    'cetak-label-a4-4-qrcode' => 'Cetak Label A4-1 (Qrcode + No. Panggil)',
    'cetak-label-a4-2' => 'Cetak Label A4-2 (Barcode + No. Panggil)',
    'cetak-label-a4-3' => 'Cetak Label A4-3 (Barcode + No. Panggil + 1 Warna)',
    'cetak-label-a4-4' => 'Cetak Label A4-4 (Barcode + No. Panggil + 1 Warna)',
    'tampil-opac' => 'Tampilkan di Opac',
    'karantina-eksemplar' => 'Karantina Eksemplar',
);
?>

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
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('Eksemplar') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Eksemplar</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Daftar Eksemplar
            <div class="btn-actions-pane-right actions-icon-btn">
                <?php if (is_allowed('eksemplar/create')) : ?>
                    <a href="<?= base_url('eksemplar/create?slug=&catalog_id=' . $catalog->ID) ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i>
                        Tambah Eksemplar
                    </a>
                <?php endif; ?>
                <a href="<?= base_url('report/eksemplar') ?>" class="btn btn-secondary" title=""><i class="fa fa-file-excel"></i>
                    Ekspor Laporan
                </a>
            </div>
        </div>
        <div class="card-footer">
            <div class="form-group col-md-6 p-0 pt-2">
                <div class="select-wrapper input-group mt-2 mb-2">
                    <div class="input-group-prepend">
                        <span class="btn btn-secondary pt-2">Pilih Aksi</span>
                    </div>

                    <select class="form-control" name="action" id="action">
                        <option></option>
                        <?php foreach ($actions as $key => $value) : ?>
                            <option value="<?= $key ?>" <?= set_select('action', $key, false); ?>><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <button class="btn bg-primary text-white worksheet-btn-load" id="btnProcess2" type="button" style="min-width:80px"><i class="fa fa-check "></i> Proses </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form name="form_items" id="form_items">
                <table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center" width="35">
                                <input type="checkbox" class="check_data" title="Pilih Semua">
                            </th>
                            <th class="text-center" width="100">No. Barcode</th>
                            <th class="text-center" width="100">Tanggal Pengadaan</th>
                            <th class="text-center" width="100">No. Induk</th>
                            <th class="text-center" width="">Data Bibliografis</th>
                            <th class="text-center" width="">OPAC</th>
                            <th class="text-center" width="">DRM</th>
                            <th class="text-center" width="">Karantina</th>
                            <th class="text-center" width="80">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>

<?= $this->section('script'); ?>
<script>
    // --- Inisialisasi SweetAlert Global ---
    $(document).ready(function() {
        <?php if (session()->getFlashdata('swal_icon')) : ?>
            Swal.fire({
                type: '<?= session()->getFlashdata('swal_icon') ?>', 
                icon: '<?= session()->getFlashdata('swal_icon') ?>', // Dukungan untuk SWAL 2 versi terbaru
                title: '<?= session()->getFlashdata('swal_title') ?>',
                html: '<?= session()->getFlashdata('swal_html') ?? session()->getFlashdata('swal_text') ?>',
                showConfirmButton: false,
                timer: 3000
            });
        <?php endif; ?>
    });

    $('#branch_id').select2({
        maximumInputLength: 3
    });

    var t;
    $(document).ready(function() {
        // --- Inisialisasi DataTables ---
        t = $('#tbl_data').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": '<?php echo site_url('api/eksemplar/datatable/0/' . $catalog->ID) ?>',
            },
            "dom": "<'row'<'col-md-6 col-sm-8 col-xs-12 text-left'f><'col-md-6 col-sm-4 col-xs-12 d-none d-sm-block text-right'p>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12 text-right'i>>",
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
            "columns": [
                { data: 'ID', className: 'text-center', orderable: false },
                { data: 'NomorBarcode', className: 'text-left' },
                { data: 'TanggalPengadaan' },
                { data: 'NoInduk' },
                { data: 'Catalog_id' },
                { data: 'IsOPAC', className: 'text-center', orderable: false },
                { data: 'ISDRM', className: 'text-center', orderable: false },
                { data: "IsQUARANTINE", className: 'text-center', orderable: false },
                { data: 'action', className: 'text-center', orderable: false },
            ],
            "drawCallback": function(data, type, full, meta) {
                var api = this.api();
                $('[data-toggle="tooltip"]').tooltip();
                $('.apply-status').bootstrapToggle();
                
                // Toggle per baris data
                $(".apply-status").off('change').on('change', function() {
                    var url = $(this).attr('data-href');
                    var field = $(this).attr('data-field');
                    var value = $(this).is(':checked');
                    var data_post = 'field=' + field + '&value=' + value;

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: data_post,
                    })
                    .done(function(res) {
                        if (res.error == false) {
                            Swal.fire({
                                title: 'Berhasil',
                                html: res.message,
                                type: 'success',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 3000,
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal',
                                text: res.message,
                                type: 'error',
                                icon: 'error',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    })
                    .fail(function(res) {
                        Swal.fire({
                            title: 'Oops',
                            text: 'Maaf, terjadi kesalahan. Coba beberapa saat lagi atau hubungi Admin',
                            type: 'error',
                            icon: 'error',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    });
                });
            }
        });

        // --- Handle Tombol Proses Aksi Massal (Cetak / OPAC / Karantina) ---
        $('#btnProcess2').on('click', function() {
            var action = $('#action').val();
            
            if (!action) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Silakan pilih aksi terlebih dahulu!',
                    type: 'warning',
                    icon: 'warning',
                    showConfirmButton: true
                });
                return false;
            }
            
            var checkedItems = [];
            $('#tbl_data tbody input.check:checked').each(function() {
                checkedItems.push($(this).val());
            });

            if (checkedItems.length === 0) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Silakan pilih minimal satu eksemplar!',
                    type: 'warning',
                    icon: 'warning',
                    showConfirmButton: true
                });
                return false;
            }
            
            var targetUrl = '';
            
            // Penentuan URL Tujuan
            if (action.includes('cetak-label')) {
                targetUrl = '<?= base_url('eksemplar/print_label') ?>';
            } else if (action === 'tampil-opac') {
                targetUrl = '<?= base_url('eksemplar/proses_opac') ?>';
            } else if (action === 'karantina-eksemplar') {
                targetUrl = '<?= base_url('eksemplar/proses_karantina') ?>';
            }

            // Pembuatan form virtual pengganti AJAX untuk menghindari error 303 Redirect
            if (targetUrl !== '') {
                var form = $('<form>', {
                    'method': 'post',
                    'action': targetUrl,
                    'id': 'actionForm'
                });
                
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'eksemplar_ids',
                    'value': checkedItems.join(',')
                }));
                
                if (action.includes('cetak-label')) {
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'eksemplar_tpl',
                        'value': action
                    }));
                }
                
                form.appendTo('body').submit();
            }
        });

        // --- Logika Checkbox "Pilih Semua" ---
        $(document).on('change', '.check_data', function() {
            var isChecked = $(this).prop('checked');
            $('#tbl_data tbody .check').prop('checked', isChecked);
        });

        $('#tbl_data tbody').on('change', '.check', function() {
            if (!$(this).prop('checked')) {
                $('.check_data').prop('checked', false);
            } else {
                var totalCheckbox = $('#tbl_data tbody .check').length;
                var totalChecked = $('#tbl_data tbody .check:checked').length;
                if(totalCheckbox == totalChecked) {
                    $('.check_data').prop('checked', true);
                }
            }
        });

        t.on('draw', function() {
            $('.check_data').prop('checked', false);
        });
    });
</script>
<?= $this->endSection('script'); ?>