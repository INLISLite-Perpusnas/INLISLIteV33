<?php
$request = service('request'); ?>

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
                        <div><?= lang('UsulanKoleksi.action.update') ?> <?= lang('UsulanKoleksi.module') ?>
                              <div class="page-title-subheading"><?= lang('UsulanKoleksi.form.complete_the_data') ?>.</div>
                        </div>
                  </div>
                  <div class="page-title-actions">
                        <nav class="" aria-label="breadcrumb">
                              <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> <?= lang('UsulanKoleksi.label.home') ?></a></li>
                                    <li class="breadcrumb-item"><a href="<?= base_url('usulankoleksi') ?>"><?= lang('UsulanKoleksi.module') ?></a></li>
                                    <li class="active breadcrumb-item" aria-current="page"><?= lang('UsulanKoleksi.action.update') ?> <?= lang('UsulanKoleksi.module') ?></li>
                              </ol>
                        </nav>
                  </div>
            </div>
      </div>
      <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form <?= lang('UsulanKoleksi.action.update') ?> <?= lang('UsulanKoleksi.module') ?>
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? ''; ?></div>
                  <?= get_message('message'); ?>

                  <form id="frm" class="col-md-12 mx-auto" method="post" action="">
                        <div class="form-row">
                              <div class="col-md-9">
                                    <div class="position-relative form-group">
                                          <label for="name"><?= lang('UsulanKoleksi.field.name') ?>*</label>
                                          <div>
                                                <input type="text" class="form-control" id="name" name="name" placeholder="<?= lang('UsulanKoleksi.field.name') ?>" value="<?= set_value('name', $usulankoleksi->name); ?>" />
                                                <small class="info help-block text-muted"><?= lang('UsulanKoleksi.field.name') ?></small>
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-3">
                                    <div class="position-relative form-group">
                                          <label for="sort"><?= lang('UsulanKoleksi.field.sort') ?></label>
                                          <div>
                                                <input type="number" class="form-control" id="sort" name="sort" placeholder="<?= lang('UsulanKoleksi.field.sort') ?>" value="<?= set_value('sort', $usulankoleksi->sort) ?>" />
                                                <small class="info help-block text-muted"><?= lang('UsulanKoleksi.field.sort') ?></small>
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-group">
                              <label for="description"><?= lang('UsulanKoleksi.field.description') ?></label>
                              <div>
                                    <textarea id="description" name="description" placeholder="<?= lang('UsulanKoleksi.field.description') ?>" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description', $usulankoleksi->description) ?></textarea>
                              </div>
                        </div>

                        <div class="form-group">
                              <button type="submit" class="btn btn-primary" name="submit"><?= lang('UsulanKoleksi.action.save') ?></button>
                        </div>
                  </form>
            </div>
      </div>
</div>


<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>

<?= $this->endSection('script'); ?>