<?php
$request = service('request');
$slug = $request->getGet('slug');

?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style') ?>
<style>
	.select2 {
		text-transform: none;
		font-weight: normal;
	}
</style>
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-server icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Pengaturan Detail Katalog
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
						<li class="breadcrumb-item">Administrasi</li>
						<li class="breadcrumb-item">Pengaturan Katalog</li>
						<li class="breadcrumb-item active">Pengaturan Detail Katalog</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-header">
			<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Pengaturan Detail Katalog
		</div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? '' ?></div> <?= get_message('message') ?>
			<form id="frm_create" method="post" action="<?= base_url('master-pengaturan-detail-katalog/index') ?>" onsubmit="return validateForm()">
				<div class="main-card mt-3 mb-3 card car-border">
					<div class="card-header">
						Tag
						<div class="btn-actions-pane-right actions-icon-btn">
							<div class="select-wrapper input-group">
								<select class="form-control select2" id="field_id" name="field_id" placeholder="Tag">
									<?php foreach ($fields as $row) : ?>
										<option data-id="<?= $row->ID ?>" data-code="<?= $row->Tag ?>" data-name="<?= $row->Name ?>" value="<?= $row->ID ?>"><?= $row->Tag ?> - <?= $row->Name ?></option>
									<?php endforeach; ?>
								</select>
								<div class="input-group-append">
									<button type="button" name="add" data-tbody="scd-tbody" class="btn btn-success item-btn-add"><i class="fa fa-plus"></i> Tambah</button>
								</div>
							</div>
						</div>
					</div>
					<div class="card-body">
						<table style="width: 100%;" id="scd-tbl" class="table table-hover table-striped table-bordered">
							<thead>
								<tr>
									<th width="80">Kode</th>
									<th>Nama</th>
									<th width="50" class="text-center">Aksi</th>
								</tr>
							</thead>
							<tbody id="scd-tbody">
								<?php foreach ($scds as $row) : ?>
									<?php $index = $row->Field_id; ?>
									<tr class="rm-row">
										<td>
											<input type="hidden" name="index1[]" value="<?= $index ?>">
											<input type="text" class="form-control" name="code1[<?= $index ?>]" placeholder="" value="<?= $row->Tag ?>" readonly />
										</td>
										<td>
											<input type="text" class="form-control" name="name1[<?= $index ?>]" placeholder="" value="<?= $row->Name ?>" readonly />
										</td>
										<td class="text-center">
											<button data-id="<?= $row->ID ?>" data-code="<?= $row->Tag ?>" data-name="<?= $row->Name ?>" type="button" class="btn btn-danger scd-btn-remove" data-href="<?= base_url('api/master-pengaturan-detail-katalog/scd_delete/' . $row->ID) ?>"><i class="fa fa-times"></i></button>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<script>
	$('#field_id').select2({
		width: "resolve"
	});
	$(document).ready(function() {
		$(document).on('click', '.scd-btn-remove', function() {
			var url = $(this).data('href');
			var row = $(this).closest('tr');

			var id = $(this).data('id');
			var code = $(this).data('code');
			var name = $(this).data('name');
			var option = `<option data-id="${id}" data-code="${code}" data-name="${name}" value="${id}">${code} - ${name}</option>`;
			$('#field_id').append(option);

			sortOptions('#field_id');

			deleteConfirm(function(result) {
				if (result.value) {
					if (!url) {
						row.remove();
						return false;
					} else {
						makeAjaxCall(url, "DELETE").then(function(respJson) {
							deleteInfo();
							row.remove();
						}, function(reason) {
							deleteInfo(false);
							console.log("Error in processing your request", reason);
						});
					}
				}
			});
		});

		$(".item-btn-add").click(function() {
			var tbody = $(this).data('tbody');
			var selected = $('#field_id').find(":selected");
			var index = selected.val();
			var code = selected.data('code');
			var name = selected.data('name');
			var baseUrl = '<?= base_url() ?>';
			var url_post = `${baseUrl}/api/master-pengaturan-detail-katalog/scd_create/${index}`;

			makeAjaxCall(url_post, "POST").then(function(respJson) {
				successInfo();
				var url_delete = `${baseUrl}/api/master-pengaturan-detail-katalog/scd_delete/${respJson.insertID}`;
				$('#' + tbody).append(`
					<tr class="rm-row">
						<td>
							<input type="hidden" name="index0[]" value="` + index + `">
							<input type="text" class="form-control" name="code0[` + index + `]" placeholder="" value="` + code + `" readonly />
						</td>
						<td>                        
							<input type="text" class="form-control" name="name0[` + index + `]" placeholder="" value="` + name + `" readonly />
						</td>
						<td class="text-center">
							<button data-id="` + index + `" data-code="` + code + `" data-name="` + name + `" type="button" class="btn btn-danger scd-btn-remove" data-href="` + url_delete + `"><i class="fa fa-times"></i></button>
						</td>
					</tr>
				`);
				selected.remove();

			}, function(reason) {
				successInfo(false);
				console.log("Error in processing your request", reason);
			});
		});

		function sortOptions(domID) {
			var select = $(domID);
			var options = select.find('option');
			options.sort(function(a, b) {
				var textA = $(a).text().toUpperCase();
				var textB = $(b).text().toUpperCase();

				if (textA < textB) {
					return -1;
				} else if (textA > textB) {
					return 1;
				} else {
					return 0;
				}
				``
			});

			select.html(options);
		}
	});
</script>
<?= $this->endSection('script') ?>