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
				<div>Edit Tag - <?= $tag->Tag ?> (<?= $tag->Name ?>)
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
						<li class="active breadcrumb-item" aria-current="page">Edit</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-header">
			<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Form Edit - Format Penamaan dan Aturan Tag
		</div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? '' ?></div> <?= get_message('message') ?>
			<form id="frm_create" method="post" action="<?= base_url('master-tag/edit/' . $tag->ID) ?>" onsubmit="return validateForm()">
				<div class="form-row">
					<div class="col-md-3">
						<div class="position-relative form-group">
							<label for="format">Format*</label>
							<div>
								<select class="form-control" id="form_add_format" name="format" placeholder="Format " required>
									<?php foreach (get_table('formats', 'ID, Name', 'Name IS NOT NULL', 'data') as $row) : ?>
										<option value="<?= $row->ID ?>" <?= ($row->ID == $tag->Format_id) ? 'selected' : '' ?>><?= $row->Name ?></option>
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
										<option value="<?= $row->ID ?>" <?= ($row->ID == $tag->Group_id) ? 'selected' : '' ?>><?= $row->Name ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="position-relative form-group">
							<label for="code">Tag*</label>
							<div>
								<input type="text" class="form-control" id="form_add_code" name="code" placeholder="Kode Tag " value="<?= set_value('code', $tag->Tag) ?>" required maxlength="3" />
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="position-relative form-group">
							<label for="name">Nama*</label>
							<div>
								<input type="text" class="form-control" id="form_add_name" name="name" placeholder="Nama Tag " value="<?= set_value('name', $tag->Name) ?>" required />
							</div>
						</div>
					</div>
				</div>

				<div class="row col-md-6 p-0">
					<div class="col">
						<div class="form-group" style="display: inline-block">
							<div>
								<label for="enabled">Enabled </label><br>
								<input type="checkbox" class="apply-status" name="enabled" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal" <?= $tag->Enabled == 1 ? 'checked' : '' ?>>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="form-group" style="display: inline-block">
							<div>
								<label for="fixed">Fixed </label><br>
								<input type="checkbox" class="apply-status" name="fixed" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal" <?= $tag->Fixed == 1 ? 'checked' : '' ?>>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="form-group" style="display: inline-block">
							<div>
								<label for="mandatory">Mandatory </label><br>
								<input type="checkbox" class="apply-status" name="mandatory" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal" <?= $tag->Mandatory == 1 ? 'checked' : '' ?>>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="form-group" style="display: inline-block">
							<div>
								<label for="repeatable">Repeatable </label><br>
								<input type="checkbox" class="apply-status" name="repeatable" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal" <?= $tag->Repeatable == 1 ? 'checked' : '' ?>>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="form-group" style="display: inline-block">
							<div>
								<label for="customable">Customable </label><br>
								<input type="checkbox" class="apply-status" name="customable" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal" <?= $tag->IsCustomable == 1 ? 'checked' : '' ?>>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="position-relative form-group">
							<label for="length">Length </label>
							<div>
								<input type="number" class="form-control" id="form_add_length" min="-1" max="255" name="length" placeholder="Length " value="<?= set_value('length', $tag->Length) ?>" style="max-width:60px" />
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
									<th width="80">Code</th>
									<th>Name</th>
									<th width="80">Delimiter</th>
									<th width="80">Sort No.</th>
									<th width="80">Display</th>
									<th width="80">Repeatable</th>
									<th width="50" class="text-center">Aksi</th>
								</tr>
							</thead>
							<tbody id="subruas-tbody">
								<?php foreach ($fielddatas as $row) : ?>
									<?php $index = $row->Code; ?>
									<tr class="rm-row">
										<td>
											<input type="hidden" name="index0[]" value="<?= $index ?>">
											<input type="text" class="form-control" name="code0[<?= $index ?>]" placeholder="" value="<?= $row->Code ?>" required maxlength="1" />
										</td>
										<td>
											<input type="text" class="form-control" name="name0[<?= $index ?>]" placeholder="" value="<?= $row->Name ?>" required />
										</td>
										<td>
											<input type="text" class="form-control" name="delimiter0[<?= $index ?>]" placeholder="" value="<?= $row->Delimiter ?>" maxlength="1" />
										</td>
										<td>
											<input type="number" class="form-control" name="sortno0[<?= $index ?>]" placeholder="" value="<?= $row->SortNo ?>" />
										</td>
										<td class="text-center">
											<input type="hidden" name="isshow0[<?= $index ?>]" value="0">
											<input type="checkbox" class="form-control" name="isshow0[<?= $index ?>]" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="mini" <?= $row->IsShow == 1 ? 'checked' : '' ?>>
										</td>
										<td class="text-center">
											<input type="hidden" name="repeatable0[<?= $index ?>]" value="0">
											<input type="checkbox" class="form-control" name="repeatable0[<?= $index ?>]" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="mini" <?= $row->Repeatable == 1 ? 'checked' : '' ?>>
										</td>
										<td class="text-center">
											<button type="button" class="btn btn-danger subruas-btn-remove" data-href="<?= base_url('api/master-tag/field_data_delete/' . $tag->ID . '/' . $row->Code) ?>"><i class="fa fa-times"></i></button>
										</td>
									</tr>
								<?php endforeach; ?>
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
											<th width="80">Kode</th>
											<th>Nama</th>
											<th width="50" class="text-center">Aksi</th>
										</tr>
									</thead>
									<tbody id="indikator1-tbody">
										<?php foreach ($indicator1s as $row) : ?>
											<?php $index = $row->Code; ?>
											<tr class="rm-row">
												<td>
													<input type="hidden" name="index1[]" value="<?= $index ?>">
													<input type="text" class="form-control" name="code1[<?= $index ?>]" placeholder="" value="<?= $row->Code ?>" required />
												</td>
												<td>
													<input type="text" class="form-control" name="name1[<?= $index ?>]" placeholder="" value="<?= $row->Name ?>" required />
												</td>
												<td class="text-center">
													<button type="button" class="btn btn-danger indikator1-btn-remove" data-href="<?= base_url('api/master-tag/field_indicator1_delete/' . $tag->ID . '/' . $row->Code) ?>"><i class="fa fa-times"></i></button>
												</td>
											</tr>
										<?php endforeach; ?>
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
											<th width="80">Kode</th>
											<th>Nama</th>
											<th width="50" class="text-center">Aksi</th>
										</tr>
									</thead>
									<tbody id="indikator2-tbody">
										<?php foreach ($indicator2s as $row) : ?>
											<?php $index = $row->Code; ?>
											<tr class="rm-row">
												<td>
													<input type="hidden" name="index2[]" value="<?= $index ?>">
													<input type="text" class="form-control" name="code2[<?= $index ?>]" placeholder="" value="<?= $row->Code ?>" required />
												</td>
												<td>
													<input type="text" class="form-control" name="name2[<?= $index ?>]" placeholder="" value="<?= $row->Name ?>" required />
												</td>
												<td class="text-center">
													<button type="button" class="btn btn-danger indikator2-btn-remove" data-href="<?= base_url('api/master-tag/field_indicator2_delete/' . $tag->ID . '/' . $row->Code) ?>"><i class="fa fa-times"></i></button>
												</td>
											</tr>
										<?php endforeach; ?>
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

		$(".subruas-btn-add").click(function() {
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
					<td>                        
						<input type="text" class="form-control" name="delimiter0[` + index + `]" placeholder="" value="," />
					</td>
					<td>     
						<input type="number" class="form-control" name="sortno0[` + index + `]" placeholder="" value="0" />         
					</td>
					<td class="text-center">
						<input type="hidden" name="isshow0[` + index + `]" value="0">
						<input type="checkbox" class="form-control isshow" name="isshow0[` + index + `]" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="mini">	
					</td>
					<td class="text-center">
						<input type="hidden" name="repeatable0[` + index + `]" value="0">
						<input type="checkbox" class="form-control fielddatas_repeatable" name="repeatable0[` + index + `]" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="mini">			
					</td>
					<td class="text-center">
						<button type="button" class="btn btn-danger subruas-btn-remove" data-href=""><i class="fa fa-times"></i></button>
					</td>
				</tr>
			`);
			$('.isshow, .fielddatas_repeatable').bootstrapToggle();
		});

		$(document).on('click', '.indikator1-btn-remove', function() {
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

		$(".indikator1-btn-add").click(function() {
			var index = Date.now();
			var tbody = $(this).data('tbody');

			$('#' + tbody).append(`
				<tr class="rm-row">
					<td>
						<input type="hidden" name="index1[]" value="` + index + `">
						<input type="text" class="form-control" name="code1[` + index + `]" placeholder="" required/>
					</td>
					<td>                        
						<input type="text" class="form-control" name="name1[` + index + `]" placeholder="" required/>
					</td>
					<td class="text-center">
						<button type="button" class="btn btn-danger indikator1-btn-remove" data-href=""><i class="fa fa-times"></i></button>
					</td>
				</tr>
			`);
		});

		$(document).on('click', '.indikator2-btn-remove', function() {
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


		$(".indikator2-btn-add").click(function() {
			var index = Date.now();
			var tbody = $(this).data('tbody');

			$('#' + tbody).append(`
				<tr class="rm-row">
					<td>
						<input type="hidden" name="index2[]" value="` + index + `">
						<input type="text" class="form-control" name="code2[` + index + `]" placeholder="" required/>
					</td>
					<td>                        
						<input type="text" class="form-control" name="name2[` + index + `]" placeholder="" required/>
					</td>
					<td class="text-center">
						<button type="button" class="btn btn-danger indikator2-btn-remove" data-href=""><i class="fa fa-times"></i></button>
					</td>
				</tr>
			`);
		});
	});
</script>
<?= $this->endSection('script') ?>