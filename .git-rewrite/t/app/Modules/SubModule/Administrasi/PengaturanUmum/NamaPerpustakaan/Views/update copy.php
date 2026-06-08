<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
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
				<div>Nama Perpustakaan
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
						<li class="breadcrumb-item">Administrasi</li>
						<li class="breadcrumb-item">Pengaturan Umum</li>
						<li class="breadcrumb-item active">Nama Perpustakaan</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8">
			<div class="main-card mb-3 card">
				<div class="card-header">
					<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Pengaturan Nama Perpustakaan
				</div>
				<div class="card-body">
					<div id="infoMessage"><?= $message ?? '' ?></div> <?= get_message('message') ?>
					<form id="frm_create" method="post" action="<?= base_url('master-nama-perpustakaan/index') ?>" onsubmit="return validateForm()">
						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="NamaPerpustakaan">Nama Perpustakaan</label>
									<div>
										<input type="text" class="form-control" name="NamaPerpustakaan" id="NamaPerpustakaan" placeholder="" value="<?= get_setting_parameter('NamaPerpustakaan', is_profiling()); ?>">
									</div>
								</div>
							</div>
						</div>

						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="NamaLokasiPerpustakaan">Nama Lokasi Perpustakaan</label>
									<div>
										<input type="text" class="form-control" name="NamaLokasiPerpustakaan" id="NamaLokasiPerpustakaan" placeholder="" value="<?= get_setting_parameter('NamaLokasiPerpustakaan', is_profiling()); ?>">
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="JenisPerpustakaan">Jenis Perpustakaan</label>
									<div>
										<select class="form-control" id="JenisPerpustakaan" name="JenisPerpustakaan" placeholder="" required>
											<?php foreach (get_table('jenis_perpustakaan', 'ID, Name', 'Name IS NOT NULL', 'data') as $row) : ?>
												<option value="<?= $row->ID ?>" <?= (get_setting_parameter('JenisPerpustakaan', is_profiling()) == $row->ID) ? 'selected' : '' ?>><?= $row->Name ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="EmailPerpustakaan">Email Perpustakaan</label>
									<div>
										<input type="text" class="form-control" name="EmailPerpustakaan" id="EmailPerpustakaan" placeholder="" value="<?= get_setting_parameter('EmailPerpustakaan', is_profiling()); ?>">
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="InstagramPerpustakaan">Instagram Perpustakaan</label>
									<div>
										<input type="text" class="form-control" name="InstagramPerpustakaan" id="InstagramPerpustakaan" placeholder="" value="<?= get_setting_parameter('InstagramPerpustakaan', is_profiling()); ?>">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="YoutubePerpustakaan">Youtube Perpustakaan</label>
									<div>
										<input type="text" class="form-control" name="YoutubePerpustakaan" id="YoutubePerpustakaan" placeholder="" value="<?= get_setting_parameter('YoutubePerpustakaan', is_profiling()); ?>">
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="TwitterPerpustakaan">Twitter Perpustakaan</label>
									<div>
										<input type="text" class="form-control" name="TwitterPerpustakaan" id="TwitterPerpustakaan" placeholder="" value="<?= get_setting_parameter('TwitterPerpustakaan', is_profiling()); ?>">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="FacebookPerpustakaan">Facebook Perpustakaan</label>
									<div>
										<input type="text" class="form-control" name="FacebookPerpustakaan" id="FacebookPerpustakaan" placeholder="" value="<?= get_setting_parameter('FacebookPerpustakaan', is_profiling()); ?>">
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-12">
								<div class="form-group" style="display: inline-block">
									<div>
										<label for="IsUseKop">Gunakan Kop di Laporan </label><br>
										<input type="checkbox" class="apply-status" name="IsUseKop" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal" <?= (get_setting_parameter('IsUseKop', is_profiling()) == 1 ? 'checked' : '') ?>>
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
		<div class="col-md-4">
			<div class="main-card mb-3 card">
				<div class="card-header">
					<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Pengaturan Logo
					<div class="btn-actions-pane-right actions-icon-btn">
						<div class="menu-header-btn-pane">
							<a href="javascript:void(0);" data-id="" data-format=".jpg,.png" data-format-title="Format (JPG|PNG). Max 1 Files @ 1MB" data-field="LogoPerpustakaan" data-title="Upload Logo" class="mb-2 mr-2 btn btn-warning upload-data">
								<i class="fa fa-edit"></i> Update Logo
							</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<?php
					$default = base_url('perpusnas.png');
					$param_image = get_setting_parameter('Logo', is_profiling());
					$file_image = (!empty($param_image)) ? base_url('uploads/logo/' . $param_image) : $default;
					?>
					<div class="form-row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="content"></label>
								<div>
									<img width="150px" src="<?= $file_image ?>" alt="Image" class="img">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="main-card mb-3 card">
				<div class="card-header">
					<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Pengaturan Kop
					<div class="btn-actions-pane-right actions-icon-btn">
						<div class="menu-header-btn-pane">
							<a href="javascript:void(0);" data-id="" data-format=".jpg,.png" data-format-title="Format (JPG|PNG). Max 1 Files @ 1MB" data-field="KopPerpustakaan" data-title="Upload Kop" class="mb-2 mr-2 btn btn-warning upload-data">
								<i class="fa fa-edit"></i> Update Kop
							</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<?php
					$default = base_url('perpusnas.png');
					$param_image = get_setting_parameter('Logo', is_profiling());
					$file_image = (!empty($param_image)) ? base_url('uploads/logo/' . $param_image) : $default;
					?>
					<div class="form-row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="content"></label>
								<div>
									<img width="150px" src="<?= $file_image ?>" alt="Image" class="img">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<?= $this->include('User\Views\upload_modal'); ?>
<?= $this->endSection('script') ?>