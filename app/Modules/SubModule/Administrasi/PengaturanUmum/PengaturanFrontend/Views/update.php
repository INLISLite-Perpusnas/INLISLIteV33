<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-server icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Pengaturan Cache & Halaman Frontend
					<div class="page-title-subheading">Aktifkan / nonaktifkan cache dan fitur halaman frontend.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
						<li class="breadcrumb-item">Administrasi</li>
						<li class="breadcrumb-item">Pengaturan Umum</li>
						<li class="breadcrumb-item">Cache & Halaman Frontend</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<form method="post" action="<?= base_url('master-pengaturan-frontend/index') ?>">
		<?= csrf_field() ?>
		<?php foreach ($groups as $group => $features) : ?>
			<div class="main-card mb-3 card">
				<div class="card-header">
					<i class="header-icon lnr-<?= $group === 'Cache' ? 'database' : 'laptop' ?> icon-gradient bg-plum-plate"> </i> <?= esc($group) ?>
				</div>
				<div class="card-body">
					<div class="form-row">
						<?php foreach ($features as $name => $toggle) : ?>
							<div class="col-md-3 col-sm-6">
								<div class="form-group">
									<label for="toggle_<?= esc($name) ?>"><?= esc($toggle['label']) ?>
										<span class="has-tip" data-tip="<?= esc($toggle['description'], 'attr') ?>"><i class="fa fa-info-circle text-muted"></i></span>
									</label><br>
									<span class="has-tip" data-tip="<?= esc($toggle['description'], 'attr') ?>">
										<input type="checkbox" id="toggle_<?= esc($name) ?>" name="feature_toggles[<?= esc($name) ?>]" value="1" data-toggle="toggle" data-onstyle="success" data-on="Aktif" data-off="Nonaktif" data-size="normal" <?= $toggle['checked'] ? 'checked' : '' ?>>
									</span>
									<?php if ($toggle['ttl']) : ?>
										<div class="mt-2">
											<label for="<?= esc($toggle['ttl']['name']) ?>" class="small mb-1">Waktu cache
												<span class="has-tip" data-tip="Lama data disimpan di cache sebelum diambil ulang dari database (1–1440 menit)."><i class="fa fa-info-circle text-muted"></i></span>
											</label>
											<div class="input-group input-group-sm" style="max-width: 170px;">
												<input type="number" min="1" max="1440" class="form-control" name="<?= esc($toggle['ttl']['name']) ?>" id="<?= esc($toggle['ttl']['name']) ?>" value="<?= esc($toggle['ttl']['value']) ?>" required>
												<div class="input-group-append"><span class="input-group-text">menit</span></div>
											</div>
										</div>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach ?>
					</div>
				</div>
			</div>
		<?php endforeach ?>

		<div class="form-group">
			<?php if (is_allowed('master-pengaturan-frontend/edit')) : ?>
				<span class="has-tip" data-tip="Simpan semua pengaturan cache & halaman frontend. Cache beranda langsung dibersihkan."><button type="submit" class="btn btn-primary" name="submit">Simpan</button></span>
			<?php endif; ?>
		</div>
	</form>
</div>
<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<?= $this->endSection('script') ?>
