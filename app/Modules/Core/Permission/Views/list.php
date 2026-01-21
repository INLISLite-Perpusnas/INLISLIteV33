<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
  
    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Permission
            <div class="btn-actions-pane-right actions-icon-btn">
                <?php if (is_allowed('permission/create') || is_member('admin')) : ?>
                    <a data-toggle="modal" data-target="#modal_create" href="javascript:void(0);" class="btn btn-success" title="Tambah Permission"><i class="fa fa-plus"></i> Tambah Permission</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <table style="width: 100%;" id="tbl_permissions" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Menu</th>
                        <th>Route</th>
                        <th>Permission</th>
                        <th>Kategori Permission</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($permissions as $row) : ?>
                        <tr>
                            <td width="35"></td>
                            <td><?= _spec($row->menu); ?></td>
                            <td><?= _spec($row->route); ?></td>
                            <td><?= _spec($row->name); ?></td>
                            <td><?= _spec($row->category); ?></td>
                            <td><?= _spec($row->description); ?></td>
                            <td width="100" class="text-center">
                                <?php if (is_allowed('permission/edit') ||  is_member('admin')) : ?>
                                    <a href="javascript:void(0);" data-href="<?= base_url('api/permission/detail/' . $row->id); ?>" data-toggle="tooltip" data-placement="top" title="Edit Permission" class="btn btn-xs btn-warning show-data"><i class="pe-7s-note font-weight-bold"> </i></a>
                                <?php endif; ?>
                                <?php if (is_allowed('permission/delete') ||  is_member('admin')) : ?>
                                    <a href="javascript:void(0);" data-href="<?= base_url('permission/delete/' . $row->id); ?>" data-toggle="tooltip" data-placement="top" title="Hapus Permission" class="btn btn-xs btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>
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
<?= $this->include('Permission\Views\add_modal'); ?>
<?= $this->include('Permission\Views\update_modal'); ?>
<script>
    setDataTable('#tbl_permissions', disableOrderCols = [0, 4], defaultOrderCols = [1, 'asc'], autoNumber = true);

    $('#tbl_permissions').on('click', '.remove-data', function() {
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