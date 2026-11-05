<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<style>
    /* Agar tinggi kartu seragam dalam satu baris dan tidak ada overlap */
    .card-menu {
        height: 100%;
    }

    .permission-section {
        height: 100%;
        padding: 0.5rem;
        border: 1px solid rgba(0, 0, 0, 0.125);
        border-radius: 0.25rem;
    }

    .permission-section-title {
        margin-bottom: 0.5rem;
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* Nama permission yang panjang turun ke baris berikutnya, tombol tetap di tempatnya */
    #permission-container .widget-content-left.flex2 {
        flex: 1 1 0;
        min-width: 0;
    }

    #permission-container .widget-heading {
        overflow-wrap: anywhere;
    }

    #permission-container .widget-content-left:first-child,
    #permission-container .widget-content-right {
        flex-shrink: 0;
    }

    #permission-container .widget-content-right {
        margin-left: 0.5rem;
    }
</style>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
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
                        <li class="breadcrumb-item" aria-current="page">Permission</li>
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
                        <button type="button" id="btnCheckAll" class="btn btn-ina-outline pt-2"><i class="fa fa-check-square mr-1"></i> Check All</button>
                        <button type="button" id="btnUncheckAll" class="btn btn-ina-outline pt-2"><i class="fa fa-square mr-1"></i> Uncheck All</button>
                        <button type="button" id="btnReset" class="btn btn-secondary pt-2"><i class="fa fa-history mr-1"></i> Reset</button>
                        <button type="submit" class="btn btn-catalog-add pt-2"><i class="fa fa-save mr-1"></i> Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="permission-container">
            <?php foreach ($permission_tree as $node) : ?>
                <?php if (isset($node['sections'])) : ?>
                <div class="col-12 mb-3 menu-tree">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <b class="text-primary"><?= esc(strtoupper($node['name'])) ?></b>
                        </div>
                        <div class="card-body p-2">
                            <div class="row">
                                <?php foreach ($node['sections'] as $section => $rows) : ?>
                                    <div class="col-md-6 col-lg-4 col-xl-3 mb-3 parent-menu" data-menu="<?= esc(strtolower($node['name'] . ' ' . $section), 'attr') ?>">
                                        <div class="permission-section">
                                            <div class="permission-section-title"><?= esc($section) ?></div>
                                            <?php foreach ($rows as $row) : ?>
                                                <?= view('Group\Views\permission_item', ['row' => $row, 'permissions_users' => $permissions_users]) ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else : ?>
                <div class="col-md-6 col-lg-4 col-xl-3 mb-3 parent-menu" data-menu="<?= strtolower($node['name']); ?>">
                    <div class="card card-menu shadow-sm">
                        <div class="card-header bg-light">
                            <b class="text-primary"><?= strtoupper($node['name']) ?></b>
                        </div>
                        <div class="card-body p-2">
                            <?php foreach ($node['rows'] as $row) : ?>
                                <?= view('Group\Views\permission_item', ['row' => $row, 'permissions_users' => $permissions_users]) ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
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
        $('.menu-tree').each(function() {
            $(this).toggle($(this).find('.parent-menu.checkAll').length > 0);
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