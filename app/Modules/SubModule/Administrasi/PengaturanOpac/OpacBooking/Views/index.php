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
				<div>Pemesanan Koleksi
					<div class="page-title-subheading">Pengaturan pemesanan (booking) koleksi melalui OPAC.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
						<li class="breadcrumb-item">Administrasi</li>
						<li class="breadcrumb-item">Pengaturan Opac</li>
						<li class="breadcrumb-item">Pemesanan Koleksi</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="main-card mb-3 card">
				<div class="card-header">
					<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Pemesanan Koleksi
				</div>
				<div class="card-body">
					<?= get_message('message') ?>
					<form method="post" action="<?= base_url('master-opac-booking/index') ?>">
						<?= csrf_field() ?>
						<div class="position-relative form-group">
							<label>Aktifkan Booking</label>
							<div class="form-check">
								<input class="form-check-input" type="checkbox" name="IsBookingActivated" id="IsBookingActivated" value="1" <?= ($setting['IsBookingActivated'] == '1') ? 'checked' : '' ?>>
								<label class="form-check-label" for="IsBookingActivated">Ya</label>
							</div>
						</div>
						<div class="position-relative form-group">
							<label for="JumlahBookingMaksimal">Jumlah Pemesanan Maksimal *</label>
							<input type="number" min="1" class="form-control" name="JumlahBookingMaksimal" id="JumlahBookingMaksimal" value="<?= esc($setting['JumlahBookingMaksimal']) ?>" required>
						</div>
						<div class="position-relative form-group">
							<label for="BookingExpired">Lama Pemesanan Sebelum Kadaluarsa (jam:menit:detik) *</label>
							<input type="time" step="1" class="form-control" name="BookingExpired" id="BookingExpired" value="<?= esc($setting['BookingExpired']) ?>" required>
						</div>
						<div class="alert alert-info">
							<h6><i class="fa fa-info-circle"></i> Catatan</h6>
							Apabila masa jatuh tempo tersisa setelah jam tutup perpustakaan, maka akan diakumulasikan setelah jam buka pada hari operasional berikutnya.
						</div>
						<div class="form-group">
							<?php if (is_allowed('master-opac-booking/edit')) : ?>
								<span class="has-tip" data-tip="Simpan pengaturan pemesanan (booking) koleksi di OPAC."><button type="submit" class="btn btn-catalog-add" name="submit">Simpan</button></span>
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
