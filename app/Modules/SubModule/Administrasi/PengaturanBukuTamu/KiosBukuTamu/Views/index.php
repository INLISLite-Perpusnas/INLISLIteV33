<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style') ?>
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>
<?php
$canCreate = is_allowed('master-kios-buku-tamu/create');
$canDelete = is_allowed('master-kios-buku-tamu/delete');
$now = date('Y-m-d H:i:s');
?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-monitor icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Aktivasi Kios
					<div class="page-title-subheading">Buat kode sekali pakai agar perangkat kios dapat membuka buku tamu, baca di tempat, peminjaman dan pengembalian mandiri tanpa login.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
						<li class="breadcrumb-item">Pengaturan Buku Tamu</li>
						<li class="breadcrumb-item">Aktivasi Kios</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<?php if (!empty($new_code)) : ?>
		<div class="alert alert-success" role="alert">
			<div>Kode aktivasi untuk <strong><?= esc($new_code['location']) ?></strong>:</div>
			<div style="font-size: 2rem; font-weight: 700; letter-spacing: .15em; font-family: monospace;"><?= esc($new_code['code']) ?></div>
			<div>Masukkan di kios pada halaman <strong><?= esc(base_url('buku-tamu/lokasi')) ?></strong> sebelum pukul <?= esc($new_code['expires']) ?>. Kode hanya bisa dipakai sekali dan tidak ditampilkan lagi.</div>
		</div>
	<?php endif; ?>

	<?php if ($canCreate) : ?>
		<div class="main-card mb-3 card">
			<div class="card-header">
				<i class="header-icon lnr-laptop icon-gradient bg-plum-plate"> </i> Buat Kode Aktivasi
			</div>
			<div class="card-body">
				<form method="post" action="<?= base_url('master-kios-buku-tamu/create') ?>">
					<?= csrf_field() ?>
					<div class="form-row align-items-end">
						<div class="col-md-6">
							<div class="form-group">
								<label for="Location_id">Lokasi Ruang</label>
								<select class="form-control" id="Location_id" name="Location_id" required>
									<option value="">-- Pilih lokasi --</option>
									<?php foreach ($locations as $location) : ?>
										<option value="<?= esc($location->ID, 'attr') ?>"><?= esc($location->Code . ' - ' . $location->Name) ?></option>
									<?php endforeach ?>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<span class="has-tip" data-tip="Membuat kode acak yang berlaku 10 menit dan hanya bisa dipakai sekali untuk mengaktifkan satu kios."><button type="submit" class="btn btn-primary">Buat Kode</button></span>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	<?php endif; ?>

	<div class="main-card mb-3 card">
		<div class="card-header">
			<i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i> Daftar Kios
		</div>
		<div class="card-body table-responsive">
			<table class="table table-hover table-bordered mb-0">
				<thead>
					<tr>
						<th>Lokasi</th>
						<th>Status</th>
						<th>Kode Dibuat</th>
						<th>Diaktifkan</th>
						<th>IP Kios</th>
						<th>Berlaku Sampai</th>
						<?php if ($canDelete) : ?><th>Aksi</th><?php endif; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($kiosks as $kiosk) : ?>
						<?php
						if ($kiosk->RevokedAt) {
							[$label, $class] = ['Dicabut', 'secondary'];
						} elseif ($kiosk->ActivatedAt) {
							[$label, $class] = $kiosk->ExpiresAt < $now ? ['Kedaluwarsa', 'warning'] : ['Aktif', 'success'];
						} else {
							[$label, $class] = $kiosk->CodeExpiresAt < $now ? ['Kode kedaluwarsa', 'warning'] : ['Menunggu aktivasi', 'info'];
						}
						?>
						<tr>
							<td><?= esc(trim($kiosk->LocationCode . ' - ' . $kiosk->LocationName, ' -')) ?></td>
							<td><span class="badge badge-<?= $class ?> bg-<?= $class ?>"><?= esc($label) ?></span></td>
							<td><?= esc($kiosk->CreateDate) ?></td>
							<td><?= esc($kiosk->ActivatedAt ?? '-') ?></td>
							<td><?= esc($kiosk->ActivatedTerminal ?? '-') ?></td>
							<td><?= esc($kiosk->ExpiresAt ?? '-') ?></td>
							<?php if ($canDelete) : ?>
								<td>
									<?php if (!$kiosk->RevokedAt) : ?>
										<form method="post" action="<?= base_url('master-kios-buku-tamu/delete') ?>" onsubmit="return confirm('Cabut akses kios ini? Kios harus diaktifkan ulang dengan kode baru.');">
											<?= csrf_field() ?>
											<input type="hidden" name="id" value="<?= esc($kiosk->ID, 'attr') ?>">
											<span class="has-tip" data-tip="Mencabut akses kios. Layanan kios di perangkat ini langsung tidak bisa dipakai."><button type="submit" class="btn btn-sm btn-danger">Cabut</button></span>
										</form>
									<?php endif; ?>
								</td>
							<?php endif; ?>
						</tr>
					<?php endforeach ?>
					<?php if (!$kiosks) : ?>
						<tr><td colspan="<?= $canDelete ? 7 : 6 ?>" class="text-center text-muted">Belum ada kios.</td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<?= $this->endSection('script') ?>
