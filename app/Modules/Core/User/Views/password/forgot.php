<?= $this->extend('layout/backend/blank'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-container">
      <div class="h-100">
            <div class="h-100 no-gutters row">
                  <div class="d-none d-lg-block col-lg-6">
                        <div class="slider-light">
                              <div class="slick-slider">
                                    <div>
                                          <div class="position-relative h-100 d-flex justify-content-center align-items-center bg-sunny-morning" tabindex="-1">
                                                <div class="slide-img-bg opacity-6" style="background-image: url('<?= base_url('themes/uigniter'); ?>/images/originals/citydark.jpg');"></div>
                                                <div class="slider-content">
                                                      <h3>Complex, but lightweight</h3>
                                                      <p>We've included a lot of components that cover almost all use cases for any type of application.</p>
                                                </div>
                                          </div>
                                    </div>
                                    <div>
                                          <div class="position-relative h-100 d-flex justify-content-center align-items-center bg-plum-plate" tabindex="-1">
                                                <div class="slide-img-bg" style="background-image: url('<?= base_url('themes/uigniter'); ?>/images/originals/city.jpg');"></div>
                                                <div class="slider-content">
                                                      <h3>Perfect Balance</h3>
                                                      <p>UIgniter is like a dream. Some think it's too good to be true! Extensive collection of unified React Boostrap Components and Elements.</p>
                                                </div>
                                          </div>
                                    </div>
                                    <div>
                                          <div class="position-relative h-100 d-flex justify-content-center align-items-center bg-premium-dark" tabindex="-1">
                                                <div class="slide-img-bg" style="background-image: url('<?= base_url('themes/uigniter'); ?>/images/originals/citynights.jpg');"></div>
                                                <div class="slider-content">
                                                      <h3>Scalable, Modular, Consistent</h3>
                                                      <p>Easily exclude the components you don't require. Lightweight, consistent Bootstrap based styles across all elements and components</p>
                                                </div>
                                          </div>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="h-100 d-flex bg-white justify-content-center align-items-center col-lg-6">
                        <div class="mx-auto app-login-box col-sm-12 col-md-10 col-lg-9">
                              <h4>
                                    <div>Forgot Password</div>
                                    <span>Use the form below to recover your password.</span>
                              </h4>
                              <div class="divider row"></div>
                              <div id="infoMessage"><?= $message ?? ''; ?></div>
                              <div>
                                    <form class="" action="<?= base_url('auth/forgot_password'); ?>" method="post">
                                          <div class="form-row">
                                                <div class="col-md-12">
                                                      <div class="position-relative form-group"><label for="identity" class=""><?= $identity_label; ?></label><input name="identity" id="identity" placeholder="" type="text" class="form-control"></div>
                                                </div>
                                          </div>
                                          <div class="mt-4 d-flex align-items-center">
                                                <h6 class="mb-0"><a href="<?= base_url('auth/login') ?>" class="text-primary">Sign in existing account</a></h6>
                                                <div class="ml-auto">
                                                      <button class="btn btn-primary btn-lg">Recover Password</button>
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
<?= $this->endSection('script'); ?>