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
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="active breadcrumb-item" aria-current="page">Eksemplar</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>


	<div class="main-card mb-3 card">
		<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Daftar Eksemplar
			<div class="btn-actions-pane-right actions-icon-btn">
				<?php if (is_allowed('eksemplar/create')) : ?>
					<a href="<?= base_url('eksemplar/create?slug=' . $slug) ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i>
						Tambah Eksemplar
					</a>
				<?php endif; ?>
				<!-- <a href="<?= base_url('report/eksemplar') ?>" class="btn btn-secondary" title=""><i class="fa fa-file-excel"></i>
					Ekspor Laporan
				</a> -->
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
				<!-- Bagian tabel yang diupdate untuk menambahkan kolom Status -->
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
            <th class="text-center" width="80">Status</th>
            <th class="text-center" width="">OPAC</th>
            <th class="text-center" width="">DRM</th>
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
        "columns": [
            {
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
                data: 'StatusName',
                className: 'text-center',
                orderable: true
            },
            {
                data: 'IsOPAC',
                className: 'text-center',
                orderable: false
            },
            {
                data: 'ISDRM',
                className: 'text-center',
                orderable: false
            },
            {
                data: "IsQUARANTINE",
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
            [0, "desc"]
        ],
        // ... sisa kode drawCallback dan initComplete tetap sama
    });
});
</script>
<?= $this->endSection('script'); ?>