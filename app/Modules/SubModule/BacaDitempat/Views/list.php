<?php
$request = service('request');
// Replace null coalescing operator for compatibility with older PHP versions
$slug = $request->getGet('slug');
$slug = ($slug !== null) ? $slug : '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-note icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Baca Ditempat - Anggota
                    <div class="page-title-subheading">Daftar semua Baca Ditempat Anggota</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('bacaditempat') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item" aria-current="page">Baca Ditempat</li>
                      
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
        <li class="nav-item">
            <a class="btn-header-argon active" href="<?= base_url('baca-di-tempat') ?>">
                <span>Anggota</span>
            </a>
        </li>
        <li class="nav-item">
            <a style="padding-left: 10px !important;" class="btn-header-argon" href="<?= base_url('baca-di-tempat/non_anggota') ?>">
                <span>Non Anggota</span>
            </a>
        </li>
    </ul>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Baca Ditempat
            <div class="btn-actions-pane-right actions-icon-btn">
                <a data-bs-toggle="modal" data-bs-target="#modalPengembalian" data-toggle="modal" data-target="#modalPengembalian" href="javascript:void(0);" class="btn btn-primary btn-sm"><i class="pe-7s-plus"></i> Tambah</a>
            </div>
        </div>
        <div class="card-body">
            <?= get_message('message'); ?>
            <table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" width="35">No</th>
                        <th class="text-center" width="120">Tgl. Kunjungan</th>
                        <th class="text-center">Nama Anggota</th>
                        <th class="text-center" width="100">Nomor Anggota</th>
                        <th class="text-center">Nomor Barcode Koleksi</th>
                        <th class="text-center">Lokasi Perpustakaan</th>
                        <th class="text-center">Lokasi Ruang</th>
                        <th class="text-center" width="120">Status Pengembalian</th>
                        <th class="text-center" width="50">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<!-- Modal Pengembalian -->
<div class="modal fade" id="modalPengembalian" tabindex="-1" role="dialog" aria-labelledby="modalPengembalianLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPengembalianLabel">Proses Pengembalian Buku</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="alertPengembalian" class="alert" style="display:none;"></div>
                <div class="form-group">
                    <label for="inputBarcode">Nomor Barcode Buku <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="inputBarcode" placeholder="Scan atau ketik nomor barcode">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnProsesPengembalian">
                    <span id="btnProsesText">Proses</span>
                    <span id="btnProsesSpinner" class="spinner-border spinner-border-sm" role="status" style="display:none;"></span>
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    var t;
    $(document).ready(function() {
        t = $('#tbl_data').DataTable({
            "processing": true,
            "serverSide": true,
            "scrollX": true,
            "scrollCollapse": true,
            "ajax": {
                "url": '<?php echo site_url('api/baca-di-tempat/datatable') ?>',
            },
             "dom": "<'row mb-2'<'col-md-6 col-sm-12 text-left'l><'col-md-6 col-sm-12 text-right'f>>" +
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
            "columns": [{
                    data: 'no',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'VisitDate',
                    className: 'text-center'
                },
                {
                    data: 'Member_name'
                },
                {
                    data: 'Member_no'
                },
                {
                    data: 'Barcode_no'
                },
                {
                    data: 'LocationLibrary_name',
                    className: 'text-center'
                },
                {
                    data: 'Location_name',
                    className: 'text-center'
                },
                {
                    data: 'Is_return',
                    className: 'text-center'
                },
                {
                    data: 'action',
                    className: 'text-center',
                    orderable: false
                },
            ],
            "columnDefs": [{
                    targets: [0, 8],
                    searchable: false
                },
                {
                    targets: [0, 8],
                    orderable: false
                },
            ],
            "order": [
                [1, "desc"]
            ],
            "drawCallback": function(data, type, full, meta) {
                var api = this.api();
                var data = api.rows().data();
                $('[data-toggle="tooltip"]').tooltip();
            },
            "initComplete": function(settings, json) {
                var $searchInput = $('div.dataTables_filter input');
                $searchInput.unbind();
                $searchInput.bind('keyup', function(e) {
                    if (e.keyCode == 13) {
                        if (this.value.length == 0) {
                            t.search('').draw();
                        }

                        if (this.value.length >= 3) {
                            t.search(this.value).draw();
                        }
                    }
                });
            }
        });
    });

    $('#modalPengembalian').on('show.bs.modal', function() {
        $('#inputBarcode').val('');
        $('#alertPengembalian').hide();
    });

    $('#modalPengembalian').on('shown.bs.modal', function() {
        $('#inputBarcode').focus();
    });

    $('#inputBarcode').on('keypress', function(e) {
        if (e.which == 13) $('#btnProsesPengembalian').click();
    });

    $('#btnProsesPengembalian').on('click', function() {
        var barcode = $('#inputBarcode').val().trim();
        if (!barcode) {
            $('#alertPengembalian').removeClass('alert-success alert-danger').addClass('alert-warning').text('Nomor barcode tidak boleh kosong.').show();
            return;
        }
        $('#btnProsesText').text('Memproses...');
        $('#btnProsesSpinner').show();
        $('#btnProsesPengembalian').prop('disabled', true);

        $.ajax({
            url: '<?= site_url('api/baca-di-tempat/process_return') ?>',
            method: 'POST',
            data: { barcode: barcode },
            dataType: 'json',
            success: function(res) {
                $('#alertPengembalian').removeClass('alert-warning alert-danger').addClass('alert-success').text(res.message).show();
                t.ajax.reload(null, false);
            },
            error: function(xhr) {
                var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan, silahkan coba lagi.';
                $('#alertPengembalian').removeClass('alert-warning alert-success').addClass('alert-danger').text(msg).show();
            },
            complete: function() {
                $('#btnProsesText').text('Proses');
                $('#btnProsesSpinner').hide();
                $('#btnProsesPengembalian').prop('disabled', false);
                $('#inputBarcode').val('').focus();
            }
        });
    });

    $("body").on("click", ".remove-data", function() {
        var url = $(this).attr('data-href');
        console.log(url);
        Swal.fire({
            title: '<?= lang('App.swal.are_you_sure') ?>',
            text: "<?= lang('App.swal.can_not_be_restored') ?>",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#dd6b55',
            confirmButtonText: '<?= lang('App.btn.yes') ?>',
            cancelButtonText: '<?= lang('App.btn.no') ?>'
        }).then((result) => {
            if (result.value) {
                window.location.href = url;
            }
        });
        return false;
    });
</script>
<?= $this->endSection('script'); ?>