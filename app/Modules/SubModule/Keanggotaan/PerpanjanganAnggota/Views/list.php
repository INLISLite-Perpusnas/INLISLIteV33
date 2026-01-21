<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">


    <div class="main-card mb-3 card">
    <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Perpanjangan Anggota
			<div class="btn-actions-pane-right actions-icon-btn">
				<?php if (is_allowed('perpanjangan-anggota/create')) : ?>
					<a href="<?= base_url('perpanjangan-anggota/create') ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i>
						Tambah Perpanjagan Anggota
					</a>
				<?php endif; ?>
			</div>
		</div>
        <div class="card-body">
            <?= get_message('message'); ?>
            <table style="width: 100%;" id="tbl_perpanjangans" class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Nomor Anggota</th>
                        <th>Keterangan</th>
                        <th>Biaya</th>
                        <th>Lunas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($perpanjangans as $row) : ?>
                        <tr>
                            <td width="35"></td>
                            <td width="200">
                                <?= _spec($row->nama); ?> <br>
                            </td>
                            <td width="200">
                                <?= _spec($row->MembersNo); ?> <br>
                            </td>
                            <td width="200"><?= _spec($row->Keterangan); ?></td>
                        
                            <td width="100">
                            <?= _spec($row->Biaya); ?>
                            </td>
                          
                            <td width="50">
                            <input type="checkbox" class="apply-status"
                                data-href="<?=base_url('perpanjangan-anggota/apply_status');?>" data-field="is_lunas"
                                data-id="<?=$row->ID?>" <?=($row->IsLunas == 1) ? 'checked' : ''?> data-toggle="toggle"
                                data-onstyle="success">
                        </td>
                            </td>
                            <td width="35">
                                
                                <?php if(is_allowed('perpanjangan-anggota/delete')):?>
                                    <a href="javascript:void(0);" data-href="<?= base_url('perpanjangan-anggota/delete/' . $row->ID); ?>" data-toggle="tooltip" data-placement="top" title="Hapus  rud" class="btn btn-xs btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>
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
    setDataTable('#tbl_perpanjangans', disableOrderCols = [0, 6], defaultOrderCols = [6, 'desc'], autoNumber = true);

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