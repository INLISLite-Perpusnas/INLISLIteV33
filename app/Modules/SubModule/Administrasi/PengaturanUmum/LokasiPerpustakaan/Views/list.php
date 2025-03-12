<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
$regionModel = new \Region\Models\RegionModel();

$npp_provinsi = "";
$npp_kabkota = "";
if (is_member('sa_prov') || is_member('sa_kabkot')) {
    $region = npp_region(user()->npp_provinsi_id, 1);
    if (!empty($region)) {
        $npp_provinsi = $region->name;
    }
}

if (is_member('sa_kabkot')) {
    $region = npp_region(user()->npp_kabkota_id, 2);
    if (!empty($region)) {
        $npp_kabkota = $region->name;
    }
}
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
                <div>Lokasi Perpustakaan
                    <div class="page-title-subheading">Daftar semua Lokasi Perpustakaan</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('lokasiperpustakaan') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Lokasi Perpustakaan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <?php if (is_profiling() && (branch_id() == 0)) : ?>
        <div class="main-card mb-3 card">
            <div class="card-header"><i class="header-icon lnr-search icon-gradient bg-plum-plate"> </i> Filter
                <div class="btn-actions-pane-right actions-icon-btn">

                </div>
            </div>
            <div class="card-body">
                <form name="form_items" id="form_search" action="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Provinsi</label>
                                <div class="select-wrapper">
                                    <select class="form-control select2" id="provinsi_id" name="provinsi_id" style="width:100%"></select>
                                </div>
                                <small class="help-block with-errors"></small>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kota / Kabupaten</label>
                                <div class="select-wrapper">
                                    <select class="form-control select2" id="kabkota_id" name="kabkota_id" style="width:100%">
                                        <option value="">-</option>
                                    </select>
                                </div>
                                <small class="help-block with-errors"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kecamatan</label>
                                <div class="select-wrapper">
                                    <select class="form-control select2" id="kecamatan_id" name="kecamatan_id" style="width:100%">
                                        <option value="">-</option>
                                    </select>
                                </div>
                                <small class="help-block with-errors"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kelurahan / Desa</label>
                                <div class="select-wrapper">
                                    <select class="form-control select2" id="kelurahan_id" name="kelurahan_id" style="width:100%">
                                        <option value="">-</option>
                                    </select>
                                </div>
                                <small class="help-block with-errors"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="groups">NPP (Mitra Perpustakaan)</label>
                            <div class="select-wrapper">
                                <select class="form-control selectx" id="npp" name="npp" tabindex="-1" aria-hidden="true" style="width:100%">
                                    <option value="">-</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Lokasi Perpustakaan
            <div class="btn-actions-pane-right actions-icon-btn">
                <?php if (is_allowed('lokasiperpustakaan/create')) : ?>
                    <a data-toggle="modal" data-target="#modal_create" href="javascript:void(0);" class="btn btn-success" title="Tambah"><i class="fa fa-plus"></i> Lokasi Perpustakaan</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <?= get_message('message'); ?>
            <table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" width="35">No</th>
                        <th class="text-center"><span class="text-primary">Branch ID</span> <br> NPP / Mitra Perpustakaan</th>
                        <th class="text-center" width="100">Kode</th>
                        <th class="text-center" width="200">Nama Lokasi Perpustakaan</th>
                        <th class="text-center">Alamat</th>
                        <th class="text-center" width="80">Status</th>
                        <th class="text-center" width="180">Aksi</th>
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
<?= $this->include('LokasiPerpustakaan\Views\add_modal'); ?>
<?= $this->include('LokasiPerpustakaan\Views\update_modal'); ?>
<script>
    var t;
    $(document).ready(function() {
        t = $('#tbl_data').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": '<?php echo site_url('api-lokasi-perpustakaan/datatable/' . $slug) ?>',
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
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'Perpustakaan',
                    className: 'text-left'
                },
                {
                    data: 'Code',
                    className: 'text-left'
                },
                {
                    data: 'Name'
                },
                {
                    data: 'Address'
                },
                {
                    data: 'active',
                    className: 'text-center'
                },
                {
                    data: 'action',
                    className: 'text-center',
                    orderable: false
                },
            ],
            "columnDefs": [{
                    targets: [0, 4, 5],
                    searchable: false
                },
                {
                    targets: [0, 5],
                    orderable: false
                },
            ],
            "order": [
                [1, "asc"]
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