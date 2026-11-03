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
				<div>Pengaturan Faset
					<div class="page-title-subheading">Jumlah baris faset yang ditampilkan pada hasil pencarian OPAC.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
						<li class="breadcrumb-item">Administrasi</li>
						<li class="breadcrumb-item">Pengaturan Opac</li>
						<li class="breadcrumb-item">Pengaturan Faset</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<?= get_message('message') ?>
	<form method="post" action="<?= base_url('master-opac-faset/index') ?>">
		<?= csrf_field() ?>
		<div class="row">
			<?php foreach ($faset as $prefix => $label) : ?>
				<div class="col-md-4">
					<div class="main-card mb-3 card">
						<div class="card-header">
							<i class="header-icon lnr-funnel icon-gradient bg-plum-plate"> </i> <?= esc($label) ?>
						</div>
						<div class="card-body">
							<div class="position-relative form-group">
								<label for="<?= $prefix ?>Max">Maksimal Jumlah Baris *</label>
								<input type="number" min="1" class="form-control" name="<?= $prefix ?>Max" id="<?= $prefix ?>Max" value="<?= esc($setting[$prefix . 'Max']) ?>" required>
							</div>
							<div class="position-relative form-group mb-0">
								<label for="<?= $prefix ?>Min">Minimal Jumlah Baris *</label>
								<input type="number" min="1" class="form-control" name="<?= $prefix ?>Min" id="<?= $prefix ?>Min" value="<?= esc($setting[$prefix . 'Min']) ?>" required>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="form-group">
			<?php if (is_allowed('master-opac-faset/edit')) : ?>
				<span class="has-tip" data-tip="Simpan jumlah baris semua faset pada hasil pencarian OPAC."><button type="submit" class="btn btn-catalog-add" name="submit">Simpan</button></span>
			<?php endif; ?>
		</div>
	</form>
</div>
<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<?= $this->endSection('script') ?>
