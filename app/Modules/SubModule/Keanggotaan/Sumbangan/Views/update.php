<?php
$request = service('request');
?>

<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<style>
    .select2-container--default .select2-selection--single {
        padding: 6px;
        height: 37px;
        font-size: 1.1em;
        position: relative;
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-edit icon-gradient bg-mixed-hopes"></i>
                </div>
                <div><?= lang('Sumbangan.action.edit') ?> <?= lang('Sumbangan.module') ?>
                    <div class="page-title-subheading">Silakan perbarui data sumbangan di bawah ini.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('sumbangan') ?>"><?= lang('Sumbangan.module') ?></a></li>
                        <li class="active breadcrumb-item" aria-current="page"><?= lang('Sumbangan.action.edit') ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Form Ubah <?= lang('Sumbangan.module') ?>
        </div>
        <div class="card-body">
            <div id="infoMessage"><?= $message ?? ''; ?></div>
            <?= get_message('message'); ?>

            <form id="frm_edit" class="col-md-12 mx-auto" method="post" action="<?= base_url('sumbangan/edit/' . $sumbangan->ID); ?>">
                <?= csrf_field(); ?>
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label for="Member_id">Nama Anggota</label>
                            <div>
                                <select class="form-control select2" name="Member_id" id="t_member_id" style="width:100%">
                                    <option value="">-Pilih Anggota-</option>
                                    <?php foreach (get_ref_table('members', 'ID, Fullname') as $row) : ?>
                                        <option value="<?= $row->ID ?>" <?= ($row->ID == (old('Member_id') ?? $sumbangan->Member_id)) ? 'selected' : '' ?>>
                                            <?= $row->Fullname ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label for="Jumlah"><?= lang('Sumbangan.field.total') ?></label>
                            <div>
                                <input type="text" class="form-control" id="frm_edit_jumlah" name="Jumlah" 
                                    placeholder="<?= lang('Sumbangan.field.total') ?>" 
                                    value="<?= old('Jumlah') ?? $sumbangan->Jumlah; ?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="Keterangan"><?= lang('Sumbangan.field.description') ?> </label>
                    <div>
                        <textarea id="frm_edit_description" name="Keterangan" 
                            placeholder="<?= lang('Sumbangan.field.description') ?>" 
                            rows="3" class="form-control autosize-input" 
                            style="min-height: 38px;"><?= old('Keterangan') ?? $sumbangan->Keterangan; ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" name="submit">
                        <i class="fa fa-save"></i> <?= lang('Sumbangan.action.save') ?>
                    </button>
                    <a href="<?= base_url('sumbangan') ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
<?= $this->endSection('script'); ?>