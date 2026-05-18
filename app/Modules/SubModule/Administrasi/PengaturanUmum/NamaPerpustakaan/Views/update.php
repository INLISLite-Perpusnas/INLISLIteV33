<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">

<style>
	.select2 {
		text-transform: none;
		font-weight: normal;
	}

	.search-results {
		max-height: 300px;
		overflow-y: auto;
		border: 1px solid #ddd;
		border-radius: 4px;
		margin-top: 5px;
	}

	.search-result-item {
		padding: 10px;
		border-bottom: 1px solid #eee;
		cursor: pointer;
		transition: background-color 0.2s;
	}

	.search-result-item:hover {
		background-color: #f8f9fa;
	}

	.search-result-item:last-child {
		border-bottom: none;
	}

	.search-result-item .result-title {
		font-weight: bold;
		color: #333;
	}

	.search-result-item .result-subtitle {
		font-size: 0.9em;
		color: #666;
		margin-top: 2px;
	}

	.loading-spinner {
		text-align: center;
		padding: 20px;
	}

	.search-section {
		background-color: #f8f9fa;
		border: 1px solid #e9ecef;
		border-radius: 5px;
		padding: 20px;
		margin-bottom: 20px;
	}

	/* Custom SweetAlert2 styling */
	.swal2-popup {
		border-radius: 8px;
		box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
	}

	.swal2-title {
		font-size: 1.5em;
		font-weight: 600;
	}

	.swal2-content {
		font-size: 1em;
		line-height: 1.5;
	}

	.swal2-confirm {
		border-radius: 5px;
		padding: 10px 20px;
		font-weight: 500;
	}

	.swal2-timer-progress-bar {
		background: rgba(255, 255, 255, 0.6);
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
				<div>Nama Perpustakaan
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
						<li class="breadcrumb-item">Administrasi</li>
						<li class="breadcrumb-item">Pengaturan Umum</li>
						<li class="breadcrumb-item">Nama Perpustakaan</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8">
			<!-- Search Section -->
			<div class="main-card mb-3 card">
				<div class="card-header">
					<i class="header-icon lnr-magnifier icon-gradient bg-info"> </i> Pencarian Data Perpustakaan
				</div>
				<div class="card-body">
					<div class="search-section">
						<div class="form-row">
							<div class="col-md-10">
								<div class="position-relative form-group">
									<label for="search_perpus">Cari NPP atau Nama Perpustakaan</label>
									<input type="text" id="search_perpus" class="form-control" placeholder="Masukkan NPP atau Nama Perpustakaan (minimal 3 karakter)">
									<small class="form-text text-muted">Ketik minimal 3 karakter untuk memulai pencarian</small>
								</div>
							</div>
							<div class="col-md-2">
								<div class="position-relative form-group">
									<label>&nbsp;</label>
									<button class="btn btn-primary btn-block" type="button" id="btn_search_perpus">
										<i class="fa fa-search"></i> Cari
									</button>
								</div>
							</div>
						</div>

						<!-- Search Results -->
						<div id="search_results" class="search-results" style="display: none;"></div>

						<!-- Selected Data Preview -->
						<div id="selected_data_preview" class="mt-3" style="display: none;">
							<div class="alert alert-success">
								<strong>Data Terpilih:</strong>
								<div id="preview_content"></div>
								<button type="button" id="btn_apply_data" class="btn btn-sm btn-success mt-2">
									<i class="fa fa-check"></i> Terapkan Data
								</button>
								<button type="button" id="btn_clear_selection" class="btn btn-sm btn-secondary mt-2 ml-2">
									<i class="fa fa-times"></i> Batal
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Main Form -->
			<div class="main-card mb-3 card">
				<div class="card-header">
					<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Pengaturan Nama Perpustakaan
				</div>
				<div class="card-body">
					<div id="infoMessage"><?= $message ?? '' ?></div> <?= get_message('message') ?>
					<form id="frm_create" method="post" action="<?= base_url('master-nama-perpustakaan/update') ?>">
						<!-- Hidden fields for search data -->
						<input type="hidden" id="branch_id" name="branch_id" value="<?= set_value('branch_id', $Branch_id) ?>">

						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="npp_perpustakaan">NPP</label>
									<div>
										<input type="text" class="form-control" name="npp_perpustakaan" id="npp_perpustakaan" placeholder="" value="<?= set_value('npp_perpustakaan', $npp_perpustakaan) ?>">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="nama_perpustakaan">Nama Lembaga</label>
									<div>
										<input type="text" class="form-control" name="nama_perpustakaan" id="nama_perpustakaan" placeholder="" value="<?= set_value('nama_perpustakaan', $nama_perpustakaan) ?>">
									</div>
								</div>
							</div>
						</div>

						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="nama_lokasi_perpustakaan">Alamat</label>
									<div>
										<textarea class="form-control" name="nama_lokasi_perpustakaan" id="nama_lokasi_perpustakaan" placeholder=""><?= set_value('nama_lokasi_perpustakaan', $nama_lokasi_perpustakaan) ?></textarea>
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="provinsi_id">Provinsi</label>
									<select class="form-control" name="provinsi_id" id="provinsi_id">
										<option value="">-- Pilih Provinsi --</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="kabkota_id">Kabupaten / Kota</label>
									<select class="form-control" name="kabkota_id" id="kabkota_id">
										<option value="">-- Pilih Kab/Kota --</option>
									</select>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="kecamatan_id">Kecamatan</label>
									<select class="form-control" name="kecamatan_id" id="kecamatan_id">
										<option value="">-- Pilih Kecamatan --</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="kelurahan_id">Kelurahan / Desa</label>
									<select class="form-control" name="kelurahan_id" id="kelurahan_id">
										<option value="">-- Pilih Kelurahan --</option>
									</select>
								</div>
							</div>
						</div>

						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="jenis_perpustakaan">Jenis Perpustakaan</label>
									<div>
										<input type="text" class="form-control" name="jenis_perpustakaan" id="jenis_perpustakaan" placeholder="" value="<?= set_value('jenis_perpustakaan', $jenis_perpustakaan) ?>">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="email_perpustakaan">Email</label>
									<div>
										<input type="text" class="form-control" name="email_perpustakaan" id="email_perpustakaan" placeholder="" value="<?= set_value('email_perpustakaan', $email_perpustakaan) ?>">
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="instagram">Instagram</label>
									<div>
										<input type="text" class="form-control" name="instagram" id="instagram" placeholder="" value="<?= set_value('instagram', $instagram) ?>">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="youtube">Youtube</label>
									<div>
										<input type="text" class="form-control" name="youtube" id="youtube" placeholder="" value="<?= set_value('youtube', $youtube) ?>">
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="facebook">Facebook</label>
									<div>
										<input type="text" class="form-control" name="facebook" id="facebook" placeholder="" value="<?= set_value('facebook', $facebook) ?>">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="phone">No. Telepon</label>
									<div>
										<input type="text" class="form-control" name="phone" id="phone" placeholder="" value="<?= set_value('phone', $phone) ?>">
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="LayananOperasionl">Jam Oprasional</label>
									<div>
										<?php
										$LayananOperasionl_Str = '&lt;b&gt;Jam Operasional Layanan&lt;/b&gt;&lt;br&gt;
										&lt;i class=&quot;right-icon bx bx-chevrons-right&quot;&gt;&lt;/i&gt;Senin-Jumat 08.00 - 16.00 WIB&lt;br&gt;
										&lt;i class=&quot;right-icon bx bx-chevrons-right&quot;&gt;&lt;/i&gt;Sabtu-Minggu 08.00 - 15.00 WIB&lt;br&gt;
										&lt;i class=&quot;right-icon bx bx-chevrons-right&quot;&gt;&lt;/i&gt;Cuti Bersama dan Libur Nasional &lt;b&gt;Tutup&lt;/b&gt;&lt;br&gt;';
										$LayananOperasionl = $jam_operasional ?: $LayananOperasionl_Str;

										// Decode the HTML content to display it properly
										$LayananOperasionl_Display = htmlspecialchars_decode($LayananOperasionl, ENT_QUOTES);
										?>
										<textarea class="form-control" name="LayananOperasionl" id="LayananOperasionl" placeholder="" rows="5"><?= set_value('LayananOperasionl', $LayananOperasionl_Display) ?></textarea>
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="tulisan_banner">Tulisan Banner</label>
									<div>
										<textarea class="form-control" name="tulisan_banner" id="tulisan_banner" placeholder="" rows="5"><?= set_value('tulisan_banner', $tulisan_banner) ?></textarea>
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="tentang_kami">Tentang Kami</label>
									<div>
										<textarea class="form-control" name="tentang_kami" id="tentang_kami" placeholder="" rows="5"><?= set_value('tentang_kami', $tentang_kami) ?></textarea>
									</div>
								</div>
							</div>
						</div>

						<div class="form-row">
							<div class="col-md-12">
								<div class="form-group" style="display: inline-block">
									<div>
										<label for="IsUseKop">Gunakan Kop di Laporan </label><br>
										<input type="checkbox" class="apply-status" name="IsUseKop" value="1" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal" <?= ($is_use_kop) ? 'checked' : '' ?>>
									</div>
								</div>
							</div>
						</div>

						<div class="form-group">
							<button type="submit" class="btn btn-primary" name="submit">Simpan</button>
							<button type="button" class="btn btn-success ml-2" id="btn_daftarkan_inlislite">
								<i class="fa fa-paper-plane"></i> Daftarkan InlisLite
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="main-card mb-3 card">
				<div class="card-header">
					<i class="lnr-cog icon-gradient bg-plum-plate"> </i>&nbsp;Pengaturan Logo
					<div class="btn-actions-pane-right actions-icon-btn">
						<div class="menu-header-btn-pane">
							<a data-bs-toggle="modal" data-bs-target="#modal_upload_logo" data-toggle="modal" data-target="#modal_upload_logo" href="javascript:void(0);"
								class="mb-2 mr-2 btn btn-warning">
								<i class="fa fa-edit"></i> Update
							</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<?php
					$default = base_url('perpusnas.png');
					$file_image = (!empty($logo)) ? base_url('uploads/branch/' . $logo) : $default;
					?>
					<div class="form-row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="content"></label>
								<div>
									<img width="150px" src="<?= $file_image ?>" alt="Image" class="img">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="main-card mb-3 card">
				<div class="card-header">
					<i class="lnr-cog icon-gradient bg-plum-plate"> </i>&nbsp;Pengaturan Kop
					<div class="btn-actions-pane-right actions-icon-btn">
						<div class="menu-header-btn-pane">
							<a data-bs-toggle="modal" data-bs-target="#modal_upload_logokop" data-toggle="modal" data-target="#modal_upload_logokop" href="javascript:void(0);"
								class="mb-2 mr-2 btn btn-warning">
								<i class="fa fa-edit"></i> Update
							</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<?php
					$default = base_url('perpusnas.png');
					$file_image = (!empty($logo_kop)) ? base_url('uploads/branch/' . $logo_kop) : $default;
					?>
					<div class="form-row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="content"></label>
								<div>
									<img width="150px" src="<?= $file_image ?>" alt="Image" class="img">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
	$(document).ready(function() {
		<?php if (session()->getFlashdata('swal_icon')) : ?>
			Swal.fire({
				type: '<?= session()->getFlashdata('swal_icon') ?>', // gunakan 'icon' jika SweetAlert2 versi terbaru
				title: '<?= session()->getFlashdata('swal_title') ?>',
				html: '<?= session()->getFlashdata('swal_html') ?? session()->getFlashdata('swal_text') ?>',
				showConfirmButton: false,
				timer: 3000,
				icon: 'success'
			});
		<?php endif; ?>
	});
</script>
<script>
	// Konfigurasi global SweetAlert2
	const Toast = Swal.mixin({
		toast: true,
		position: 'top-end',
		showConfirmButton: false,
		timer: 3000,
		timerProgressBar: true,
		didOpen: (toast) => {
			toast.addEventListener('mouseenter', Swal.stopTimer)
			toast.addEventListener('mouseleave', Swal.resumeTimer)
		}
	});

	// Helper functions untuk notifikasi cepat
	function showToastSuccess(message) {
		Toast.fire({
			icon: 'success',
			title: message
		});
	}

	function showToastError(message) {
		Toast.fire({
			icon: 'error',
			title: message
		});
	}

	function showToastWarning(message) {
		Toast.fire({
			icon: 'warning',
			title: message
		});
	}

	function showToastInfo(message) {
		Toast.fire({
			icon: 'info',
			title: message
		});
	}

	// JavaScript utama untuk pencarian perpustakaan
	document.addEventListener('DOMContentLoaded', function() {
		var searchInput = document.getElementById('search_perpus');
		var searchButton = document.getElementById('btn_search_perpus');
		var resultsContainer = document.getElementById('search_results');
		var selectedDataPreview = document.getElementById('selected_data_preview');
		var previewContent = document.getElementById('preview_content');
		var applyDataBtn = document.getElementById('btn_apply_data');
		var clearSelectionBtn = document.getElementById('btn_clear_selection');

		var selectedData = null;

		// Search functionality
		function performSearch() {
			var keyword = searchInput.value.trim();
			resultsContainer.innerHTML = '';
			resultsContainer.style.display = 'none';
			selectedDataPreview.style.display = 'none';

			if (keyword.length < 3) {
				Swal.fire({
					icon: 'warning',
					title: 'Perhatian!',
					text: 'Masukkan minimal 3 karakter untuk pencarian.',
					showConfirmButton: true,
					confirmButtonText: 'OK',
					confirmButtonColor: '#ffc107'
				});
				return;
			}

			// Show loading
			resultsContainer.innerHTML = '<div class="loading-spinner"><i class="fa fa-spinner fa-spin"></i> Mencari...</div>';
			resultsContainer.style.display = 'block';

			// Fetch data from controller
			fetch('<?= base_url('master-nama-perpustakaan/searchperpustakaan') ?>?q=' + encodeURIComponent(keyword))
				.then(response => response.json())
				.then(data => {
					resultsContainer.innerHTML = '';

					if (data.status === 'success' && data.data && data.data.length > 0) {
						data.data.forEach(function(item) {
							var resultItem = document.createElement('div');
							resultItem.className = 'search-result-item';
							resultItem.innerHTML = `
                            <div class="result-title">NPP: ${item.npp || 'N/A'} | ${item.nama || 'N/A'}</div>
                            <div class="result-subtitle">
                                Jenis: ${item.jenis || 'N/A'} | 
                                Alamat: ${item.alamat || 'N/A'}
                            </div>
                        `;

							resultItem.addEventListener('click', function() {
								selectedData = item;
								selectResult(item);
							});

							resultsContainer.appendChild(resultItem);
						});
					} else {
						resultsContainer.innerHTML = '<div class="search-result-item">Tidak ada data ditemukan.</div>';
					}
				})
				.catch(err => {
					console.error('Error:', err);
					resultsContainer.innerHTML = '<div class="search-result-item text-danger">Terjadi kesalahan saat pencarian.</div>';

					// Show error notification
					Swal.fire({
						icon: 'error',
						title: 'Terjadi Kesalahan!',
						text: 'Koneksi bermasalah. Silakan coba lagi.',
						showConfirmButton: true,
						confirmButtonText: 'OK',
						confirmButtonColor: '#dc3545'
					});
				});
		}

		// Select result
		function selectResult(item) {
			previewContent.innerHTML = `
            <strong>NPP:</strong> ${item.npp || 'N/A'}<br>
            <strong>Nama:</strong> ${item.nama || 'N/A'}<br>
            <strong>Jenis:</strong> ${item.jenis || 'N/A'}<br>
            <strong>Alamat:</strong> ${item.alamat || 'N/A'}<br>
            <strong>Email:</strong> ${item.email || 'N/A'}
        `;

			selectedDataPreview.style.display = 'block';
			resultsContainer.style.display = 'none';
			searchInput.value = `${item.npp || ''} - ${item.nama || ''}`;
		}

		// Apply selected data to form
		function applySelectedData() {
			if (!selectedData) {
				Swal.fire({
					icon: 'warning',
					title: 'Perhatian!',
					text: 'Tidak ada data yang dipilih.',
					showConfirmButton: true,
					confirmButtonText: 'OK',
					confirmButtonColor: '#ffc107'
				});
				return;
			}

			// Update form fields
			document.getElementById('npp_perpustakaan').value = selectedData.npp || '';
			document.getElementById('nama_perpustakaan').value = selectedData.nama || '';
			document.getElementById('jenis_perpustakaan').value = selectedData.jenis || '';
			document.getElementById('nama_lokasi_perpustakaan').value = selectedData.alamat || '';
			document.getElementById('email_perpustakaan').value = selectedData.email || '';

			// Reload region cascade with data from search result
			savedProvinsiCode  = selectedData.provinsi_id  || '';
			savedKabkotaCode   = selectedData.kabkota_id   || '';
			savedKecamatanCode = selectedData.kecamatan_id || '';
			savedKelurahanCode = selectedData.kelurahan_id || '';
			loadProvinces(function() {
				if (savedProvinsiCode) {
					loadCities(savedProvinsiCode, function() {
						if (savedKabkotaCode) {
							loadDistricts(savedKabkotaCode, function() {
								if (savedKecamatanCode) loadSubDistricts(savedKecamatanCode);
							});
						}
					});
				}
			});

			// Hide preview
			selectedDataPreview.style.display = 'none';

			// Show success message with SweetAlert
			Swal.fire({
				icon: 'success',
				title: 'Berhasil!',
				text: 'Data berhasil diterapkan ke form. Silakan simpan untuk menyimpan perubahan.',
				showConfirmButton: true,
				confirmButtonText: 'OK',
				confirmButtonColor: '#28a745'
			});
		}

		// Clear selection
		function clearSelection() {
			selectedData = null;
			selectedDataPreview.style.display = 'none';
			searchInput.value = '';
			resultsContainer.innerHTML = '';
			resultsContainer.style.display = 'none';
		}

		// Event listeners
		searchButton.addEventListener('click', performSearch);

		searchInput.addEventListener('keypress', function(e) {
			if (e.key === 'Enter') {
				e.preventDefault();
				performSearch();
			}
		});

		applyDataBtn.addEventListener('click', applySelectedData);
		clearSelectionBtn.addEventListener('click', clearSelection);

		// Hide search results when clicking outside
		document.addEventListener('click', function(e) {
			if (!resultsContainer.contains(e.target) &&
				!searchInput.contains(e.target) &&
				!searchButton.contains(e.target)) {
				if (selectedDataPreview.style.display === 'none') {
					resultsContainer.style.display = 'none';
				}
			}
		});
	});
</script>

<?= $this->include('NamaPerpustakaan\Views\upload_modal'); ?>
<?= $this->include('NamaPerpustakaan\Views\upload_modal_kop'); ?>

<script>
// Region cascade dropdown logic
var savedProvinsiCode   = '<?= esc($provinsi_id ?? '') ?>';
var savedKabkotaCode    = '<?= esc($kabkota_id ?? '') ?>';
var savedKecamatanCode  = '<?= esc($kecamatan_id ?? '') ?>';
var savedKelurahanCode  = '<?= esc($kelurahan_id ?? '') ?>';

function stripDots(val) {
    return val ? String(val).replace(/\./g, '') : '';
}

function populateSelect(selectEl, items, savedCode) {
    items.forEach(function(item) {
        var opt = document.createElement('option');
        opt.value = item.code;                              // dotted code, used for cascade API calls
        opt.dataset.stripped = stripDots(item.code);        // dots-removed code, sent to Flask
        opt.textContent = item.name;
        if (item.code == savedCode || stripDots(item.code) === stripDots(savedCode)) {
            opt.selected = true;
        }
        selectEl.appendChild(opt);
    });
}

function getStrippedCode(selectId) {
    var sel = document.getElementById(selectId);
    if (!sel || !sel.value) return null;
    var opt = sel.options[sel.selectedIndex];
    return (opt && opt.dataset.stripped) ? opt.dataset.stripped : null;
}

function getSelectedNpp(selectId) {
    var sel = document.getElementById(selectId);
    if (!sel || !sel.value) return '';
    var opt = sel.options[sel.selectedIndex];
    return opt ? (opt.dataset.npp || '') : '';
}

function loadProvinces(callback) {
    fetch('<?= base_url('api/region/province') ?>')
        .then(function(r) { return r.json(); })
        .then(function(resp) {
            var sel = document.getElementById('provinsi_id');
            sel.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
            populateSelect(sel, resp.data || resp, savedProvinsiCode);
            if (typeof callback === 'function') callback();
        });
}

function loadCities(provinsiCode, callback) {
    var sel = document.getElementById('kabkota_id');
    sel.innerHTML = '<option value="">-- Pilih Kab/Kota --</option>';
    if (!provinsiCode) return;
    fetch('<?= base_url('api/region/city/') ?>' + provinsiCode)
        .then(function(r) { return r.json(); })
        .then(function(resp) {
            populateSelect(sel, resp.data || resp, savedKabkotaCode);
            if (typeof callback === 'function') callback();
        });
}

function loadDistricts(kabkotaCode, callback) {
    var sel = document.getElementById('kecamatan_id');
    sel.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
    if (!kabkotaCode) return;
    fetch('<?= base_url('api/region/district/') ?>' + kabkotaCode)
        .then(function(r) { return r.json(); })
        .then(function(resp) {
            populateSelect(sel, resp.data || resp, savedKecamatanCode);
            if (typeof callback === 'function') callback();
        });
}

function loadSubDistricts(kecamatanCode, callback) {
    var sel = document.getElementById('kelurahan_id');
    sel.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
    if (!kecamatanCode) return;
    fetch('<?= base_url('api/region/sub_district/') ?>' + kecamatanCode)
        .then(function(r) { return r.json(); })
        .then(function(resp) {
            populateSelect(sel, resp.data || resp, savedKelurahanCode);
            if (typeof callback === 'function') callback();
        });
}

// Cascade on manual change
document.getElementById('provinsi_id').addEventListener('change', function() {
    savedKabkotaCode   = '';
    savedKecamatanCode = '';
    savedKelurahanCode = '';
    document.getElementById('kabkota_id').innerHTML   = '<option value="">-- Pilih Kab/Kota --</option>';
    document.getElementById('kecamatan_id').innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
    document.getElementById('kelurahan_id').innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
    loadCities(this.value);
});

document.getElementById('kabkota_id').addEventListener('change', function() {
    savedKecamatanCode = '';
    savedKelurahanCode = '';
    document.getElementById('kecamatan_id').innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
    document.getElementById('kelurahan_id').innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
    loadDistricts(this.value);
});

document.getElementById('kecamatan_id').addEventListener('change', function() {
    savedKelurahanCode = '';
    document.getElementById('kelurahan_id').innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
    loadSubDistricts(this.value);
});

// Pre-load on page ready with saved values cascading
loadProvinces(function() {
    if (savedProvinsiCode) {
        loadCities(savedProvinsiCode, function() {
            if (savedKabkotaCode) {
                loadDistricts(savedKabkotaCode, function() {
                    if (savedKecamatanCode) {
                        loadSubDistricts(savedKecamatanCode);
                    }
                });
            }
        });
    }
});

// Daftarkan InlisLite button
document.getElementById('btn_daftarkan_inlislite').addEventListener('click', function() {
    Swal.fire({
        icon: 'question',
        title: 'Daftarkan InlisLite?',
        html: 'Data perpustakaan yang telah disimpan akan dikirimkan untuk pendaftaran InlisLite.<br>Pastikan data sudah lengkap dan benar.',
        showCancelButton: true,
        confirmButtonText: 'Ya, Daftarkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d'
    }).then(function(result) {
        if (!result.isConfirmed) return;

        var btn = document.getElementById('btn_daftarkan_inlislite');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mendaftarkan...';

        fetch('<?= base_url('master-nama-perpustakaan/daftarkan-inlislite') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                npp:          document.getElementById('npp_perpustakaan').value,
                nama:         document.getElementById('nama_perpustakaan').value,
                alamat:       document.getElementById('nama_lokasi_perpustakaan').value,
                email:        document.getElementById('email_perpustakaan').value,
                jenis:        document.getElementById('jenis_perpustakaan').value,
                phone:        document.getElementById('phone').value,
                provinsi_id:  getStrippedCode('provinsi_id'),
                kabkota_id:   getStrippedCode('kabkota_id'),
                kecamatan_id: getStrippedCode('kecamatan_id'),
                kelurahan_id: getStrippedCode('kelurahan_id')
            })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-paper-plane"></i> Daftarkan InlisLite';
            if (data.status === 'success') {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message || 'Pendaftaran InlisLite berhasil.' });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message || 'Pendaftaran InlisLite gagal.' });
            }
        })
        .catch(function(err) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-paper-plane"></i> Daftarkan InlisLite';
            Swal.fire({ icon: 'error', title: 'Kesalahan Koneksi', text: 'Tidak dapat terhubung ke server.' });
        });
    });
});
</script>

<?= $this->endSection('script') ?>