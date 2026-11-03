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
				<div>Koleksi Terbaru
					<div class="page-title-subheading">Pengaturan tampilan koleksi terbaru pada halaman OPAC.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
						<li class="breadcrumb-item">Administrasi</li>
						<li class="breadcrumb-item">Pengaturan Opac</li>
						<li class="breadcrumb-item">Koleksi Terbaru</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="main-card mb-3 card">
				<div class="card-header">
					<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Koleksi Terbaru
				</div>
				<div class="card-body">
					<?= get_message('message') ?>
					<form method="post" action="<?= base_url('master-opac-koleksi-terbaru/index') ?>">
						<?= csrf_field() ?>
						<div class="position-relative form-group">
							<label>Tampilkan Koleksi Terbaru *</label>
							<div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="ShowKoleksiTerbaru" id="show_true" value="TRUE" <?= ($setting['ShowKoleksiTerbaru'] == 'TRUE') ? 'checked' : '' ?>>
									<label class="form-check-label" for="show_true">Ya</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="ShowKoleksiTerbaru" id="show_false" value="FALSE" <?= ($setting['ShowKoleksiTerbaru'] != 'TRUE') ? 'checked' : '' ?>>
									<label class="form-check-label" for="show_false">Tidak</label>
								</div>
							</div>
						</div>
						<div class="position-relative form-group">
							<label for="KoleksiTerbaruShow">Maksimal Jumlah Koleksi *</label>
							<input type="number" min="1" class="form-control" name="KoleksiTerbaruShow" id="KoleksiTerbaruShow" value="<?= esc($setting['KoleksiTerbaruShow']) ?>" required>
						</div>
						<div class="form-group">
							<?php if (is_allowed('master-opac-koleksi-terbaru/edit')) : ?>
								<span class="has-tip" data-tip="Simpan pengaturan section Koleksi Terbaru di beranda. Cache beranda langsung dibersihkan."><button type="submit" class="btn btn-catalog-add" name="submit">Simpan</button></span>
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
