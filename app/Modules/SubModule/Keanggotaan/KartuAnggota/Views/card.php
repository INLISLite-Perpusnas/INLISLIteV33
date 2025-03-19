<?php
$request = service('request');
$slug = $request->getGet('slug') ?? 'kartu';
$branch_id = user()->branch_id ?? '';

$db = db_connect('backend');
$builder = $db
	->table('t_template')
	->select('t_template.*')
	->where('t_template.category', 'kartu')
	->orderBy('t_template.sort', 'asc');

$templates = $builder->get()->getResult();
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style') ?>
<style>
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
				<div>Kartu Anggota <?= ucwords(unslugify($slug)) ?>
					<div class="page-title-subheading">Daftar semua Kartu Anggota
					</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url(
																	'dashboard'
																) ?>"><i class="fa fa-home"></i> Beranda</a></li>
						<li class="breadcrumb-item">Kartu Anggota </li>
						<li class="active breadcrumb-item" aria-current="page"><?= ucwords(
																					unslugify($slug)
																				) ?> </li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<div class="main-card mb-3 card">
		<div class="card-header"><i class="header-icon lnr-license icon-gradient bg-plum-plate">
			</i>Kartu Anggota - Depan
			<div class="btn-actions-pane-right actions-icon-btn">

			</div>
		</div>
		<div class="card-body">
			<?= get_message('message') ?>
			<div class="row">
				<?php foreach ($templates as $row) : ?>
					<?php
					$default = base_url('uploads/master-template/' . $row->file_image);
					$image = (!empty($card->file_image)) ? base_url('uploads/master-template/' . $card->file_image) : $default;
					$card = get_kartuanggota($branch_id, $row->id);

					$card_checked = '';
					$card_id = 0;
					$card_active = 0;
					if (!empty($card)) {
						$card_checked = ($card->active == '1') ? 'checked' : '';
						$card_id = $card->id;
						$card_active = $card->active;
					}
					?>
					<div class="col-md-6">
						<div class="card mb-3">
							<img alt="img" src="<?= $image ?>" onerror="this.onerror=null;this.src='<?= $default ?>';" class="card-img-top">
							<div class="card-body">
								<h5 class="card-title"><?= $row->title ?></h5>
								<div class="clearfix mb-3">
									<div class="float-left">
										<b>Status Aktif </b> &nbsp; <input type="checkbox" class="apply-status" data-href="<?= base_url('api/kartuanggota/switch/' . $card_id . '?template_id=' . $row->id . '&branch_id=' . $branch_id) ?>" data-checked="<?= $card_checked ?>" data-field="active" <?= $card_checked ?> data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tidak" data-size="mini">
									</div>
									<div class="float-right">
										<a href="javascript:void(0);" data-parent="" data-id="<?= $row->id ?>" data-field="file_image" data-url="<?= base_url('api/kartuanggota/upload_file') ?>" data-dz_url="<?= base_url('kartuanggota/do_upload') ?>" data-controller="kartuanggota" data-title="Upload File" data-sub_title="Format (.jpg, .jpeg, .png). Max 10MB" data-redirect="<?= base_url('master/kartuanggota/card') ?>" data-format=".jpg,.jpeg,.png" data-count="1" data-max="10" data-toggle="tooltip" data-placement="top" title="Upload File" class="btn btn-warning upload-data">
											<i class="fa fa-upload"> </i> Ubah Latar Belakang
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="row">
				<div class="col-md-12">

				</div>
			</div>
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
				"url": '<?php echo site_url('api/kartuanggota/datatable/' . $slug); ?>',
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
</script>
<?= $this->endSection('script') ?>