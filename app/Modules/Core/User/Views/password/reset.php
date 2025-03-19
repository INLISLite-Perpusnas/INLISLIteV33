<?= $this->extend('layout/backend/blank'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-container">
      <div class="h-100">
            <div class="h-100 no-gutters row">
                  <div class="d-none d-lg-block col-lg-7">
                        <div class="slider-light">
                              <div class="slick-slider">
                                    <?php foreach ($banners as $row) : ?>
                                          <div>
                                                <div class="position-relative h-100 d-flex justify-content-center align-items-center bg-primary" tabindex="-1">
                                                      <div class="slide-img-bg" style="background-image: url('<?= base_url('uploads/banner/' . $row->file_image) ?>');"></div>
                                                      <div class="slider-content">
                                                            <h2>
                                                                  <?= $row->name ?>
                                                            </h2>
                                                            <h5>
                                                                  <?= $row->description ?>
                                                            </h5>
                                                      </div>
                                                </div>
                                          </div>
                                    <?php endforeach; ?>
                              </div>
                        </div>
                  </div>
                  <div class="h-100 d-flex bg-white justify-content-center align-items-center col-md-12 col-lg-5">
                        <div class="mx-auto app-login-box col-sm-12 col-md-10 col-lg-9">
                              <div class="app-logo">
                              </div>
                              <center>
                                    <?php if (get_parameter('show-logo-login') == 1) : ?>
                                          <a href="<?= base_url() ?>"><img src="<?= base_url('balisa_favicon.png') ?>" width="200" class="mb-4" /></a>
                                    <?php endif; ?>
                                    <h4 class="mb-0"><?= strtoupper(get_parameter('site-description')) ?></h4>
                              </center>
                              <div class="divider row"></div>
                              <div id="infoMessage"><?= $message ?? ''; ?></div>
                              <div>
                                    <form class="" action="<?= base_url('/auth/reset_password/' . $code); ?>" method="post">
                                          <div class="form-row">
                                                <div class="col-md-12">
                                                      <div class="position-relative form-group"><label for="new" class="">New Password</label><input name="new" id="new" placeholder="" type="password" class="form-control"></div>
                                                </div>
                                          </div>
                                          <div class="form-row">
                                                <div class="col-md-12">
                                                      <div class="position-relative form-group"><label for="new_confirm" class="">Confirm New Password</label><input name="new_confirm" id="new_confirm" placeholder="" type="password" class="form-control"></div>
                                                </div>
                                          </div>
                                          <?php echo form_hidden('user_id', $user_id); ?>
                                          <div class="mt-4 d-flex align-items-center">
                                                <h6 class="mb-0"><a href="<?= base_url('auth/login') ?>" class="text-primary">Sign in existing account</a></h6>
                                                <div class="ml-auto">
                                                      <button class="btn btn-primary btn-lg">Update Password</button>
                                                </div>
                                          </div>
                                    </form>
                              </div>
                        </div>
                  </div>
            </div>
      </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<!--Slick Carousel -->
<script src="<?= base_url('themes/uigniter'); ?>/js/vendors/carousel-slider.js"></script>
<script src="<?= base_url('themes/uigniter'); ?>/js/scripts-init/carousel-slider.js"></script>
<?= $this->endSection('script'); ?>