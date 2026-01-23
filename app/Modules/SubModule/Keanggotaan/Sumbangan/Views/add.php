<?php
$request = service('request');
?>

<?php $core = config('Core');
$layout = (!empty($core->layout_backend)) ? $core->layout_backend : 'hamkamannan\adminigniter\Views\layout\backend\main'; ?>
<?= $this->extend($layout); ?>
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
                    <i class="pe-7s-photo icon-gradient bg-strong-bliss"></i>
                </div>
                <div><?= lang('Sumbangan.action.add') ?> <?= lang('Sumbangan.module') ?>
                    <div class="page-title-subheading"><?= lang('Sumbangan.form.complete_the_data') ?>.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i>
                                Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('sumbangan') ?>"><?= lang('Sumbangan.module') ?></a></li>
                        <li class="active breadcrumb-item" aria-current="page"><?= lang('Sumbangan.action.add') ?>
                            <?= lang('Sumbangan.module') ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form
            <?= lang('Sumbangan.action.add') ?> <?= lang('Sumbangan.module') ?>
        </div>
        <div class="card-body">
            <div id="infoMessage"><?= $message ?? ''; ?></div>
            <?= get_message('message'); ?>

            <form id="frm_create" class="col-md-12 mx-auto" method="post" action="<?= base_url('sumbangan/create'); ?>">
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label for="name">Nama Anggota</label>
                            <div>

                                <select class="form-control select2" name="Member_id" id="t_member_id" tabindex="-1" aria-hidden="true" style="width:100%">
                                    <option value="">-No Anggota-</option>
                                    <?php foreach (get_ref_table('members', 'ID, Fullname') as $row) : ?>
                                        <option value="<?= $row->ID ?>"><?= $row->Fullname ?></option>
                                    <?php endforeach; ?>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label for="name"><?= lang('Sumbangan.field.total') ?></label>
                            <div>
                                <input type="text" class="form-control" id="frm_create_jumlah" name="Jumlah" placeholder="<?= lang('Sumbangan.field.total') ?> " value="<?= set_value('Jumlah'); ?>" />

                            </div>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <label for="description"><?= lang('Sumbangan.field.description') ?> </label>
                    <div>
                        <textarea id="frm_create_description" name="keterangan" placeholder="<?= lang('Sumbangan.field.description') ?> " rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('Keterangan') ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" name="submit"><?= lang('Sumbangan.action.save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>


<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    $('.select2').select2();
</script>
<?= $this->endSection('script'); ?>