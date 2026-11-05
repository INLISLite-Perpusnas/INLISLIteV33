<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<style>
	.location-settings {
		background-color: #fff;
		margin-top: 120px;
		padding: 2rem 0 4rem;
	}
	.location-card {
		max-width: 860px;
		margin-inline: auto;
		padding: clamp(1.25rem, 4vw, 2.5rem);
		border: 1px solid #d7dee8;
		border-radius: 1rem;
		box-shadow: 0 10px 30px rgba(15, 23, 42, .08);
	}
	.location-card label { font-weight: 600; margin-bottom: .5rem; }
	.location-card .form-control:focus,
	.location-card .btn:focus-visible {
		outline: 3px solid #ffbf47;
		outline-offset: 2px;
	}
	.required-marker { color: #b42318; }
</style>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-guestbook.css') ?>?v=<?= @filemtime(FCPATH . 'assets/css/ina-guestbook.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="location-settings gb-ina" aria-labelledby="location-settings-title">
	<div class="container">
		<div class="location-card">
		<h2 id="location-settings-title">Aktivasi Kios Perpustakaan</h2>
		<p class="text-secondary">Halaman ini hanya untuk perangkat kios perpustakaan (buku tamu, baca di tempat, peminjaman dan pengembalian mandiri). Masukkan kode aktivasi yang dibuat administrator di menu Aktivasi Kios.</p>

		<?php if (!empty($current_location)): ?>
			<div class="alert alert-info" role="status">
				Perangkat ini sudah aktif untuk lokasi <strong><?= esc($current_location->Name) ?></strong>.
				<a href="<?= base_url('buku-tamu') ?>">Buka buku tamu</a> · <a href="<?= base_url('baca-ditempat') ?>">Baca di tempat</a> · <a href="<?= base_url('peminjaman-mandiri') ?>">Peminjaman mandiri</a> · <a href="<?= base_url('pengembalian-mandiri') ?>">Pengembalian mandiri</a>
			</div>
		<?php endif; ?>

		<?php if (!empty($message)): ?>
			<div class="alert alert-danger" role="alert">
				<?= esc(trim(strip_tags((string) $message))) ?>
			</div>
		<?php endif; ?>

		<div class="row mt-4">
			<div class="col-lg-12">
				<div class="contact-wrap contact-pages mb-0">
					<div class="contact-form contact-form-mb">
						<form id="frm_register" method="post" action="<?= base_url('buku-tamu/lokasi') ?>" autocomplete="off">
							<?= csrf_field() ?>
							<div class="form-group">
								<label for="Code">Kode Aktivasi <span class="required-marker" aria-hidden="true">*</span></label>
								<input type="text" name="Code" id="Code" class="form-control" required
									maxlength="9" spellcheck="false" autocapitalize="characters"
									value="<?= esc(old('Code')) ?>" aria-describedby="code-help">
								<small id="code-help" class="form-text text-secondary">Contoh: K7QM-4XPD. Kode berlaku 10 menit dan hanya bisa dipakai sekali.</small>
							</div>
							<div class="d-flex justify-content-end gap-2 mt-4">
								<button type="submit" class="btn btn-primary">Aktifkan Kios</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		</div>
	</div>
</section>

<?= $this->endsection() ?>
