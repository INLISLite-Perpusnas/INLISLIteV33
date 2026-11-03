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
					<i class="pe-7s-bookmarks icon-gradient bg-strong-bliss"></i>
				</div>
				<div><?= esc($title) ?>
					<div class="page-title-subheading">Pesanan koleksi oleh anggota melalui OPAC</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="breadcrumb-item">Sirkulasi</li>
						<li class="breadcrumb-item" aria-current="page"><?= esc($title) ?></li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<div class="main-card mb-3 card">
		<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i><?= esc($title) ?>
			<div class="btn-actions-pane-right actions-icon-btn form-inline">
				<label for="filter_status" class="mr-2 mb-0">Status</label>
				<select id="filter_status" class="form-control form-control-sm">
					<option value="">Semua</option>
					<?php foreach (\Pemesanan\Controllers\Api\Pemesanan::STATUSES as $status): ?>
						<option value="<?= $status ?>"><?= $status ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table style="width: 100%;" id="tbl_data" class="table table-hover table-bordered">
					<thead class="bg-night-sky text-light">
						<tr>
							<th class="text-center" width="35">No</th>
							<th class="text-center" width="130">Tgl. Pesan</th>
							<th class="text-center" width="110">No. Anggota</th>
							<th class="text-center">Nama Anggota</th>
							<th class="text-center" width="110">No. Barcode</th>
							<th class="text-center">Judul</th>
							<th class="text-center">Penerbit</th>
							<th class="text-center">Lokasi Ruang</th>
							<th class="text-center" width="130">Batas Ambil</th>
							<th class="text-center" width="90">Status</th>
							<th class="text-center" width="100">Aksi</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
		<div class="card-footer">
			<a href="<?= site_url('sirkulasi-pemesanan') ?>" class="btn btn-info"><i class="fa fa-redo"></i> Reset List</a>
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
				"url": '<?= site_url('api/sirkulasi-pemesanan/datatable') ?>',
				"data": function(d) {
					d.status = $('#filter_status').val();
				}
			},
			"dom": "<'row mb-2'<'col-md-6 col-sm-12 text-left'l><'col-md-6 col-sm-12 text-right'f>>" +
				"<'row'<'col-md-12'tr>>" +
				"<'row mt-2 align-items-center'<'col-md-5 col-sm-12 text-left'i><'col-md-7 col-sm-12 d-flex justify-content-end'p>>",
			"pagingType": "full_numbers",
			"oLanguage": {
				"sSearch": "<i class='fa fa-search'></i> _INPUT_",
				"sLengthMenu": "Tampilkan : _MENU_",
				"sInfo": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
				"sInfoEmpty": "Tidak ada data",
				"sInfoFiltered": "(disaring dari _MAX_ data)",
				"sEmptyTable": "Belum ada koleksi yang dipesan",
				"sZeroRecords": "Data tidak ditemukan",
				"oPaginate": {
					"sNext": "<i class='fa fa-chevron-right'></i>",
					"sPrevious": "<i class='fa fa-chevron-left'></i>",
					"sLast": "Akhir",
					"sFirst": "Awal",
				}
			},
			"order": [],
			"columns": [
				{ data: 'no', className: 'text-center', orderable: false, searchable: false },
				{ data: 'bookingDate', className: 'text-nowrap', searchable: false },
				{ data: 'memberId' },
				{ data: 'Fullname' },
				{ data: 'NomorBarcode', className: 'text-nowrap' },
				{ data: 'Title' },
				{ data: 'Publisher' },
				{ data: 'LocationName' },
				{ data: 'bookingExpired', className: 'text-nowrap', searchable: false },
				{ data: 'status', className: 'text-center', orderable: false, searchable: false },
				{ data: 'action', className: 'text-center', orderable: false, searchable: false },
			],
		});

		$('#filter_status').on('change', function() {
			t.ajax.reload();
		});

		// Muat ulang tiap 60 detik agar pesanan baru dari OPAC langsung terlihat (seperti INLISLite 3)
		setInterval(function() {
			t.ajax.reload(null, false);
		}, 60000);
	});

	$('body').on('click', '.btn-batal-pesan', function(e) {
		e.preventDefault();
		var form = $(this).closest('form');
		Swal.fire({
			title: 'Batalkan pesanan?',
			text: 'Eksemplar ' + $(this).data('barcode') + ' akan kembali dapat dipesan atau dipinjam anggota lain.',
			type: 'warning',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Ya, batalkan',
			cancelButtonText: 'Tidak'
		}).then(function(result) {
			if (result.value || result.isConfirmed) {
				form.submit();
			}
		});
	});
</script>
<?= $this->endSection('script'); ?>
