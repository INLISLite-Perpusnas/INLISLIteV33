<?php
$regionModel = new \Region\Models\RegionModel();
$request = service('request');
$npp_provinsi = "";
$npp_kabkota = "";
if (is_member('sa_prov') || is_member('sa_kabkot')) {
	$region = npp_region(user()->npp_provinsi_id, 1);
	if (!empty($region)) {
		$npp_provinsi = $region->name;
	}
}

if (is_member('sa_kabkot')) {
	$region = npp_region(user()->npp_kabkota_id, 2);
	if (!empty($region)) {
		$npp_kabkota = $region->name;
	}
}
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

	<?php if (is_profiling() && (branch_id() == 0)) : ?>
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
								<label>Provinsi</label>
								<div class="select-wrapper">
									<select class="form-control select2" id="provinsi_id" name="provinsi_id" style="width:100%"></select>
								</div>
								<small class="help-block with-errors"></small>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Kota / Kabupaten</label>
								<div class="select-wrapper">
									<select class="form-control select2" id="kabkota_id" name="kabkota_id" style="width:100%">
										<option value="">-</option>
									</select>
								</div>
								<small class="help-block with-errors"></small>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Kecamatan</label>
								<div class="select-wrapper">
									<select class="form-control select2" id="kecamatan_id" name="kecamatan_id" style="width:100%">
										<option value="">-</option>
									</select>
								</div>
								<small class="help-block with-errors"></small>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Kelurahan / Desa</label>
								<div class="select-wrapper">
									<select class="form-control select2" id="kelurahan_id" name="kelurahan_id" style="width:100%">
										<option value="">-</option>
									</select>
								</div>
								<small class="help-block with-errors"></small>
							</div>
						</div>
						<div class="col-md-6">
							<label for="groups">NPP (Mitra Perpustakaan)</label>
							<div class="select-wrapper">
								<select class="form-control selectx" id="npp" name="npp" tabindex="-1" aria-hidden="true" style="width:100%">
									<option value="">-</option>
								</select>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	<?php endif; ?>

	<div class="main-card mb-3 card">
		<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Daftar Katalog
			<div class="btn-actions-pane-right actions-icon-btn">
				<?php if (is_allowed('katalog/create') && (branch_id() > 0)) : ?>
					<?php if (get_setting_parameter('FormEntriKatalog', is_profiling()) == 'Simple') : ?>
						<a href="<?= base_url('katalog/create?rda=1') ?>" class=" btn btn-primary" title=""><i class="fa fa-plus"></i>
							Tambah Katalog RDA
						</a>
						<a href="<?= base_url('katalog/create?rda=0') ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i>
							Tambah Katalog AACR
						</a>
					<?php else : ?>
						<a href="<?= base_url('katalog/create_marc') ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i>
							Tambah Katalog
						</a>
					<?php endif; ?>
				<?php endif; ?>
				<a href="<?= base_url('report/katalog') ?>" class="btn btn-secondary" title=""><i class="fa fa-file-excel"></i>
					Ekspor Laporan
				</a>
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
								<th class="text-center"><span class="text-primary">Branch ID</span> <br> NPP / Perpustakaan</th>
								<th class="text-center">BIBID</th>
								<th class="text-center">Judul</th>
								<th class="text-center">Edisi</th>
								<th class="text-center">Publisher</th>
								<th class="text-center">Deskripsi Fisik</th>
								<th class="text-center">No. Panggil</th>
								<th class="text-center">Eksemplar</th>
								<th class="text-center">OPAC</th>
								<th class="text-center">Pedoman Katalog</th>
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
	var t;
	$(document).ready(function() {
		t = $('#tbl_data').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				"url": "<?php echo site_url('api/katalog/datatable') ?>",
				"type": "POST",

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
					data: 'Perpustakaan',
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
					data: 'IsRDA',
					className: 'text-center',
					orderable: true
				},
				{
					data: 'action',
					className: 'text-center',
					orderable: false
				},
				{
					data: 'NPP_Provinsi_id', //11
					visible: false,
				},
				{
					data: 'NPP_KabKota_id', //12
					visible: false,
				},
				{
					data: 'NPP_Kecamatan_id', //13
					visible: false,
				},
				{
					data: 'NPP_Kelurahan_id', //14
					visible: false,
				},
				{
					data: 'Branch_id', //15
					visible: false,
				},
				{
					data: 'Code', //16
					visible: false,
				},
				{
					data: 'Name', //17
					visible: false,
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
<script>
	let is_profiling = `<?= is_profiling() ?>`;
	let is_admin = `<?= is_member('admin') ?>`;
	let is_sa_prov = `<?= is_member('sa_prov') ?>`;
	let is_sa_kabkot = `<?= is_member('sa_kabkot') ?>`;
	let is_sa_pkpt = `<?= is_member('sa_pkpt') ?>`;
	let is_sa_psm = `<?= is_member('sa_psm') ?>`;
	let npp_provinsi_id = '<?= user()->npp_provinsi_id ?>';
	let npp_kabkota_id = '<?= user()->npp_kabkota_id ?>';
	let npp_provinsi = '<?= $npp_provinsi ?>';
	let npp_kabkota = '<?= $npp_kabkota ?>';

	if (is_profiling) {
		if (is_sa_kabkot || is_sa_prov) {
			$('#provinsi_id').html(`<option value="">${npp_provinsi}</option>`);

			if (is_sa_kabkot) {
				$('#kabkota_id').html(`<option value="">${npp_kabkota}</option>`);
				getData(`<?= base_url('api/region/district') ?>/${npp_kabkota_id}`, `#kecamatan_id`, false, 'Pilih');
			} else {
				getData(`<?= base_url('api/region/city') ?>/${npp_provinsi_id}.`, `#kabkota_id`, false, 'Pilih');
			}
		} else {
			getData(`<?= base_url('api/region/province') ?>`, `#provinsi_id`, false, 'Pilih');
		}

		$('#provinsi_id').change(function(e) {
			var provinsi_id = $(this).val();
			if (!provinsi_id) {
				$('#kabkota_id').html('<option value="">Pilih</option>');
				$('#kecamatan_id').html('<option value="">Pilih</option>');
				$('#kelurahan_id').html('<option value="">Pilih</option>');
				$('#npp').html('<option value="">Pilih</option>');
			} else {
				t.columns(12).search(provinsi_id).draw();
				getData(`<?= base_url('api/region/city') ?>/${provinsi_id}.`, `#kabkota_id`, false, 'Pilih');

				var code = provinsi_id.replace(".", "").replace(".", "");
				getData(`<?= base_url('api-mitra-perpustakaan/branch') ?>/${code}/NPP_Provinsi_id`, `#npp`, false, 'Pilih', true);
			}
		});

		$('#kabkota_id').change(function(e) {
			var kabkota_id = $(this).val();
			if (!kabkota_id) {
				$('#kecamatan_id').html('<option value="">Pilih</option>');
				$('#kelurahan_id').html('<option value="">Pilih</option>');
				$('#npp').html('<option value="">Pilih</option>');
			} else {
				t.columns(13).search(kabkota_id.replace(".", "")).draw();
				getData(`<?= base_url('api/region/district') ?>/${kabkota_id}`, `#kecamatan_id`, false, 'Pilih');

				var code = kabkota_id.replace(".", "").replace(".", "");
				getData(`<?= base_url('api-mitra-perpustakaan/branch') ?>/${code}/NPP_KabKota_id`, `#npp`, false, 'Pilih', true);
			}
		});

		$('#kecamatan_id').change(function(e) {
			var kecamatan_id = $(this).val();
			if (!kecamatan_id) {
				$('#kelurahan_id').html('<option value="">Pilih</option>');
				$('#npp').html('<option value="">Pilih</option>');
			} else {
				t.columns(14).search(kecamatan_id.replace(".", "").replace(".", "")).draw();
				getData(`<?= base_url('api/region/sub_district') ?>/${kecamatan_id}`, `#kelurahan_id`, false, 'Pilih');

				var code = kecamatan_id.replace(".", "").replace(".", "");
				getData(`<?= base_url('api-mitra-perpustakaan/branch') ?>/${code}/NPP_Kecamatan_id`, `#npp`, false, 'Pilih', true);
			}
		});

		$('#kelurahan_id').change(function(e) {
			var kelurahan_id = $(this).val();
			if (!kecamatan_id) {
				$('#npp').html('<option value="">Pilih</option>');
			} else {
				t.columns(15).search(kelurahan_id.replace(".", "").replace(".", "").replace(".", "")).draw();

				var code = kelurahan_id.replace(".", "").replace(".", "").replace(".", "");
				getData(`<?= base_url('api-mitra-perpustakaan/branch') ?>/${code}/NPP_Kelurahan_id`, `#npp`, false, 'Pilih', true);
			}
		});

		$('#npp').change(function(e) {
			var npp_id = $(this).val();
			t.columns(16).search(npp_id).draw();
		});
	}
</script>
<?= $this->endSection('script'); ?>