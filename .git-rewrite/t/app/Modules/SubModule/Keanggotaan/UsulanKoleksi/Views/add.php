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
                        <div><?= lang('UsulanKoleksi.action.add') ?> <?= lang('UsulanKoleksi.module') ?>
                              <div class="page-title-subheading"><?= lang('UsulanKoleksi.form.complete_the_data') ?>.</div>
                        </div>
                  </div>
                  <div class="page-title-actions">
                        <nav class="" aria-label="breadcrumb">
                              <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                                    <li class="breadcrumb-item"><a href="<?= base_url('usulankoleksi') ?>"><?= lang('UsulanKoleksi.module') ?></a></li>
                                    <li class="active breadcrumb-item" aria-current="page"><?= lang('UsulanKoleksi.action.add') ?> <?= lang('UsulanKoleksi.module') ?></li>
                              </ol>
                        </nav>
                  </div>
            </div>
      </div>
      <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form <?= lang('UsulanKoleksi.action.add') ?> <?= lang('UsulanKoleksi.module') ?>
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? ''; ?></div>
                  <?= get_message('message'); ?>

                  <form id="frm_create" class="col-md-12 mx-auto" method="post" action="<?= base_url('usulankoleksi/create'); ?>">

                        <div class="form-group">
                              <label for="title"><?= lang('UsulanKoleksi.field.title') ?> </label>
                              <div>
                                    <input type="text" class="form-control" id="frm_create_title" name="title" placeholder="<?= lang('UsulanKoleksi.field.title') ?> " value="<?= set_value('title'); ?>" />
                                    <small class="info help-block text-muted"><?= lang('UsulanKoleksi.field.title') ?></small>
                              </div>
                        </div>

                        <div class="form-group">
                              <label for="author"><?= lang('UsulanKoleksi.field.author') ?> </label>
                              <div>
                                    <input type="text" class="form-control" id="frm_create_author" name="author" placeholder="<?= lang('UsulanKoleksi.field.author') ?> " value="<?= set_value('author'); ?>" />
                                    <small class="info help-block text-muted"><?= lang('UsulanKoleksi.field.author') ?></small>
                              </div>
                        </div>

                        <div class="form-group">
                              <label for="publisher"><?= lang('UsulanKoleksi.field.publisher') ?> </label>
                              <div>
                                    <input type="text" class="form-control" id="frm_create_publisher" name="publisher" placeholder="<?= lang('UsulanKoleksi.field.publisher') ?> " value="<?= set_value('publisher'); ?>" />
                                    <small class="info help-block text-muted"><?= lang('UsulanKoleksi.field.publisher') ?></small>
                              </div>
                        </div>

                        <div class="form-row">
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="publishyear"><?= lang('UsulanKoleksi.field.publishyear') ?></label>
                                          <div>
                                                <input type="number" class="form-control" id="frm_create_publishyear" name="publishyear" placeholder="<?= lang('UsulanKoleksi.field.publishyear') ?> " value="<?= set_value('publishyear'); ?>" />
                                                <small class="info help-block text-muted"><?= lang('UsulanKoleksi.field.publishyear') ?></small>
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="isbn"><?= lang('UsulanKoleksi.field.isbn') ?> </label>
                                          <div>
                                                <input type="number" class="form-control" id="frm_create_isbn" name="isbn" placeholder="<?= lang('UsulanKoleksi.field.isbn') ?> " value="<?= set_value('isbn') ?>" />
                                                <small class="info help-block text-muted"><?= lang('UsulanKoleksi.field.isbn') ?></small>
                                          </div>
                                    </div>
                              </div>
                        </div>


                        <div class="form-group">
                              <label for="description"><?= lang('UsulanKoleksi.field.description') ?> </label>
                              <div>
                                    <textarea id="frm_create_description" name="description" placeholder="<?= lang('UsulanKoleksi.field.description') ?> " rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description') ?></textarea>
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