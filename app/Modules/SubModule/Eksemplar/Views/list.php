<?php
$request = service('request');
$slug = $request->getGet('slug');
$branch_id = $request->getGet('branch_id');
$actions = array(
	'cetak-label-a4-1' => 'Cetak Label A4-1 (Barcode + No. Panggil)',
	'cetak-label-a4-2' => 'Cetak Label A4-2 (Barcode + No. Panggil)',
	'cetak-label-a4-3' => 'Cetak Label A4-3 (Barcode + No. Panggil + 1 Warna)',
	'cetak-label-a4-4' => 'Cetak Label A4-4 (Barcode + No. Panggil + 1 Warna)',
	'cetak-label-a4-4-qrcode' => 'Cetak Label A4-4 (Qrcode + No. Panggil + 1 Warna)',
	// 'cetak-label-a4-5' => 'Cetak Label A4-4 (Barcode + No. Panggil + 1 Warna)',
	// 'cetak-label-a4-5' => 'Cetak Label A4-4 (Barcode + No. Panggil + 1 Warna)',
	'tampil-opac' => 'Tampilkan di Opac',
	'karantina-eksemplar' => 'Karantina Eksemplar',
);
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-server icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Eksemplar
					<div class="page-title-subheading">Daftar semua Eksemplar</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('Eksemplar') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="active breadcrumb-item" aria-current="page">Eksemplar</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<?php if (is_member('admin') && false) : ?>
		<div class="main-card mb-3 card">
			<div class="card-header"><i class="header-icon lnr-search icon-gradient bg-plum-plate"> </i> Filter
				<div class="btn-actions-pane-right actions-icon-btn">

				</div>
			</div>
			<div class="card-body">
				<form name="form_items" id="form_search" action="">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Provinsi*</label>
								<div class="select-wrapper">
									<select required data-error="Pilih Provinsi" class="form-control select2" id="Province" name="Province" style="width:100%"></select>
								</div>
								<small class="help-block with-errors"></small>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Kota*</label>
								<div class="select-wrapper">
									<select class="form-control select2" id="City" name="City" style="width:100%">
										<option value="">-Select-</option>
									</select>
								</div>
								<small class="help-block with-errors"></small>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Kecamatan*</label>
								<div class="select-wrapper">
									<select class="form-control select2" id="District" name="Kecamatan" style="width:100%">
										<option value="">-Select-</option>
									</select>
								</div>
								<small class="help-block with-errors"></small>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Kelurahan*</label>
								<div class="select-wrapper">
									<select class="form-control select2" id="SubDistrict" name="Kelurahan" style="width:100%">
										<option value="">-Select-</option>
									</select>
								</div>
								<small class="help-block with-errors"></small>
							</div>
						</div>
					</div>
					<div class="position-relative form-group">
						<label for="groups">Perpustakaan Mitra*</label>
						<div class="select-wrapper">
							<select class="form-control selectx" name="branch_id" id="Branch" tabindex="-1" aria-hidden="true" style="width:100%">
								<option value="">-Select-</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<button type="submit" class="btn btn-primary">Apply</button>
					</div>
				</form>
			</div>
		</div>
	<?php endif; ?>

	<div class="main-card mb-3 card">
		<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Daftar Eksemplar
			<div class="btn-actions-pane-right actions-icon-btn">
				<?php if (is_allowed('eksemplar/create')) : ?>
					<a href="<?= base_url('eksemplar/create?slug=' . $slug) ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i>
						Tambah Eksemplar
					</a>
				<?php endif; ?>
				<a href="<?= base_url('report/eksemplar') ?>" class="btn btn-secondary" title=""><i class="fa fa-file-excel"></i>
					Ekspor Laporan
				</a>
			</div>
		</div>
		<div class="card-footer">
			<div class="form-group col-md-6 p-0 pt-2">
				<div class="select-wrapper input-group mt-2 mb-2">

					<div class="input-group-prepend">
						<span class="btn btn-secondary pt-2">Pilih Aksi</span>
					</div>

					<select class="form-control" name="action" id="action">
						<option></option>
						<?php foreach ($actions as $key => $value) : ?>
							<option value="<?= $key ?>" <?= set_select('action', $key, false); ?>><?= $value ?></option>
						<?php endforeach; ?>
					</select>
					<form class="input-group-append" name="form_process" id="form_process" action="" method="post">
						<input type="hidden" value="" name="eksemplar_ids" id="eksemplar_ids">
						<input type="hidden" value="" name="eksemplar_tpl" id="eksemplar_tpl">
						<button type="submit" class="btn bg-primary text-white worksheet-btn-load" id="btnProcess" type="button" style="min-width:80px"><i class="fa fa-check "></i> Proses </button>
					</form>
				</div>
			</div>
		</div>
		<div class="card-body">
			<form name="form_items" id="form_items">
				<table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
					<thead>
						<tr>
							<th class="text-center" width="35">
								<input type="checkbox" class="check_data" title="Pilih Semua">
							</th>
							<th class="text-center" width="100">No. Barcode</th>
							<th class="text-center" width="100">Tanggal Pengadaan</th>
							<th class="text-center" width="100">No. Induk</th>
							<th class="text-center" width="">Data Bibliografis</th>
							<th class="text-center" width="">OPAC</th>
							<th class="text-center" width="">Karantina</th>
							<th class="text-center" width="80">Aksi</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</form>
		</div>
		
	</div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
	let text = 'https://data.perpusnas.go.id/public/direktori/list?provinsi_id=&kabkota_id=&kecamatan_id=&kelurahan_id=';
	$('#url').val(text);

	getData(`<?= base_url('api/region/province') ?>`, `#Province`);
	$('#Province').change(function(e) {
		let text = 'https://data.perpusnas.go.id/public/direktori/list?provinsi_id=&kabkota_id=&kecamatan_id=&kelurahan_id=';
		$('#url').val(text);

		var code = $(this).val();
		getData(`<?= base_url('api/region/city') ?>/${code}.`, `#City`);

		code = code.replace(".", "");
		code = code.replace(".", "");

		getData(`<?= base_url('api-mitra-perpustakaan/branch') ?>/${code}`, `#Branch`);

	});
	$('#City').change(function(e) {
		var code = $(this).val();
		getData(`<?= base_url('api/region/district') ?>/${code}`, `#District`);

		code = code.replace(".", "");
		code = code.replace(".", "");

		getData(`<?= base_url('api-mitra-perpustakaan/branch') ?>/${code}/NPP_KabKota_id`, `#Branch`);
	});
	$('#District').change(function(e) {
		var code = $(this).val();
		getData(`<?= base_url('api/region/sub_district') ?>/${code}`, `#SubDistrict`);

		code = code.replace(".", "");
		code = code.replace(".", "");

		getData(`<?= base_url('api-mitra-perpustakaan/branch') ?>/${code}/NPP_Kecamatan_id`, `#Branch`);
	});
	$('#SubDistrict').change(function(e) {
		var name = $("#SubDistrict option:selected").text();
		var code = $(this).val();

		code = code.replace(".", "");
		code = code.replace(".", "");
		code = code.replace(".", "");

		getData(`<?= base_url('api-mitra-perpustakaan/branch') ?>/${code}/NPP_Kelurahan_id`, `#Branch`);
	});
