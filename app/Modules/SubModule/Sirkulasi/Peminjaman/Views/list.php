<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
	tr.group,
	tr.group:hover {
		background-color: #F0F3F5 !important;
	}

	dl {
		display: grid;
		grid-template-columns: max-content auto;
	}

	dt {
		grid-column-start: 1;
		width: 100px;
		font-weight: normal;
	}

	dd {
		grid-column-start: 2;
	}
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-refresh-2 icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Peminjaman
					<div class="page-title-subheading">Daftar semua Peminjaman</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('peminjaman') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="active breadcrumb-item" aria-current="page">Peminjaman</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<div class="main-card mb-3 card">
		<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Daftar Peminjaman
			<div class="btn-actions-pane-right actions-icon-btn">
				<?php if (is_allowed('sirkulasi-peminjaman/create')) : ?>
					<!-- <a data-toggle="modal" data-target="#modal_create" href="javascript:void(0);" class="btn btn-success" title="Tambah"><i class="fa fa-plus"></i> Peminjaman</a> -->
					<a href="<?= base_url('sirkulasi-peminjaman/create') ?>" class="btn btn-success" title="Tambah"><i class="fa fa-plus"></i> Peminjaman</a>
				<?php endif; ?>
			</div>
		</div>
		<div class="card-body">
			<table style="width: 100%;" id="tbl_data" class="table table-hover table-bordered">
				<thead class="bg-night-sky text-light">
					<tr>
						<th class="text-center" width="35">No</th>
						<th class="text-center" width="100">No. Barcode</th>
						<th class="text-center">Penerbit / Judul</th>
						<th class="text-center" width="100">Tgl. Pinjam/ Jatuh Tempo</th>
						<th class="text-center">Hari Terlambat</th>
						<th class="text-center" width="120">Lokasi Perpustakaan</th>
						<th class="text-center" width="100">Updated Date</th>
						<th class="text-center" width="100">Aksi</th>
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
<?= $this->include('Peminjaman\Views\add_modal'); ?>
<?= $this->include('Peminjaman\Views\update_modal'); ?>
<script>
	var groupColumn = 8;
	var t;
	$(document).ready(function() {
		t = $('#tbl_data').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				"url": '<?php echo site_url('api/sirkulasi-peminjaman/datatable/' . $slug) ?>',
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
					data: 'NomorBarcode'
				},
				{
					data: 'Title'
				},
				{
					data: 'LoanDate',
					className: 'text-center'
				},
				{
					data: 'LateDays',
					className: 'text-center'
				},
				{
					data: 'LocationLibrary'
				},
				{
					data: 'UpdateDate'
				},
				{
					data: 'action',
					className: 'text-center',
					orderable: false
				},
				{
					data: 'CollectionLoan_id',
					visible: false
				},
				{
					data: 'ID',
					visible: false
				},
				{
					data: 'Fullname',
					visible: false
				},
				{
					data: 'DueDate',
					visible: false
				},
				{
					data: 'Publisher',
					visible: false
				},
			],
			"columnDefs": [{
					targets: [0, 7],
					searchable: false
				},
				{
					targets: [0, 2, 3, 4, 5, 7],
					orderable: false
				},
				{
					targets: groupColumn,
					visible: false
				},
			],
			"order": [
				[6, "desc"]
			],
			"drawCallback": function(data, type, full, meta) {
				var api = this.api();
				var data = api.rows().data();
				var rows = api.rows({
					page: 'current'
				}).nodes();
				var last = null;

				api
					.column(groupColumn, {
						page: 'current'
					})
					.data()
					.each(function(group, i) {
						if (last !== group) {
							$(rows)
								.eq(i)
								.before('<tr class="group"><td colspan="10">' + group + '</td></tr>');
							last = group;
						}
					});

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

	$('#tbl_data tbody').on('click', 'tr.group', function() {
		var currentOrder = table.order()[0];
		if (currentOrder[0] === groupColumn && currentOrder[1] === 'asc') {
			table.order([groupColumn, 'desc']).draw();
		} else {
			table.order([groupColumn, 'asc']).draw();
		}
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