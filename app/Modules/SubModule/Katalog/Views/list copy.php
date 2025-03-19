<?php
$request = service('request');
$slug = $request->getGet('slug');
$get_provinsi = user()->npp_provinsi_id ?? 0;
$branch_id = branch_id();
$branch_code = $request->getGet('branch_id');
$provinsi = $request->getGet('Province');
$kabkota = str_replace(".", "", $request->getGet('City'));
$kecamatan = str_replace(".", "", $request->getGet('Kecamatan'));
$kelurahan = str_replace(".", "", $request->getGet('Kelurahan'));
// set_setting_parameter('Branch_id',0);
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
				<div>Katalog
					<div class="page-title-subheading">Daftar semua Katalog</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('Katalog') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="active breadcrumb-item" aria-current="page">Katalog</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<?php if (is_member('admin') || is_member('sa_prov') || is_member('sa_kabkot') || is_member('sa_psm')) : ?>

		<div class="main-card mb-3 card">
			<div class="card-header"><i class="header-icon lnr-search icon-gradient bg-plum-plate"> </i> Filter
				<div class="btn-actions-pane-right actions-icon-btn">

				</div>
			</div>
			<div class="card-body">
				<form name="form_items" id="form_search" action="">
					<div class="row">
						<?php if (is_member('admin')) : ?>
							<div class="col-md-6">
								<div class="form-group">
									<label>Provinsi*</label>
									<div class="select-wrapper">
										<select required data-error="Pilih Provinsi" class="form-control select2" id="Province" name="Province" style="width:100%"></select>
									</div>
									<small class="help-block with-errors"></small>
								</div>
							</div>
						<?php endif; ?>

						<?php if (is_member('sa_prov')) : ?>
							<div class="col-md-6">
								<div class="form-group">
									<label>Provinsi*</label>
									<div class="select-wrapper">
										<select required data-error="Pilih Provinsi" class="form-control select2" name="Province" style="width:100%">
											<?php foreach (get_ref_table('t_region', 'id, code, name', 'code=' . $get_provinsi, 'data') as $row) : ?>
												<option value="<?= $row->code ?>"><?= $row->name ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<small class="help-block with-errors"></small>
								</div>
							</div>
						<?php endif; ?>
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
		<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Daftar Katalog
			<div class="btn-actions-pane-right actions-icon-btn">
				<?php if (is_allowed('katalog/create')) : ?>
					<?php if (get_setting_parameter('FormEntriKatalog', is_profiling()) == 'Simple') : ?>
						<a href="<?= base_url('katalog/create') ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i>
							Tambah Katalog
						</a>
					<?php else : ?>
						<a href="<?= base_url('katalog/create_marc') ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i>
							Tambah Katalog
						</a>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<form name="form_items" id="form_items">
					<table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
						<thead>
							<tr>
								<th class="text-center" width="35">
									<input type="checkbox" class="check_data" title="Pilih Semua">
								</th>
								<th class="text-center" width="100">Nama Perpustakaan</th>
								<th class="text-center" width="100">NPP Perpustakaan</th>
								<th class="text-center" width="100">BIBID</th>
								<th class="text-center">Judul</th>
								<th class="text-center" width="">Edisi</th>
								<th class="text-center" width="">Publisher</th>
								<th class="text-center" width="">Deskripsi Fisik</th>
								<th class="text-center" width="">No. Panggil</th>
								<th class="text-center" width="">Eksemplar</th>
								<th class="text-center" width="">OPAC</th>
								<th class="text-center" width="80">Aksi</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</form>
			</div>
		</div>
		<div class="card-footer">
			<div class="d-block">
				<button type="button" id="proses_karantina" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="Semua katalog yang terpilih"><i class="fa fa-shopping-cart"></i> Pindahkan ke Karantina</button> &nbsp;
				<button type="button" id="proses_opac" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Semua katalog yang terpilih"><i class="fa fa-check-square"></i> Tampilkan di OPAC</button>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
	let is_profiling = `<?= is_profiling() ?>`;
	if (<?= is_member('sa_prov') ?>) {
		code = '<?php echo $get_provinsi ?>';

		getData(`<?= base_url('api/region/city') ?>/${code}.`, `#City`);
	}

	let branch_id = `<?= branch_id() ?>`





	if (is_profiling) {

		getData(`<?= base_url('api/region/province') ?>`, `#Province`);

		// code = code.replace(".", "");


		getData(`<?= base_url('api-mitra-perpustakaan/branch') ?>/${code}`, `#Branch`);
		$('#Province').change(function(e) {
			var code = $(this).val();

			getData(`<?= base_url('api/region/city') ?>/${code}.`, `#City`);

			code = code.replace(".", "");
			// code = code.replace(".", "");


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

		$('#branch_id').select2({
			maximumInputLength: 3
		});
	}
</script>
<script>
	var t;
	$(document).ready(function() {
		t = $('#tbl_data').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				"url": "<?php echo site_url('api/katalog/datatable?branch_code=' . $branch_code . '&provinsi_id=' . $provinsi) . '&kabkota_id=' . $kabkota . '&kecamatan_id=' . $kecamatan . '&kelurahan_id=' . $kelurahan ?>",

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
					orderable: false
				},
				{
					data: 'Nama Perpustakaan',
					className: 'text-left'
				},
				{
					data: 'NPP Perpustakaan',
					className: 'text-left'
				},
				{
					data: 'BIBID',
					className: 'text-left'
				},
				{
					data: 'Title'
				},
				{
					data: 'Edition'
				},
				{
					data: 'Publisher'
				},
				{
					data: 'PhysicalDescription'
				},
				{
					data: 'ControlNumber'
				},
				{
					data: 'Eksemplar',
					className: 'text-center'
				},
				{
					data: 'IsOPAC',
					className: 'text-center',
					orderable: false
				},
				{
					data: 'action',
					className: 'text-center',
					orderable: false
				},
			],
			"order": [
				[1, "desc"]
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
							console.log(res)

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
		});
	});

	$(".check_data").click(function() {
		$('#tbl_data input:checkbox').not(this).prop('checked', this.checked);
	});

	$('#proses_karantina').click(function() {
		var form = $('#form_items');
		var serialize_bulk = form.serialize();
		var url = "<?= base_url('katalog/proses_karantina') ?>" + '?' + serialize_bulk;
		console.log(serialize_bulk);
		console.log(url);

		Swal.fire({
			title: 'Anda yakin?',
			html: "Semua katalog yang terpilih akan ditambah <br>ke Karantina Katalog",
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

	$('#proses_opac').click(function() {
		var form = $('#form_items');
		var serialize_bulk = form.serialize();
		var url = "<?= base_url('katalog/proses_opac') ?>" + '?' + serialize_bulk;
		console.log(serialize_bulk);
		console.log(url);

		Swal.fire({
			title: 'Anda yakin?',
			html: "Semua katalog yang terpilih akan ditampilkan di OPAC",
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
<?= $this->endSection('script'); ?>