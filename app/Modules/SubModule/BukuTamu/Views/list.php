<?php
$request = service('request');
$slug = $request->getGet('slug');
if ($slug === null) {
    $slug = '';
}
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
                <div>Buku Tamu - Anggota
                    <div class="page-title-subheading">Daftar semua Buku Tamu Anggota</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('bukutamu') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item" aria-current="page">Buku Tamu</li>
                        <li class="breadcrumb-item" aria-current="page">Anggota</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
        <li class="nav-item">
            <a class="btn-header-argon" href="<?= base_url('bukutamu') ?>">
                <span>Anggota</span>
            </a>
        </li>
        <li class="nav-item">
            <a style="padding-left: 10px !important;" class="btn-header-argon" href="<?= base_url('bukutamu/non_anggota') ?>">
                <span>Bukan Anggota </span>
            </a>
        </li>
        <li class="nav-item">
            <a style="padding-left: 10px !important;" class="btn-header-argon" href="<?= base_url('bukutamu/rombongan') ?>">
                <span>Rombongan</span>
            </a>
        </li>
    </ul>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Buku Tamu
            <div class="btn-actions-pane-right actions-icon-btn">

            </div>
        </div>
        <div class="card-body">
            <?= get_message('message'); ?>
            <table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" width="35">No</th>
                        <th class="text-center" width="120">Tgl. Kunjungan</th>
                        <th class="text-center" width="120">Nama Anggota</th>
                        <th class="text-center" width="100">Nomor Anggota</th>
                        <th class="text-center">Lokasi Perpustakaan</th>
                        <th class="text-center">Lokasi Ruang</th>
                        <th class="text-center" style="min-width: 150px;">Aksi</th>
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
    var t;
    $(document).ready(function() {
        t = $('#tbl_data').DataTable({
            "processing": true,
            "serverSide": true,
            "scrollX": true,
            "scrollCollapse": true,
            "ajax": {
                "url": '<?php echo site_url('api/bukutamu/datatable') ?>',
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
                    data: 'LocationLibrary_name',
                    className: 'text-center'
                },
                {
                    data: 'Location_name',
                    className: 'text-center'
                },
                {
                    data: 'action',
                    className: 'text-center',
                    orderable: false
                },
            ],
            "columnDefs": [{
                    targets: [0, 6],
                    searchable: false
                },
                {
                    targets: [0, 6],
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