</script>
<script>
	$('#branch_id').select2({
		maximumInputLength: 3
	});
	var t;
	$(document).ready(function() {
		t = $('#tbl_data').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				"url": '<?php echo site_url('api/eksemplar/datatable') ?>',
			},
			"dom": "<'row'<'col-md-6 col-sm-8 col-xs-12 text-left'f><'col-md-6 col-sm-4 col-xs-12 d-none d-sm-block text-right'p>>" +
				"<'row'<'col-md-12'tr>>" +
				"<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12 text-right'i>>",
			"pagingType": "full_numbers",
			"oLanguage": {
				"sSearch": "<i class='fa fa-search'></i> _INPUT_",
				"sLengthMenu": "_MENU_",
				"oPaginate": {
					"sNext": "<i class='fa fa-chevron-right'></i>",
					"sPrevious": "<i class='fa fa-chevron-left'></i>",
					"sLast": "<i class='fa fa-chevron-double-right'></i>",
					"sFirst": "<i class='fa fa-chevron-double-left'></i>",
				}
			},
			"columns": [{
					data: 'ID',
					className: 'text-center',
					orderable: true
				},
				{
					data: 'NomorBarcode',
					className: 'text-left'
				},
				{
					data: 'TanggalPengadaan'
				},
				{
					data: 'NoInduk'
				},
				{
					data: 'Catalog_id'
				},
				{
					data: 'IsOPAC',
					className: 'text-center',
					orderable: false
				},
				{
					data:"IsQUARANTINE",
					className: 'text-center',
					orderable: false
				},
				{
					data: 'action',
					className: 'text-center',
					orderable: false
				},
			],"order": [
				[0, "desc"]
			],
			"drawCallback": function(data, type, full, meta) {
				var api = this.api();
				var data = api.rows().data();
				$('[data-toggle="tooltip"]').tooltip();
				$('.apply-status').bootstrapToggle();
				$(".apply-status").on('change', function() {
					var url = $(this).attr('data-href');
					var field = $(this).attr('data-field');
					var value = $(this).is(':checked');
					var data_post = 'field=' + field + '&value=' + value;

					$.ajax({
							url: url,
							type: 'POST',
							data: data_post,
						})
						.done(function(res) {
							
							if (res.error == false) {
								Swal.fire({
									title: 'Berhasil',
									html: res.message,
									type: 'success',
									showConfirmButton: false,
									timer: 5000,
								}).then(() => {});
							} else {
								Swal.fire({
									title: 'Gagal',
									text: res.message,
									type: 'error',
									showConfirmButton: false,
									timer: 5000
								}).then(() => {});
							}
						})
						.fail(function(res) {
							console.log(res);

							Swal.fire({
								title: 'Oups',
								text: 'Maaf, terjadi kesalahan. Coba beberapa saat lagi atau hubungi Admin',
								type: 'error',
								showConfirmButton: false,
								timer: 5000
							}).then(() => {});
						});
				});
			},
			"initComplete": function(settings, json) {
				// var $searchInput = $('div.dataTables_filter input');
				// $searchInput.unbind();
				// $searchInput.bind('keyup', function(e) {
				// 	if(e.keyCode == 13){
				// 		if(this.value.length == 0){
				// 			t.search('').draw();
				// 		}

				// 		if(this.value.length >= 3){
				// 			t.search( this.value ).draw();
				// 		}
				// 	} 
				// });
			}
		});
	});

	$(".check_data").click(function() {
		$('#tbl_data input:checkbox').not(this).prop('checked', this.checked);
	});

	$("body").on("click", ".remove-data", function() {
		var url = $(this).attr('data-href');
		console.log(url);
		Swal.fire({
			title: '<?= lang('App.swal.are_you_sure') ?>',
			text: "<?= lang('App.swal.can_not_be_restored') ?>",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#dd6b55',
			confirmButtonText: '<?= lang('App.btn.yes') ?>',
			cancelButtonText: '<?= lang('App.btn.no') ?>'
		}).then((result) => {
			if (result.value) {
				window.location.href = url;
			}
		});
		return false;
	});

	// Updated action change handler
	$('#action').change(function(e) {
		var form = $('#form_items');
		var serialize_bulk = form.serialize();
		var action = $(this).val();
		
		// Extract IDs from the form for any action
		var eksemplar_ids = extractIds(serialize_bulk);
		$('#eksemplar_ids').val(eksemplar_ids);
		$('#eksemplar_tpl').val(action);
		
		// Set the appropriate form action based on selected action
		if (action.includes('cetak-label')) {
			$('#form_process').attr('action', 'eksemplar/print_label');
		} else if (action === 'karantina-eksemplar') {
			$('#form_process').attr('action', 'eksemplar/proses_karantina');
		} else if (action === 'tampil-opac') {
			$('#form_process').attr('action', 'eksemplar/proses_opac');
		}
	});

	// Add this event handler for the process button
	$('#btnProcess').click(function(e) {
		var action = $('#action').val();
		var eksemplar_ids = $('#eksemplar_ids').val();
		
		// Don't submit if no action selected or no items checked
		if (!action || action.trim() === '') {
			e.preventDefault();
			Swal.fire({
				title: 'Peringatan',
				text: 'Silahkan pilih aksi terlebih dahulu',
				type: 'warning',
				showConfirmButton: true
			});
			return false;
		}
		
		if (!eksemplar_ids || eksemplar_ids.length === 0) {
			e.preventDefault();
			Swal.fire({
				title: 'Peringatan',
				text: 'Silahkan pilih minimal satu eksemplar',
				type: 'warning',
				showConfirmButton: true
			});
			return false;
		}
		
		// For karantina and opac actions, show confirmation dialog
		if (action === 'karantina-eksemplar') {
			e.preventDefault();
			Swal.fire({
				title: 'Anda yakin?',
				html: "Semua eksemplar yang terpilih akan ditambah <br>ke Karantina Eksemplar",
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#dd6b55',
				confirmButtonText: 'Ya',
				cancelButtonText: 'Tidak'
			}).then((result) => {
				if (result.value) {
					$('#form_process').submit();
				}
			});
		} else if (action === 'tampil-opac') {
			e.preventDefault();
			Swal.fire({
				title: 'Anda yakin?',
				html: "Semua eksemplar yang terpilih akan ditampilkan di OPAC",
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#dd6b55',
				confirmButtonText: 'Ya',
				cancelButtonText: 'Tidak'
			}).then((result) => {
				if (result.value) {
					$('#form_process').submit();
				}
			});
		}
		// For other actions like print labels, just allow the form to submit normally
	});

	// Function to extract IDs from form serialization
	function extractIds(serializedData) {
		var idArray = [];
		var urlParams = new URLSearchParams(serializedData);
		
		// Extract the ID values
		urlParams.getAll('ID[]').forEach(function(id) {
			idArray.push(id);
		});
		
		return idArray.length > 0 ? idArray : '';
	}
</script>
<?= $this->endSection('script'); ?>