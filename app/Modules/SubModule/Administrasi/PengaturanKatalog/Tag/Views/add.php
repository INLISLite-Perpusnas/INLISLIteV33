<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
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
				<div>Tambah Tag
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
						<li class="breadcrumb-item">Administrasi</li>
						<li class="breadcrumb-item">Pengaturan Katalog</li>
						<li class="breadcrumb-item">Tag</li>
						<li class="active breadcrumb-item" aria-current="page">Tambah</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-header">
			<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Tambah - Format Penamaan dan Aturan Tag
		</div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? '' ?></div> <?= get_message('message') ?>
			<form id="form_add" method="post" action="<?= base_url('master-tag/create') ?>" onsubmit="return validateForm()">
				<div class="form-row">
					<div class="col-md-3">
						<div class="position-relative form-group">
							<label for="format">Format*</label>
							<div>
								<select class="form-control" id="form_add_format" name="format" placeholder="Format " required>
									<?php foreach (get_table('formats', 'ID, Name', 'Name IS NOT NULL', 'data') as $row) : ?>
										<option value="<?= $row->ID ?>"><?= $row->Name ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="position-relative form-group">
							<label for="group">Group*</label>
							<div>
								<select class="form-control" id="form_add_group" name="group" placeholder="group " required>
									<?php foreach (get_table('fieldgroups', 'ID, Name', 'Name IS NOT NULL', 'data') as $row) : ?>
										<option value="<?= $row->ID ?>"><?= $row->Name ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="position-relative form-group">
							<label for="code">Tag*</label>
							<div>
								<input type="text" class="form-control" id="form_add_code" name="code" placeholder="Kode Tag " value="<?= set_value('code') ?>" required maxlength="3" />
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="position-relative form-group">
							<label for="name">Nama*</label>
							<div>
								<input type="text" class="form-control" id="form_add_name" name="name" placeholder="Nama Tag " value="<?= set_value('name') ?>" required />
							</div>
						</div>
					</div>
				</div>

				<div class="row col-md-6 p-0">
					<div class="col">
						<div class="form-group" style="display: inline-block">
							<div>
								<label for="enabled">Enabled </label><br>
								<input type="checkbox" class="apply-status" name="enabled" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal">
							</div>
						</div>
					</div>
					<div class="col">
						<div class="form-group" style="display: inline-block">
							<div>
								<label for="fixed">Fixed </label><br>
								<input type="checkbox" class="apply-status" name="fixed" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal">
							</div>
						</div>
					</div>
					<div class="col">
						<div class="form-group" style="display: inline-block">
							<div>
								<label for="mandatory">Mandatory </label><br>
								<input type="checkbox" class="apply-status" name="mandatory" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal">
							</div>
						</div>
					</div>
					<div class="col">
						<div class="form-group" style="display: inline-block">
							<div>
								<label for="repeatable">Repeatable </label><br>
								<input type="checkbox" class="apply-status" name="repeatable" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal">
							</div>
						</div>
					</div>
					<div class="col">
						<div class="form-group" style="display: inline-block">
							<div>
								<label for="customable">Customable </label><br>
								<input type="checkbox" class="apply-status" name="customable" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal">
							</div>
						</div>
					</div>
					<div class="col">
						<div class="position-relative form-group">
							<label for="length">Length </label>
							<div>
								<input type="number" class="form-control" id="form_add_length" min="-1" max="255" name="length" placeholder="Length " value="<?= set_value('length', -1) ?>" style="max-width:60px" />
							</div>
						</div>
					</div>
				</div>

				<div class="main-card mt-3 mb-3 card car-border">
					<div class="card-header">
						Sub Ruas
						<div class="btn-actions-pane-right actions-icon-btn">
							<button type="button" name="add" data-tbody="subruas-tbody" class="btn btn-success subruas-btn-add"><i class="fa fa-plus"></i> Item</button>
						</div>
					</div>
					<div class="card-body">
						<table style="width: 100%;" id="subruas-tbl" class="table table-hover table-striped table-bordered">
							<thead>
								<tr>
									<th width="300">Code</th>
									<th width="300">Name</th>
									<th>Delimiter</th>
									<th>Sort</th>
									<th>Display</th>
									<th>Repeatable</th>
									<th class="text-center">Aksi</th>
								</tr>
							</thead>
							<tbody id="subruas-tbody">
							</tbody>
						</table>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="main-card mt-3 mb-3 card car-border">
							<div class="card-header">
								Indikator 1
								<div class="btn-actions-pane-right actions-icon-btn">
									<button type="button" name="add" data-tbody="indikator1-tbody" class="btn btn-success indikator1-btn-add"><i class="fa fa-plus"></i> Item</button>
								</div>
							</div>
							<div class="card-body">
								<table style="width: 100%;" id="indikator1-tbl" class="table table-hover table-striped table-bordered">
									<thead>
										<tr>
											<th width="300">Kode</th>
											<th width="300">Nama</th>
											<th>Aksi</th>
										</tr>
									</thead>
									<tbody id="indikator1-tbody">
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="main-card mt-3 mb-3 card car-border">
							<div class="card-header">
								Indikator 2
								<div class="btn-actions-pane-right actions-icon-btn">
									<button type="button" name="add" data-tbody="indikator2-tbody" class="btn btn-success indikator2-btn-add"><i class="fa fa-plus"></i> Item</button>
								</div>
							</div>
							<div class="card-body">
								<table style="width: 100%;" id="indikator2-tbl" class="table table-hover table-striped table-bordered">
									<thead>
										<tr>
											<th width="300">Kode</th>
											<th width="300">Nama</th>
											<th>Aksi</th>
										</tr>
									</thead>
									<tbody id="indikator2-tbody">
									</tbody>
								</table>
							</div>
						</div>
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
		$(document).on('click', '.subruas-btn-remove', function() {
			var url = $(this).data('href');
			var row = $(this).closest('tr');

			row.remove();
			return false;
		});

		$(".subruas-btn-add").click(function() {
			var index = Date.now();
			var tbody = $(this).data('tbody');

			$('#' + tbody).append(`
				<tr class="rm-row">
					<td width="300">
						<input type="hidden" name="index0[]" value="` + index + `">
						<input type="text" class="form-control" name="code0[` + index + `]" placeholder="" />
					</td>
					<td width="300">                        
						<input type="text" class="form-control" name="name0[` + index + `]" placeholder="" />
					</td>
					<td>                        
						<input type="text" class="form-control" name="delimiter0[` + index + `]" placeholder="" value="," />
					</td>
					<td>     
						<input type="number" class="form-control" name="sortno0[` + index + `]" placeholder="" value="0" />         
					</td>
					<td class="text-center">
						<input type="hidden" name="isshow0[` + index + `]" value="0"><input type="checkbox" name="isshow0[` + index + `]" value="1" class="form-control">
					</td>
					<td class="text-center">
						<input type="hidden" name="repeatable0[` + index + `]" value="0"><input type="checkbox" name="repeatable0[` + index + `]" value="1" class="form-control">			
					</td>
					<td class="text-left">
						<button type="button" class="btn btn-danger subruas-btn-remove" data-href=""><i class="fa fa-times"></i></button>
					</td>
				</tr>
			`);
		});

		$(document).on('click', '.indikator1-btn-remove', function() {
			var url = $(this).data('href');
			var row = $(this).closest('tr');

			row.remove();
			return false;
		});

		$(".indikator1-btn-add").click(function() {
			var index = Date.now();
			var tbody = $(this).data('tbody');

			$('#' + tbody).append(`
				<tr class="rm-row">
					<td width="300">
						<input type="hidden" name="index1[]" value="` + index + `">
						<input type="text" class="form-control" name="code1[` + index + `]" placeholder="" />
					</td>
					<td width="300">                        
						<input type="text" class="form-control" name="name1[` + index + `]" placeholder="" />
					</td>
					<td class="text-left">
						<button type="button" class="btn btn-danger indikator1-btn-remove" data-href=""><i class="fa fa-times"></i></button>
					</td>
				</tr>
			`);
		});

		$(document).on('click', '.indikator2-btn-remove', function() {
			var url = $(this).data('href');
			var row = $(this).closest('tr');

			row.remove();
			return false;
		});


		$(".indikator2-btn-add").click(function() {
			var index = Date.now();
			var tbody = $(this).data('tbody');

			$('#' + tbody).append(`
				<tr class="rm-row">
					<td width="300">
						<input type="hidden" name="index2[]" value="` + index + `">
						<input type="text" class="form-control" name="code2[` + index + `]" placeholder="" />
					</td>
					<td width="300">                        
						<input type="text" class="form-control" name="name2[` + index + `]" placeholder="" />
					</td>
					<td class="text-left">
						<button type="button" class="btn btn-danger indikator2-btn-remove" data-href=""><i class="fa fa-times"></i></button>
					</td>
				</tr>
			`);
		});
	});
</script>
<?= $this->endSection('script') ?>