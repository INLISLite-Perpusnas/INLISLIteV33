<?= $this->extend('layout/backend/blank'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>
<?= $this->section('page'); ?>
<div class="app-container">
      <div class="h-100">
            <div class="h-100 no-gutters row">
                  <div class="h-100 d-flex bg-white justify-content-center align-items-center col-md-12 col-lg-7">
                        <div class="mx-auto app-login-box col-sm-12 col-md-10 col-lg-9">
                              <center>
                                    <?php if (get_parameter('show-logo-login') == 1) : ?>
                                          <a href="<?= base_url() ?>"><img src="<?= base_url(get_parameter('logo')) ?>" width="400" class="mb-4" /></a>
                                    <?php endif; ?>
                                    <h5 class="mb-0"><?= strtoupper(get_parameter('site-description')) ?></h5>
                              </center>
                              <div class="divider row"></div>
                              <div id="infoMessage"><?= $message ?? ''; ?></div>
                              <div>
                                    <div id="infoMessage"><?= $message ?? ''; ?></div>
                                    <?= get_message('message'); ?>
                                    <form id="frm_register_user" method="post" action="<?= base_url('user/register'); ?>">
                                          <div class="form-row">
                                                <div class="col-md-12">
                                                      <div class="position-relative form-group"><label for="type" class=""><span class="text-danger">*</span> Tipe</label>
                                                            <select class="form-control" name="type" id="type" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                                                  <option value="perorangan">Perorangan</option>
                                                                  <option value="badan hukum">Badan Hukum</option>
                                                            </select>
                                                      </div>
                                                </div>
                                                <div class="col-md-6">
                                                      <div class="position-relative form-group"><label for="first_name" class=""><span class="text-danger">*</span> Nama Depan</label><input name="first_name" id="first_name" placeholder="Nama Depan" type="text" class="form-control" value="<?= set_value('first_name'); ?>"></div>
                                                </div>
                                                <div class="col-md-6">
                                                      <div class="position-relative form-group"><label for="last_name" class=""><span class="text-danger">*</span> Nama Belakang</label><input name="last_name" id="last_name" placeholder="Nama Belakang" type="text" class="form-control" value="<?= set_value('last_name'); ?>"></div>
                                                </div>
                                                <div class="col-md-6">
                                                      <div class="position-relative form-group"><label for="email" class=""><span class="text-danger">*</span> Alamat Email</label><input name="email" id="email" placeholder="Alamat Email" type="text" class="form-control" value="<?= set_value('email'); ?>"></div>
                                                </div>
                                                <div class="col-md-6">
                                                      <div class="position-relative form-group"><label for="phone" class="">No. Handphone</label><input name="phone" id="phone" placeholder="No. Handphone" type="text" class="form-control" value="<?= set_value('phone'); ?>"></div>
                                                </div>
                                                <div class="col-md-6">
                                                      <div class="position-relative form-group"><label for="password" class=""><span class="text-danger">*</span> Password</label><input name="password" id="password" placeholder="Password" type="password" class="form-control" value="<?= set_value('password'); ?>"> <small class="info help-block">Min 8 karakter. </small> </div>
                                                </div>
                                                <div class="col-md-6">
                                                      <div class="position-relative form-group"><label for="password_confirm" class=""><span class="text-danger">*</span> Ulangi Password</label><input name="password_confirm" id="password_confirm" placeholder="Confirm password" type="password" class="form-control"></div>
                                                </div>
                                          </div>
                                          <div class="mt-3 position-relative form-check"><input name="check" id="exampleCheck" type="checkbox" class="form-check-input"><label for="exampleCheck" class="form-check-label">Setujui <a href="javascript:void(0);">Syarat dan Kententuan</a>.</label></div>
                                          <div class="mt-4 d-flex align-items-center">
                                                <h5 class="mb-0">Sudah memiliki Akun? <a href="<?= base_url('auth/login') ?>" class="text-primary">Masuk</a></h5>
                                                <div class="ml-auto">
                                                      <button type="submit" class="btn btn-primary btn-wide btn-pill btn-shadow btn-hover-shine btn-lg" name="submit">Daftar</button>
                                                </div>
                                          </div>
                                    </form>
                              </div>
                        </div>
                  </div>
                  <div class="d-none d-lg-block col-lg-5">
                        <div class="slider-light">
                              <div class="slick-slider">
                                    <?php foreach ($banners as $row) : ?>
                                          <div>
                                                <div class="position-relative h-100 d-flex justify-content-center align-items-center bg-premium-dark" tabindex="-1">
                                                      <div class="slide-img-bg" style="background-image: url('<?= base_url('uploads/banner/' . $row->file_image); ?>');"></div>
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
            </div>
      </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<!--Slick Carousel -->
<script>
      $('.select2').select2()
</script>
<script src="<?= base_url('themes/uigniter'); ?>/js/vendors/carousel-slider.js"></script>
<script src="<?= base_url('themes/uigniter'); ?>/js/scripts-init/carousel-slider.js"></script>
<?= $this->endSection('script'); ?>