<?php
$baseModel = new \Base\Models\BaseModel();
$request = service('request');

$menu_id = $request->getGet('menu_id') ?? ($menu_id ?? '');

$baseModel->setTable('c_menus');
$menu = $baseModel
    ->select('c_menus.*')
    ->where('id', $menu_id)
    ->first();
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/vendors') ?>/nestable/nestable.css">
<style>
    .dd {
        max-width: none !important;
    }

    .dd-handle-label {
        opacity: 0;
    }

    .clickable {
        cursor: pointer;
    }

    .menu-toggle-activate {
        cursor: pointer;
    }

    .menu-toggle-activate_inactive>.dd3-content {
        background: #F7D2DC !important;
    }

    .dd-item>button {
        display: block;
        position: relative;
        cursor: pointer;
        float: left;
        width: 30px;
        height: 35px;
        margin: 2px 0;
        padding: 0;
        text-indent: 100%;
        white-space: nowrap;
        overflow: hidden;
        border: 0;
        background: transparent;
        font-size: 16px;
        line-height: 1;
        text-align: center;
        font-weight: bold;
    }

    .dd-item>button:before {
        content: '+';
        display: block;
        position: absolute;
        width: 100%;
        text-align: center;
        text-indent: 0;
    }

    .dd-item>button[data-action="collapse"]:before {
        content: '-';
    }

    .app-page-title {
        padding: 15px 30px;
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-config icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Referensi
                    <div class="page-title-subheading">Daftar Semua Referensi</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item">Setting</li>
                        <li class="active breadcrumb-item" aria-current="page">Reference</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="main-card mb-3 card">
                <div class="card-header"><i class="header-icon pe-7s-keypad icon-gradient bg-plum-plate"> </i> Kategori
                    <div class="btn-actions-pane-right actions-icon-btn">
                        <?php if (is_member('admin')) : ?>
                            <a data-toggle="modal" data-target="#category_modal_create" href="javascript:void(0);" class=" btn btn-success" title="Tambah Kategori Menu"><i class="fa fa-plus"></i> Tambah Kategori</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dd" id="nestable" style="width:100% !important">
                        <?php $menus = display_menu_reference(3, 0, 1); ?>
                        <?php if (empty($menus)) : ?>
                            <div class="box-no-data">Data Menu tidak ada</div>
                        <?php else : ?>
                            <?= $menus; ?>
                        <?php endif; ?>
                    </div>
                    <div class="nestable-output"></div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="main-card mb-3 card">
                <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i> <?= ($menu->name ?? 'Semua Referensi') ?>
                    <div class="btn-actions-pane-right actions-icon-btn">
                        <?php if (is_member('admin')) : ?>
                            <a data-toggle="modal" data-target="#modal_create" href="javascript:void(0);" class=" btn btn-success" title=""><i class="fa fa-plus"></i> Tambah Referensi</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <table style="width: 100%;" id="tbl_references" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kategori</th>
                                <th>Label</th>
                                <th>Urutan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($references as $row) : ?>
                                <tr>
                                    <td width="35"></td>
                                    <td width="200">
                                        <?= _spec($row->category); ?>
                                    </td>
                                    <td>
                                        <?= _spec($row->name); ?>
                                    </td>
                                    <td width="50">
                                        <?= _spec($row->sort); ?>
                                    </td>
                                    <td width="80">
                                        <?php if (is_member('admin')) : ?>
                                            <a href="javascript:void(0);" data-href="<?= base_url('api/reference/detail/' . $row->id); ?>" data-toggle="tooltip" data-placement="top" title="Ubah Referensi" class="btn btn-xs btn-warning show-data"><i class="pe-7s-note font-weight-bold"> </i></a>
                                        <?php endif; ?>
                                        <?php if (is_member('admin')) : ?>
                                            <a href="javascript:void(0);" data-href="<?= base_url('reference/delete/' . $row->id . '?menu_id=' . $menu_id); ?>" data-toggle="tooltip" data-placement="top" title="Hapus Referensi" class="btn btn-xs btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= $this->include('Reference\Views\add_modal'); ?>
<?= $this->include('Reference\Views\update_modal'); ?>
<?= $this->include('Reference\Views\category_add_modal'); ?>
<script src="<?= base_url('assets/vendors'); ?>/nestable/jquery.nestable.js"></script>

<script>
    $(document).ready(function() {
        setDataTable('#tbl_references', disableOrderCols = [0, 4], defaultOrderCols = [3, 'asc'], autoNumber = true);

        $('#tbl_references').on('click', '.remove-data', function() {
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

        $('#menu_references').on('click', '.remove-data', function() {
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



        $('.clickable').on('click', function() {
            var href = $(this).attr('data-href');
            window.location.href = href;
            return false;
        });

        $('#categories').on('click', '.show-data', function() {
            var url = $(this).attr('data-href');
            $.ajax({
                url: url,
                type: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#frm_edit').attr("data-id", response.id);
                    $('#frm_edit_name').val(response.name);
                    $('#frm_edit_sort').val(response.sort);
                    $('#frm_edit_description').val(response.description);
                    $('#modal_edit').modal('show');
                }
            });
        });

        function updateOrderMenu(ignoreMessage) {
            $('.loading').removeClass('loading-hide');
            var shownotif = true;
            var menu = $('.dd').nestable('serialize');

            if (typeof shownotif == 'undefined') {
                var shownotif = true;
            }

            if (typeof ignoreMessage == 'undefined') {
                var ignoreMessage = false;
            }

            // console.log(menu);
            $.ajax({
                    url: BASE_URL + '/api/menu/save_ordering',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        'menu': menu,
                    },
                })
                .done(function(data) {
                    console.log(data);
                    if (data.status === 201) {
                        if (shownotif) {
                            if (!ignoreMessage) {
                                toastr['success'](data.messages.success);
                            }
                        }
                    } else {
                        if (shownotif) {
                            if (!ignoreMessage) {
                                toastr['error']('Menu gagal diubah');
                            }
                        }
                    }
                })
                .fail(function(data) {
                    if (!ignoreMessage) {
                        toastr['error']('Menu gagal diubah');
                    }
                });
        }

        var BASE_URL = '<?= base_url() ?>';
        var timeout;

        $('#nestable').nestable({
            group: 1,
            maxDepth: 2,
            collapsedClass: 'dd-collapsed',
        }).nestable();

        $('.dd').on('change', function() {
            clearTimeout(timeout);
            timeout = setTimeout(updateOrderMenu, 2000);
        });
    });
</script>
<?= $this->endSection('script'); ?>