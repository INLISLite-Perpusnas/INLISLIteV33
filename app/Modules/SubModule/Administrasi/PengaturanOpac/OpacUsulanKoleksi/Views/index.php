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
				<div>Usulan Koleksi
					<div class="page-title-subheading">Pengaturan fitur pengusulan koleksi oleh pemustaka pada OPAC.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
						<li class="breadcrumb-item">Administrasi</li>
						<li class="breadcrumb-item">Pengaturan Opac</li>
						<li class="breadcrumb-item">Usulan Koleksi</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="main-card mb-3 card">
				<div class="card-header">
					<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Usulan Koleksi
				</div>
				<div class="card-body">
					<?= get_message('message') ?>
					<form method="post" action="<?= base_url('master-opac-usulan-koleksi/index') ?>">
						<?= csrf_field() ?>
						<div class="position-relative form-group">
							<label>Tampilkan Usulan Koleksi *</label>
							<div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="UsulanKoleksi" id="show_true" value="TRUE" <?= ($setting['UsulanKoleksi'] == 'TRUE') ? 'checked' : '' ?>>
									<label class="form-check-label" for="show_true">Ya</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="UsulanKoleksi" id="show_false" value="FALSE" <?= ($setting['UsulanKoleksi'] != 'TRUE') ? 'checked' : '' ?>>
									<label class="form-check-label" for="show_false">Tidak</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<?php if (is_allowed('master-opac-usulan-koleksi/edit')) : ?>
								<span class="has-tip" data-tip="Simpan pengaturan fitur usulan koleksi."><button type="submit" class="btn btn-catalog-add" name="submit">Simpan</button></span>
							<?php endif; ?>
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
