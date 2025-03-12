<?php
$request = service('request'); ?>

<?php $core = config('Core');
$layout = (!empty($core->layout_backend)) ? $core->layout_backend : 'hamkamannan\adminigniter\Views\layout\backend\main'; ?>
<?= $this->extend($layout); ?>
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
                <div><?= lang('KeranjangAnggota.action.add') ?> <?= lang('KeranjangAnggota.module') ?>
                    <div class="page-title-subheading"><?= lang('KeranjangAnggota.form.complete_the_data') ?>.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i>
                                Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('keranjanganggota') ?>"><?= lang('KeranjangAnggota.module') ?></a>
                        </li>
                        <li class="active breadcrumb-item" aria-current="page">
                            <?= lang('KeranjangAnggota.action.add') ?> <?= lang('KeranjangAnggota.module') ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form
            <?= lang('KeranjangAnggota.action.add') ?> <?= lang('KeranjangAnggota.module') ?>
        </div>
        <div class="card-body">
            <div id="infoMessage"><?= $message ?? ''; ?></div>
            <?= get_message('message'); ?>

            <form id="frm_create" class="col-md-12 mx-auto" method="post" action="<?= base_url('keranjanganggota/create'); ?>">


                <div class="form-row">
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <label for="name">Nomor Anggota</label>
                            <div>

                                <select class="form-control select2" name="t_anggota_id" id="t_anggota_id" tabindex="-1" aria-hidden="true" style="width:100%">
                                    <option value="">-No Anggota-</option>
                                    <?php foreach ($anggota as $row) : ?>
                                        <option value="<?= $row->id ?>"><?= $row->MemberNo ?></option>
                                    <?php endforeach; ?>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="position-relative form-group">
                            <label for="name"><?= lang('Sumbangan.field.total') ?></label>
                            <div>
                                <input type="text" class="form-control" id="frm_create_jumlah" name="jumlah" placeholder="<?= lang('Sumbangan.field.total') ?> " value="<?= set_value('jumlah'); ?>" />
                                <small class="info help-block text-muted"><?= lang('Sumbangan.field.name') ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="position-relative form-group">
                            <label for="sort"><?= lang('Sumbangan.field.sort') ?> </label>
                            <div>
                                <input type="number" class="form-control" id="frm_create_sort" name="sort" placeholder="<?= lang('Sumbangan.field.sort') ?> " value="<?= set_value('sort') ?>" />
                                <small class="info help-block text-muted"><?= lang('Sumbangan.field.sort') ?>
                                    Sumbangan</small>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="form-row">
                <div class="col-md-6">
                        <div class="position-relative form-group">
                            <label for="name"><?= lang('Sumbangan.field.name') ?>*</label>
                            <div>
                                <input type="text" class="form-control" id="frm_create_name" name="name"
                                    placeholder="<?= lang('Sumbangan.field.name') ?> "
                                    value="<?= set_value('name'); ?>" />
                                <small class="info help-block text-muted"><?= lang('Sumbangan.field.name') ?></small>
                            </div>
                        </div>
                    </div>
                                    </div> -->
                <div class="form-group">
                    <label for="description"><?= lang('Sumbangan.field.description') ?> </label>
                    <div>
                        <textarea id="frm_create_description" name="description" placeholder="<?= lang('Sumbangan.field.description') ?> " rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description') ?></textarea>
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

<?= $this->endSection('script'); ?>