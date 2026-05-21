<?= $this->extend('App\Views\layout\main'); ?>
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
                <div><?= lang('Sumbangan.module') ?> 
                    <div class="page-title-subheading"><?= lang('Sumbangan.info.list_all') ?>  <?= lang('Sumbangan.module') ?> </div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('sumbangan') ?>"><i class="fa fa-home"></i> <?= lang('Sumbangan.label.home') ?></a></li>
                        <li class="breadcrumb-item" aria-current="page"><?= lang('Sumbangan.module') ?> </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i><?= lang('Sumbangan.label.table') ?> <?= lang('Sumbangan.module') ?> 
            <div class="btn-actions-pane-right actions-icon-btn">
                <?php if(is_allowed('sumbangan/create')):?>
                    <a href="<?= base_url('sumbangan/create') ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i> <?= lang('Sumbangan.action.add') ?> <?= lang('Sumbangan.module') ?> </a>
                <?php endif;?>
            </div>
        </div>
        <div class="card-body">
            <?= get_message('message'); ?>
            <table style="width: 100%;" id="tbl_sumbangans" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th><?= lang('Sumbangan.field.no') ?> </th>
                        <th><?= lang('Sumbangan.field.name') ?></th>
                        <th>Nomor Anggota</th>
                        <th><?= lang('Sumbangan.field.description') ?></th>
                  
                        <th><?= lang('Sumbangan.field.created_by') ?></th>
                        <th><?= lang('Sumbangan.field.updated_by') ?></th>
                        <th><?= lang('Sumbangan.label.action') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($sumbangans as $row) : ?>
                        <tr>
                            <td width="35"><?=$no++?></td>
                            <td width="200">
                                <?= _spec($row->nama); ?> <br>
                            </td>
                            <td width="200">
                                <?= _spec($row->MembersNo); ?> <br>
                            </td>
                            <td><?= _spec($row->Keterangan); ?></td>
                           
                           
                            <td width="100">
                                <span class="badge badge-info"><?= _spec($row->CreateDate); ?></span><br>
                        
                            </td>
                            <td width="100">
                                <span class="badge badge-info"><?= _spec($row->UpdateDate); ?></span><br>
                               
                            </td>
                            <td width="35">
                                <?php if(is_allowed('sumbangan/read')):?>
                                    <!-- <a href="<?= base_url('sumbangan/detail/' . $row->ID) ?>" data-toggle="tooltip" data-placement="top" title="Detail Sumbangan" class="btn btn-xs btn-info show-data"><i class="pe-7s-note2 font-weight-bold"> </i></a> -->
                                <?php endif;?>
                                <?php if(is_allowed('sumbangan/edit')):?>
                                    <a href="<?= base_url('sumbangan/edit/' . $row->ID) ?>" data-toggle="tooltip" data-placement="top" title="Ubah Sumbangan" class="btn btn-xs btn-warning mb-1 show-data"><i class="pe-7s-note font-weight-bold"> </i></a>
                                <?php endif;?>
                                <?php if(is_allowed('sumbangan/delete')):?>
                                    <a href="javascript:void(0);" data-href="<?= base_url('sumbangan/delete/' . $row->ID); ?>" data-toggle="tooltip" data-placement="top" title="Hapus  rud" class="btn btn-xs btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>
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
    setDataTable('#tbl_sumbangans', disableOrderCols = [0, 6], defaultOrderCols = [1, 'asc'], autoNumber = true);

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