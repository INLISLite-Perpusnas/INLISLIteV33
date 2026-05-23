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
				<div>Tambah Jenis Bahan Pustaka
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
						<li class="breadcrumb-item">Administrasi</li>
						<li class="breadcrumb-item">Pengaturan Katalog</li>
						<li class="breadcrumb-item">Jenis Bahan Pustaka</li>
						<li class="breadcrumb-item" aria-current="page">Tambah</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-header">
			<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Form Tambah - Jenis Bahan Pustaka
		</div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? '' ?></div> <?= get_message('message') ?>
			<form id="frm_create" method="post" action="<?= base_url('master-jenis-bahan-pustaka/create') ?>" onsubmit="return validateForm()">
				<div class="form-row">
					<div class="col-md-2">
						<div class="position-relative form-group">
							<label for="format">Format*</label>
							<div>
								<select class="form-control" id="form_add_format" name="format" placeholder="Format" readonly>
									<?php foreach (get_table('formats', 'ID, Name', 'Name IS NOT NULL', 'data') as $row) : ?>
										<option value="<?= $row->ID ?>" <?= ($row->Name == 'Bibliografi') ? 'selected' : '' ?>><?= $row->Name ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-2">
						<div class="position-relative form-group">
							<label for="code">Kode*</label>
							<div>
								<input type="text" class="form-control" id="code" name="code" placeholder="Kode Bahan Pustaka" value="<?= set_value('code') ?>" required />
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="position-relative form-group">
							<label for="name">Nama*</label>
							<div>
								<input type="text" class="form-control" id="name" name="name" placeholder="Nama Bahan Pustaka" value="<?= set_value('name') ?>" required />
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="position-relative form-group">
							<label for="description">Keterangan</label>
							<div>
								<input type="text" class="form-control" id="description" name="description" placeholder="Keterangan " value="<?= set_value('description') ?>" />
							</div>
						</div>
					</div>
					<div class="col-md-2">
						<div class="position-relative form-group">
							<label for="sort">Nomor Urut</label>
							<div>
								<input type="number" class="form-control" id="sort" name="sort" placeholder="Nomor Urut " value="<?= set_value('sort', 0) ?>" />
							</div>
						</div>
					</div>
				</div>

				<div class="row p-0">
					<div class="col-md-2">
						<div class="form-group" style="display: inline-block">
							<div>
								<label for="serial">Serial </label><br>
								<input type="checkbox" class="apply-status" name="serial" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal">
							</div>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group" style="display: inline-block">
							<div>
								<label for="kartografi">Kartografi </label><br>
								<input type="checkbox" class="apply-status" name="kartografi" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal">
							</div>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group" style="display: inline-block">
							<div>
								<label for="musik">Musik </label><br>
								<input type="checkbox" class="apply-status" name="musik" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal">
							</div>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group" style="display: inline-block">
							<div>
								<label for="berisi_artikel">Berisi Artikel </label><br>
								<input type="checkbox" class="apply-status" name="berisi_artikel" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal">
							</div>
						</div>
					</div>
				</div>

				<div class="main-card mt-3 mb-3 card car-border">
					<div class="card-header">
						Tag
						<div class="btn-actions-pane-right actions-icon-btn">
							<div class="select-wrapper input-group">
								<select class="form-control select2" id="field_id" name="field_id" placeholder="Tag">
									<?php foreach (get_table('fields', 'ID, Tag, Name', 'Name IS NOT NULL', 'data') as $row) : ?>
										<option data-id="<?= $row->ID ?>" data-code="<?= $row->Tag ?>" data-name="<?= $row->Name ?>" value="<?= $row->ID ?>"><?= $row->Tag ?> - <?= $row->Name ?></option>
									<?php endforeach; ?>
								</select>
								<div class="input-group-append">
									<button type="button" name="add" data-tbody="tag-tbody" class="btn btn-success item-btn-add"><i class="fa fa-plus"></i> Tambah</button>
								</div>
							</div>
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
							<tbody id="tag-tbody">
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
	$('#field_id').select2({
		width: "resolve"
	});
	$(document).ready(function() {
		$(document).on('click', '.wsf-btn-remove', function() {
			var row = $(this).closest('tr');
			row.remove();

			var id = $(this).data('id');
			var code = $(this).data('code');
			var name = $(this).data('name');
			var option = `<option data-id="${id}" data-code="${code}" data-name="${name}" value="${id}">${code} - ${name}</option>`;
			$('#field_id').append(option);
		});

		$(".item-btn-add").click(function() {
			var tbody = $(this).data('tbody');
			var selected = $('#field_id').find(":selected");
			var index = selected.val();
			var code = selected.data('code');
			var name = selected.data('name');

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
						<button data-id="` + index + `" data-code="` + code + `" data-name="` + name + `" type="button" class="btn btn-danger wsf-btn-remove" data-href=""><i class="fa fa-times"></i></button>
					</td>
				</tr>
			`);
			selected.remove();
		});
	});
</script>
<?= $this->endSection('script') ?>