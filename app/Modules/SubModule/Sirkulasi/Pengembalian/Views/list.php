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
                <div>Pengembalian
                    <div class="page-title-subheading">Daftar semua Pengembalian</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('pengembalian') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item" aria-current="page">Pengembalian</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>



    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Daftar Pengembalian
            <div class="btn-actions-pane-right actions-icon-btn">
                <?php if (is_allowed('sirkulasi-pengembalian/create')) : ?>
                    <!-- <a data-toggle="modal" data-target="#modal_create" href="javascript:void(0);" class="btn btn-success" title="Tambah"><i class="fa fa-plus"></i> Pengembalian</a> -->
                    <a href="<?= base_url('sirkulasi-pengembalian/create') ?>" class="btn btn-success" title="Tambah"><i class="fa fa-plus"></i> Pengembalian</a>
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
                        <th class="text-center" width="100">Tgl. Pinjam/ Jatuh Tempo</th>
                        <th class="text-center">Tgl. Kembali</th>
                        <th class="text-center">Hari Terlambat</th>
                        <th class="text-center" width="120">Lokasi Perpustakaan</th>
                        <th class="text-center" width="100">Buku Digital</th>
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
<script>
    // Index 8 adalah CollectionLoan_id yang berisi info Member & No Transaksi
    var groupColumn = 8;
    var t;

    $(document).ready(function() {
        t = $('#tbl_data').DataTable({
            "processing": true,
            "serverSide": true,
            "scrollCollapse": true,
            "scrollX": true,
            "ajax": {
                "url": '<?php echo site_url('api/sirkulasi-pengembalian/datatable/' . $slug) ?>',
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
                }, // 0
                {
                    data: 'NomorBarcode',
                    className: 'text-nowrap',
                    render: function(data, type, row) {
                        if (!data) {
                            return '-';
                        }

                        // Ambil text barcode dari HTML
                        var barcode = $('<div>').html(data).text().trim();

                        // Escape
                        var barcodeAttr = $('<div>').text(barcode).html();

                        return '<span class="barcode-copy"' +
                            ' data-barcode="' + barcodeAttr + '"' +
                            ' title="Klik untuk menyalin barcode"' +
                            ' style="' +
                            'display:inline-flex;' +
                            'align-items:center;' +
                            'gap:5px;' +
                            'padding:3px 5px;' +
                            'border:1px solid transparent;' +
                            'border-radius:4px;' +
                            'background:transparent;' +
                            'box-shadow:none;' +
                            'cursor:pointer;' +
                            'user-select:none;' +
                            'transition:all .15s ease;' +
                            '"' +
                            ' onmouseover="' +
                            'this.style.background=\'#f8f9fa\';' +
                            'this.style.borderColor=\'#dee2e6\';' +
                            'this.style.boxShadow=\'0 2px 5px rgba(0,0,0,.12)\';' +
                            'this.style.transform=\'translateY(-1px)\';' +
                            '"' +
                            ' onmouseout="' +
                            'this.style.background=\'transparent\';' +
                            'this.style.borderColor=\'transparent\';' +
                            'this.style.boxShadow=\'none\';' +
                            'this.style.transform=\'translateY(0)\';' +
                            '"' +
                            '>' +
                            '<span>' + barcodeAttr + '</span>' + '<i class="fa fa-copy text-primary barcode-copy-icon" style="opacity:.65;"></i>' +
                            '</span>';
                    }
                }, // 1
                {
                    data: 'Title'
                }, // 2
                {
                    data: 'LoanDate',
                    className: 'text-center'
                }, // 3
                {
                    data: 'ActualReturn',
                    className: 'text-center'
                }, // 4
                {
                    data: 'LateDays',
                    className: 'text-center'
                }, // 5
                {
                    data: 'LocationLibrary'
                }, // 6
                {
                    data: 'ISDRM',
                    className: 'text-center'
                }, // 7
                {
                    data: 'CollectionLoan_id',
                    visible: false
                }, // 8 (Untuk Grouping)
                {
                    data: 'Fullname',
                    visible: false
                }, // 9
                {
                    data: 'DueDate',
                    visible: false
                }, // 10
                {
                    data: 'Publisher',
                    visible: false
                }, // 11
            ],
            "columnDefs": [{
                    targets: [0, 8, 9, 10, 11],
                    searchable: false
                },
                {
                    targets: [0, 2, 3, 5, 6, 7],
                    orderable: false
                }
            ],
            "order": [
                [groupColumn, "desc"]
            ],
            "drawCallback": function(settings) {
                var api = this.api();
                var rows = api.rows({
                    page: 'current'
                }).nodes();
                var last = null;

                api.column(groupColumn, {
                        page: 'current'
                    })
                    .data()
                    .each(function(group, i) {
                        if (last !== group) {
                            $(rows).eq(i).before(
                                '<tr class="group"><td colspan="8">' + group + '</td></tr>'
                            );
                            last = group;
                        }
                    });
            },
            "initComplete": function(settings, json) {
                var $searchInput = $('div.dataTables_filter input');
                $searchInput.unbind();
                $searchInput.bind('keyup', function(e) {
                    if (e.keyCode == 13) {
                        t.search(this.value).draw();
                    }
                });
            }
        });
    });

    $(document).on('click', '.barcode-copy', function() {
        var $element = $(this);
        var barcode = $element.attr('data-barcode');
        var $icon = $element.find('.barcode-copy-icon');

        navigator.clipboard.writeText(barcode).then(function() {

            // Ubah icon copy -> check
            $icon
                .removeClass('fa-copy text-primary')
                .addClass('fa-check text-success');

            // Kembalikan setelah 1 detik
            setTimeout(function() {
                $icon
                    .removeClass('fa-check text-success')
                    .addClass('fa-copy text-primary');
            }, 1000);

        }).catch(function(err) {
            console.error('Gagal menyalin barcode:', err);
        });
    });

    // Handle klik pada baris grup untuk sorting
    $('#tbl_data tbody').on('click', 'tr.group', function() {
        var currentOrder = t.order()[0];
        if (currentOrder[0] === groupColumn && currentOrder[1] === 'asc') {
            t.order([groupColumn, 'desc']).draw();
        } else {
            t.order([groupColumn, 'asc']).draw();
        }
    });
</script>
<?= $this->endSection('script'); ?>