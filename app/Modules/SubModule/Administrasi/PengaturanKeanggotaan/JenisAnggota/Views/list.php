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
				<div>Jenis Anggota
					<div class="page-title-subheading">Daftar semua Jenis Anggota</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('jenisanggota') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="breadcrumb-item" aria-current="page">Administrasi</li>
                        <li class="breadcrumb-item" aria-current="page">Pengaturan Keanggotaan</li>
						<li class="breadcrumb-item" aria-current="page">Jenis Anggota</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<div class="main-card mb-3 card">
		<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Jenis Anggota
			<div class="btn-actions-pane-right actions-icon-btn">
				
					 <a data-bs-toggle="modal" data-bs-target="#modal_create" data-toggle="modal" data-target="#modal_create" href="javascript:void(0);" class="btn btn-success" title="Tambah"><i class="fa fa-plus"></i> Jenis Anggota</a>
				
			</div>
		</div>
		<div class="card-body">
			<?= get_message('message'); ?>
			<table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
				<thead>
					<tr>
						<th class="text-center" width="35">No</th>
						<th class="text-center">Jenis Anggota</th>
						<th class="text-center" width="90">Masa Berlaku</th>
						<th class="text-center" width="90">Biaya Pendaftaran</th>
						<th class="text-center" width="90">Biaya Perpanjangan</th>
						<th class="text-center" width="90">Maksimal Pinjam Koleksi</th>
						<th class="text-center" width="90">Maksimal Lama Pinjam Koleksi</th>
						<th class="text-center" width="90">Upload Dokumen Keanggotaan</th>
						<th class="text-center" width="90">Default Lokasi</th>
						<th class="text-center" width="90">Default Jenis Bahan</th>
						<th class="text-center" width="90">Status</th>
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
<?= $this->include('JenisAnggota\Views\add_modal'); ?>
<?= $this->include('JenisAnggota\Views\update_modal'); ?>
<script>
	var t;
	$(document).ready(function() {
		t = $('#tbl_data').DataTable({
			"processing": true,
			"serverSide": true,
			"scrollX": true, // ✅ TAMBAHKAN INI
			"autoWidth": false, // optional biar lebih rapi
			"ajax": {
				"url": '<?php echo site_url('api/master-jenis-anggota/datatable/' . $slug) ?>',
			},
			"dom": "<'row mb-2'<'col-md-6 col-sm-12 text-left'l><'col-md-6 col-sm-12 text-right'f>>" +
				"<'row'<'col-md-12'tr>>" +
				"<'row mt-2'<'col-md-5 col-sm-12 text-left'i><'col-md-7 col-sm-12 d-flex justify-content-end'p>>",

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
					orderable: false,
					width: "60px"
				},
				{
					data: 'jenisanggota',
					width: "250px"
				}, // 🔥 diperlebar
				{
					data: 'MasaBerlakuAnggota',
					className: 'text-center',
					width: "150px"
				},
				{
					data: 'BiayaPendaftaran',
					className: 'text-right',
					width: "180px"
				},
				{
					data: 'BiayaPerpanjangan',
					className: 'text-right',
					width: "180px"
				},
				{
					data: 'MaxPinjamKoleksi',
					className: 'text-center',
					width: "180px"
				},
				{
					data: 'MaxLoanDays',
					className: 'text-center',
					width: "180px"
				},
				{
					data: 'UploadDokumenKeanggotaanOnline',
					className: 'text-center',
					width: "220px"
				},
				{
					data: 'DefaultLokasi',
					className: 'text-center',
					width: "200px"
				},
				{
					data: 'DefaultBahan',
					className: 'text-center',
					width: "200px"
				},
				{
					data: 'active',
					className: 'text-center',
					orderable: false,
					searchable: false,
					width: "100px"
				},
				{
					data: 'action',
					className: 'text-center',
					orderable: false,
					width: "200px"
				}
			],
			"columnDefs": [{
					targets: [0, 4, 6],
					searchable: false
				},
				{
					targets: [0, 6],
					orderable: false
				},
			],
			"order": [
				[7, "desc"]
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
									icon: 'success',
									html: res.message,
									type: 'success',
									showConfirmButton: false,
									timer: 5000,
								}).then(() => {});
							} else {
								Swal.fire({
									title: 'Gagal',
									icon: 'error',
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