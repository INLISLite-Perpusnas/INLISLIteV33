<?php
helper('region');
helper('user');
$request = service('request');
$slug = $request->getGet('slug');
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
	.modal {
		overflow: visible;
	}

	.select2-container {
		z-index: 2050 !important;
		/* Ensure it has a high z-index above the modal */
	}

	.select2-container .select2-dropdown {
		z-index: 2051 !important;
		/* Ensure the dropdown itself is above the modal */
	}
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-user icon-gradient bg-strong-bliss"></i>
				</div>
				<div>User
					<div class="page-title-subheading">Daftar Semua User</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="breadcrumb-item">Otorisasi</li>
						<li class="breadcrumb-item" aria-current="page">User</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
		<li class="nav-item">
			<a class="btn-header-argon <?= ($slug === 'semua') ? 'active' : '' ?>" href="<?= base_url('user/index?slug=semua') ?>">
				<span>SEMUA</span>
			</a>
		</li>
		<?php foreach (get_groups() as $group) : ?>
			<li class="nav-item">
				<a class="btn-header-argon <?= ($slug == $group->name) ? 'active' : '' ?>" href="<?= base_url('user/index?slug=' . $group->name) ?>">
					<span><?= strtoupper($group->name) ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>



	<div class="main-card mb-3 card">
		<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel User
			<div class="btn-actions-pane-right actions-icon-btn">
				<?php if (!empty($slug)) : ?>
					<?php if (is_allowed('user/create')) : ?>
					  <a data-bs-toggle="modal" data-bs-target="#modal_create" data-toggle="modal" data-target="#modal_create" href="javascript:void(0);" class=" btn btn-success" title=""><i class="fa fa-plus"></i> Tambah User</a>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
		<div class="card-body">
			<table style="width: 100%;" id="tbl_users" class="table table-hover table-striped table-bordered">
				<thead>
					<tr>
						<th class="text-center" width="35">No</th>
						<th class="text-center">Nama Lengkap</th>
						<th class="text-center">NPP (Mitra Perpustakaan)</th>
						<th class="text-center">Group</th>
						<th class="text-center" width="80">Status</th>
						<th class="text-center" width="100">Update At</th>
						<th class="text-center" width="180">Aksi</th>
					</tr>
				</thead>
				<tbody id="tbl_users_tbody"></tbody>
			</table>
		</div>
	</div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= $this->include('User\Views\add_modal'); ?>
<script>
	let is_profiling = `<?= is_profiling() ?>`;
	let branch_id = `<?= branch_id() ?>`;

	if (is_profiling) {
		getData(`<?= base_url('api/region/province') ?>`, `#Province`);
		$('#Province').change(function(e) {
			var code = $(this).val();
			getData(`<?= base_url('api/region/city') ?>/${code}.`, `#City`);

			code = code.replace(".", "");
			code = code.replace(".", "");

			getData(`<?= base_url('api-mitra-perpustakaan/branch') ?>/${code}`, `#Branch`);

		});
		$('#City').change(function(e) {
			var code = $(this).val();
			getData(`<?= base_url('api/region/district') ?>/${code}`, `#District`);

			code = code.replace(".", "");
			code = code.replace(".", "");

			getData(`<?= base_url('api-mitra-perpustakaan/branch') ?>/${code}/NPP_KabKota_id`, `#Branch`);
		});
		$('#District').change(function(e) {
			var code = $(this).val();
			getData(`<?= base_url('api/region/sub_district') ?>/${code}`, `#SubDistrict`);

			code = code.replace(".", "");
			code = code.replace(".", "");

			getData(`<?= base_url('api-mitra-perpustakaan/branch') ?>/${code}/NPP_Kecamatan_id`, `#Branch`);
		});
		$('#SubDistrict').change(function(e) {
			var name = $("#SubDistrict option:selected").text();
			var code = $(this).val();

			code = code.replace(".", "");
			code = code.replace(".", "");
			code = code.replace(".", "");

			getData(`<?= base_url('api-mitra-perpustakaan/branch') ?>/${code}/NPP_Kelurahan_id`, `#Branch`);
		});

		$('#branch_id').select2({
			maximumInputLength: 3
		});
	}
</script>
<script>
	var url = "<?php echo site_url('api/user/datatable') ?>";
	var slug = "<?= $slug ?>";
	var t;
	$(document).ready(function() {
		t = $('#tbl_users').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				"url": `${url}?slug=${slug}`,
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
					orderable: false
				},
				{
					data: 'username',
				},
				{
					data: 'branch_id'
				},
				{
					data: 'group_id',
					className: 'text-center',
				},
				{
					data: 'active',
					className: 'text-center',
				},
				{
					data: 'updated_at',
					className: 'text-center',
				},
				{
					data: 'action',
					className: 'text-center',
					orderable: false
				},
			],
			"order": [
				['5', 'desc']
			],
			"drawCallback": function(data, type, full, meta) {
				var api = this.api();
				var data = api.rows().data();
				$('[data-toggle="tooltip"]').tooltip();
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

	$('#search_name').on('keyup', function(e) {
		t.columns('name:name').search(this.value).draw();
	});

	$('#search_value').on('keyup', function() {
		t.columns('value:name').search(this.value).draw();
	});

	$('#search_description').on('keyup', function() {
		t.columns('description:name').search(this.value).draw();
	});

	$('#tbl_users').on('click', '.remove-data', function() {
		var url = $(this).attr('data-href');
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

	$(".apply-param-status").on('change', function() {
		var switchStatus = $(this).is(':checked');
		var paramName = $(this).attr('data-param');
		var paramValue = $(this).attr('data-class');

		if (switchStatus) {
			setParameter(paramName, 1);
		} else {
			setParameter(paramName, 0);
		}
	});

	$(document).ready(function() {
		getData(`<?= base_url('api/region/province') ?>`, `#Province`);
		$('#Province').change(function(e) {
			var code = $(this).val();

			getData(`<?= base_url('api/region/city') ?>/${code}.`, `#City`);
		});
		$('#City').change(function(e) {
			var code = $(this).val();
			getData(`<?= base_url('api/region/district') ?>/${code}`, `#District`);
		});
		$('#District').change(function(e) {
			var code = $(this).val();
			getData(`<?= base_url('api/region/sub_district') ?>/${code}`, `#SubDistrict`);
		});
	});
</script>
<?= $this->endSection('script'); ?>