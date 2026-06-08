<?php
$request = service('request');
$date_from = $request->getGet('date_from') ?? '';
$date_to = $request->getGet('date_to') ?? '';
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
                    <i class="pe-7s-graph2 icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Laporan Kunjungan
                    <div class="page-title-subheading">Daftar Semua Kunjungan</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('auth') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Laporan</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Laporan Kunjungan </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="card-body pt-0">
            <h5 class="card-title">Periode Kunjungan</h5>
            <!-- <form action=""> -->
            <button class="btn btn-primary" id="reportrange">
                <i class="fa fa-calendar pr-1"></i>
                <span></span>
                <i class="fa pl-1 fa-caret-down"></i>
            </button>

            <button class="btn btn-primary" id="btnSearch">
                <i class="fa fa-search pr-1"></i> <span>Search</span>
            </button>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Laporan Kunjungan
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="<?= base_url('report/visitor_export'); ?>" class=" btn btn-success"><i class="fa fa-file-excel"></i> Export Excel</a>
            </div>
        </div>
        <div class="card-body">
            <table style="width: 100%;" id="tbl_visitor" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th width="35">No</th>
                        <th>Tanggal Kunjungan</th>
                        <th>Jumlah Kunjungan</th>
                        <th>Alamat IP</th>
                        <th>Kota</th>
                        <th>Negara</th>
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
        t = $('#tbl_visitor').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": '<?php echo site_url('api/report/visitor_datatable/' . $date_from . '/' . $date_to) ?>',
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
                    data: 'no',
                    orderable: false
                },
                {
                    data: 'timestamp'
                },
                {
                    data: 'hits'
                },
                {
                    data: 'ip_address'
                },
                {
                    data: 'ip_city'
                },
                {
                    data: 'ip_country'
                },
            ],
            "order": [
                [1, "desc"]
            ],
            "drawCallback": function(data, type, full, meta) {},
            "initComplete": function(settings, json) {
                var $searchInput = $('div.dataTables_filter input');
                $searchInput.unbind();
                $searchInput.bind('keyup', function(e) {
                    if (e.keyCode == 13) {
                        if (this.value.length == 0) {
                            t.draw();
                        }

                        if (this.value.length > 3) {
                            t.search(this.value).draw();
                        }
                    }
                });
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        var start = moment();
        var end = moment();

        var date_from, date_to, url;

        function cb(start, end) {
            $('#reportrange span').html(start.format('D/M/YYYY') + ' - ' + end.format('D/M/YYYY'));
            date_from = start.format('YYYY') + '-' + start.format('M').padStart(2, '0') + '-' + start.format('D').padStart(2, '0');
            date_to = end.format('YYYY') + '-' + end.format('M').padStart(2, '0') + '-' + end.format('D').padStart(2, '0');
            url = '<?= base_url('report/visitor') ?>' + '?date_from=' + date_from + '&date_to=' + date_to;
            console.log(url);
        }

        $('#reportrange').daterangepicker({
            startDate: start.format('D/M/YYYY'),
            endDate: end.format('D/M/YYYY'),
            showDropdowns: true,
            "opens": "right",
            ranges: {
                'Hari ini': [moment(), moment()],
                'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Minggu ini': [moment().startOf('week'), moment().endOf('week')],
                'Minggu lalu': [moment().subtract(1, 'week').startOf('week'), moment().subtract(1, 'week').endOf('week')],
                'Bulan ini': [moment().startOf('month'), moment().endOf('month')],
                'Bulan lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Tahun ini': [moment().startOf('year'), moment().endOf('year')],
                'Tahun lalu': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
            },
        }, cb);

        cb(start, end);

        $('#btnSearch').click(function() {
            window.location.replace(url);
        });
    });
</script>
<?= $this->endSection('script'); ?>