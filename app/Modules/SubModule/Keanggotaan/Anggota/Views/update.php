<?php
$request = service('request');

$slug = $request->getGet('slug') ?? 'keanggotaan';
$member_id = $request->getGet('member_id') ?? 0;

$member = get_ref_single('members', 'ID=' . $anggota->ID, 'data');
$jenis_anggota = get_ref_single('jenis_anggota', 'id=' . $member->JenisAnggota_id, 'data');
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
	/* ========================================
   FORM VALIDATION STATES
   ======================================== */

	/* Loading overlay saat form di-submit */
	.form-loading-overlay {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background: rgba(0, 0, 0, 0.5);
		z-index: 9999;
		display: none;
		align-items: center;
		justify-content: center;
	}

	.form-loading-overlay.active {
		display: flex;
	}

	.form-loading-content {
		background: white;
		padding: 2rem;
		border-radius: 0.5rem;
		text-align: center;
	}

	.form-loading-spinner {
		border: 4px solid #f3f3f3;
		border-top: 4px solid #007bff;
		border-radius: 50%;
		width: 40px;
		height: 40px;
		animation: spin 1s linear infinite;
		margin: 0 auto 1rem;
	}

	@keyframes spin {
		0% {
			transform: rotate(0deg);
		}

		100% {
			transform: rotate(360deg);
		}
	}

	/* Button states */
	.btn-primary:disabled {
		cursor: not-allowed;
		opacity: 0.65;
		background-color: #6c757d;
		border-color: #6c757d;
	}

	/* ========================================
   ERROR STYLING untuk Select2 & Form Fields
   ======================================== */

	/* Select2 dengan error */
	.select2-container.border-danger .select2-selection {
		border-color: #dc3545 !important;
		box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
	}

	/* Input dengan error */
	.form-control.is-invalid {
		border-color: #dc3545;
		padding-right: calc(1.5em + 0.75rem);
		background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
		background-repeat: no-repeat;
		background-position: right calc(0.375em + 0.1875rem) center;
		background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
	}

	/* Required indicator */
	label .text-danger {
		font-weight: bold;
	}

	/* Hover effect on Select2 */
	.select2-container--default .select2-selection--single:hover,
	.select2-container--default .select2-selection--multiple:hover {
		border-color: #80bdff;
	}

	/* Focus state */
	.select2-container--default.select2-container--focus .select2-selection--single,
	.select2-container--default.select2-container--focus .select2-selection--multiple {
		border-color: #80bdff;
		box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
	}

	/* Disabled Select2 */
	.select2-container--default .select2-selection--single.select2-selection--disabled,
	.select2-container--default .select2-selection--multiple.select2-selection--disabled {
		background-color: #e9ecef;
		cursor: not-allowed;
	}

	/* ========================================
   ACCORDION STYLING
   ======================================== */

	.accordion-wrapper .card {
		margin-bottom: 1rem;
		border: 1px solid rgba(0, 0, 0, 0.125);
		border-radius: 0.25rem;
	}

	.accordion-wrapper .card-header {
		background-color: #f8f9fa;
		border-bottom: 1px solid rgba(0, 0, 0, 0.125);
	}

	.accordion-wrapper .btn-link {
		color: #212529;
		text-decoration: none;
		width: 100%;
		text-align: left;
		display: block;
	}

	.accordion-wrapper .btn-link:hover {
		color: #007bff;
		text-decoration: none;
	}

	/* Collapsed state indicator */
	.accordion-wrapper .btn-link[aria-expanded="false"]::after {
		content: "+";
		float: right;
		font-weight: bold;
		font-size: 1.2rem;
	}

	.accordion-wrapper .btn-link[aria-expanded="true"]::after {
		content: "−";
		float: right;
		font-weight: bold;
		font-size: 1.2rem;
	}

	/* ========================================
   FORM IMPROVEMENTS
   ======================================== */

	/* Better spacing for form groups */
	.form-group {
		margin-bottom: 1.5rem;
	}

	/* Help text styling */
	.form-text {
		display: block;
		margin-top: 0.25rem;
	}

	/* Checkbox styling */
	.custom-control-label {
		cursor: pointer;
		font-weight: normal;
	}

	.custom-control-input:checked~.custom-control-label::before {
		background-color: #007bff;
		border-color: #007bff;
	}

	/* ========================================
   BUTTON STYLING
   ======================================== */

	.btn-primary {
		background-color: #007bff;
		border-color: #007bff;
	}

	.btn-primary:hover:not(:disabled) {
		background-color: #0069d9;
		border-color: #0062cc;
	}

	.btn-primary:disabled {
		cursor: not-allowed;
		opacity: 0.65;
	}

	/* ========================================
   RESPONSIVE IMPROVEMENTS
   ======================================== */

	@media (max-width: 767.98px) {

		.form-row>.col-md-3,
		.form-row>.col-md-6,
		.form-row>.col-md-12 {
			margin-bottom: 1rem;
		}

		.accordion-wrapper .btn-link h5 {
			font-size: 1rem;
		}
	}

	/* ========================================
   SWEET ALERT CUSTOM STYLING
   ======================================== */

	.swal2-popup {
		font-size: 1rem !important;
	}

	.swal2-html-container {
		max-height: 400px;
		overflow-y: auto;
	}

	/* List dalam SweetAlert */
	.swal2-html-container .text-left {
		text-align: left !important;
	}

	/* ========================================
   LOADING SPINNER
   ======================================== */

	.fa-spinner {
		animation: fa-spin 1s infinite linear;
	}

	@keyframes fa-spin {
		0% {
			transform: rotate(0deg);
		}

		100% {
			transform: rotate(360deg);
		}
	}

	/* ========================================
   CARD BODY SEPARATOR
   ======================================== */

	.card-body.border-top {
		border-top: 1px solid rgba(0, 0, 0, 0.125) !important;
		margin-top: 1rem;
		padding-top: 1.25rem;
	}

	/* ========================================
   PRINT BUTTON STYLING
   ======================================== */

	.btn-group-print .btn {
		margin-right: 0.5rem;
		margin-bottom: 0.5rem;
	}

	/* ========================================
   TABLE AUDIT TRAIL
   ======================================== */

	.table-bordered th {
		background-color: #f8f9fa;
		font-weight: 600;
		width: 200px;
	}

	.table-bordered td {
		background-color: #ffffff;
	}

	/* ========================================
   CAMERA SECTION
   ======================================== */

	.is_camera,
	.is_upload {
		transition: all 0.3s ease;
	}

	#video,
	#photo {
		border-radius: 0.25rem;
		box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
	}

	#startbutton {
		transition: all 0.2s ease;
	}

	#startbutton:hover {
		background-color: rgba(0, 180, 0, 0.6);
		transform: scale(1.05);
	}
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>

