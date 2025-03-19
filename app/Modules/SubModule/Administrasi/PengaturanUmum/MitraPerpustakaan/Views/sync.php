<?php
$request = service('request'); ?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>


<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-refresh-2 icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Sync Mitra Perpustakaan
					<div class="page-title-subheading">Sinkronisasi Mitra Perpustakaan</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="active breadcrumb-item"><a href="<?= base_url('master-mitra-perpustakaan') ?>">Mitra Perpustakaan</a></li>
						<li class="active breadcrumb-item" aria-current="page">Sync Mitra Perpustakaan</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<div class="main-card mb-3 card">
		<div class="card-header">
			<i class="header-icon lnr-sync icon-gradient bg-plum-plate"> </i> Form Sinkronisasi
			<div class="btn-actions-pane-right actions-icon-btn">

			</div>
		</div>
		<div class="card-body">
			<form name="form_items" id="form_search" method="post" action="<?= base_url('master-mitra-perpustakaan/sync') ?>">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label>Provinsi*</label>
							<div class="select-wrapper">
								<select required data-error="Pilih Provinsi" class="form-control select2" id="Province" name="Province" style="width:100%"></select>
							</div>
							<small class="help-block with-errors"></small>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Kota*</label>
							<div class="select-wrapper">
								<select class="form-control select2" id="City" name="City" style="width:100%"></select>
							</div>
							<small class="help-block with-errors"></small>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Kecamatan*</label>
							<div class="select-wrapper">
								<select class="form-control select2" id="District" name="Kecamatan" style="width:100%"></select>
							</div>
							<small class="help-block with-errors"></small>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Kelurahan*</label>
							<div class="select-wrapper">
								<select class="form-control select2" id="SubDistrict" name="Kelurahan" style="width:100%"></select>
							</div>
							<small class="help-block with-errors"></small>
						</div>
					</div>
				</div>

				<div class="form-row mt-5">
					<div class="col-md-12 mb-3">
						<div class="input-group">
							<div class="input-group-prepend"><span class="input-group-text"><b>URL</b></span></div>
							<input type="text" class="form-control" id="url" name="url" placeholder="Endpoint API" value="https://data.perpusnas.go.id/public/direktori/list?provinsi_id=32&kabkota_id=3273&kecamatan_id=327322&kelurahan_id=" />
							<div class="input-group-append">
								<button type="submit" class="btn btn-primary" name="submit">Proses </button>
							</div>
							<div class="input-group-append">
								<button type="button" class="btn btn-secondary" id="Reset">Reset </button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>


<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
	let provinsi_id = "";
	let kabkota_id = "";
	let kecamatan_id = "";
	let kelurahan_id = "";
	let params = [];
	let url = 'https://data.perpusnas.go.id/public/direktori/list?';

	generateURL();

	getData(`<?= base_url('api/region/province') ?>`, `#Province`);
	$('#Reset').click(function(e) {
		$('#Province').val("").trigger('change');
		$('#City').empty();
		$('#District').empty();
		$('#SubDistrict').empty();
		provinsi_id = "";
		kabkota_id = "";
		kecamatan_id = "";
		kelurahan_id = "";
		params = [];
		url = 'https://data.perpusnas.go.id/public/direktori/list?';
		$('#url').val(url);
	});

	$('#Province').change(function(e) {
		var code = $(this).val();
		getData(`<?= base_url('api/region/city') ?>/${code}.`, `#City`);

		code = code.replace(".", "");
		provinsi_id = code.replace(".", "");
		params.push('provinsi_id=' + provinsi_id);
		console.log(params);
		generateURL();
	});
	$('#City').change(function(e) {
		var code = $(this).val();
		getData(`<?= base_url('api/region/district') ?>/${code}`, `#District`);

		code = code.replace(".", "");
		var kabkota_id = code.replace(".", "");
		params.push('kabkota_id=' + kabkota_id);
		console.log(params);
		generateURL();
	});
	$('#District').change(function(e) {
		var code = $(this).val();
		getData(`<?= base_url('api/region/sub_district') ?>/${code}`, `#SubDistrict`);

		code = code.replace(".", "");
		kecamatan_id = code.replace(".", "");
		params.push('kecamatan_id=' + kecamatan_id);
		console.log(params);
		generateURL();
	});
	$('#SubDistrict').change(function(e) {
		var name = $("#SubDistrict option:selected").text();
		var code = $(this).val();

		code = code.replace(".", "");
		code = code.replace(".", "");
		kelurahan_id = code.replace(".", "");
		params.push('kelurahan_id=' + kelurahan_id);
		console.log(params);
		generateURL();
	});

	function generateURL() {
		url = 'https://data.perpusnas.go.id/public/direktori/list?';
		removeParam('kelurahan_id');
		params.push('kelurahan_id=' + kelurahan_id);

		url += params.join('&');
		$('#url').val(url);
	}

	function removeParam(key) {
		params = params.filter(function(param) {
			return !param.startsWith(key + '=');
		});
	}
</script>
<?= $this->endSection('script'); ?>