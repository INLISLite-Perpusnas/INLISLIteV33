<?php
$request = service('request');
$slug = $request->getGet('slug') ?? 'depan';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style') ?>
<style>
	.table td,
	.table th {
		vertical-align: middle;
	}
</style>
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-note2 icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Template <?= ucwords(unslugify($slug)) ?>
					<div class="page-title-subheading">Daftar semua Template
					</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url(
																	'dashboard'
																) ?>"><i class="fa fa-home"></i> Beranda</a></li>
						<li class="breadcrumb-item">Template </li>
						<li class="active breadcrumb-item" aria-current="page"><?= ucwords(
																					unslugify($slug)
																				) ?> </li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
		<?php foreach (get_ref('ref-template', 'slug') as $row) : ?>
			<li class="nav-item">
				<a class="nav-link <?= ($row->slug == $slug) ? 'active' : '' ?>" href="<?= base_url('master-template-kartu?slug=' . $row->slug) ?>">
					<span><?= strtoupper($row->name) ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<div class="main-card mb-3 card">
		<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate">
			</i>Tabel Template <?= ucwords(unslugify($slug)) ?>
			<div class="btn-actions-pane-right actions-icon-btn">
				<?php if (is_allowed('template/create')) : ?>
					<a href="<?= base_url(
									'master-template-kartu/create?slug=' . $slug
								) ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i>
						Tambah Template </a>
				<?php endif; ?>
			</div>
		</div>
		<div class="card-body">
			<?= get_message('message') ?>
			<table style="width: 100%;" id="tbl_pages" class="table table-hover table-striped table-bordered">
				<thead>
					<tr>
						<th>No. </th>
						<th>Kategori</th>
						<th>Nama Template</th>
						<th>Keterangan</th>
						<th>Ukuran</th>
						<th>Background</th>
						<th>Aktif</th>
						<th width="150">Aksi</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<?= $this->include('Widget\Views\modal_upload'); ?>
<script>
	var t;
	$(document).ready(function() {
		t = $('#tbl_pages').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				"url": '<?php echo site_url('api/master-template-kartu/datatable/' . $slug); ?>',
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
					orderable: false
				},
				{
					data: 'category'
				},
				{
					data: 'title'
				},
				{
					data: 'description'
				},
				{
					data: 'file_size',
					className: 'text-center'
				},
				{
					data: 'file_image',
					className: 'text-center'
				},
				{
					data: 'active'
				},
				{
					data: 'action',
					className: 'text-center',
					orderable: false
				},
			],
			"order": [],
			"drawCallback": function(data, type, full, meta) {
				var api = this.api();
				var data = api.rows().data();
				$('[data-toggle="tooltip"]').tooltip();
				$.each(data, function(i, row) {
					$("#lazy" + row.id).Lazy();
				});

				$('.image-link').magnificPopup({
					type: 'image'
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
							t.draw();
						}

						if (this.value.length > 3) {
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
<?= $this->endSection('script') ?>