<div class="app-main__inner">


	<div class="row">
		<div class="col-lg-12">
			<?= view('Member\Views\member_profile', array('member' => $member ?? '', 'jenis_anggota' => $jenis_anggota ?? [])) ?>
		</div>
	</div>

	<div class="main-card mb-3 card">
		<div class="card-header-tab card-header">
			<div class="card-header-title">
				<i class="header-icon lnr-layers icon-gradient bg-plum-plate"> </i>
				Informasi
			</div>
			<?php if (!$is_anggota) : ?>
				<ul class="nav">
					<li class="nav-item"><a href="<?= base_url('anggota/edit/' . $anggota->ID . '?slug=keanggotaan') ?>" class="nav-link show <?= ($slug == 'keanggotaan') ? 'active' : '' ?>">Keanggotaan</a></li>
					<li class="nav-item"><a href="<?= base_url('anggota/edit/' . $anggota->ID . '?slug=pelanggaran') ?>" class="nav-link show <?= ($slug == 'pelanggaran') ? 'active' : '' ?>">Pelanggaran</a></li>
					<li class="nav-item"><a href="<?= base_url('anggota/edit/' . $anggota->ID . '?slug=peminjaman') ?>" class="nav-link show <?= ($slug == 'peminjaman') ? 'active' : '' ?>">Peminjaman</a></li>
					<li class="nav-item"><a href="<?= base_url('anggota/edit/' . $anggota->ID . '?slug=perpanjangan') ?>" class="nav-link show <?= ($slug == 'perpanjangan') ? 'active' : '' ?>">Perpanjangan</a></li>
					<li class="nav-item"><a href="<?= base_url('anggota/edit/' . $anggota->ID . '?slug=sumbangan') ?>" class="nav-link show <?= ($slug == 'sumbangan') ? 'active' : '' ?>">Sumbangan</a></li>
				</ul>
			<?php endif; ?>
		</div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? ''; ?></div>
			<?= get_message('message'); ?>

			<?= $this->include("Anggota\Views\section\\$slug"); ?>
		</div>
	</div>
