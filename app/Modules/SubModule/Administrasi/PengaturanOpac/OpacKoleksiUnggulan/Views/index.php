<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-server icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Koleksi Unggulan
					<div class="page-title-subheading">Pengaturan dan daftar koleksi unggulan pada halaman OPAC.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
						<li class="breadcrumb-item">Administrasi</li>
						<li class="breadcrumb-item">Pengaturan Opac</li>
						<li class="breadcrumb-item">Koleksi Unggulan</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<?= get_message('message') ?>
	<div class="row">
		<div class="col-md-4">
			<div class="main-card mb-3 card">
				<div class="card-header">
					<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Pengaturan
				</div>
				<div class="card-body">
					<form method="post" action="<?= base_url('master-opac-koleksi-unggulan/index') ?>">
						<?= csrf_field() ?>
						<div class="position-relative form-group">
							<label>Tampilkan Koleksi Unggulan *</label>
							<div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="ShowKoleksiUnggulan" id="show_true" value="TRUE" <?= ($setting['ShowKoleksiUnggulan'] == 'TRUE') ? 'checked' : '' ?>>
									<label class="form-check-label" for="show_true">Ya</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="ShowKoleksiUnggulan" id="show_false" value="FALSE" <?= ($setting['ShowKoleksiUnggulan'] != 'TRUE') ? 'checked' : '' ?>>
									<label class="form-check-label" for="show_false">Tidak</label>
								</div>
							</div>
						</div>
						<div class="position-relative form-group">
							<label for="KoleksiUnggulanShow">Maksimal Jumlah Koleksi *</label>
							<input type="number" min="1" class="form-control" name="KoleksiUnggulanShow" id="KoleksiUnggulanShow" value="<?= esc($setting['KoleksiUnggulanShow']) ?>" required>
						</div>
						<div class="form-group">
							<?php if (is_allowed('master-opac-koleksi-unggulan/edit')) : ?>
								<span class="has-tip" data-tip="Simpan pengaturan tampil & jumlah maksimal Koleksi Unggulan di beranda."><button type="submit" class="btn btn-catalog-add" name="submit">Simpan</button></span>
							<?php endif; ?>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="main-card mb-3 card">
				<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Daftar Koleksi Unggulan
					<div class="btn-actions-pane-right actions-icon-btn">
						<?php if (is_allowed('master-opac-koleksi-unggulan/create')) : ?>
							<span class="has-tip" data-tip="Buka daftar katalog untuk memilih judul yang dijadikan Koleksi Unggulan.">
								<a data-bs-toggle="modal" data-bs-target="#modal_catalog" data-toggle="modal" data-target="#modal_catalog" href="javascript:void(0);" class="btn btn-catalog-add"><i class="fa fa-plus"></i> Pilih Judul</a>
							</span>
						<?php endif; ?>
					</div>
				</div>
				<div class="card-body">
					<table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
						<thead>
							<tr>
								<th class="text-center" width="35">No</th>
								<th class="text-center">Judul</th>
								<th class="text-center">Pengarang</th>
								<th class="text-center">Tahun Terbit</th>
								<th class="text-center">Jenis Bahan</th>
								<th class="text-center" width="60">Aksi</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<div class="modal fade" id="modal_catalog" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					<i class="header-icon lnr-book icon-gradient bg-plum-plate"> </i> Daftar Katalog
				</h5>
				<button type="button" class="close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table style="width: 100%;" id="tbl_catalog" class="table table-hover table-striped table-bordered">
					<thead>
						<tr>
							<th class="text-center" width="35">No</th>
							<th class="text-center">BIB-ID</th>
							<th class="text-center">Judul</th>
							<th class="text-center">Pengarang</th>
							<th class="text-center">Tahun Terbit</th>
							<th class="text-center">Jenis Bahan</th>
							<th class="text-center" width="70">Aksi</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>
<script>
	var dtOptions = {
		"processing": true,
		"serverSide": true,
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
		"drawCallback": function() {
		},
	};

	// Kirim aksi sebagai POST + token CSRF (create/delete hanya menerima POST)
	function postTo(url, id) {
		var form = $('<form method="post"></form>').attr('action', url);
		form.append($('<input type="hidden">').attr('name', '<?= csrf_token() ?>').val('<?= csrf_hash() ?>'));
		form.append($('<input type="hidden" name="id">').val(id));
		$('body').append(form);
		form.submit();
	}

	var t, tc;
	$(document).ready(function() {

		t = $('#tbl_data').DataTable($.extend({}, dtOptions, {
			"ajax": {
				"url": '<?= site_url('api/master-opac-koleksi-unggulan/datatable') ?>',
			},
			"columns": [
				{ data: 'no', className: 'text-center', orderable: false, searchable: false },
				{ data: 'Title' },
				{ data: 'Author' },
				{ data: 'PublishYear', className: 'text-center' },
				{ data: 'worksheet', searchable: false },
				{ data: 'action', className: 'text-center', orderable: false, searchable: false },
			],
			"order": [[1, "asc"]],
		}));

		$('#modal_catalog').on('shown.bs.modal', function() {
			if (tc) {
				tc.columns.adjust();
				return;
			}
			tc = $('#tbl_catalog').DataTable($.extend({}, dtOptions, {
				"ajax": {
					"url": '<?= site_url('api/master-opac-koleksi-unggulan/catalog') ?>',
				},
				"columns": [
					{ data: 'no', className: 'text-center', orderable: false, searchable: false },
					{ data: 'BIBID' },
					{ data: 'Title' },
					{ data: 'Author' },
					{ data: 'PublishYear', className: 'text-center' },
					{ data: 'worksheet', searchable: false },
					{ data: 'action', className: 'text-center', orderable: false, searchable: false },
				],
				"order": [[1, "desc"]],
			}));
		});
	});

	$("body").on("click", ".pick-data", function() {
		postTo($(this).attr('data-href'), $(this).attr('data-id'));
		return false;
	});

	$("body").on("click", ".remove-data", function() {
		var url = $(this).attr('data-href');
		var id = $(this).attr('data-id');
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
				postTo(url, id);
			}
		});
		return false;
	});
</script>
<?= $this->endSection('script') ?>
