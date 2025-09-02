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
					<form id="frm_create" method="post" action="<?= base_url('master-nama-perpustakaan/update') ?>">
						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="Code">NPP</label>
									<div>
										<input type="text" class="form-control" name="npp_perpustakaan" id="npp_perpustakaan" placeholder="" value="<?= set_value('npp_perpustakaan', $npp_perpustakaan) ?>" readonly>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="Name">Nama Lembaga</label>
									<div>
										<input type="text" class="form-control" name="nama_perpustakaan" id="nama_perpustakaan" placeholder="" value="<?= set_value('nama_perpustakaan', $nama_perpustakaan) ?>" readonly>
									</div>
								</div>
							</div>
						</div>
						<!-- <div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="slug">URL Perpustakaan (<?= env('app.libURL') ?>/{<b>url-perpustakaan</b>})</label>
									<div>
										<input type="text" class="form-control" name="Url" id="Url" placeholder="" value="<?= set_value('Url', $branch->slug ?? '') ?>" required>
									</div>
								</div>
							</div>
						</div> -->

						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="Address">Alamat</label>
									<div>
										<textarea class="form-control" name="nama_lokasi_perpustakaan" id="nama_lokasi_perpustakaan" placeholder=""><?= set_value('nama_lokasi_perpustakaan', $nama_lokasi_perpustakaan) ?></textarea>
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="Email">Jenis Perpustakaan</label>
									<div>
										<input type="text" class="form-control" name="jenis_perpustakaan" id="jenis_perpustakaan" placeholder="" value="<?= set_value('jenis_perpustakaan', $jenis_perpustakaan) ?>" readonly>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="Email">Email</label>
									<div>
										<input type="text" class="form-control" name="email_perpustakaan" id="email_perpustakaan" placeholder="" value="<?= set_value('email_perpustakaan', $email_perpustakaan) ?>">
									</div>
								</div>
							</div>
							
						</div>
						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="IG">Instagram</label>
									<div>
										<input type="text" class="form-control" name="instagram" id="instagram" placeholder="" value="<?= set_value('instagram', $instagram) ?>">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="YT">Youtube</label>
									<div>
										<input type="text" class="form-control" name="youtube" id="YT" placeholder="" value="<?= set_value('youtube', $youtube) ?>">
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">

							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="FB">Facebook</label>
									<div>
										<input type="text" class="form-control" name="facebook" id="FB" placeholder="" value="<?= set_value('facebook', $facebook) ?>">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="Phone">No. Telepon</label>
									<div>
										<input type="text" class="form-control" name="phone" id="phone" placeholder="" value="<?= set_value('phone', $phone) ?>">
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="LayananOperasionl">Jam Oprasional</label>
									<div>
										<?php
										$LayananOperasionl_Str = '&lt;b&gt;Jam Operasional Layanan&lt;/b&gt;&lt;br&gt;
										&lt;i class=&quot;right-icon bx bx-chevrons-right&quot;&gt;&lt;/i&gt;Senin-Jumat 08.00 - 16.00 WIB&lt;br&gt;
										&lt;i class=&quot;right-icon bx bx-chevrons-right&quot;&gt;&lt;/i&gt;Sabtu-Minggu 08.00 - 15.00 WIB&lt;br&gt;
										&lt;i class=&quot;right-icon bx bx-chevrons-right&quot;&gt;&lt;/i&gt;Cuti Bersama dan Libur Nasional &lt;b&gt;Tutup&lt;/b&gt;&lt;br&gt;';
										$LayananOperasionl = $jam_operasional ?: $LayananOperasionl_Str;

										// Decode the HTML content to display it properly
										$LayananOperasionl_Display = htmlspecialchars_decode($LayananOperasionl, ENT_QUOTES);
										?>
										<textarea class="form-control" name="LayananOperasionl" id="LayananOperasionl" placeholder="" rows="5"><?= set_value('LayananOperasionl', $LayananOperasionl_Display) ?></textarea>
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="Alamat">Tulisan Banner</label>
									<div>
										<textarea class="form-control" name="tulisan_banner" id="tulisan_banner" placeholder="" rows="5"><?= set_value('tulisan_banner', $tulisan_banner) ?></textarea>
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="Alamat">Tentang Kami</label>
									<div>
										<textarea class="form-control" name="tentang_kami" id="tentang_kami" placeholder="" rows="5"><?= set_value('tentang_kami', $tentang_kami) ?></textarea>
									</div>
								</div>
							</div>
						</div>
						
						<div class="form-row">
							<div class="col-md-12">
								<div class="form-group" style="display: inline-block">
									<div>
										<label for="IsUseKop">Gunakan Kop di Laporan </label><br>
										<input type="checkbox" class="apply-status" name="IsUseKop" value="1" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal" <?= ($is_use_kop) ? 'checked' : '' ?>>
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
					<!-- GANTI button Anda dengan ini -->
					<div class="btn-actions-pane-right actions-icon-btn">
						<div class="menu-header-btn-pane">
							<a href="javascript:void(0);"
								data-toggle="modal"
								data-target="#modal_upload_logo"
								class="mb-2 mr-2 btn btn-warning">
								<i class="fa fa-edit"></i> Update Logo
							</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<?php
					$default = base_url('perpusnas.png');
					$file_image = (!empty($logo)) ? base_url('uploads/branch/' . $logo) : $default;
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
							<a href="javascript:void(0);"
								data-toggle="modal"
								data-target="#modal_upload_logokop"
								class="mb-2 mr-2 btn btn-warning">
								<i class="fa fa-edit"></i> Update Kop Laporan
							</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<?php
					$default = base_url('perpusnas.png');
					$file_image = (!empty($logo_kop)) ? base_url('uploads/branch/' . $logo_kop) : $default;
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
<?= $this->include('NamaPerpustakaan\Views\upload_modal'); ?>
<?= $this->include('NamaPerpustakaan\Views\upload_modal_kop'); ?>
<?= $this->endSection('script') ?>