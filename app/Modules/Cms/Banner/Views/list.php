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
					<i class="pe-7s-note icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Banner <?= ucwords(unslugify($slug)) ?>
					<div class="page-title-subheading">Daftar semua Banner
					</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Beranda</a></li>
						<li class="breadcrumb-item">Banner </li>
						<li class="active breadcrumb-item" aria-current="page"><?= ucwords(unslugify($slug)) ?> </li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<div class="main-card mb-3 card">
		<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate">
			</i>Tabel Banner <?= ucwords(unslugify($slug)) ?>
			<div class="btn-actions-pane-right actions-icon-btn">
				<?php if (is_allowed('banner/create')) : ?>
					<a href="<?= base_url('cms/banner/create?slug=' . $slug) ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i>
						Tambah Banner </a>
				<?php endif; ?>
			</div>
		</div>
		<div class="card-body">
			<?= get_message('message'); ?>
			<table style="width: 100%;" id="tbl_pages" class="table table-hover table-striped table-bordered">
				<thead>
					<tr>
						<th>No. </th>
						<th width="80">Cover</th>
						<th>Kategori</th>
						<th>Judul Banner</th>
						<th>Keterangan</th>
						<th>Urutan</th>
						<th>Status</th>
						<th width="190">Aksi</th>
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
<?= $this->include('Banner\Views\upload_modal'); ?>
<script>
	var t;
	$(document).ready(function() {
		t = $('#tbl_pages').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				"url": '<?php echo site_url('api/banner/datatable/' . $slug) ?>',
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
					data: 'file_cover',
					orderable: false
				},
				{
					data: 'category',
					className: 'text-center'
				},
				{
					data: 'title'
				},
				{
					data: 'description'
				},
				{
					data: 'sort',
					className: 'text-center'
				},
				{
					data: 'active'
				},
				{
					data: 'action',
					orderable: false
				},
			],
			"order": [],
			"drawCallback": function(data, type, full, meta) {
				var api = this.api();
				var data = api.rows().data();
				$.each(data, function(i, row) {
					$("#lazy" + row.id).Lazy();
				});

				$('.image-link').magnificPopup({
					type: 'image'
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

	// $('#category').change(function(e) {
	// 	e.preventDefault();
	//     t.ajax.reload();
	// });

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

	$("body").on("click", ".upload-data", function() {
		Dropzone.autoDiscover = false;

		var defaultDropzoneUrl = "<?= base_url('direktori/do_upload') ?>";
		var defaultUrl = "<?= base_url('api/direktori/upload_file') ?>";
		var defaultFormat = "application/pdf";
		var defaultFile = 1;
		var defaultRedirect = "<?= base_url('direktori') ?>";
		var defaultFormatTitle = "Format (PDF). Max 10MB";

		var id = $(this).attr('data-id');
		var parent_id = $(this).attr('data-parent');
		var field = $(this).attr('data-field');
		var title = $(this).attr('data-title');

		$('#frm_upload').attr("data-id", id);
		$('#frm_upload').attr("data-field", field);
		$('#frm_upload').attr("data-title", title);

		var data_dropzone_url = $(this).attr('data-dropzone-url');
		if (data_dropzone_url) {
			$('#upload_data_dropzone_url').val(data_dropzone_url);
		} else {
			$('#upload_data_dropzone_url').val(defaultDropzoneUrl);
		}
		console.log('upload_data_dropzone_url: ' + $('#upload_data_dropzone_url').val());

		var data_url = $(this).attr('data-url');
		if (data_url) {
			$('#upload_data_url').val(data_url);
		} else {
			$('#upload_data_url').val(defaultUrl);
		}
		console.log('upload_data_url: ' + $('#upload_data_url').val());

		var data_format = $(this).attr('data-format');
		if (data_format) {
			defaultFormat = data_format;
			$('#upload_data_format').val(data_format);
		} else {
			$('#upload_data_format').val(defaultFormat);
		}
		console.log('upload_data_format: ' + $('#upload_data_format').val());

		var data_file = $(this).attr('data-file');
		if (data_file) {
			defaultFile = data_file;
			$('#upload_data_file').val(data_file);
		} else {
			$('#upload_data_file').val(defaultFile);
		}
		console.log('upload_data_file: ' + $('#upload_data_file').val());

		var data_redirect = $(this).attr('data-redirect');
		if (data_redirect) {
			$('#upload_data_redirect').val(data_redirect);
		} else {
			$('#upload_data_redirect').val(defaultRedirect);
		}
		console.log('upload_data_redirect: ' + $('#upload_data_redirect').val());

		var data_format_title = $(this).attr('data-format-title');
		if (data_format_title) {
			$('#upload_data_format_title').html(data_format_title);
		} else {
			$('#upload_data_format_title').html(defaultFormatTitle);
		}

		$('#modal_upload_img').modal('show');
		$('#upload_id').val(id);
		$('#upload_parent_id').val(parent_id);
		$('#upload_field').val(field);
		$('#upload_title').val(title);
		$('#upload_title_span').html(title);

		setDropzone('file_pendukung', 'direktori', $('#upload_data_format').val(), $('#upload_data_file').val(), 10);
	});

	$('#modal_upload_img').on('hidden.bs.modal', function() {
		$('#file_pendukung_listed').empty();
	});
</script>
<?= $this->endSection('script'); ?>