<?php
$request = service('request'); ?>

<?php $core = config('Core');
$layout = (!empty($core->layout_backend)) ? $core->layout_backend : 'hamkamannan\adminigniter\Views\layout\backend\main'; ?>
<?= $this->extend($layout); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-id icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Anggota
                    <div class="page-title-subheading">Daftar semua Anggota </div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('anggota') ?>"><i class="fa fa-home"></i>
                                <?= lang('Anggota.label.home') ?></a></li>
                        <li class="active breadcrumb-item" aria-current="page">Laporan Anggota </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- <form  class="formhapus" method="post" multiple="multiple" action="<?= base_url('api/anggota/hapusall'); ?>"> -->
    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Laporan Anggota
            <div class="btn-actions-pane-right actions-icon-btn">

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
                            <th>Nama Anggota</th>
                            <th>No. Anggota</th>
                            <th>Email</th>
                            <th width="100">Tgl. Register</th>
                            <th width="100">Tgl. Berakhir</th>
                            <th width="130">Status Anggota</th>

                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>

<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>

<script>
    var t;
    $(document).ready(function() {
        t = $('#tbl_data').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": '<?php echo site_url('api/report/member_datatable/0'); ?>',
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
            "columns": [{
                    data: 'cid',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'FullName'
                },
                {
                    data: 'MemberNo'
                },
                {
                    data: 'Email'
                },

                {
                    data: 'RegisterDate',
                    className: 'text-center'
                },
                {
                    data: 'EndDate',
                    className: 'text-center'
                },
                {
                    data: 'StatusAnggota',
                    className: 'text-center'
                },
                // {data: 'action', className: 'text-center', searchable: false, orderable: false},
            ],
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            "order": [
                [2, "asc"]
            ],
            "drawCallback": function(data, type, full, meta) {
                var api = this.api();
                var data = api.rows().data();

                $.each(data, function(i, row) {
                    $("#lazy" + row.id).Lazy();
                });

                $('.image-link').magnificPopup({
                    type: 'image'
                });
            },
            "initComplete": function(settings, json) {
                // var $searchInput = $('div.dataTables_filter input');
                // $searchInput.unbind();
                // $searchInput.bind('keyup', function(e) {
                // 	if(e.keyCode == 13){
                // 		if(this.value.length == 0){
                // 			t.search('').draw();
                // 		}

                // 		if(this.value.length >= 3){
                // 			t.search( this.value ).draw();
                // 		}
                // 	} 
                // });
            }
        });
    });
</script>

<?= $this->endSection('script'); ?>