</div>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
	// ========================================
	// INITIALIZATION
	// ========================================

	Dropzone.autoDiscover = false;
	var file_image = setDropzone('file_image', 'anggota', '.jpg,.jpeg,.png', 1, 10);

	// ========================================
	// SELECT2 INITIALIZATION
	// ========================================

	$(document).ready(function() {
		// Initialize ALL Select2 - PENTING: Ini harus dilakukan SEBELUM load data
		$('#Province, #City, #District, #SubDistrict').select2({
			placeholder: 'Pilih...',
			allowClear: true,
			width: '100%'
		});

		$('#ProvinceNow, #CityNow, #DistrictNow, #SubDistrictNow').select2({
			placeholder: 'Pilih...',
			allowClear: true,
			width: '100%'
		});

		$('#CategoryLoan_id, select[name="LocationLoan_id[]"]').select2({
			placeholder: 'Pilih...',
			allowClear: true,
			width: '100%'
		});

		// Load region data untuk Alamat KTP
		loadRegionData('Province', 'City', 'District', 'SubDistrict',
			'<?= $anggota->ProvinceCode ?? '' ?>',
			'<?= $anggota->CityCode ?? '' ?>',
			'<?= $anggota->KecamatanCode ?? '' ?>',
			'<?= $anggota->KelurahanCode ?? '' ?>'
		);

		// Load region data untuk Alamat Domisili
		loadRegionData('ProvinceNow', 'CityNow', 'DistrictNow', 'SubDistrictNow',
			'<?= $anggota->ProvinceNowCode ?? '' ?>',
			'<?= $anggota->CityNowCode ?? '' ?>',
			'<?= $anggota->KecamatanNowCode ?? '' ?>',
			'<?= $anggota->KelurahanNowCode ?? '' ?>'
		);

		// Setup change handlers
		setupRegionHandlers('Province', 'City', 'District', 'SubDistrict');
		setupRegionHandlers('ProvinceNow', 'CityNow', 'DistrictNow', 'SubDistrictNow');
	});

	// ========================================
	// REGION HANDLING FUNCTIONS
	// ========================================

	function loadRegionData(provinceId, cityId, districtId, subdistrictId, provCode, cityCode, distCode, subdistCode) {
		// Load Province first
		getData(`<?= base_url('api/region/province') ?>`, `#${provinceId}`, provCode, function() {
			// After province loaded, load city if exists
			if (provCode && cityCode) {
				getData(`<?= base_url('api/region/city') ?>/${provCode}`, `#${cityId}`, cityCode, function() {
					// After city loaded, load district if exists
					if (distCode) {
						getData(`<?= base_url('api/region/district') ?>/${cityCode}`, `#${districtId}`, distCode, function() {
							// After district loaded, load subdistrict if exists
							if (subdistCode) {
								getData(`<?= base_url('api/region/sub_district') ?>/${distCode}`, `#${subdistrictId}`, subdistCode);
							}
						});
					}
				});
			}
		});
	}

	function setupRegionHandlers(provinceId, cityId, districtId, subdistrictId) {
		$(`#${provinceId}`).on('change', function() {
			var code = $(this).val();
			$(`#${cityId}, #${districtId}, #${subdistrictId}`).empty().trigger('change');
			if (code) {
				getData(`<?= base_url('api/region/city') ?>/${code}`, `#${cityId}`, '');
			}
		});

		$(`#${cityId}`).on('change', function() {
			var code = $(this).val();
			$(`#${districtId}, #${subdistrictId}`).empty().trigger('change');
			if (code) {
				getData(`<?= base_url('api/region/district') ?>/${code}`, `#${districtId}`, '');
			}
		});

		$(`#${districtId}`).on('change', function() {
			var code = $(this).val();
			$(`#${subdistrictId}`).empty().trigger('change');
			if (code) {
				getData(`<?= base_url('api/region/sub_district') ?>/${code}`, `#${subdistrictId}`, '');
			}
		});
	}

	// ========================================
	// ALAMAT SAMA CHECKBOX
	// ========================================

	$(document).ready(function() {
		$("#is_similar").on('change', function() {
			if ($(this).is(":checked")) {
				// Copy values
				$('#AddressNow').val($('#Address').val());
				$('#RTNow').val($('#RT').val());
				$('#RWNow').val($('#RW').val());

				// Clone region selects
				cloneSelect2('#Province', '#ProvinceNow');
				cloneSelect2('#City', '#CityNow');
				cloneSelect2('#District', '#DistrictNow');
				cloneSelect2('#SubDistrict', '#SubDistrictNow');

				// Hapus required dari alamat domisili karena sudah sama dengan KTP
				$('#ProvinceNow, #CityNow, #DistrictNow, #SubDistrictNow').removeAttr('required');
			} else {
				// Clear domisili fields
				$('#AddressNow, #RTNow, #RWNow').val('');
				$('#ProvinceNow, #CityNow, #DistrictNow, #SubDistrictNow').val(null).trigger('change');

				// Tambahkan kembali required
				$('#ProvinceNow, #CityNow, #DistrictNow, #SubDistrictNow').attr('required', 'required');
			}
		});
	});

	function cloneSelect2(sourceId, targetId) {
		var sourceVal = $(sourceId).val();
		var sourceText = $(sourceId).find('option:selected').text();

		// Clone all options
		var options = $(sourceId + ' option').clone();
		$(targetId).empty().append(options);
		$(targetId).val(sourceVal).trigger('change');
	}

	// ========================================
	// FAKULTAS & JURUSAN
	// ========================================

	$(document).ready(function() {
		$('#Fakultas_id').on('change', function() {
			var fakultasId = $(this).val();
			var $jurusanSelect = $('#Jurusan_id');

			$jurusanSelect.empty().append('<option value="">Pilih Jurusan</option>');

			if (fakultasId) {
				$.ajax({
					url: `<?= base_url('api/jurusan/getjurusan') ?>/${fakultasId}`,
					type: 'GET',
					dataType: 'json',
					success: function(response) {
						if (response.success && response.data) {
							$.each(response.data, function(key, jurusan) {
								$jurusanSelect.append(
									`<option value="${jurusan.id}">${jurusan.Nama}</option>`
								);
							});
						}
					},
					error: function(xhr, status, error) {
						console.error('Error loading jurusan:', error);
					}
				});
			}
		});
	});

	// ========================================
	// DATEPICKER
	// ========================================

	$(function() {
		$(".datepicker").datepicker({
			format: 'yyyy-mm-dd',
			autoclose: true,
			todayHighlight: true,
		});
	});

	// ========================================
	// DATATABLES
	// ========================================

	setDataTable('#tbl_pelanggaran', [0], [1, 'asc'], true);
	setDataTable('#tbl_peminjaman', [0], [1, 'asc'], true);
	setDataTable('#tbl_perpanjangan', [0], [1, 'asc'], true);
	setDataTable('#tbl_sumbangan', [0], [1, 'asc'], true);

	// ========================================
	// FORM SUBMISSION WITH PROPER VALIDATION FLOW
	// ========================================

	$(document).ready(function() {
		var isValidating = false;
		var isSubmitting = false;

		// Add loading overlay to body
		if ($('#form-loading-overlay').length === 0) {
			$('body').append(`
			<div id="form-loading-overlay" class="form-loading-overlay">
				<div class="form-loading-content">
					<div class="form-loading-spinner"></div>
					<p>Menyimpan data...</p>
				</div>
			</div>
		`);
		}

		$('#frm').on('submit', function(e) {
			e.preventDefault(); // Always prevent default

			// Jika sedang proses, abaikan
			if (isValidating || isSubmitting) {
				console.log('Already processing...');
				return false;
			}

			isValidating = true;
			var $form = $(this);

			console.log('=== FORM SUBMIT DEBUG ===');
			console.log('Form action:', $form.attr('action'));
			console.log('Form method:', $form.attr('method'));
			console.log('Starting validation...');

			// EXPAND semua accordion SEBELUM validasi
			$('.collapse').collapse('show');

			// Tunggu accordion fully expanded
			setTimeout(function() {
				console.log('Validating form fields...');

				// Validasi manual
				var errors = [];
				var firstErrorField = null;

				// Cek field required yang visible
				$form.find('[required]').each(function() {
					var $field = $(this);
					var fieldName = $field.attr('name') || $field.attr('id');
					var fieldLabel = $field.closest('.form-group').find('label').first().text() || fieldName;

					// Skip jika field hidden
					if ($field.is(':hidden')) {
						return;
					}

					var value = $field.val();

					// Cek jika kosong
					if (!value || value === '' || value === null || (Array.isArray(value) && value.length === 0)) {
						errors.push(fieldLabel.replace('*', '').trim());

						// Tandai field error
						if ($field.hasClass('select2-hidden-accessible')) {
							$field.next('.select2-container').addClass('border-danger');
						} else {
							$field.addClass('is-invalid');
						}

						if (!firstErrorField) {
							firstErrorField = $field;
						}
					} else {
						// Hapus tanda error jika valid
						if ($field.hasClass('select2-hidden-accessible')) {
							$field.next('.select2-container').removeClass('border-danger');
						} else {
							$field.removeClass('is-invalid');
						}
					}
				});

				if (errors.length > 0) {
					// Reset flag
					isValidating = false;
					console.log('❌ Validation failed:', errors);

					Swal.fire({
						icon: 'warning',
						title: 'Data Belum Lengkap',
						html: 'Harap lengkapi field berikut:<br><br>' +
							'<div class="text-left" style="max-height: 300px; overflow-y: auto;">' +
							'• ' + errors.join('<br>• ') +
							'</div>',
						confirmButtonText: 'OK'
					}).then(() => {
						// Focus ke field pertama yang error setelah SweetAlert ditutup
						if (firstErrorField) {
							$('html, body').animate({
								scrollTop: firstErrorField.offset().top - 100
							}, 500);

							setTimeout(() => {
								if (firstErrorField.hasClass('select2-hidden-accessible')) {
									firstErrorField.select2('open');
								} else {
									firstErrorField.focus();
								}
							}, 600);
						}
					});

					return false;
				}

				// ✅ VALIDASI BERHASIL
				console.log('✅ Validation passed!');

				isValidating = false;
				isSubmitting = true;

				// Disable button dan show loading
				$('#btn-submit').prop('disabled', true)
					.html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

				// Show loading overlay
				$('#form-loading-overlay').addClass('active');

				console.log('Submitting form via AJAX...');

				// Serialize form data
				var formData = new FormData($form[0]);

				// Debug: Log form data
				console.log('Form data:');
				for (var pair of formData.entries()) {
					console.log(pair[0] + ': ' + pair[1]);
				}

				// Submit via AJAX
				$.ajax({
					url: $form.attr('action'),
					type: $form.attr('method') || 'POST',
					data: formData,
					processData: false,
					contentType: false,
					dataType: 'json', // ✅ TAMBAHKAN INI
					headers: {
						'X-Requested-With': 'XMLHttpRequest' // ✅ TAMBAHKAN INI
					},
					success: function(response) {
						console.log('✅ Submit success:', response);

						// Hide loading
						$('#form-loading-overlay').removeClass('active');

						// ✅ CEK response.success
						if (response.success) {
							Swal.fire({
								icon: 'success',
								title: 'Berhasil',
								text: response.message || 'Data anggota berhasil diperbarui',
								timer: 2000,
								showConfirmButton: false
							}).then(() => {
								window.location.reload();
							});
						} else {
							// ✅ TAMBAHKAN handling untuk response.success = false
							Swal.fire({
								icon: 'error',
								title: 'Gagal',
								text: response.message || 'Terjadi kesalahan saat menyimpan data',
								confirmButtonText: 'OK'
							});

							// Re-enable button
							$('#btn-submit').prop('disabled', false)
								.html('<i class="fa fa-save"></i> Simpan');

							isSubmitting = false;
						}
					},
					error: function(xhr, status, error) {
						console.error('❌ Submit error:', error);
						console.error('Response:', xhr.responseText);

						// Hide loading
						$('#form-loading-overlay').removeClass('active');

						// Re-enable button
						$('#btn-submit').prop('disabled', false)
							.html('<i class="fa fa-save"></i> Simpan');

						isSubmitting = false;

						// ✅ TAMPILKAN pesan error yang lebih detail
						let errorMessage = 'Terjadi kesalahan saat menyimpan data';

						// Coba parse response jika ada
						try {
							let errorResponse = JSON.parse(xhr.responseText);
							if (errorResponse.message) {
								errorMessage = errorResponse.message;
							}
						} catch (e) {
							errorMessage += ': ' + error;
						}

						Swal.fire({
							icon: 'error',
							title: 'Gagal',
							text: errorMessage,
							confirmButtonText: 'OK'
						});
					}
				});

			}, 400); // Delay 400ms untuk accordion animation

			return false;
		});
	});

	// ========================================
	// HELPER: Reset Submit Button
	// ========================================

	function resetSubmitButton() {
		$('#btn-submit').prop('disabled', false)
			.html('<i class="fa fa-save"></i> Simpan');
	}

	// Reset button jika ada error dari server
	$(document).ready(function() {
		<?php if (session()->getFlashdata('errors') || session()->getFlashdata('error')): ?>
			resetSubmitButton();
		<?php endif; ?>
	});

	// ========================================
	// PRINT HANDLERS
	// ========================================

	$("body").on("click", ".remove-data", function(e) {
		e.preventDefault();
		var url = $(this).attr('data-href');
		window.open(url, '_blank');
	});

	$("body").on("click", ".cetak-kartu", function(e) {
		e.preventDefault();
		var url = $(this).attr('data-href');
		window.open(url, '_blank');
	});

	// ========================================
	// FLASH MESSAGE
	// ========================================

	<?php if (session()->getFlashdata('success')): ?>
		Swal.fire({
			icon: 'success',
			title: 'Berhasil',
			text: "<?= session()->getFlashdata('success') ?>",
			timer: 3000,
			showConfirmButton: false
		});
	<?php endif; ?>

	<?php if (session()->getFlashdata('error')): ?>
		Swal.fire({
			icon: 'error',
			title: 'Gagal',
			text: "<?= session()->getFlashdata('error') ?>",
			confirmButtonText: 'OK'
		});
	<?php endif; ?>
</script>
<?= $this->endSection('script'); ?>