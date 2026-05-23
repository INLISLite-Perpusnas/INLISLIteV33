<?php
$request = \Config\Services::request();
$request->getUri()->setSilent();
$slug = $request->getGet('slug');
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
				<div>Form Entri Keanggotaan
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
						<li class="breadcrumb-item">Administrasi</li>
						<li class="breadcrumb-item">Pengaturan Katalog</li>
						<li class="breadcrumb-item">Form Entri Keanggotaan</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="main-card mb-3 card">
				<div class="card-header">
					<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Form Entri Keanggotaan
				</div>
				<div class="card-body">
					<div id="infoMessage"><?= $message ?? '' ?></div> <?= get_message('message') ?>
					<form id="frm_create" method="post" action="<?= base_url('master-entri-keanggotaan/update_data') ?>" onsubmit="return validateForm()">
						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="alias">Penentuan Nomor Anggota</label>
									<div>
										<?php $parameter = $TipeNomorAnggota; ?>
										<div class="form-check">
											<input class="form-check-input" type="radio" name="TipeNomorAnggota" id="Otomatis" value="Otomatis" <?= ($parameter == "Otomatis") ? 'checked' : '' ?>>
											<label class="form-check-label" for="simple">
												Otomatis
											</label>
										</div>
										<div class="form-check">
											<input class="form-check-input" type="radio" name="TipeNomorAnggota" id="Manual" value="Manual" <?= ($parameter == "Manual") ? 'checked' : '' ?>>
											<label class="form-check-label" for="advance">
												Manual
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="form-row form-for-otomatis" style="<?=  $TipeNomorAnggota == 'Manual' ? 'display:none;' : '' ?>">
							<label>Tipe Penomoran Anggota</label>
							<?php $parameter = $TipePenomoranAnggota; ?>
							<select class="form-control" name="TipePenomoranAnggota" id="TipePenomoranAnggota">
								<option value="">-- Pilih Tipe Penomoran Anggota --</option>
								<option value="1" <?= ($parameter == "1") ? 'selected' : '' ?>>1 (YYMMDD99999)</option>
								<option value="2" <?= ($parameter == "2") ? 'selected' : '' ?>>2(YYYYMM99)</option>
								<option value="3"	<?= ($parameter == "3") ? 'selected' : '' ?>>3(99999L2015)</option>
								<option value="4"<?= ($parameter == "4") ? 'selected' : '' ?>>4(NIK)</option>
							</select>
						</div>

						<div class="form-row form-for-otomatis" style="<?= $TipeNomorAnggota == 'Manual' ? 'display:none;' : '' ?>">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="alias">Cetak Slip Perpanjangan</label>
									<div>
										<?php $parameter = $IsCetakSlipPerpanjangan; ?>
										<div class="form-check">
											<input class="form-check-input" type="radio" name="IsCetakSlipPerpanjangan" id="Otomatis" value="Ya" <?= ($parameter == "Ya") ? 'checked' : '' ?>>
											<label class="form-check-label" for="simple">
												Ya
											</label>
										</div>
										<div class="form-check">
											<input class="form-check-input" type="radio" name="IsCetakSlipPerpanjangan" id="Manual" value="Tidak" <?= ($parameter == "Tidak") ? 'checked' : '' ?>>
											<label class="form-check-label" for="advance">
												Tidak
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="form-row form-for-otomatis" style="<?= $TipeNomorAnggota == 'Manual' ? 'display:none;' : '' ?>">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="alias">Cetak Slip Pelanggaran</label>
									<div>
										<?php $parameter = $IsCetakSlipPelanggaran; ?>
										<div class="form-check">
											<input class="form-check-input" type="radio" name="IsCetakSlipPelanggaran" id="Ya" value="Ya" <?= ($parameter == "Ya") ? 'checked' : '' ?>>
											<label class="form-check-label" for="simple">
												Ya
											</label>
										</div>
										<div class="form-check">
											<input class="form-check-input" type="radio" name="IsCetakSlipPelanggaran" id="Tidak" value="Tidak" <?= ($parameter == "Tidak") ? 'checked' : '' ?>>
											<label class="form-check-label" for="advance">
												Tidak
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="form-row form-for-otomatis" style="<?= $TipeNomorAnggota == 'Manual' ? 'display:none;' : '' ?>">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="alias">Cetak Slip Pendaftaran</label>
									<div>
										<?php $parameter = $IsCetakSlipPendaftaran; ?>
										<div class="form-check">
											<input class="form-check-input" type="radio" name="IsCetakSlipPendaftaran" id="Ya" value="Ya" <?= ($parameter == "Ya") ? 'checked' : '' ?>>
											<label class="form-check-label" for="simple">
												Ya
											</label>
										</div>
										<div class="form-check">
											<input class="form-check-input" type="radio" name="IsCetakSlipPendaftaran" id="Tidak" value="Tidak" <?= ($parameter == "Tidak") ? 'checked' : '' ?>>
											<label class="form-check-label" for="advance">
												Tidak
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
<script>
$(document).ready(function() {
    $('input[name="TipeNomorAnggota"]').change(function() {
        const selectedValue = $('input[name="TipeNomorAnggota"]:checked').val();
        if (selectedValue == 'Manual') {
          $('.form-for-otomatis').hide()
        } else {
          $('.form-for-otomatis').show()
        }
    });
});
</script>
<?= $this->endSection('script') ?>