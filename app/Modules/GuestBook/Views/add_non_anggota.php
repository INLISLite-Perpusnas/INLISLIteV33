<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>
<style>
	:root {
		--primary: #039550;
		--primary-dark: #027a42;
		--primary-light: #e6f4ee;
		--accent: #f8c43a;
		--light-gray: #f5f5f5;
		--text: #333333;
		--white: #ffffff;
		--shadow: rgba(0, 0, 0, 0.1);
		--border-radius: 12px;
	}

	.page-container {
		background: linear-gradient(135deg, var(--primary-light), #ffffff);
		min-height: 100vh;
		padding: 20px 0;
	}

	.content-wrapper {
		max-width: 1200px;
		margin: 0 auto;
		padding: 0 20px;
	}

	/* Header Section */
	.page-header {
		background: white;
		border-radius: var(--border-radius);
		padding: 30px;
		margin-bottom: 30px;
		box-shadow: 0 5px 15px var(--shadow);
		border-left: 5px solid var(--primary);
	}

	.library-info h1 {
		color: var(--primary);
		font-size: 1.8rem;
		font-weight: 700;
		margin-bottom: 5px;
	}

	.library-location {
		color: #666;
		font-size: 1.1rem;
		margin-bottom: 20px;
	}

	.breadcrumb-custom {
		background: var(--primary-light);
		padding: 12px 20px;
		border-radius: 25px;
		margin: 0;
		display: inline-flex;
		align-items: center;
		gap: 10px;
		font-size: 0.9rem;
	}

	.breadcrumb-custom a {
		color: var(--primary);
		text-decoration: none;
		font-weight: 500;
	}

	.breadcrumb-custom .active {
		color: var(--primary-dark);
		font-weight: 600;
	}

	/* Navigation Tabs */
	.nav-tabs-custom {
		background: white;
		border-radius: var(--border-radius);
		padding: 10px;
		margin-bottom: 30px;
		box-shadow: 0 3px 10px var(--shadow);
		display: flex;
		gap: 10px;
	}

	.nav-tab {
		flex: 1;
		padding: 15px 20px;
		text-align: center;
		border-radius: 8px;
		text-decoration: none;
		color: #666;
		font-weight: 500;
		transition: all 0.3s ease;
		background: transparent;
		border: 2px solid transparent;
	}

	.nav-tab:hover {
		background: var(--primary-light);
		color: var(--primary);
		text-decoration: none;
	}

	.nav-tab.active {
		background: var(--primary);
		color: white;
		border-color: var(--primary-dark);
		box-shadow: 0 3px 10px rgba(3, 149, 80, 0.3);
	}

	.nav-tab i {
		margin-right: 8px;
		font-size: 1.1rem;
	}

	/* Form Section */
	.form-section {
		background: white;
		border-radius: var(--border-radius);
		padding: 40px;
		margin-bottom: 30px;
		box-shadow: 0 5px 15px var(--shadow);
	}

	.form-title {
		color: var(--primary);
		font-size: 1.8rem;
		font-weight: 600;
		margin-bottom: 10px;
		text-align: center;
	}

	.form-subtitle {
		color: #666;
		margin-bottom: 40px;
		font-size: 1.1rem;
		text-align: center;
	}

	/* Form Styling */
	.form-group {
		margin-bottom: 25px;
	}

	.form-group label {
		color: var(--text);
		font-weight: 600;
		margin-bottom: 8px;
		display: block;
		font-size: 0.95rem;
	}

	.form-group label.required::after {
		content: " *";
		color: #dc3545;
		font-weight: bold;
	}

	.form-control {
		width: 100%;
		padding: 15px 20px;
		border: 2px solid #e9ecef;
		border-radius: 10px;
		font-size: 16px;
		transition: all 0.3s ease;
		background-color: #fff;
	}

	.form-control:focus {
		outline: none;
		border-color: var(--primary);
		box-shadow: 0 0 0 3px rgba(3, 149, 80, 0.1);
		background-color: #fff;
	}

	.form-control::placeholder {
		color: #999;
	}

	/* Form Row */
	.form-row {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 20px;
		margin-bottom: 20px;
	}

	.form-row-3 {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
		gap: 20px;
		margin-bottom: 20px;
	}

	/* Textarea */
	textarea.form-control {
		resize: vertical;
		min-height: 100px;
	}

	/* Submit Button */
	.submit-section {
		text-align: center;
		padding-top: 20px;
		border-top: 2px solid var(--light-gray);
	}

	.save-btn {
		background: linear-gradient(135deg, var(--primary), var(--primary-dark));
		color: white;
		border: none;
		padding: 18px 40px;
		border-radius: 50px;
		font-size: 1.1rem;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.3s ease;
		box-shadow: 0 5px 15px rgba(3, 149, 80, 0.3);
		display: inline-flex;
		align-items: center;
		gap: 10px;
		min-width: 200px;
		justify-content: center;
	}

	.save-btn:hover {
		transform: translateY(-2px);
		box-shadow: 0 8px 25px rgba(3, 149, 80, 0.4);
	}

	.save-btn:disabled {
		opacity: 0.6;
		cursor: not-allowed;
		transform: none;
	}

	.save-btn:disabled:hover {
		transform: none;
		box-shadow: 0 5px 15px rgba(3, 149, 80, 0.3);
	}

	.save-btn i {
		font-size: 1.2rem;
	}

	/* Alert Messages */
	.alert-custom {
		border-radius: var(--border-radius);
		border: none;
		padding: 20px 25px;
		margin-bottom: 25px;
		font-size: 1rem;
		display: flex;
		align-items: center;
		box-shadow: 0 3px 10px var(--shadow);
	}

	.alert-warning-custom {
		background: linear-gradient(135deg, #fff3cd, #ffeaa7);
		color: #856404;
		border-left: 5px solid #ffc107;
	}

	.alert-success-custom {
		background: linear-gradient(135deg, #d4edda, #b8e6c1);
		color: #155724;
		border-left: 5px solid #28a745;
	}

	.alert-error-custom {
		background: linear-gradient(135deg, #f8d7da, #f5c2c7);
		color: #721c24;
		border-left: 5px solid #dc3545;
	}

	.alert-custom i {
		font-size: 1.5rem;
		margin-right: 15px;
	}

	.alert-close {
		margin-left: auto;
		background: none;
		border: none;
		font-size: 1.5rem;
		cursor: pointer;
		opacity: 0.7;
	}

	.alert-close:hover {
		opacity: 1;
	}

	/* Validation Styling */
	.form-control.is-invalid {
		border-color: #dc3545;
		box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
	}

	.invalid-feedback {
		color: #dc3545;
		font-size: 0.875rem;
		margin-top: 5px;
		display: block;
	}

	/* Responsive */
	@media (max-width: 768px) {
		.nav-tabs-custom {
			flex-direction: column;
		}

		.nav-tab {
			margin-bottom: 5px;
		}

		.form-row {
			grid-template-columns: 1fr;
		}

		.form-row-3 {
			grid-template-columns: 1fr;
		}

		.page-header {
			padding: 20px;
		}

		.form-section {
			padding: 25px;
		}
	}

	/* Animation */
	@keyframes fadeInUp {
		from {
			opacity: 0;
			transform: translateY(30px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	.content-wrapper > * {
		animation: fadeInUp 0.6s ease forwards;
	}

	.content-wrapper > *:nth-child(1) { animation-delay: 0.1s; }
	.content-wrapper > *:nth-child(2) { animation-delay: 0.2s; }
	.content-wrapper > *:nth-child(3) { animation-delay: 0.3s; }
	.content-wrapper > *:nth-child(4) { animation-delay: 0.4s; }
</style>

<div class="page-container" style="padding-top: 100px !important; padding-bottom: 40px !important;">
	<div class="content-wrapper">
		<!-- Header Section -->
		<div class="page-header">
			<div class="library-info">
				<h1><?= $data->Name ?? 'Perpustakaan' ?></h1>
				<div class="library-location"><?= $data->LocationLibrary_name ?? 'Dinas Perpustakaan dan Kearsipan' ?></div>
				<nav class="breadcrumb-custom">
					<a href="<?= base_url() ?>"><i class="fas fa-home"></i> Beranda</a>
					<span>/</span>
					<span>Buku Tamu</span>
					<span>/</span>
					<span class="active">Bukan Anggota</span>
				</nav>
			</div><br>
				<h2 style="background-color: #28a745; color: #fff; padding: 10px; border-radius: 5px;">Total Kunjungan Hari ini <?=$totalKunjungan ?? '0'?></h2>
		</div>

		<!-- Navigation Tabs -->
		<div class="nav-tabs-custom">
			<a href="<?= base_url('buku-tamu') ?>" class="nav-tab">
				<i class="fas fa-user"></i>
				<span>Anggota</span>
			</a>
			<a href="<?= base_url('buku-tamu/non_anggota') ?>" class="nav-tab active">
				<i class="fas fa-user-plus"></i>
				<span>Bukan Anggota</span>
			</a>
			<a href="<?= base_url('buku-tamu/rombongan') ?>" class="nav-tab">
				<i class="fas fa-users"></i>
				<span>Rombongan</span>
			</a>
		</div>

		<!-- Validation Errors (if any) -->
		<?php if (!empty($message)) : ?>
			<div class="alert-custom alert-error-custom" id="validationAlert">
				<i class="fas fa-exclamation-circle"></i>
				<div>
					<strong>Perhatian!</strong><br>
					<?= $message ?>
				</div>
				<button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">
					<span>&times;</span>
				</button>
			</div>
		<?php endif; ?>

		<!-- Form Section -->
		<div class="form-section">
			<h2 class="form-title">Buku Tamu Non Anggota</h2>
			<p class="form-subtitle">Silakan isi data diri Anda untuk tercatat sebagai pengunjung perpustakaan</p>

			<form id="frm_create" method="post" action="<?= base_url('buku-tamu/non_anggota') ?>">
				<?= csrf_field() ?>
				
				<!-- Nama Pengunjung -->
				<div class="form-group">
					<label for="Nama" class="required">Nama Pengunjung</label>
					<input type="text" 
						   class="form-control <?= session('errors.Nama') ? 'is-invalid' : '' ?>" 
						   name="Nama" 
						   id="Nama" 
						   placeholder="Masukkan nama lengkap Anda"
						   value="<?= old('Nama') ?>" 
						   required>
					<?php if (session('errors.Nama')) : ?>
						<div class="invalid-feedback"><?= session('errors.Nama') ?></div>
					<?php endif; ?>
				</div>

				<!-- Row 1: Pekerjaan, Pendidikan, Jenis Kelamin -->
				<div class="form-row-3">
					<div class="form-group">
						<label for="Profesi_id" class="required">Pekerjaan</label>
						<select class="form-control <?= session('errors.Profesi_id') ? 'is-invalid' : '' ?>" 
								name="Profesi_id" 
								id="Profesi_id" 
								required>
							<option value="">-- Pilih Pekerjaan --</option>
							<?php foreach (get_ref_table('master_pekerjaan', 'id, pekerjaan', null, 'data') as $row) : ?>
								<option value="<?= $row->id ?>" <?= old('Profesi_id') == $row->id ? 'selected' : '' ?>>
									<?= $row->pekerjaan ?>
								</option>
							<?php endforeach; ?>
						</select>
						<?php if (session('errors.Profesi_id')) : ?>
							<div class="invalid-feedback"><?= session('errors.Profesi_id') ?></div>
						<?php endif; ?>
					</div>

					<div class="form-group">
						<label for="PendidikanTerakhir_id" class="required">Pendidikan Terakhir</label>
						<select class="form-control <?= session('errors.PendidikanTerakhir_id') ? 'is-invalid' : '' ?>" 
								name="PendidikanTerakhir_id" 
								id="PendidikanTerakhir_id" 
								required>
							<option value="">-- Pilih Pendidikan --</option>
							<?php foreach (get_ref_table('master_pendidikan', 'id, Nama', null, 'data') as $row) : ?>
								<option value="<?= $row->id ?>" <?= old('PendidikanTerakhir_id') == $row->id ? 'selected' : '' ?>>
									<?= $row->Nama ?>
								</option>
							<?php endforeach; ?>
						</select>
						<?php if (session('errors.PendidikanTerakhir_id')) : ?>
							<div class="invalid-feedback"><?= session('errors.PendidikanTerakhir_id') ?></div>
						<?php endif; ?>
					</div>

					<div class="form-group">
						<label for="JenisKelamin_id" class="required">Jenis Kelamin</label>
						<select class="form-control <?= session('errors.JenisKelamin_id') ? 'is-invalid' : '' ?>" 
								name="JenisKelamin_id" 
								id="JenisKelamin_id" 
								required>
							<option value="">-- Pilih Jenis Kelamin --</option>
							<?php foreach (get_ref_table('jenis_kelamin', 'ID, Name', 'active=1', 'data') as $row) : ?>
								<option value="<?= $row->ID ?>" <?= old('JenisKelamin_id') == $row->ID ? 'selected' : '' ?>>
									<?= $row->Name ?>
								</option>
							<?php endforeach; ?>
						</select>
						<?php if (session('errors.JenisKelamin_id')) : ?>
							<div class="invalid-feedback"><?= session('errors.JenisKelamin_id') ?></div>
						<?php endif; ?>
					</div>
				</div>

				<!-- Alamat -->
				<div class="form-group">
					<label for="Alamat">Alamat Sesuai Identitas</label>
					<textarea id="Alamat" 
							  name="Alamat" 
							  class="form-control <?= session('errors.Alamat') ? 'is-invalid' : '' ?>" 
							  placeholder="Masukkan alamat lengkap sesuai identitas"
							  rows="3"><?= old('Alamat') ?></textarea>
					<?php if (session('errors.Alamat')) : ?>
						<div class="invalid-feedback"><?= session('errors.Alamat') ?></div>
					<?php endif; ?>
				</div>
				<?php if ($SettingBukuTamu == 1) : ?>
					<div class="form-group">
							<div class="col-md-6" style="padding-left: 0;">
								
									<label for="TujuanKunjungan_id">Tujuan Kunjungan</label>
									<select class="form-control" name="TujuanKunjungan_id" id="TujuanKunjungan_id">
									  <option value="" disabled selected> ----- Pilih ----- </option>
									  <?php foreach ($tujuan_kunjungan as $row) : ?>
											<?php // PERBAIKAN: Menyamakan ID dengan id 
											?>
											<option value="<?= $row->ID ?>" <?= set_select('TujuanKunjungan_id', $row->ID) ?>><?= $row->TujuanKunjungan ?></option>
										<?php endforeach; ?>
									</select>
							
							</div>
						</div>
				<?php endif; ?>

				<!-- Submit Button -->
				<div class="submit-section">
					<button type="submit" class="save-btn" id="saveBtn" name="submit">
						<i class="fas fa-save"></i>
						<span class="btn-text">Simpan Buku Tamu</span>
						<span class="btn-loading" style="display: none;">
							<i class="fas fa-spinner fa-spin"></i>
							Menyimpan...
						</span>
					</button>
					<p style="margin-top: 15px; color: #666; font-size: 0.9rem;">
						Dengan mengisi formulir ini, data kunjungan Anda akan tercatat dalam buku tamu perpustakaan.
					</p>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
// Auto-focus pada input pertama
document.addEventListener('DOMContentLoaded', function() {
	const firstInput = document.getElementById('Nama');
	if (firstInput) {
		firstInput.focus();
	}

	// Auto-hide validation alerts after 7 seconds
	const validationAlert = document.getElementById('validationAlert');
	
	if (validationAlert) {
		setTimeout(() => {
			validationAlert.style.animation = 'fadeOut 0.5s ease forwards';
			setTimeout(() => {
				validationAlert.style.display = 'none';
			}, 500);
		}, 7000);
	}

	// Check for toastr messages dan tampilkan sebagai notifikasi
	checkToastrMessages();
});

// Function untuk check toastr messages
function checkToastrMessages() {
	<?php if (get_message('toastr_msg')): ?>
		const toastrMsg = "<?= get_message('toastr_msg') ?>";
		const toastrType = "<?= get_message('toastr_type') ?>";
		
		if (toastrMsg && toastrType) {
			showCustomNotification(toastrMsg, toastrType);
			
			// Clear the messages after showing
			<?php 
			unset_message('toastr_msg'); 
			unset_message('toastr_type'); 
			?>
		}
	<?php endif; ?>
}

// Function untuk show custom notification
function showCustomNotification(message, type) {
	const alertClass = type === 'success' ? 'alert-success-custom' : 'alert-error-custom';
	const iconClass = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
	const title = type === 'success' ? 'Berhasil!' : 'Perhatian!';
	
	const notification = document.createElement('div');
	notification.className = `alert-custom ${alertClass}`;
	notification.style.position = 'fixed';
	notification.style.top = '20px';
	notification.style.right = '20px';
	notification.style.zIndex = '9999';
	notification.style.minWidth = '300px';
	notification.style.animation = 'slideInRight 0.5s ease forwards';
	
	notification.innerHTML = `
		<i class="${iconClass}"></i>
		<div>
			<strong>${title}</strong><br>
			${message}
		</div>
		<button type="button" class="alert-close" onclick="this.parentElement.remove()">
			<span>&times;</span>
		</button>
	`;
	
	document.body.appendChild(notification);
	
	// Auto remove after 5 seconds
	setTimeout(() => {
		notification.style.animation = 'slideOutRight 0.5s ease forwards';
		setTimeout(() => {
			if (notification.parentNode) {
				notification.remove();
			}
		}, 500);
	}, 5000);
}

// Form validation
document.getElementById('frm_create').addEventListener('submit', function(e) {
	const saveBtn = document.getElementById('saveBtn');
	const btnText = saveBtn.querySelector('.btn-text');
	const btnLoading = saveBtn.querySelector('.btn-loading');
	
	// Basic validation
	const nama = document.getElementById('Nama').value.trim();
	const profesi = document.getElementById('Profesi_id').value;
	const pendidikan = document.getElementById('PendidikanTerakhir_id').value;
	const jenisKelamin = document.getElementById('JenisKelamin_id').value;
	
	if (!nama || !profesi || !pendidikan || !jenisKelamin) {
		e.preventDefault();
		showCustomNotification('Mohon lengkapi semua field yang wajib diisi (bertanda *)', 'error');
		return false;
	}
	
	// Show loading state
	saveBtn.disabled = true;
	btnText.style.display = 'none';
	btnLoading.style.display = 'inline-flex';
	
	// Set timeout untuk re-enable button jika ada error (fallback)
	setTimeout(() => {
		if (saveBtn.disabled) {
			saveBtn.disabled = false;
			btnText.style.display = 'inline-flex';
			btnLoading.style.display = 'none';
		}
	}, 10000); // 10 detik timeout
});

// Remove validation styling on input
document.querySelectorAll('.form-control').forEach(input => {
	input.addEventListener('input', function() {
		this.classList.remove('is-invalid');
		const feedback = this.parentNode.querySelector('.invalid-feedback');
		if (feedback) {
			feedback.style.display = 'none';
		}
	});
});

// Animasi untuk alerts
document.querySelectorAll('.alert-close').forEach(button => {
	button.addEventListener('click', function() {
		const alert = this.parentElement;
		alert.style.animation = 'fadeOut 0.3s ease forwards';
		setTimeout(() => {
			alert.style.display = 'none';
		}, 300);
	});
});

// Add animations
const style = document.createElement('style');
style.textContent = `
	@keyframes fadeOut {
		from { opacity: 1; transform: translateY(0); }
		to { opacity: 0; transform: translateY(-20px); }
	}
	
	@keyframes slideInRight {
		from { 
			opacity: 0; 
			transform: translateX(100%); 
		}
		to { 
			opacity: 1; 
			transform: translateX(0); 
		}
	}
	
	@keyframes slideOutRight {
		from { 
			opacity: 1; 
			transform: translateX(0); 
		}
		to { 
			opacity: 0; 
			transform: translateX(100%); 
		}
	}
`;
document.head.appendChild(style);
</script>

<?php if (session()->getFlashdata('success')) : ?>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		Swal.fire({
			icon: 'success',
			title: 'Berhasil!',
			text: '<?= session()->getFlashdata('success') ?>',
			showConfirmButton: false,
			timer: 2000
		});
	});
</script>
<?php endif; ?>

<?= $this->endsection() ?>