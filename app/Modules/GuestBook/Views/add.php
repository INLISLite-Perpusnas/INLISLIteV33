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

	/* Search Section */
	.search-section {
		background: white;
		border-radius: var(--border-radius);
		padding: 40px;
		margin-bottom: 30px;
		box-shadow: 0 5px 15px var(--shadow);
		text-align: center;
	}

	.search-title {
		color: var(--primary);
		font-size: 1.5rem;
		font-weight: 600;
		margin-bottom: 10px;
	}

	.search-subtitle {
		color: #666;
		margin-bottom: 30px;
		font-size: 1.1rem;
	}

	.search-form {
		max-width: 500px;
		margin: 0 auto;
	}

	.search-input-group {
		position: relative;
		display: flex;
		border-radius: 50px;
		overflow: hidden;
		box-shadow: 0 5px 20px var(--shadow);
		background: white;
	}

	.search-input {
		flex: 1;
		padding: 18px 25px;
		border: none;
		font-size: 16px;
		outline: none;
		background: white;
	}

	.search-input::placeholder {
		color: #999;
	}

	.search-btn {
		background: var(--primary);
		color: white;
		border: none;
		padding: 18px 30px;
		cursor: pointer;
		transition: all 0.3s;
		font-size: 16px;
		font-weight: 600;
	}

	.search-btn:hover {
		background: var(--primary-dark);
	}

	.search-btn i {
		margin-right: 8px;
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

	/* Member Profile Section */
	.member-profile {
		background: white;
		border-radius: var(--border-radius);
		padding: 30px;
		margin-bottom: 30px;
		box-shadow: 0 5px 15px var(--shadow);
	}

	.welcome-header {
		background: linear-gradient(135deg, var(--primary), var(--primary-dark));
		color: white;
		padding: 25px;
		border-radius: var(--border-radius);
		margin-bottom: 25px;
		text-align: center;
	}

	.welcome-header h2 {
		margin: 0;
		font-size: 1.5rem;
		font-weight: 600;
	}

	.welcome-header .member-number {
		opacity: 0.9;
		font-size: 1rem;
		margin-top: 5px;
	}

	/* Action Section */
	.action-section {
		background: white;
		border-radius: var(--border-radius);
		padding: 30px;
		box-shadow: 0 5px 15px var(--shadow);
		text-align: center;
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
	}

	.save-btn:hover {
		transform: translateY(-2px);
		box-shadow: 0 8px 25px rgba(3, 149, 80, 0.4);
	}

	.save-btn i {
		font-size: 1.2rem;
	}

	/* Responsive */
	@media (max-width: 768px) {
		.nav-tabs-custom {
			flex-direction: column;
		}

		.nav-tab {
			margin-bottom: 5px;
		}

		.search-input-group {
			flex-direction: column;
			border-radius: 15px;
		}

		.search-btn {
			border-radius: 0 0 15px 15px;
		}

		.page-header {
			padding: 20px;
		}

		.search-section {
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

	.content-wrapper>* {
		animation: fadeInUp 0.6s ease forwards;
	}

	.content-wrapper>*:nth-child(1) {
		animation-delay: 0.1s;
	}

	.content-wrapper>*:nth-child(2) {
		animation-delay: 0.2s;
	}

	.content-wrapper>*:nth-child(3) {
		animation-delay: 0.3s;
	}

	.content-wrapper>*:nth-child(4) {
		animation-delay: 0.4s;
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
					<span class="active">Anggota</span>
				</nav>
			</div><br>
			<h2 style="background-color: #28a745; color: #fff; padding: 10px; border-radius: 5px;">Total Kunjungan Hari ini <?= $totalKunjungan ?? '0' ?></h2>
		</div>

		<!-- Navigation Tabs -->
		<div class="nav-tabs-custom">
			<a href="<?= base_url('buku-tamu') ?>" class="nav-tab active">
				<i class="fas fa-user"></i>
				<span>Anggota</span>
			</a>
			<a href="<?= base_url('buku-tamu/non_anggota') ?>" class="nav-tab">
				<i class="fas fa-user-plus"></i>
				<span>Bukan Anggota</span>
			</a>
			<a href="<?= base_url('buku-tamu/rombongan') ?>" class="nav-tab">
				<i class="fas fa-users"></i>
				<span>Rombongan</span>
			</a>


		</div>

		<?php if (empty($member)) : ?>
			<!-- Search Section -->
			<div class="search-section">
				<h2 class="search-title">Cari Anggota Perpustakaan</h2>
				<p class="search-subtitle">Masukkan nomor anggota untuk melanjutkan ke buku tamu</p>

				<form method="get" action="<?= base_url('buku-tamu') ?>" class="search-form">
					<?= csrf_field() ?>
					<div class="search-input-group">
						<input type="text"
							class="search-input"
							name="member_no"
							id="member_no"
							placeholder="Masukkan nomor anggota..."
							required>
						<button class="search-btn" type="submit">
							<i class="fas fa-search"></i>
							Cari Anggota
						</button>
					</div>
				</form>
			</div>

			<!-- Warning Alert -->
			<div class="alert-custom alert-warning-custom">
				<i class="fas fa-exclamation-triangle"></i>
				<div>
					<strong>Perhatian!</strong><br>
					Silakan masukkan nomor anggota terlebih dahulu untuk melanjutkan pengisian buku tamu.
				</div>
				<button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">
					<span>&times;</span>
				</button>
			</div>

		<?php else : ?>
			<!-- Welcome Message -->
			<div class="alert-custom alert-success-custom">
				<i class="fas fa-check-circle"></i>
				<div>
					<strong>Selamat Datang, <?= $member->Fullname ?>!</strong><br>
					Terima kasih telah berkunjung ke perpustakaan kami.
				</div>
				<button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">
					<span>&times;</span>
				</button>
			</div>

			<!-- Member Profile -->
			<div class="member-profile">
				<div class="welcome-header">
					<h2>Profil Anggota</h2>
					<div class="member-number">No. Anggota: <?= $member->MemberNo ?></div>
				</div>
				<?= view('Member\Views\member_profile', array('member' => $member ?? '', 'jenis_anggota' => $jenis_anggota ?? [])) ?>
			</div>

			<!-- Action Section -->
			<div class="action-section">
				<form method="post" action="<?php echo base_url('buku-tamu/store_anggota'); ?>">
					<?= csrf_field() ?>
					<input type="hidden" name="member_no" value="<?= $member->MemberNo ?>">

					<?php if ($SettingBukuTamu == 1) : ?>

						<?php // PERBAIKAN: Menambahkan tanda kutip pembuka pada class 
						?>
						<div class="form-group">
							<div class="col-md-6" style="padding-left: 0;">
								<div class="position-relative form-group">
									<label for="TujuanKunjungan_id">Tujuan Kunjungan</label>
									<select class="form-control" name="TujuanKunjungan_id" id="TujuanKunjungan_id">
										<?php foreach (get_table('tujuan_kunjungan', 'ID, TujuanKunjungan', null, 'data') as $row) : ?>
											<?php // PERBAIKAN: Menyamakan ID dengan id 
											?>
											<option value="<?= $row->ID ?>" <?= set_select('TujuanKunjungan_id', $row->ID) ?>><?= $row->TujuanKunjungan ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
						</div><br></br>
					<?php endif; ?>

					<button class="save-btn" type="submit">
						<i class="fas fa-save"></i>
						Simpan Buku Tamu
					</button>
				</form>
				<p style="margin-top: 15px; color: #666; font-size: 0.9rem;">
					Dengan menekan tombol di atas, kunjungan Anda akan tercatat dalam buku tamu perpustakaan.
				</p>
			</div>
		<?php endif; ?>
	</div>
</div>

<script>
	// Auto-focus pada input search
	document.addEventListener('DOMContentLoaded', function() {
		const searchInput = document.getElementById('member_no');
		if (searchInput) {
			searchInput.focus();
		}
	});

	// Enter key untuk submit form
	document.getElementById('member_no')?.addEventListener('keypress', function(e) {
		if (e.key === 'Enter') {
			e.preventDefault();
			this.closest('form').submit();
		}
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

	// Add fadeOut animation
	const style = document.createElement('style');
	style.textContent = `
	@keyframes fadeOut {
		from { opacity: 1; transform: translateY(0); }
		to { opacity: 0; transform: translateY(-20px); }
	}
`;
	document.head.appendChild(style);
</script>
<?php if (session()->getFlashdata('success')) : ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
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