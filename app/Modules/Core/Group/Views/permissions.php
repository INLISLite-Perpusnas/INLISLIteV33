<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<style>
    /* Agar tinggi kartu seragam dalam satu baris dan tidak ada overlap */
    .card-menu {
        height: 100%;
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
                <div>Permission - Role <b><?= ucfirst(str_replace('_', ' ', $group->name ?? '')) ?></b>
                    <div class="page-title-subheading">Daftar Semua Permission</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item">Otorisasi</li>
                        <li class="breadcrumb-item">Role</li>
                        <li class="active breadcrumb-item" aria-current="page">Permission</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <form id="frm_create" method="post" action="<?= base_url('group/permissions/' . $group_id) ?>" onsubmit="return validateForm()">
        
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="btn bg-info text-white pt-2" style="cursor: default;">Permission :</span>
                    </div>
                    <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Kata kunci...">
                    
                    <div class="input-group-append">
                        <button type="button" id="btnCheckAll" class="btn btn-primary pt-2"><i class="fa fa-check-square mr-1"></i> Check All</button>
                        <button type="button" id="btnUncheckAll" class="btn btn-primary pt-2"><i class="fa fa-square mr-1"></i> Uncheck All</button>
                        <button type="button" id="btnReset" class="btn btn-primary pt-2"><i class="fa fa-history mr-1"></i> Reset</button>
                        <button type="submit" class="btn btn-primary pt-2"><i class="fa fa-save mr-1"></i> Save</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="permission-container">
            <?php
            $menu_permissions = [];
            foreach ($permissions as $row) { 
                $menu_permissions[$row->menu][] = $row;
            }
            ksort($menu_permissions);
            ?>
            
            <?php foreach (array_keys($menu_permissions) as $menu) : ?>
                <div class="col-md-6 col-lg-4 col-xl-3 mb-3 parent-menu" data-menu="<?= strtolower($menu); ?>">
                    <div class="card card-menu shadow-sm">
                        <div class="card-header bg-light">
                            <b class="text-primary"><?= strtoupper($menu) ?></b>
                        </div>
                        <div class="card-body p-2">
                            <?php foreach ($menu_permissions[$menu] as $row) : ?>
                                <div class="widget-content p-0 mb-2 border-bottom pb-1">
                                    <div class="widget-content-wrapper">
                                        <div class="widget-content-left mr-2">
                                            <input type="checkbox" name="permissions[<?= $row->id; ?>][]" id="<?= $row->id; ?>" <?= (in_array($row->id, $permissions_users)) ? 'checked' : '' ?> data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="mini">
                                        </div>
                                        <div class="widget-content-left flex2">
                                            <div class="widget-heading" style="font-size: 0.85rem; line-height: 1.2;">
                                                <?= str_replace(['-', '_'], ' ', _spec($row->name)); ?>
                                            </div>
                                        </div>
                                        <?php if (is_member('admin')) : ?>
                                        <div class="widget-content-right">
                                            <a href="#" data-href="<?= base_url('permission/delete/' . $row->id) ?>" class="btn btn-outline-danger btn-sm py-0 px-1 remove-data" title="Hapus"><i class="fa fa-trash-alt"></i></a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </form>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    $('#keyword').keyup(function() {
        var keyword = $(this).val().toLowerCase();
        filter(keyword);
    });

    function filter(keyword) {
        $('.parent-menu').each(function() {
            var menu = $(this).data('menu');
            if (typeof menu === 'string' && menu.includes(keyword)) {
                $(this).addClass('checkAll').show();
            } else {
                $(this).removeClass('checkAll').hide();
            }
        });
    }

    $('#btnReset').click(function() {
        $('#keyword').val(''); // Menambahkan reset inputan juga
        filter('');
    });

    $('#btnCheckAll').click(function() {
        // Mengubah selector menjadi #permission-container karena card-columns sudah dihapus
        var checkboxes = $('#permission-container .checkAll input[type="checkbox"]');
        if (checkboxes.length > 0) {
            checkboxes.each(function() {
                var checkboxId = $(this).attr('id');
                if (!$(this).prop('checked')) {
                    $(this).bootstrapToggle('toggle');
                    $(this).prop('checked', true);
                    $(this).val(checkboxId);
                }
            });
        }
    });

    $('#btnUncheckAll').click(function() {
        // Mengubah selector menjadi #permission-container
        var checkboxes = $('#permission-container .checkAll input[type="checkbox"]');
        if (checkboxes.length > 0) {
            checkboxes.each(function() {
                var checkboxId = $(this).attr('id');
                if ($(this).prop('checked')) {
                    $(this).bootstrapToggle('toggle');
                    $(this).prop('checked', false);
                    $(this).removeAttr('value');
                }
            });
        }
    });
    
    filter('');

    // Mengubah delegasi event dari .card-columns ke #permission-container
    $('#permission-container').on('click', '.remove-data', function(e) {
        e.preventDefault(); // Mencegah link default berjalan
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
</script>
<?= $this->endSection('script'); ?>