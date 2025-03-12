<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
      <div class="app-page-title">
            <div class="page-title-wrapper">
                  <div class="page-title-heading">
                        <div class="page-title-icon">
                              <i class="pe-7s-lock icon-gradient bg-strong-bliss"></i>
                        </div>
                        <div>Ubah Password
                              <div class="page-title-subheading">Ubah password akun Anda</div>
                        </div>
                  </div>
                  <div class="page-title-actions">
                        <nav class="" aria-label="breadcrumb">
                              <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                                    <li class="active breadcrumb-item" aria-current="page">Ubah Password</li>
                              </ol>
                        </nav>
                  </div>
            </div>
      </div>
      <div class="main-card mb-3 card">
            <div class="card-header">
                  <i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Form Ubah Password
            </div>
            <div class="card-body">
                  <div id="infoMessage"><?= $message ?? ''; ?></div>
                  <?= get_message('message'); ?>
                  <form id="frm_change_password" class="col-md-12 mx-auto" method="post" action="<?= base_url('user/change_password/'); ?>">
                        <div class="form-row">
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="password">Password Lama*</label>
                                          <div class="input-group" id="show_hide_password_old">
                                                <input type="password" class="form-control" id="password_old" name="password_old" placeholder="Password Lama" value="<?= set_value('password_old') ?>" />
                                                <div class="input-group-append">
                                                      <a class="btn btn-primary" href="#"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                                </div>
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <div class="form-row">
                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="password">Password*</label>
                                          <div class="input-group" id="show_hide_password">
                                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="<?= set_value('password') ?>">
                                                <div class="input-group-append">
                                                      <a class="btn btn-primary" href="#"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                                </div>
                                          </div>
                                          <!-- <span id="strength">Type Password</span><br> -->
                                          <small class="info help-block">Password Security Policy:
                                                <ul>
                                                      <li id="firstRegex">Password diawali huruf kapital (A-Z)</li>
                                                      <li>Password mengandung minimal 1 angka (0-9)</li>
                                                      <li>Password mengandung minimal 1 karakter spesial (!@#%)</li>
                                                      <li>Password memiliki panjang 8-15 karakter</li>
                                                </ul>
                                          </small>
                                    </div>
                              </div>

                              <div class="col-md-6">
                                    <div class="position-relative form-group">
                                          <label for="password">Konfirmasi Password*</label>
                                          <div class="input-group" id="show_hide_password_confirm">
                                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Konfirmasi Password" value="<?= set_value('password_confirm') ?>" />
                                                <div class="input-group-append">
                                                      <a class="btn btn-primary" href="#"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                                </div>
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <?php echo form_hidden('id', get_user_id()); ?>
                        <div class="form-group">
                              <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
                        </div>
                  </form>
            </div>
      </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
      $(document).ready(function() {
            $("#show_hide_password_old a").on('click', function(event) {
                  event.preventDefault();
                  if ($('#show_hide_password_old input').attr("type") == "text") {
                        $('#show_hide_password_old input').attr('type', 'password');
                        $('#show_hide_password_old i').addClass("fa-eye-slash");
                        $('#show_hide_password_old i').removeClass("fa-eye");
                  } else if ($('#show_hide_password_old input').attr("type") == "password") {
                        $('#show_hide_password_old input').attr('type', 'text');
                        $('#show_hide_password_old i').removeClass("fa-eye-slash");
                        $('#show_hide_password_old i').addClass("fa-eye");
                  }
            });

            $("#show_hide_password a").on('click', function(event) {
                  event.preventDefault();
                  if ($('#show_hide_password input').attr("type") == "text") {
                        $('#show_hide_password input').attr('type', 'password');
                        $('#show_hide_password i').addClass("fa-eye-slash");
                        $('#show_hide_password i').removeClass("fa-eye");
                  } else if ($('#show_hide_password input').attr("type") == "password") {
                        $('#show_hide_password input').attr('type', 'text');
                        $('#show_hide_password i').removeClass("fa-eye-slash");
                        $('#show_hide_password i').addClass("fa-eye");
                  }
            });

            $("#show_hide_password_confirm a").on('click', function(event) {
                  event.preventDefault();
                  if ($('#show_hide_password_confirm input').attr("type") == "text") {
                        $('#show_hide_password_confirm input').attr('type', 'password');
                        $('#show_hide_password_confirm i').addClass("fa-eye-slash");
                        $('#show_hide_password_confirm i').removeClass("fa-eye");
                  } else if ($('#show_hide_password_confirm input').attr("type") == "password") {
                        $('#show_hide_password_confirm input').attr('type', 'text');
                        $('#show_hide_password_confirm i').removeClass("fa-eye-slash");
                        $('#show_hide_password_confirm i').addClass("fa-eye");
                  }
            });
      });
</script>
<?= $this->endSection('script'); ?>