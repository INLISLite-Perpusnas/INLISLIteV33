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
                    <i class="pe-7s-config icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Parameter
                    <div class="page-title-subheading">Daftar Semua Parameter</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item">Setting</li>
                        <li class="breadcrumb-item active" aria-current="page">Paramater</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <?php if (get_parameter('show-top-checkbox') == 1) : ?>
        <div class="row">
            <div class="col-md-3">
                <div class="widget-content p-0">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left mr-3">
                            <div class="switch has-switch switch-container-class" data-class="branch">
                                <div class="switch-animate switch-on">
                                    <input type="checkbox" class="apply-param-status" data-param="branch" data-class="1" <?= (get_parameter('branch') == '1') ? 'checked' : '' ?> data-toggle="toggle" data-onstyle="success">
                                </div>
                            </div>
                        </div>
                        <div class="widget-content-left">
                            <div class="widget-heading">Profiling</div>
                            <div class="widget-subheading">Multi Branch</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget-content p-0">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left mr-3">
                            <div class="switch has-switch switch-container-class" data-class="show-layout-setting">
                                <div class="switch-animate switch-on">
                                    <input type="checkbox" class="apply-param-status" data-param="show-layout-setting" data-class="1" <?= (get_parameter('show-layout-setting') == '1') ? 'checked' : '' ?> data-toggle="toggle" data-onstyle="success">
                                </div>
                            </div>
                        </div>
                        <div class="widget-content-left">
                            <div class="widget-heading">Layout</div>
                            <div class="widget-subheading">Tampilkan Layout</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>

    <ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
        <li class="nav-item">
            <a class="nav-link <?= ($slug == '') ? 'active' : '' ?>" href="<?= base_url('parameter/?slug=') ?>">
                <span>Semua</span>
            </a>
        </li>
        <?php foreach (get_ref('ref-parameter', 'slug') as $row) : ?>
            <li class="nav-item">
                <a class="nav-link <?= ($slug == (slugify($row->name))) ? 'active' : '' ?>" href="<?= base_url('parameter/?slug=' . slugify(($row->name))) ?>">
                    <span><?= $row->name ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Paramater
            <div class="btn-actions-pane-right actions-icon-btn">
                <a data-toggle="modal" data-target="#modal_create" href="javascript:void(0);" class=" btn btn-success" title=""><i class="fa fa-plus"></i> Tambah Parameter</a>
            </div>
        </div>
        <div class="card-body">
            <table style="width: 100%;" id="tbl_params" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" width="35">No</th>
                        <th class="text-center" width="100">Kategori</th>
                        <th class="text-center">Nama Parameter</th>
                        <th class="text-center">Nilai Parameter</th>
                        <th class="text-center">Keterangan</th>
                        <th class="text-center" width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbl_params_tbody"></tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= $this->include('Parameter\Views\add_modal'); ?>
<?= $this->include('Parameter\Views\update_modal'); ?>
<script>
    var t;
    $(document).ready(function() {
        t = $('#tbl_params').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": '<?php echo site_url('api/parameter/datatable/' . $slug); ?>',
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
                    orderable: false,
                    className: 'text-center'
                },
                {
                    data: 'category',
                    className: 'text-left'
                },
                {
                    data: 'name',
                    className: 'text-left'
                },
                {
                    data: 'value',
                    className: 'text-left'
                },
                {
                    data: 'description',
                    className: 'text-left'
                },
                {
                    data: 'action',
                    orderable: false,
                    className: 'text-center'
                },
            ],
            "order": [
                [2, "asc"]
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

    $('#tbl_params').on('click', '.remove-data', function() {
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
            setParameter(paramName, 1);
        } else {
            setParameter(paramName, 0);
        }
    });
</script>
<?= $this->endSection('script'); ?>