<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
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
                    <i class="pe-7s-server icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Kata Sandang
                    <div class="page-title-subheading">Daftar semua Kata Sandang</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
                        <li class="breadcrumb-item">Administrasi</li>
                        <li class="breadcrumb-item">Pengaturan Katalog</li>
                        <li class="breadcrumb-item" aria-current="page">Kata Sandang</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card col-md-12">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Kata Sandang
            <div class="btn-actions-pane-right actions-icon-btn">
               <a data-bs-toggle="modal" data-bs-target="#modal_create" data-toggle="modal" data-target="#modal_create" href="javascript:void(0);" class=" btn btn-success" title=""><i class="fa fa-plus"></i> Tambah Kata Sandang</a>
            </div>
        </div>
        <div class="card-body">
            <table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" width="35">No</th>
                        <th class="text-center" width="100">Tag</th>
                        <th class="text-center">Nama</th>
                        <th class="text-center" width="150">Jumlah Karakter</th>
                        <th class="text-center" width="150">Update Date</th>
                        <th class="text-center" stye="min-width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbl_data_tbody"></tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= $this->include('KataSandang\Views\add_modal'); ?>
<?= $this->include('KataSandang\Views\update_modal'); ?>
<script>
    var t;
    $(document).ready(function() {
        t = $('#tbl_data').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": '<?php echo site_url('api/master-kata-sandang/datatable/' . $slug); ?>',
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
                    orderable: false,
                    className: 'text-center'
                },
                {
                    data: 'tag',
                    className: 'text-center'
                },
                {
                    data: 'name',
                    className: 'text-left'
                },
                {
                    data: 'length',
                    className: 'text-center'
                },
                {
                    data: 'update_date',
                    className: 'text-center'
                },
                {
                    data: 'action',
                    orderable: false,
                    className: 'text-center'
                },
            ],
            "order": [
                [4, "desc"]
            ],
            "drawCallback": function(data, type, full, meta) {
                var api = this.api();
                var data = api.rows().data();
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

    $('#search_name').on('keyup', function(e) {
        t.columns('name:name').search(this.value).draw();
    });

    $('#search_value').on('keyup', function() {
        t.columns('value:name').search(this.value).draw();
    });

    $('#search_description').on('keyup', function() {
        t.columns('description:name').search(this.value).draw();
    });

    $('#tbl_data').on('click', '.remove-data', function() {
        var url = $(this).attr('data-href');
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

    $(".apply-param-status").on('change', function() {
        var switchStatus = $(this).is(':checked');
        var paramName = $(this).attr('data-param');
        var paramValue = $(this).attr('data-class');

        if (switchStatus) {
            setKataSandang(paramName, 1);
        } else {
            setKataSandang(paramName, 0);
        }
    });
</script>
<?= $this->endSection('script'); ?>