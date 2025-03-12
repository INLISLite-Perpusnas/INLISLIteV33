<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style') ?>
<style>
	.select2 {
		text-transform: none;
		font-weight: normal;
	}
</style>
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-server icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Pengaturan Setting Perpanjangan Keanggotaan Mandiri
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
						<li class="breadcrumb-item">Administrasi</li>
						<li class="breadcrumb-item">Pengaturan Katalog</li>
						<li class="breadcrumb-item active">Pengaturan Setting Perpanjangan Keanggotaan Mandiri</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="main-card mb-3 card">
				<div class="card-header">
					<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i>Pengaturan Setting Perpanjangan Keanggotaan Mandiri
				</div>
				<div class="card-body">
					<div id="infoMessage"><?= $message ?? '' ?></div> <?= get_message('message') ?>
					<form id="frm_create" method="post" action="<?= base_url('master-perpanjangan-keangotaan-mandiri') ?>" onsubmit="return validateForm()">
						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="alias">Pengaturan Setting Perpanjangan Keanggotaan Mandiri*</label>
									<div>
										<?php $parameter = get_setting_parameter('PerpanjanganKeanggotaanMandiri', is_profiling()); ?>
										<div class="form-check">
											<input class="form-check-input" type="radio" name="parameter" id="simple" value="1" <?= ($parameter == 1) ? 'checked' : '' ?>>
											<label class="form-check-label" for="simple">
												Aktif
											</label>
										</div>
										<div class="form-check">
											<input class="form-check-input" type="radio" name="parameter" id="advance" value="0" <?= ($parameter == 0) ? 'checked' : '' ?>>
											<label class="form-check-label" for="advance">
												Tidak Aktif
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="form-group">
							<button type="submit" class="btn btn-primary" name="submit">Simpan</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<?= $this->endSection('script') ?>