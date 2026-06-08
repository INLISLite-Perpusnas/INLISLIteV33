<?php
$request = service('request'); ?>
<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-id icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Keranjang Anggota
					<div class="page-title-subheading">Daftar semua Keranjang Anggota </div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
						<li class="breadcrumb-item">Keanggotaan</li>
						<li class="breadcrumb-item active">Keranjang Anggota</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<div class="main-card mb-3 card">
		<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Keranjang Anggota
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
							<th>Nama Anggota</th>
							<th>No. Anggota</th>
							<th>Tgl. Register</th>
							<th>Tgl. Berakhir</th>
							<th>Aksi</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</form>
		</div>
		<div class="card-footer">
			<div class="d-block">
				<button type="button" id="pulihkan_keranjang" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="Semua anggota yang terpilih"><i class="fa fa-undo"></i> Pulihkan dari Keranjang</button> &nbsp;
				<button type="button" id="hapus_permanen" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Semua anggota yang terpilih"><i class="fa fa-trash"></i> Hapus Permanen</button>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>

<?= $this->include('Anggota\Views\modal_upload'); ?>
<?= $this->include('Anggota\Views\modal_camera'); ?>
<script>
	Dropzone.autoDiscover = false;
</script>

<script>
	var t;
	$(document).ready(function() {
		t = $('#tbl_data').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				"url": '<?php echo site_url('api/anggota/datatable/1'); ?>',
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
					data: 'cid',
					className: 'text-center',
					orderable: false,
					searchable: false
				},
				{
					data: 'FullName'
				},
				{
					data: 'MemberNo'
				},
				{
					data: 'RegisterDate',
					className: 'text-center'
				},
				{
					data: 'EndDate',
					className: 'text-center'
				},
				{
					data: 'action',
					className: 'text-center',
					searchable: false,
					orderable: false
				},
			],
			"order": [
				[2, "asc"]
			],
			"drawCallback": function(data, type, full, meta) {
				var api = this.api();
				var data = api.rows().data();

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

				$.each(data, function(i, row) {
					$("#lazy" + row.id).Lazy();
				});

				$('.image-link').magnificPopup({
					type: 'image'
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

	$('#pulihkan_keranjang').click(function() {
		var form = $('#form_items');
		var serialize_bulk = form.serialize();
		var url = "<?= base_url('anggota/pulihkan_keranjang') ?>" + '?' + serialize_bulk;
		console.log(serialize_bulk);
		console.log(url);

		Swal.fire({
			title: 'Anda yakin?',
			html: "Semua anggota yang terpilih akan dipulihkan ke daftar anggota",
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
		var url = "<?= base_url('anggota/hapus_permanen') ?>" + '?' + serialize_bulk;
		console.log(serialize_bulk);
		console.log(url);

		Swal.fire({
			title: 'Anda yakin?',
			html: "Semua anggota yang terpilih akan dihapus secara permanen",
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