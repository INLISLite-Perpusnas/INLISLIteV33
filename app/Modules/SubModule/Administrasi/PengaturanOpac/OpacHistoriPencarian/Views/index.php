<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-server icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Histori Pencarian
					<div class="page-title-subheading">Riwayat pencarian pemustaka pada OPAC.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
						<li class="breadcrumb-item">Administrasi</li>
						<li class="breadcrumb-item">Pengaturan Opac</li>
						<li class="breadcrumb-item">Histori Pencarian</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<div class="main-card mb-3 card">
		<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Histori Pencarian</div>
		<div class="card-body">
			<?php if (!$tableExists) : ?>
				<div class="alert alert-warning mb-0">Tabel <code>opaclogs</code> belum tersedia pada database, sehingga histori pencarian belum dapat ditampilkan.</div>
			<?php else : ?>
				<table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
					<thead>
						<tr>
							<th class="text-center" width="35">No</th>
							<th class="text-center">Waktu</th>
							<th class="text-center">No. Anggota</th>
							<th class="text-center">IP</th>
							<th class="text-center">Jenis Pencarian</th>
							<th class="text-center">Kata Kunci</th>
							<th class="text-center">Jenis Bahan</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
	</div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?php if ($tableExists) : ?>
	<script>
		var t;
		$(document).ready(function() {
			t = $('#tbl_data').DataTable({
				"processing": true,
				"serverSide": true,
				"ajax": {
					"url": '<?= site_url('api/master-opac-histori-pencarian/datatable') ?>',
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
				"columns": [
					{ data: 'no', className: 'text-center', orderable: false, searchable: false },
					{ data: 'waktu', className: 'text-center' },
					{ data: 'User_id' },
					{ data: 'ip' },
					{ data: 'jenis_pencarian' },
					{ data: 'keyword' },
					{ data: 'jenis_bahan' },
				],
				"order": [[1, "desc"]],
			});
		});
	</script>
<?php endif; ?>
<?= $this->endSection('script'); ?>
