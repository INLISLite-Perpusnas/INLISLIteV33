<?php helper('app'); ?>
<?= $this->extend('App\Views\layout\blank' ); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-container">
    <div class="h-100 bg-animation">
        <div class="d-flex h-100 justify-content-center align-items-center" style="background-image: linear-gradient(45deg, #0343A7, #00983A);">
            <div class="mx-auto app-login-box" style="opacity:0.85;">
                <div class="app-logo-inverse mx-auto mb-3"></div>
                <div class="modal-dialog w-100 mx-auto" style="box-shadow:none;">
                    <div class="modal-content" style="background-color:#fefefe; border-radius:15px; padding: 30px; border:none;">
						<form class="" action="<?= route_to('login') ?>" method="post">
							<?= csrf_field() ?>

							<div class="text-center">
								<?php if (get_parameter('show-logo-login') == 1) : ?>
									<a href="<?= base_url() ?>"><img src="<?= base_url(get_parameter('logo')) ?>" width="130"/></a>
								<?php endif; ?>

								<div class="text-center; font-weight-bold; text-dark; mb-3; mt-3">
									<span style="font-size:20px;"><?=get_parameter('site-description')?></span>
								</div>
							</div>

							<div class="modal-body">
								<div id="infoMessage" class="bg-corporate-secondary text-white">
									<?= view('Myth\Auth\Views\_message_block') ?>
								</div>

								<div class="form-row">
									<div class="col-md-12">
										<div class="position-relative form-group">
											<input type="text" class="form-control text-dark form-control" name="login" placeholder="Masukkan Username" style="border-color: #0343A7; border-radius: 15px; padding: 15px; background-color:#fefefe;">
										</div>
									</div>
									<div class="col-md-12 mb-1">
										<div class="position-relative form-group">
											<input type="password" class="form-control text-dark form-control" name="password"  placeholder="Masukkan Kata Sandi" style="border-color: #0343A7; border-radius: 15px; padding: 15px; background-color:#fefefe">
										</div>
									</div>
								</div>

								<div class="text-center">
									<button type="submit" class="btn btn-primary btn-lg btn-block" style="border-radius: 17px; padding:8px 0px; background-color: #0343A7">
										<span style="font-size:16px;">Login</span>
									</button>
								<div>
								<?php if ($config->activeResetter) : ?>
								<div class="text-center mt-3">
									<a href="<?= route_to('forgot') ?>" class="text-white" style="font-weight-100 font-size:18px; text-decoration:none;">Lupa Kata Sandi</a>
								</div>
								<?php endif;?>

								<?php if ($config->allowRegistration) : ?>
								<div class="divider"></div>
								<div class="text-center">
									<a href="<?= route_to('register') ?>" class="text-dark" style="font-weight:bold; font-size:18px; text-decoration:none;">Tidak memiliki akun? Daftar</a>
								</div>
								<?php endif;?>
							</div>
                        </form>
                    </div>
                </div>
                <div class="text-center text-dark opacity-8 mt-3"><?= ( date('Y') . ' Perpustakaan Nasioanl RI') ?></div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>