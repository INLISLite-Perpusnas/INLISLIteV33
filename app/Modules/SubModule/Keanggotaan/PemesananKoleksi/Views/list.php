<?= $this->extend('hamkamannan\adminigniter\layout\backend\main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-photo icon-gradient bg-strong-bliss"></i>
                </div>
                <div><?= lang('PemesananKoleksi.module') ?> 
                    <div class="page-title-subheading"><?= lang('PemesananKoleksi.info.list_all') ?>  <?= lang('PemesananKoleksi.module') ?> </div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('pemesanankoleksi') ?>"><i class="fa fa-home"></i> <?= lang('PemesananKoleksi.label.home') ?></a></li>
                        <li class="active breadcrumb-item" aria-current="page"><?= lang('PemesananKoleksi.module') ?> </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i><?= lang('PemesananKoleksi.label.table') ?> <?= lang('PemesananKoleksi.module') ?> 
            <div class="btn-actions-pane-right actions-icon-btn">
                <?php if(is_allowed('pemesanankoleksi/create')):?>
                    <a href="<?= base_url('pemesanankoleksi/create') ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i> <?= lang('PemesananKoleksi.action.add') ?> <?= lang('PemesananKoleksi.module') ?> </a>
                <?php endif;?>
            </div>
        </div>
        <div class="card-body">
            <?= get_message('message'); ?>
            <table style="width: 100%;" id="tbl_pemesanankoleksis" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th><?= lang('PemesananKoleksi.field.no') ?> </th>
                        <th><?= lang('PemesananKoleksi.field.created_at') ?> </th>
                        <th>Info Anggota</th>
                        <th>Info Koleksi</th>
                        <th>Ruang</th>
                        <th>Status</th>
                       <th><?= lang('PemesananKoleksi.label.action') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pemesanankoleksis as $row) : ?>
                        <tr>
                            <td width="35"></td>
                            <td width="200">
                                <?= _spec($row->title); ?> <br>
                            </td>
                            <td><?= _spec($row->author); ?></td>
                            <td><?= _spec($row->description); ?></td>
                            <td width="100">
                                <span class="badge badge-info"><?= _spec($row->created_at); ?></span><br>
                                <span class="badge badge-info"><?= _spec($row->created_name); ?></span>
                            </td>
                            <td width="100">
                                <span class="badge badge-info"><?= _spec($row->updated_at); ?></span><br>
                                <span class="badge badge-info"><?= _spec($row->updated_name ?? '-'); ?></span>
                            </td>
                            <td width="35">
                                <?php if(is_allowed('pemesanankoleksi/read')):?>
                                    <!-- <a href="<?= base_url('pemesanankoleksi/detail/' . $row->id) ?>" data-toggle="tooltip" data-placement="top" title="Detail PemesananKoleksi" class="btn btn-xs btn-info show-data"><i class="pe-7s-note2 font-weight-bold"> </i></a> -->
                                <?php endif;?>
                                <?php if(is_allowed('pemesanankoleksi/edit')):?>
                                    <a href="<?= base_url('pemesanankoleksi/edit/' . $row->id) ?>" data-toggle="tooltip" data-placement="top" title="Ubah PemesananKoleksi" class="btn btn-xs btn-warning mb-1 show-data"><i class="pe-7s-note font-weight-bold"> </i></a>
                                <?php endif;?>
                                <?php if(is_allowed('pemesanankoleksi/delete')):?>
                                    <a href="javascript:void(0);" data-href="<?= base_url('pemesanankoleksi/delete/' . $row->id); ?>" data-toggle="tooltip" data-placement="top" title="Hapus  rud" class="btn btn-xs btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>
                                <?php endif;?>
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

<script>
    setDataTable('#tbl_pemesanankoleksis', disableOrderCols = [0, 6], defaultOrderCols = [5, 'desc'], autoNumber = true);

    $("body").on("click", ".remove-data", function() {
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