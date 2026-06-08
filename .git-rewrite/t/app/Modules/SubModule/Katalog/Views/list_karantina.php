<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
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
				<div>Karantina Katalog
					<div class="page-title-subheading">Daftar semua Karantina Katalog</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('Katalog') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="breadcrumb-item">Katalog</li>
						<li class="active breadcrumb-item" aria-current="page">Karantina Katalog</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<div class="main-card mb-3 card">
		<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Karantina Katalog
			<div class="btn-actions-pane-right actions-icon-btn">
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
							<th class="text-center" width="100">BIBID</th>
							<th class="text-center">Judul</th>
							<th class="text-center" width="">Edisi</th>
							<th class="text-center" width="">Publisher</th>
							<th class="text-center" width="">Deskripsi Fisik</th>
							<th class="text-center" width="">No. Panggil</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</form>
		</div>
		<div class="card-footer">
			<div class="d-block">
				<button type="button" id="pulihkan_katalog" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="Semua katalog yang terpilih"><i class="fa fa-undo"></i> Pulihkan dari Karantina</button> &nbsp;
				<button type="button" id="hapus_permanen" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Semua katalog yang terpilih"><i class="fa fa-trash"></i> Hapus Permanen</button>
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
				"url": '<?php echo site_url('api/katalog/datatable/1') ?>',
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
					data: 'BIBID',
					className: 'text-center'
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
			],
			"order": [
				[1, "asc"]
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

	$(".check_data").click(function() {
		$('#tbl_data input:checkbox').not(this).prop('checked', this.checked);
	});

	$('#pulihkan_katalog').click(function() {
		var form = $('#form_items');
		var serialize_bulk = form.serialize();
		var url = "<?= base_url('katalog/pulihkan_katalog') ?>" + '?' + serialize_bulk;
		console.log(serialize_bulk);
		console.log(url);

		Swal.fire({
			title: 'Anda yakin?',
			html: "Semua katalog yang terpilih akan dipulihkan <br>ke Daftar Katalog",
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

	$('#hapus_permanen').click(function() {
		var form = $('#form_items');
		var serialize_bulk = form.serialize();
		var url = "<?= base_url('katalog/hapus_permanen') ?>" + '?' + serialize_bulk;
		console.log(serialize_bulk);
		console.log(url);

		Swal.fire({
			title: 'Anda yakin?',
			html: "Semua katalog yang terpilih akan dihapus permanen.<br><?= lang('App.swal.can_not_be_restored') ?>",
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