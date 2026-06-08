<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-users icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Role
                    <div class="page-title-subheading">Daftar Semua Role</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item">Otorisasi</li>
                        <li class="active breadcrumb-item" aria-current="page">Role</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Role
            <div class="btn-actions-pane-right actions-icon-btn">
                <?php if (is_member('admin')) : ?>
                    <a data-toggle="modal" data-target="#modal_create" href="javascript:void(0);" class="btn btn-success" title="Tambah Role"><i class="fa fa-plus"></i> Tambah Role</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <table style="width: 100%;" id="tbl_groups" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Nama Role</th>
                        <th class="text-center">Keterangan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groups as $row) : ?>
                        <tr>
                            <td width="35"></td>
                            <td width="200"><?= _spec($row->name); ?></td>
                            <td><?= _spec($row->description); ?></td>
                            <td width="180" class="text-center">
                                <?php if (is_member('admin')) : ?>
                                    <a href="<?= base_url('group/permissions/' . $row->id); ?>" data-toggle="tooltip" data-placement="top" title="Permission" class="btn btn-primary"><i class="fa fa-cogs"> </i></a>
                                <?php endif; ?>
                                <?php if (is_member('admin')) : ?>
                                    <a href="javascript:void(0);" data-href="<?= base_url('api/group/detail/' . $row->id); ?>" data-toggle="tooltip" data-placement="top" title="Edit Role " class="btn btn-warning show-data"><i class="pe-7s-note font-weight-bold"> </i></a>
                                <?php endif; ?>
                                <?php if (is_member('admin')) : ?>
                                    <a href="javascript:void(0);" data-href="<?= base_url('group/delete/' . $row->id); ?>" data-toggle="tooltip" data-placement="top" title="Hapus Role" class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= $this->include('Group\Views\add_modal'); ?>
<?= $this->include('Group\Views\update_modal'); ?>

<script>
    setDataTable('#tbl_groups', disableOrderCols = [0, 3], defaultOrderCols = [0, 'asc'], autoNumber = true);

    $('#tbl_groups').on('click', '.remove-data', function() {
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