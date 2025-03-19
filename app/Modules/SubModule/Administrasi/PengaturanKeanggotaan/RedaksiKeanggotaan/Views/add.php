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
				<div>Redaksi Keanggotaan
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
						<li class="breadcrumb-item">Administrasi</li>
						<li class="breadcrumb-item">Pengaturan Anggota</li>
						<li class="breadcrumb-item">Redaksi Keanggotaan</li>
						<li class="active breadcrumb-item" aria-current="page">Tambah Redaksi Keanggotaan</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-header">
			<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Form Ubah Redaksi Kenaggotaan
		</div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? '' ?></div> <?= get_message('message') ?>
			<form id="frm_create" method="post" action="<?= base_url('master-redaksi-keanggotaan/create') ?>" onsubmit="return validateForm()">
				<div class="form-row">
					<div class="col-md-6">
						<div class="position-relative form-group">
							<label for="format">Kategori</label>
							<div>
								<input type="text" class="form-control" id="form_add_format" name="NameCategory" placeholder="Kategori " value="<?= set_value('NameCategory') ?>" />
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="position-relative form-group">
							<div>
								<label for="enabled">Sort </label><br>
								<input type="number" class="form-control" name="SortNum" value="<?= set_value('SortNum') ?>">
							</div>
						</div>
					</div>

				</div>

				<div class="form-row">
					<div class="col-md-12">
						<div class="position-relative form-group">
							<label for="description">Isi</label>
							<div>
								<textarea id="yourTextareaId" name="Contents" placeholder="Isi" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('Contents') ?></textarea>
							</div>
						</div>
					</div>
				</div>

				<div class="form-row mt-2">


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
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
	tinymce.init({
		selector: '#yourTextareaId',
		height: 300, // Adjust the height as needed
		plugins: 'link image code', // Add any additional plugins you want to use
		toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code',
	});
</script>
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
						<input type="hidden" name="isshow0[` + index + `]" value="0"><input type="checkbox" name="isshow0[` + index + `]" value="1" class="form-control isshow">
					</td>
					<td class="text-center">
						<input type="hidden" name="repeatable0[` + index + `]" value="0"><input type="checkbox" name="repeatable0[` + index + `]" value="1" class="form-control fielddatas_repeatable">			
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