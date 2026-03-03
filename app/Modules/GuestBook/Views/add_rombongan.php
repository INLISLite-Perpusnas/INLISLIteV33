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

	/* Section Headers */
	.section-header {
		background: linear-gradient(135deg, var(--primary-light), #ffffff);
		padding: 15px 25px;
		border-radius: 10px;
		margin: 30px 0 20px 0;
		border-left: 4px solid var(--primary);
	}

	.section-header h3 {
		color: var(--primary);
		font-size: 1.3rem;
		font-weight: 600;
		margin: 0;
		display: flex;
		align-items: center;
		gap: 10px;
	}

	.section-header i {
		font-size: 1.4rem;
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
		grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
		gap: 20px;
		margin-bottom: 20px;
	}

	.form-row-2 {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 20px;
		margin-bottom: 20px;
	}

	.form-row-3 {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
		gap: 20px;
		margin-bottom: 20px;
	}

	.form-row-4 {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
		gap: 15px;
		margin-bottom: 20px;
	}

	/* Count Input Special Styling */
	.count-input {
		position: relative;
	}

	.count-input .form-control {
		text-align: center;
		font-weight: 600;
		font-size: 1.1rem;
		background: var(--primary-light);
		border-color: var(--primary);
	}

	.count-input .form-control:focus {
		background: white;
	}

	/* Summary Box */
	.summary-box {
		background: linear-gradient(135deg, var(--primary-light), #ffffff);
		border: 2px solid var(--primary);
		border-radius: 15px;
		padding: 20px;
		margin: 20px 0;
		text-align: center;
	}

	.summary-total {
		font-size: 2rem;
		font-weight: 700;
		color: var(--primary);
		margin: 10px 0;
	}

	.summary-label {
		color: var(--text);
		font-weight: 600;
		font-size: 1.1rem;
	}

	/* Textarea */
	textarea.form-control {
		resize: vertical;
		min-height: 100px;
	}

	/* Submit Button */
	.submit-section {
		text-align: center;
		padding-top: 30px;
		border-top: 2px solid var(--light-gray);
		margin-top: 30px;
	}

	.save-btn {
		background: linear-gradient(135deg, var(--primary), var(--primary-dark));
		color: white;
		border: none;
		padding: 20px 50px;
		border-radius: 50px;
		font-size: 1.2rem;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.3s ease;
		box-shadow: 0 5px 15px rgba(3, 149, 80, 0.3);
		display: inline-flex;
		align-items: center;
		gap: 12px;
		min-width: 250px;
		justify-content: center;
	}

	.save-btn:hover {
		transform: translateY(-3px);
		box-shadow: 0 10px 30px rgba(3, 149, 80, 0.4);
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
		font-size: 1.3rem;
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

		.form-row,
		.form-row-2,
		.form-row-3,
		.form-row-4 {
			grid-template-columns: 1fr;
		}

		.page-header {
			padding: 20px;
		}

		.form-section {
			padding: 25px;
		}

		.save-btn {
			padding: 18px 40px;
			font-size: 1.1rem;
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

	@keyframes pulse {
		0%, 100% { transform: scale(1); }
		50% { transform: scale(1.05); }
	}

	.summary-total {
		animation: pulse 2s infinite;
	}
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
					<span class="active">Rombongan</span>
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
			<a href="<?= base_url('buku-tamu/non_anggota') ?>" class="nav-tab">
				<i class="fas fa-user-plus"></i>
				<span>Bukan Anggota</span>
			</a>
			<a href="<?= base_url('buku-tamu/rombongan') ?>" class="nav-tab active">
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
			<h2 class="form-title">Buku Tamu Rombongan</h2>
			<p class="form-subtitle">Silakan isi data rombongan dan detail anggota untuk tercatat sebagai pengunjung perpustakaan</p>

			<form id="frm_create" method="post" action="<?= base_url('buku-tamu/rombongan') ?>">
				<?= csrf_field() ?>
				
				<!-- Section 1: Data Penanggung Jawab -->
				<div class="section-header">
					<h3><i class="fas fa-user-tie"></i> Data Penanggung Jawab</h3>
				</div>

				<div class="form-row-2">
					<div class="form-group">
						<label for="NamaKetua" class="required">Nama Penanggung Jawab</label>
						<input type="text" 
							   class="form-control <?= session('errors.NamaKetua') ? 'is-invalid' : '' ?>" 
							   name="NamaKetua" 
							   id="NamaKetua" 
							   placeholder="Masukkan nama penanggung jawab"
							   value="<?= old('NamaKetua') ?>" 
							   required>
						<?php if (session('errors.NamaKetua')) : ?>
							<div class="invalid-feedback"><?= session('errors.NamaKetua') ?></div>
						<?php endif; ?>
					</div>

					<div class="form-group">
						<label for="NomerTelponKetua" class="required">Nomor Telepon</label>
						<input type="tel" 
							   class="form-control <?= session('errors.NomerTelponKetua') ? 'is-invalid' : '' ?>" 
							   name="NomerTelponKetua" 
							   id="NomerTelponKetua" 
							   placeholder="Contoh: 08123456789"
							   value="<?= old('NomerTelponKetua') ?>" 
							   required>
						<?php if (session('errors.NomerTelponKetua')) : ?>
							<div class="invalid-feedback"><?= session('errors.NomerTelponKetua') ?></div>
						<?php endif; ?>
					</div>
				</div>

				<!-- Section 2: Data Instansi -->
				<div class="section-header">
					<h3><i class="fas fa-building"></i> Data Instansi/Organisasi</h3>
				</div>

				<div class="form-row">
					<div class="form-group">
						<label for="AsalInstansi" class="required">Nama Instansi/Organisasi</label>
						<input type="text" 
							   class="form-control <?= session('errors.AsalInstansi') ? 'is-invalid' : '' ?>" 
							   name="AsalInstansi" 
							   id="AsalInstansi" 
							   placeholder="Masukkan nama instansi atau organisasi"
							   value="<?= old('AsalInstansi') ?>" 
							   required>
						<?php if (session('errors.AsalInstansi')) : ?>
							<div class="invalid-feedback"><?= session('errors.AsalInstansi') ?></div>
						<?php endif; ?>
					</div>

					<div class="form-group">
						<label for="EmailInstansi">Email Instansi</label>
						<input type="email" 
							   class="form-control <?= session('errors.EmailInstansi') ? 'is-invalid' : '' ?>" 
							   name="EmailInstansi" 
							   id="EmailInstansi" 
							   placeholder="contoh@instansi.com"
							   value="<?= old('EmailInstansi') ?>">
						<?php if (session('errors.EmailInstansi')) : ?>
							<div class="invalid-feedback"><?= session('errors.EmailInstansi') ?></div>
						<?php endif; ?>
					</div>
				</div>

				<div class="form-group">
					<label for="AlamatInstansi">Alamat Instansi</label>
					<textarea id="AlamatInstansi" 
							  name="AlamatInstansi" 
							  class="form-control <?= session('errors.AlamatInstansi') ? 'is-invalid' : '' ?>" 
							  placeholder="Masukkan alamat lengkap instansi"
							  rows="3"><?= old('AlamatInstansi') ?></textarea>
					<?php if (session('errors.AlamatInstansi')) : ?>
						<div class="invalid-feedback"><?= session('errors.AlamatInstansi') ?></div>
					<?php endif; ?>
				</div>

				<!-- Section 3: Jumlah Anggota -->
				<div class="section-header">
					<h3><i class="fas fa-users"></i> Jumlah Anggota Rombongan</h3>
				</div>

				<!-- Total dan Gender -->
				<div class="form-row-3">
					<div class="form-group count-input">
						<label for="CountPersonel" class="required">Total Anggota</label>
						<input type="number" 
							   class="form-control <?= session('errors.CountPersonel') ? 'is-invalid' : '' ?>" 
							   name="CountPersonel" 
							   id="CountPersonel" 
							   placeholder="0"
							   value="<?= old('CountPersonel', 0) ?>" 
							   min="1"
							   required>
						<?php if (session('errors.CountPersonel')) : ?>
							<div class="invalid-feedback"><?= session('errors.CountPersonel') ?></div>
						<?php endif; ?>
					</div>

					<div class="form-group count-input">
						<label for="CountLaki">Laki-laki</label>
						<input type="number" 
							   class="form-control" 
							   name="CountLaki" 
							   id="CountLaki" 
							   placeholder="0"
							   value="<?= old('CountLaki', 0) ?>" 
							   min="0">
					</div>

					<div class="form-group count-input">
						<label for="CountPerempuan">Perempuan</label>
						<input type="number" 
							   class="form-control" 
							   name="CountPerempuan" 
							   id="CountPerempuan" 
							   placeholder="0"
							   value="<?= old('CountPerempuan', 0) ?>" 
							   min="0">
					</div>
				</div>

				<!-- Summary Gender -->
				<div class="summary-box" id="genderSummary" style="display: none;">
					<div class="summary-label">Total Berdasarkan Jenis Kelamin</div>
					<div class="summary-total" id="genderTotal">0</div>
				</div>

				<!-- Section 4: Komposisi Profesi -->
				<div class="section-header">
					<h3><i class="fas fa-briefcase"></i> Komposisi Berdasarkan Profesi</h3>
				</div>

				<div class="form-row-4">
					<div class="form-group count-input">
						<label for="CountPNS">PNS</label>
						<input type="number" 
							   class="form-control profesi-count" 
							   name="CountPNS" 
							   id="CountPNS" 
							   placeholder="0"
							   value="<?= old('CountPNS', 0) ?>" 
							   min="0">
					</div>

					<div class="form-group count-input">
						<label for="CountGuru">Guru</label>
						<input type="number" 
							   class="form-control profesi-count" 
							   name="CountGuru" 
							   id="CountGuru" 
							   placeholder="0"
							   value="<?= old('CountGuru', 0) ?>" 
							   min="0">
					</div>

					<div class="form-group count-input">
						<label for="CountPSwasta">Pegawai Swasta</label>
						<input type="number" 
							   class="form-control profesi-count" 
							   name="CountPSwasta" 
							   id="CountPSwasta" 
							   placeholder="0"
							   value="<?= old('CountPSwasta', 0) ?>" 
							   min="0">
					</div>

					<div class="form-group count-input">
						<label for="CountPeneliti">Peneliti</label>
						<input type="number" 
							   class="form-control profesi-count" 
							   name="CountPeneliti" 
							   id="CountPeneliti" 
							   placeholder="0"
							   value="<?= old('CountPeneliti', 0) ?>" 
							   min="0">
					</div>

					<div class="form-group count-input">
						<label for="CountDosen">Dosen</label>
						<input type="number" 
							   class="form-control profesi-count" 
							   name="CountDosen" 
							   id="CountDosen" 
							   placeholder="0"
							   value="<?= old('CountDosen', 0) ?>" 
							   min="0">
					</div>

					<div class="form-group count-input">
						<label for="CountPensiunan">Pensiunan</label>
						<input type="number" 
							   class="form-control profesi-count" 
							   name="CountPensiunan" 
							   id="CountPensiunan" 
							   placeholder="0"
							   value="<?= old('CountPensiunan', 0) ?>" 
							   min="0">
					</div>

					<div class="form-group count-input">
						<label for="CountTNI">TNI/Polri</label>
						<input type="number" 
							   class="form-control profesi-count" 
							   name="CountTNI" 
							   id="CountTNI" 
							   placeholder="0"
							   value="<?= old('CountTNI', 0) ?>" 
							   min="0">
					</div>

					<div class="form-group count-input">
						<label for="CountWiraswasta">Wiraswasta</label>
						<input type="number" 
							   class="form-control profesi-count" 
							   name="CountWiraswasta" 
							   id="CountWiraswasta" 
							   placeholder="0"
							   value="<?= old('CountWiraswasta', 0) ?>" 
							   min="0">
					</div>

					<div class="form-group count-input">
						<label for="CountPelajar">Pelajar</label>
						<input type="number" 
							   class="form-control profesi-count" 
							   name="CountPelajar" 
							   id="CountPelajar" 
							   placeholder="0"
							   value="<?= old('CountPelajar', 0) ?>" 
							   min="0">
					</div>

					<div class="form-group count-input">
						<label for="CountMahasiswa">Mahasiswa</label>
						<input type="number" 
							   class="form-control profesi-count" 
							   name="CountMahasiswa" 
							   id="CountMahasiswa" 
							   placeholder="0"
							   value="<?= old('CountMahasiswa', 0) ?>" 
							   min="0">
					</div>

					<div class="form-group count-input">
						<label for="CountLainnya">Lainnya</label>
						<input type="number" 
							   class="form-control profesi-count" 
							   name="CountLainnya" 
							   id="CountLainnya" 
							   placeholder="0"
							   value="<?= old('CountLainnya', 0) ?>" 
							   min="0">
					</div>
				</div>

				<!-- Summary Profesi -->
				<div class="summary-box" id="profesiSummary" style="display: none;">
					<div class="summary-label">Total Berdasarkan Profesi</div>
					<div class="summary-total" id="profesiTotal">0</div>
				</div>

				<!-- Section 5: Komposisi Pendidikan -->
				<div class="section-header">
					<h3><i class="fas fa-graduation-cap"></i> Komposisi Berdasarkan Pendidikan</h3>
				</div>

				<div class="form-row-4">
					<div class="form-group count-input">
						<label for="CountSD">SD</label>
						<input type="number" 
							   class="form-control pendidikan-count" 
							   name="CountSD" 
							   id="CountSD" 
							   placeholder="0"
							   value="<?= old('CountSD', 0) ?>" 
							   min="0">
					</div>

					<div class="form-group count-input">
						<label for="CountSMP">SMP</label>
						<input type="number" 
							   class="form-control pendidikan-count" 
							   name="CountSMP" 
							   id="CountSMP" 
							   placeholder="0"
							   value="<?= old('CountSMP', 0) ?>" 
							   min="0">
					</div>

					<div class="form-group count-input">
						<label for="CountSMA">SMA</label>
						<input type="number" 
							   class="form-control pendidikan-count" 
							   name="CountSMA" 
							   id="CountSMA" 
							   placeholder="0"
							   value="<?= old('CountSMA', 0) ?>" 
							   min="0">
					</div>

					<div class="form-group count-input">
						<label for="CountD1">D1</label>
						<input type="number" 
							   class="form-control pendidikan-count" 
							   name="CountD1" 
							   id="CountD1" 
							   placeholder="0"
							   value="<?= old('CountD1', 0) ?>" 
							   min="0">
					</div>

					<div class="form-group count-input">
						<label for="CountD2">D2</label>
						<input type="number" 
							   class="form-control pendidikan-count" 
							   name="CountD2" 
							   id="CountD2" 
							   placeholder="0"
							   value="<?= old('CountD2', 0) ?>" 
							   min="0">
					</div>

					<div class="form-group count-input">
						<label for="CountD3">D3</label>
						<input type="number" 
							   class="form-control pendidikan-count" 
							   name="CountD3" 
							   id="CountD3" 
							   placeholder="0"
							   value="<?= old('CountD3', 0) ?>" 
							   min="0">
					</div>

					<div class="form-group count-input">
						<label for="CountS1">S1</label>
						<input type="number" 
							   class="form-control pendidikan-count" 
							   name="CountS1" 
							   id="CountS1" 
							   placeholder="0"
							   value="<?= old('CountS1', 0) ?>" 
							   min="0">
					</div>

					<div class="form-group count-input">
						<label for="CountS2">S2</label>
						<input type="number" 
							   class="form-control pendidikan-count" 
							   name="CountS2" 
							   id="CountS2" 
							   placeholder="0"
							   value="<?= old('CountS2', 0) ?>" 
							   min="0">
					</div>

					<div class="form-group count-input">
						<label for="CountS3">S3</label>
						<input type="number" 
							   class="form-control pendidikan-count" 
							   name="CountS3" 
							   id="CountS3" 
							   placeholder="0"
							   value="<?= old('CountS3', 0) ?>" 
							   min="0">
					</div>
				</div>

				<!-- Summary Pendidikan -->
				<div class="summary-box" id="pendidikanSummary" style="display: none;">
					<div class="summary-label">Total Berdasarkan Pendidikan</div>
					<div class="summary-total" id="pendidikanTotal">0</div>
				</div>

				<!-- Section 6: Informasi Tambahan -->
				<div class="section-header">
					<h3><i class="fas fa-info-circle"></i> Informasi Tambahan</h3>
				</div>

				<div class="form-row-2">
					<div class="form-group">
						<label for="TeleponInstansi">Telepon Instansi</label>
						<input type="tel" 
							   class="form-control <?= session('errors.TeleponInstansi') ? 'is-invalid' : '' ?>" 
							   name="TeleponInstansi" 
							   id="TeleponInstansi" 
							   placeholder="Contoh: 021-1234567"
							   value="<?= old('TeleponInstansi') ?>">
						<?php if (session('errors.TeleponInstansi')) : ?>
							<div class="invalid-feedback"><?= session('errors.TeleponInstansi') ?></div>
						<?php endif; ?>
					</div>

					<div class="form-group">
						<label for="TujuanKunjungan_ID">Tujuan Kunjungan</label>
						<select class="form-control <?= session('errors.TujuanKunjungan_ID') ? 'is-invalid' : '' ?>" 
								name="TujuanKunjungan_ID" 
								id="TujuanKunjungan_ID">
							<option value="" disabled selected> ----- Pilih ----- </option>
									  <?php foreach ($tujuan_kunjungan as $row) : ?>
								<option value="<?= $row->ID ?>" <?= old('TujuanKunjungan_ID') == $row->ID ? 'selected' : '' ?>>
									<?= $row->TujuanKunjungan ?>
								</option>
							<?php endforeach; ?>
						</select>
						<?php if (session('errors.TujuanKunjungan_ID')) : ?>
							<div class="invalid-feedback"><?= session('errors.TujuanKunjungan_ID') ?></div>
						<?php endif; ?>
					</div>
				</div>

				<div class="form-group">
					<label for="Information">Keterangan Tambahan</label>
					<textarea id="Information" 
							  name="Information" 
							  class="form-control <?= session('errors.Information') ? 'is-invalid' : '' ?>" 
							  placeholder="Masukkan keterangan atau catatan tambahan (opsional)"
							  rows="3"><?= old('Information') ?></textarea>
					<?php if (session('errors.Information')) : ?>
						<div class="invalid-feedback"><?= session('errors.Information') ?></div>
					<?php endif; ?>
				</div>

				<!-- Submit Button -->
				<div class="submit-section">
					<button type="submit" class="save-btn" id="saveBtn" name="submit">
						<i class="fas fa-save"></i>
						<span class="btn-text">Simpan Buku Tamu Rombongan</span>
						<span class="btn-loading" style="display: none;">
							<i class="fas fa-spinner fa-spin"></i>
							Menyimpan...
						</span>
					</button>
					<p style="margin-top: 20px; color: #666; font-size: 0.9rem;">
						Dengan mengisi formulir ini, data kunjungan rombongan akan tercatat dalam buku tamu perpustakaan.
					</p>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
// Auto-focus pada input pertama
document.addEventListener('DOMContentLoaded', function() {
	const firstInput = document.getElementById('NamaKetua');
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

	// Check for toastr messages
	checkToastrMessages();
	
	// Initialize calculations
	calculateTotals();
});



// Function untuk show custom notification
function showCustomNotification(message, type) {
	let alertClass, iconClass, title;
	
	switch(type) {
		case 'success':
			alertClass = 'alert-success-custom';
			iconClass = 'fas fa-check-circle';
			title = 'Berhasil!';
			break;
		case 'error':
			alertClass = 'alert-error-custom';
			iconClass = 'fas fa-exclamation-circle';
			title = 'Gagal!';
			break;
		case 'warning':
			alertClass = 'alert-warning-custom';
			iconClass = 'fas fa-exclamation-triangle';
			title = 'Perhatian!';
			break;
		default:
			alertClass = 'alert-error-custom';
			iconClass = 'fas fa-exclamation-circle';
			title = 'Perhatian!';
	}
	
	const notification = document.createElement('div');
	notification.className = `alert-custom ${alertClass}`;
	notification.style.position = 'fixed';
	notification.style.top = '20px';
	notification.style.right = '20px';
	notification.style.zIndex = '9999';
	notification.style.minWidth = '350px';
	notification.style.maxWidth = '500px';
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
	
	// Auto remove after 6 seconds for success, 8 seconds for error/warning
	const autoRemoveTime = type === 'success' ? 6000 : 8000;
	setTimeout(() => {
		notification.style.animation = 'slideOutRight 0.5s ease forwards';
		setTimeout(() => {
			if (notification.parentNode) {
				notification.remove();
			}
		}, 500);
	}, autoRemoveTime);
}

// Calculate totals function
function calculateTotals() {
	// Gender calculation
	const laki = parseInt(document.getElementById('CountLaki').value) || 0;
	const perempuan = parseInt(document.getElementById('CountPerempuan').value) || 0;
	const genderTotal = laki + perempuan;
	
	document.getElementById('genderTotal').textContent = genderTotal;
	document.getElementById('genderSummary').style.display = genderTotal > 0 ? 'block' : 'none';
	
	// Profesi calculation
	const profesiInputs = document.querySelectorAll('.profesi-count');
	let profesiTotal = 0;
	profesiInputs.forEach(input => {
		profesiTotal += parseInt(input.value) || 0;
	});
	
	document.getElementById('profesiTotal').textContent = profesiTotal;
	document.getElementById('profesiSummary').style.display = profesiTotal > 0 ? 'block' : 'none';
	
	// Pendidikan calculation
	const pendidikanInputs = document.querySelectorAll('.pendidikan-count');
	let pendidikanTotal = 0;
	pendidikanInputs.forEach(input => {
		pendidikanTotal += parseInt(input.value) || 0;
	});
	
	document.getElementById('pendidikanTotal').textContent = pendidikanTotal;
	document.getElementById('pendidikanSummary').style.display = pendidikanTotal > 0 ? 'block' : 'none';
}

// Add event listeners untuk auto calculation
document.getElementById('CountLaki').addEventListener('input', calculateTotals);
document.getElementById('CountPerempuan').addEventListener('input', calculateTotals);

document.querySelectorAll('.profesi-count').forEach(input => {
	input.addEventListener('input', calculateTotals);
});

document.querySelectorAll('.pendidikan-count').forEach(input => {
	input.addEventListener('input', calculateTotals);
});

// Form validation
document.getElementById('frm_create').addEventListener('submit', function(e) {
	const saveBtn = document.getElementById('saveBtn');
	const btnText = saveBtn.querySelector('.btn-text');
	const btnLoading = saveBtn.querySelector('.btn-loading');
	
	// Basic validation
	const namaKetua = document.getElementById('NamaKetua').value.trim();
	const nomorTelpon = document.getElementById('NomerTelponKetua').value.trim();
	const asalInstansi = document.getElementById('AsalInstansi').value.trim();
	const countPersonel = parseInt(document.getElementById('CountPersonel').value) || 0;
	
	if (!namaKetua || !nomorTelpon || !asalInstansi || countPersonel < 1) {
		e.preventDefault();
		showCustomNotification('Mohon lengkapi semua field yang wajib diisi (bertanda *)', 'error');
		return false;
	}
	
	// Validate phone number format
	const phoneRegex = /^(\+62|62|0)[0-9]{9,13}$/;
	if (!phoneRegex.test(nomorTelpon)) {
		e.preventDefault();
		showCustomNotification('Format nomor telepon tidak valid. Gunakan format: 08123456789', 'error');
		document.getElementById('NomerTelponKetua').focus();
		return false;
	}
	
	// Validate email if provided
	const email = document.getElementById('EmailInstansi').value.trim();
	if (email) {
		const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		if (!emailRegex.test(email)) {
			e.preventDefault();
			showCustomNotification('Format email tidak valid', 'error');
			document.getElementById('EmailInstansi').focus();
			return false;
		}
	}
	
	// Check if gender total matches total personel (optional warning)
	const laki = parseInt(document.getElementById('CountLaki').value) || 0;
	const perempuan = parseInt(document.getElementById('CountPerempuan').value) || 0;
	const genderTotal = laki + perempuan;
	
	if (genderTotal > 0 && genderTotal !== countPersonel) {
		if (!confirm(`Jumlah laki-laki + perempuan (${genderTotal}) tidak sama dengan total anggota (${countPersonel}). Lanjutkan?`)) {
			e.preventDefault();
			return false;
		}
	}
	
	// Check if profesi total exceeds total personel (optional warning)
	const profesiInputs = document.querySelectorAll('.profesi-count');
	let profesiTotal = 0;
	profesiInputs.forEach(input => {
		profesiTotal += parseInt(input.value) || 0;
	});
	
	// Check if pendidikan total exceeds total personel (optional warning)
	const pendidikanInputs = document.querySelectorAll('.pendidikan-count');
	let pendidikanTotal = 0;
	pendidikanInputs.forEach(input => {
		pendidikanTotal += parseInt(input.value) || 0;
	});
	
	if (profesiTotal > countPersonel) {
		e.preventDefault();
		showCustomNotification(`Total berdasarkan profesi (${profesiTotal}) tidak boleh melebihi total anggota (${countPersonel})`, 'error');
		return false;
	}
	
	if (pendidikanTotal > countPersonel) {
		e.preventDefault();
		showCustomNotification(`Total berdasarkan pendidikan (${pendidikanTotal}) tidak boleh melebihi total anggota (${countPersonel})`, 'error');
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
	}, 15000); // 15 detik timeout untuk form yang lebih kompleks
});

// Auto-format phone number
document.getElementById('NomerTelponKetua').addEventListener('input', function(e) {
	let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
	
	// Auto-format Indonesian phone numbers
	if (value.startsWith('62')) {
		value = '0' + value.substring(2);
	}
	
	e.target.value = value;
});

// Validate total personel input
document.getElementById('CountPersonel').addEventListener('input', function(e) {
	const value = parseInt(e.target.value);
	if (value < 1) {
		e.target.value = 1;
	}
	calculateTotals();
});

// Auto-calculate when total personel changes
document.getElementById('CountPersonel').addEventListener('change', function(e) {
	const totalPersonel = parseInt(e.target.value) || 0;
	
	// Optional: Auto-distribute gender if not set
	const laki = parseInt(document.getElementById('CountLaki').value) || 0;
	const perempuan = parseInt(document.getElementById('CountPerempuan').value) || 0;
	
	if (totalPersonel > 0 && laki === 0 && perempuan === 0) {
		// Auto-suggest equal distribution
		const halfTotal = Math.floor(totalPersonel / 2);
		document.getElementById('CountLaki').value = halfTotal;
		document.getElementById('CountPerempuan').value = totalPersonel - halfTotal;
		calculateTotals();
	}
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
			timer: 3000
		});
	});
</script>
<?php endif; ?>

<?= $this->endsection() ?>