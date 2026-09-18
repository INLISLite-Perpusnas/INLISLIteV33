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
	.form-status { min-height: 1.5rem; font-weight: 600; }
	.form-status.success { color: #146c43; }
	.form-status.error { color: #b42318; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="location-settings" aria-labelledby="location-settings-title">
	<div class="container">
		<div class="location-card">
		<h2 id="location-settings-title">Atur Lokasi Ruang Perpustakaan</h2>
		<p class="text-secondary">Masukkan kode lokasi yang diberikan administrator, periksa kodenya, lalu simpan pengaturan.</p>

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
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="Code">Kode Lokasi Ruang Perpustakaan <span class="required-marker" aria-hidden="true">*</span></label>
										<input type="text" name="Code" id="Code" class="form-control" required
											minlength="3" maxlength="24" pattern="[A-Za-z0-9-]+" spellcheck="false"
											value="<?= esc(old('Code')) ?>" aria-describedby="code-help code-status">
										<small id="code-help" class="form-text text-secondary">Contoh: ABC1230101. Gunakan huruf, angka, atau tanda hubung.</small>
									</div>
								</div>
								<div class="col-md-12 mt-3">
									<div class="form-group d-flex align-items-center gap-3 flex-wrap">
										<button type="button" class="btn btn-primary" id="btnCheck">Cek Kode</button>
										<span id="code-status" class="form-status" role="status" aria-live="polite"></span>
									</div>
								</div>
							</div>
							<div class="row mt-3">
								<div class="col-md-6">
									<div class="form-group">
										<label for="LocationLibrary">Lokasi Perpustakaan</label>
										<input type="text" id="LocationLibrary" class="form-control" readonly aria-readonly="true">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="Location">Lokasi Ruang</label>
										<input type="text" id="Location" class="form-control" readonly aria-readonly="true">
									</div>
								</div>
							</div>
							<div class="d-flex justify-content-end gap-2 mt-4">
								<button type="reset" class="btn btn-danger">Reset Form</button>
								<button type="submit" class="btn btn-primary" id="btnSave" disabled>Simpan Pengaturan</button>
							</div>
						</form>
					</div>
				</div>
				<div>
				</div>
			</div>
		</div>
		</div>
	</div>
</section>

<?= $this->endsection() ?>

<?= $this->section('script') ?>
<script>
	(() => {
		'use strict';

		const form = document.getElementById('frm_register');
		const codeInput = document.getElementById('Code');
		const checkButton = document.getElementById('btnCheck');
		const saveButton = document.getElementById('btnSave');
		const libraryInput = document.getElementById('LocationLibrary');
		const locationInput = document.getElementById('Location');
		const status = document.getElementById('code-status');
		const checkUrl = <?= json_encode(base_url('api-lokasi-ruang/check'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

		function clearResult() {
			libraryInput.value = '';
			locationInput.value = '';
			saveButton.disabled = true;
			status.textContent = '';
			status.className = 'form-status';
		}

		codeInput.addEventListener('input', clearResult);
		form.addEventListener('reset', () => setTimeout(clearResult, 0));

		checkButton.addEventListener('click', async () => {
			codeInput.value = codeInput.value.trim().toUpperCase();
			if (!codeInput.checkValidity()) {
				codeInput.reportValidity();
				return;
			}

			clearResult();
			checkButton.disabled = true;
			checkButton.textContent = 'Memeriksa...';
			status.textContent = 'Kode sedang diperiksa.';

			try {
				const response = await fetch(`${checkUrl}/${encodeURIComponent(codeInput.value)}`, {
					headers: { 'Accept': 'application/json' },
					credentials: 'same-origin'
				});
				if (!response.ok) throw new Error('Kode tidak valid');

				const result = await response.json();
				libraryInput.value = String(result.LocationLibrary_name || '');
				locationInput.value = String(result.Name || '');
				saveButton.disabled = false;
				status.textContent = 'Kode valid. Lokasi siap disimpan.';
				status.className = 'form-status success';
			} catch (error) {
				status.textContent = 'Kode tidak ditemukan, tidak aktif, atau terlalu banyak percobaan.';
				status.className = 'form-status error';
			} finally {
				checkButton.disabled = false;
				checkButton.textContent = 'Cek Kode';
			}
		});

		form.addEventListener('submit', (event) => {
			if (saveButton.disabled) {
				event.preventDefault();
				status.textContent = 'Periksa kode lokasi sebelum menyimpan.';
				status.className = 'form-status error';
			}
		});
	})();
</script>

<?= $this->endsection() ?>
