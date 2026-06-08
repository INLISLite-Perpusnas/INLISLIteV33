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
                        <div><?= lang('Perpanjangan.action.update') ?> <?= lang('Perpanjangan.module') ?>
                              <div class="page-title-subheading"><?= lang('Perpanjangan.form.complete_the_data') ?>.</div>
                        </div>
                  </div>
                  <div class="page-title-actions">
                        <nav class="" aria-label="breadcrumb">
                              <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> <?= lang('Perpanjangan.label.home') ?></a></li>
                                    <li class="breadcrumb-item"><a href="<?= base_url('perpanjangan') ?>"><?= lang('Perpanjangan.module') ?></a></li>
                                    <li class="active breadcrumb-item" aria-current="page"><?= lang('Perpanjangan.action.update') ?> <?= lang('Perpanjangan.module') ?></li>
                              </ol>
                        </nav>
                  </div>
            </div>
      </div>
      <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form <?= lang('Perpanjangan.action.update') ?> <?= lang('Perpanjangan.module') ?>
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? ''; ?></div>
                  <?= get_message('message'); ?>

                  <form id="frm" class="col-md-12 mx-auto" method="post" action="">
                        <div class="form-row">
                              <div class="col-md-9">
                                    <div class="position-relative form-group">
                                          <label for="name"><?= lang('Perpanjangan.field.name') ?>*</label>
                                          <div>
                                                <input type="text" class="form-control" id="name" name="name" placeholder="<?= lang('Perpanjangan.field.name') ?>" value="<?= set_value('name', $perpanjangan->name); ?>" />
                                                <small class="info help-block text-muted"><?= lang('Perpanjangan.field.name') ?></small>
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-3">
                                    <div class="position-relative form-group">
                                          <label for="sort"><?= lang('Perpanjangan.field.sort') ?></label>
                                          <div>
                                                <input type="number" class="form-control" id="sort" name="sort" placeholder="<?= lang('Perpanjangan.field.sort') ?>" value="<?= set_value('sort', $perpanjangan->sort) ?>" />
                                                <small class="info help-block text-muted"><?= lang('Perpanjangan.field.sort') ?></small>
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-group">
                              <label for="description"><?= lang('Perpanjangan.field.description') ?></label>
                              <div>
                                    <textarea id="description" name="description" placeholder="<?= lang('Perpanjangan.field.description') ?>" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description', $perpanjangan->description) ?></textarea>
                              </div>
                        </div>

                        <div class="form-group">
                              <button type="submit" class="btn btn-primary" name="submit"><?= lang('Perpanjangan.action.save') ?></button>
                        </div>
                  </form>
            </div>
      </div>
</div>


<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>

<?= $this->endSection('script'); ?>