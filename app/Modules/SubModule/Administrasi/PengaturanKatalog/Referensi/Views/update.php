<?php
$request = service('request');
$slug = $request->getGet('slug');

?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style') ?>
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-server icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Edit Referensi - <?= $ref->Name ?>
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
						<li class="breadcrumb-item">Administrasi</li>
						<li class="breadcrumb-item">Pengaturan Katalog</li>
						<li class="breadcrumb-item">Referensi</li>
						<li class="active breadcrumb-item" aria-current="page">Edit</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="main-card mb-3 card col-md-8">
		<div class="card-header">
			<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Form Edit - Referensi
		</div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? '' ?></div> <?= get_message('message') ?>
			<form id="frm_create" method="post" action="<?= base_url('master-referensi/edit/' . $ref->ID) ?>" onsubmit="return validateForm()">
				<div class="form-row">
					<div class="col-md-6">
						<div class="position-relative form-group">
							<label for="format">Format*</label>
							<div>
								<select class="form-control" id="form_add_format" name="format" placeholder="Format" required>
									<?php foreach (get_table('formats', 'ID, Name', 'Name IS NOT NULL', 'data') as $row) : ?>
										<option value="<?= $row->ID ?>" <?= ($row->ID == $ref->Format_id) ? 'selected' : '' ?>><?= $row->Name ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="position-relative form-group">
							<label for="name">Nama Referensi*</label>
							<div>
								<input type="text" class="form-control" id="form_add_name" name="name" placeholder="Nama Referensi " value="<?= set_value('name', $ref->Name) ?>" required />
							</div>
						</div>
					</div>
				</div>

				<div class="main-card mt-3 mb-3 card car-border">
					<div class="card-header">
						Item Referensi
						<div class="btn-actions-pane-right actions-icon-btn">
							<button type="button" name="add" data-tbody="subruas-tbody" class="btn btn-success item-btn-add"><i class="fa fa-plus"></i> Item</button>
						</div>
					</div>
					<div class="card-body">
						<table style="width: 100%;" id="subruas-tbl" class="table table-hover table-striped table-bordered">
							<thead>
								<tr>
									<th width="80">Kode</th>
									<th>Nama</th>
									<th width="50" class="text-center">Aksi</th>
								</tr>
							</thead>
							<tbody id="subruas-tbody">
								<?php foreach ($refItems as $row) : ?>
									<?php $index = $row->ID; ?>
									<tr class="rm-row">
										<td>
											<input type="hidden" name="index0[]" value="<?= $index ?>">
											<input type="text" class="form-control" name="code0[<?= $index ?>]" placeholder="" value="<?= $row->Code ?>" required maxlength="1" />
										</td>
										<td>
											<input type="text" class="form-control" name="name0[<?= $index ?>]" placeholder="" value="<?= $row->Name ?>" required />
										</td>
										<td class="text-center">
											<button type="button" class="btn btn-danger item-btn-remove" data-href="<?= base_url('api/master-referensi/item_data_delete/' . $row->ID) ?>"><i class="fa fa-times"></i></button>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>

				<div class="form-group">
					<button type="submit" class="btn btn-primary" name="submit">Simpan</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<script>
	$(document).ready(function() {
		$(document).on('click', '.item-btn-remove', function() {
			var url = $(this).data('href');
			var row = $(this).closest('tr');

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
			var index = Date.now();
			var tbody = $(this).data('tbody');

			$('#' + tbody).append(`
				<tr class="rm-row">
					<td>
						<input type="hidden" name="index0[]" value="` + index + `">
						<input type="text" class="form-control" name="code0[` + index + `]" placeholder="" required />
					</td>
					<td>                        
						<input type="text" class="form-control" name="name0[` + index + `]" placeholder="" required/>
					</td>
					<td class="text-center">
						<button type="button" class="btn btn-danger item-btn-remove" data-href=""><i class="fa fa-times"></i></button>
					</td>
				</tr>
			`);
		});
	});
</script>
<?= $this->endSection('script') ?>