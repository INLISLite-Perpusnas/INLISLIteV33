<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
    tr.group,
    tr.group:hover {
        background-color: #F0F3F5 !important;
    }

    tr.group td {
        font-weight: 700;
        font-size: 13px;
        color: #1B3878;
        padding: 10px 16px !important;
    }

    dl {
        display: grid;
        grid-template-columns: max-content auto;
    }

    dt {
        grid-column-start: 1;
        width: 100px;
        font-weight: normal;
    }

    dd {
        grid-column-start: 2;
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-refresh-2 icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Perpanjangan
                    <div class="page-title-subheading">Daftar semua Perpanjangan</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('sirkulasi-perpanjangan') ?>">
                                <i class="fa fa-home"></i> Home
                            </a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Perpanjangan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-list icon-gradient bg-plum-plate"></i> Daftar Perpanjangan
            <div class="btn-actions-pane-right actions-icon-btn">
                <?php if (is_allowed('sirkulasi-perpanjangan/create')) : ?>
                    <a href="<?= base_url('sirkulasi-perpanjangan/create') ?>" class="btn btn-success" title="Tambah">
                        <i class="fa fa-plus"></i> Perpanjangan
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <?= get_message('message'); ?>
            <table style="width: 100%;" id="tbl_data" class="table table-hover table-bordered">
                <thead class="bg-night-sky text-light">
                    <tr>
                        <th class="text-center" width="35">No</th>
                        <th class="text-center" width="100">No. Barcode</th>
                        <th class="text-center">Penerbit / Judul</th>
                        <th class="text-center" width="150">Tgl. Pinjam / <br>Jatuh Tempo</th>
                        <th class="text-center" width="130">Tgl. Perpanjang</th>
                        <th class="text-center" width="90">Perpanjang <br>Ke-</th>
                        <th class="text-center" width="110">Status</th>
                        <th class="text-center" width="120">Lokasi <br>Perpustakaan</th>
                        
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    // index 9 = CollectionLoan_id (hidden) — dipakai untuk group header
    var groupColumn = 9;
    var t;

    $(document).ready(function () {
        t = $('#tbl_data').DataTable({
            "processing": true,
            "serverSide": true,
			"scrollCollapse": true,
			"scrollX": true,
            "ajax": {
                "url": '<?= site_url('api/sirkulasi-perpanjangan/datatable/' . $slug) ?>',
            },
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
            "columns": [
                // 0
                { data: 'no',               className: 'text-center', orderable: false },
                // 1
                { data: 'NomorBarcode' },
                // 2
                { data: 'Title' },
                // 3
                { data: 'LoanDate',         className: 'text-center' },
                // 4
                { data: 'DateExtend',       className: 'text-center' },
                // 5
                { data: 'CountExtend',      className: 'text-center' },
                // 6
                { data: 'LoanStatus',       className: 'text-center' },
                // 7
                { data: 'LocationLibrary' },
                // 8
                { data: 'action',           className: 'text-center', orderable: false },
                // 9  — hidden, untuk group
                { data: 'CollectionLoan_id', visible: false },
                // 10 — hidden
                { data: 'Fullname',          visible: false },
                // 11 — hidden
                { data: 'DueDate',           visible: false },
                // 12 — hidden
                { data: 'PublishLocation',   visible: false },
                // 13 — hidden
                { data: 'Publisher',         visible: false },
                // 14 — hidden
                { data: 'PublishYear',       visible: false },
            ],
            "columnDefs": [
                { targets: [0, 7],       searchable: false },
                { targets: [0, 2, 3, 4], orderable: false  },
                { targets: 9,            visible: false },
                { targets: 10,           visible: false },
                { targets: 11,           visible: false },
                { targets: 12,           visible: false },
                { targets: 13,           visible: false },
                { targets: 14,           visible: false },
            ],
            "order": [[4, "desc"]],

            "drawCallback": function (settings) {
                var api  = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;

                api.column(groupColumn, { page: 'current' }).data().each(function (group, i) {
                    if (last !== group) {
                        var rowData  = api.row(i).data();
                        var fullname = rowData.Fullname ?? '';
                        var memberNo = group;

                        $(rows).eq(i).before(
                            '<tr class="group">' +
                                '<td colspan="9">' +
                                    '<i class="fa fa-id-card mr-1 text-secondary"></i> ' +
                                    memberNo +
                                    (fullname
                                        ? ' &nbsp;<span class="text-muted font-weight-normal">(' + fullname + ')</span>'
                                        : '') +
                                '</td>' +
                            '</tr>'
                        );
                        last = group;
                    }
                });

                $(".apply-status").bootstrapToggle();

                $(".apply-status").on('change', function () {
                    var url       = $(this).attr('data-href');
                    var field     = $(this).attr('data-field');
                    var value     = $(this).is(':checked');
                    var data_post = 'field=' + field + '&value=' + value;

                    $.ajax({ url: url, type: 'POST', data: data_post })
                        .done(function (res) {
                            if (res.error == false) {
                                Swal.fire({ title: 'Berhasil', html: res.message, type: 'success', showConfirmButton: false, timer: 5000 });
                            } else {
                                Swal.fire({ title: 'Gagal', text: res.message, type: 'error', showConfirmButton: false, timer: 5000 });
                            }
                        })
                        .fail(function () {
                            Swal.fire({ title: 'Oups', text: 'Maaf, terjadi kesalahan. Coba beberapa saat lagi atau hubungi Admin', type: 'error', showConfirmButton: false, timer: 5000 });
                        });
                });
            },

            "initComplete": function (settings, json) {
                var $searchInput = $('div.dataTables_filter input');
                $searchInput.unbind();
                $searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13) {
                        if (this.value.length === 0) t.search('').draw();
                        if (this.value.length >= 3)  t.search(this.value).draw();
                    }
                });
            }
        });
    });

    // Klik group row → toggle sort
    $('#tbl_data tbody').on('click', 'tr.group', function () {
        var currentOrder = t.order()[0];
        if (currentOrder[0] === groupColumn && currentOrder[1] === 'asc') {
            t.order([groupColumn, 'desc']).draw();
        } else {
            t.order([groupColumn, 'asc']).draw();
        }
    });

    // Konfirmasi hapus
    $("body").on("click", ".remove-data", function () {
        var url = $(this).attr('data-href');
        Swal.fire({
            title             : '<?= lang('App.swal.are_you_sure') ?>',
            text              : "<?= lang('App.swal.can_not_be_restored') ?>",
            type              : 'warning',
            showCancelButton  : true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor : '#dd6b55',
            confirmButtonText : '<?= lang('App.btn.yes') ?>',
            cancelButtonText  : '<?= lang('App.btn.no') ?>'
        }).then((result) => {
            if (result.value) window.location.href = url;
        });
        return false;
    });
</script>
<?= $this->endSection('script'); ?>