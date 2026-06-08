<?php
$request = service('request'); ?>

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
                        <div><?= lang('PerpanjanganAnggota.action.update') ?> <?= lang('PerpanjanganAnggota.module') ?>
                              <div class="page-title-subheading"><?= lang('PerpanjanganAnggota.form.complete_the_data') ?>.</div>
                        </div>
                  </div>
                  <div class="page-title-actions">
                        <nav class="" aria-label="breadcrumb">
                              <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> <?= lang('PerpanjanganAnggota.label.home') ?></a></li>
                                    <li class="breadcrumb-item"><a href="<?= base_url('perpanjangananggota') ?>"><?= lang('PerpanjanganAnggota.module') ?></a></li>
                                    <li class="active breadcrumb-item" aria-current="page"><?= lang('PerpanjanganAnggota.action.update') ?> <?= lang('PerpanjanganAnggota.module') ?></li>
                              </ol>
                        </nav>
                  </div>
            </div>
      </div>
      <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form <?= lang('PerpanjanganAnggota.action.update') ?> <?= lang('PerpanjanganAnggota.module') ?>
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? ''; ?></div>
                  <?= get_message('message'); ?>

                  <form id="frm" class="col-md-12 mx-auto" method="post" action="">
                        <div class="form-row">
                              <div class="col-md-9">
                                    <div class="position-relative form-group">
                                          <label for="name"><?= lang('PerpanjanganAnggota.field.name') ?>*</label>
                                          <div>
                                                <input type="text" class="form-control" id="name" name="name" placeholder="<?= lang('PerpanjanganAnggota.field.name') ?>" value="<?= set_value('name', $perpanjangananggota->name); ?>" />
                                                <small class="info help-block text-muted"><?= lang('PerpanjanganAnggota.field.name') ?></small>
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-3">
                                    <div class="position-relative form-group">
                                          <label for="sort"><?= lang('PerpanjanganAnggota.field.sort') ?></label>
                                          <div>
                                                <input type="number" class="form-control" id="sort" name="sort" placeholder="<?= lang('PerpanjanganAnggota.field.sort') ?>" value="<?= set_value('sort', $perpanjangananggota->sort) ?>" />
                                                <small class="info help-block text-muted"><?= lang('PerpanjanganAnggota.field.sort') ?></small>
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-group">
                              <label for="description"><?= lang('PerpanjanganAnggota.field.description') ?></label>
                              <div>
                                    <textarea id="description" name="description" placeholder="<?= lang('PerpanjanganAnggota.field.description') ?>" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description', $perpanjangananggota->description) ?></textarea>
                              </div>
                        </div>

                        <div class="form-group">
                              <button type="submit" class="btn btn-primary" name="submit"><?= lang('PerpanjanganAnggota.action.save') ?></button>
                        </div>
                  </form>
            </div>
      </div>
</div>


<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>

<?= $this->endSection('script'); ?>