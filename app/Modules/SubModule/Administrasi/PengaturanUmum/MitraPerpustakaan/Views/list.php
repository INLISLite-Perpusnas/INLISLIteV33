<?php
$request = service('request');
$slug = $request->getGet('slug');
$NPP_Jenis = $request->getGet('NPP_Jenis') ?? '';
$NPP_Provinsi_id = $request->getGet('NPP_Provinsi_id') ?? '';
$NPP_KabKota_id = $request->getGet('NPP_KabKota_id') ?? '';
$NPP_Kecamatan_id = $request->getGet('NPP_Kecamatan_id') ?? '';

$params = '?NPP_Jenis=' . $NPP_Jenis . '&NPP_Provinsi_id=' . $NPP_Provinsi_id . '&NPP_KabKota_id=' . $NPP_KabKota_id . '&NPP_Kecamatan_id=' . $NPP_Kecamatan_id;
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
				<div>Mitra Perpustakaan
					<div class="page-title-subheading">Daftar semua Mitra Perpustakaan</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="active breadcrumb-item" aria-current="page">Mitra Perpustakaan</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<div class="main-card mb-3 card">
		<div class="card-header"><i class="header-icon lnr-magnifier icon-gradient bg-plum-plate"> </i> Filter
			<div class="btn-actions-pane-right actions-icon-btn">

			</div>
		</div>
		<div class="card-body">
			<form name="form_items" id="form_search" action="">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label>Jenis Perpustakaan</label>
							<div class="select-wrapper">
								<select class="form-control select2" id="Jenis" name="NPP_Jenis" style="width:100%">
									<option value="">-All-</option>
									<option value="SEKOLAH" <?= $NPP_Jenis == 'SEKOLAH' ? 'selected' : '' ?>>SEKOLAH</option>
									<option value="UMUM" <?= $NPP_Jenis == 'UMUM' ? 'selected' : '' ?>>UMUM</option>
									<option value="KHUSUS" <?= $NPP_Jenis == 'KHUSUS' ? 'selected' : '' ?>>KHUSUS</option>
									<option value="PERGURUAN TINGGI" <?= $NPP_Jenis == 'PERGURUAN TINGGI' ? 'selected' : '' ?>>PERGURUAN TINGGI</option>

								</select>
							</div>
							<small class="help-block with-errors"></small>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Provinsi</label>
							<div class="select-wrapper">
								<select required data-error="Pilih Provinsi" class="form-control select2" id="Province" name="NPP_Provinsi_id" style="width:100%">
									<option value="">-All-</option>
								</select>
							</div>
							<small class="help-block with-errors"></small>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Kota</label>
							<div class="select-wrapper">
								<select class="form-control select2" id="City" name="NPP_KabKota_id" style="width:100%">
									<option value="">-All-</option>
								</select>
							</div>
							<small class="help-block with-errors"></small>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Kecamatan</label>
							<div class="select-wrapper">
								<select class="form-control select2" id="District" name="NPP_Kecamatan_id" style="width:100%">
									<option value="">-All-</option>
								</select>
							</div>
							<small class="help-block with-errors"></small>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="main-card mb-3 card">
		<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Mitra Perpustakaan
			<div class="btn-actions-pane-right actions-icon-btn">
				<?php if (is_allowed('master-mitra-perpustakaan/create')) : ?>
					<a href="<?= base_url('master-mitra-perpustakaan/sync') ?>" class=" btn btn-primary" title=""><i class="fa fa-sync"></i>
						Sync Mitra Perpustakaan
					</a>
					<!-- <a data-toggle="modal" data-target="#modal_create" href="javascript:void(0);" class="btn btn-success" title="Tambah"><i class="fa fa-plus"></i> Tambah Mitra Perpustakaan</a> -->
				<?php endif; ?>
			</div>
		</div>
		<div class="card-body">
			<?= get_message('message'); ?>
			<table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
				<thead>
					<tr>
						<th class="text-center" width="35">No</th>
						<th class="text-center" width="70">Kode Mitra</th>
						<th class="text-center" width="80">Jenis Perpustakaan</th>
						<th class="text-center" width="200">Alamat Perpustakaan</th>
						<th class="text-center" width="200">Mitra Perpustakaan</th>
						<th class="text-center" width="80">Status</th>
						<th class="text-center" width="180">Aksi</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= $this->include('MitraPerpustakaan\Views\add_modal'); ?>
<?= $this->include('MitraPerpustakaan\Views\update_modal'); ?>
<script>
	var url = "<?php echo site_url('api-mitra-perpustakaan/datatable') ?>";
	let NPP_Jenis = "";
	let NPP_Provinsi_id = "";
	var t;
	$(document).ready(function() {
		t = $('#tbl_data').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				"url": `${url}?NPP_Jenis=${NPP_Jenis}&NPP_Provinsi_id=${NPP_Provinsi_id}`,
				"type": 'GET',
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
					data: 'no',
					className: 'text-center',
					orderable: false
				},
				{
					data: 'Alias',
					className: 'text-center'
				},
				{
					data: 'Jenis',
					className: 'text-center'
				},
				{
					data: 'Alamat'
				},
				{
					data: 'Name'
				},
				{
					data: 'active',
					className: 'text-center'
				},
				{
					data: 'action',
					className: 'text-center',
					orderable: false
				},
			],
			"columnDefs": [{
					targets: [0, 5, 6],
					searchable: false
				},
				{
					targets: [0, 2, 3, 6],
					orderable: false
				}
			],
			"order": [
				[1, "asc"]
			],
			"drawCallback": function(data, type, full, meta) {
				var api = this.api();
				var data = api.rows().data();
				$('[data-toggle="tooltip"]').tooltip();
			},
			"initComplete": function(settings, json) {
				var $searchInput = $('div.dataTables_filter input');
				$searchInput.unbind();
				$searchInput.bind('keyup', function(e) {
					if (e.keyCode == 13) {
						if (this.value.length == 0) {
							t.search('').draw();
						}

						if (this.value.length >= 3) {
							t.search(this.value).draw();
						}
					}
				});
			}
		});
	});

	function redrawDataTable() {
		NPP_Jenis = $('#Jenis').val()
		NPP_Provinsi_id = $('#Province').val();
		let NPP_KabKota_id = $('#City').val();
		let NPP_Kecamatan_id = $('#District').val();
		t.ajax.url(`${url}?NPP_Jenis=${NPP_Jenis}&NPP_Provinsi_id=${NPP_Provinsi_id}&NPP_KabKota_id=${NPP_KabKota_id}&NPP_Kecamatan_id=${NPP_Kecamatan_id}`);
		t.search($('.dataTables_filter input').val()).draw();
	}

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
</script>
<script>
	getData(`<?= base_url('api/region/province') ?>`, `#Province`, '');
	$('#Province').change(function(e) {
		var code = $(this).val();
		getData(`<?= base_url('api/region/city') ?>/${code}.`, `#City`);
		$('#City').val('');
		redrawDataTable();
	});
	$('#City').change(function(e) {
		var code = $(this).val();
		getData(`<?= base_url('api/region/district') ?>/${code}.`, `#District`);
		$('#District').val('');
		redrawDataTable();
	});
	$('#District').change(function(e) {
		redrawDataTable();
	});
	$('#Jenis').change(function(e) {
		redrawDataTable();
	});
</script>
<?= $this->endSection('script'); ?>