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
										<input type="text" class="form-control" name="Code" id="Code" placeholder="" value="<?= set_value('Code', $branch->Code) ?>" readonly>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="Name">Nama Lembaga</label>
									<div>
										<input type="text" class="form-control" name="Name" id="Name" placeholder="" value="<?= set_value('Name', $branch->Name) ?>" readonly>
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="slug">URL Perpustakaan (<?= env('app.libURL') ?>/{<b>url-perpustakaan</b>})</label>
									<div>
										<input type="text" class="form-control" name="Url" id="Url" placeholder="" value="<?= set_value('Url', $branch->slug ?? '') ?>" required>
									</div>
								</div>
							</div>
						</div>

						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="Address">Alamat</label>
									<div>
										<textarea class="form-control" name="Address" id="Address" placeholder=""><?= set_value('Address', $branch->Address) ?></textarea>
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="Email">Email</label>
									<div>
										<input type="text" class="form-control" name="Email" id="Email" placeholder="" value="<?= set_value('Email', $branch->Email) ?>">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="Phone">No. Telepon</label>
									<div>
										<input type="text" class="form-control" name="Phone" id="Phone" placeholder="" value="<?= set_value('Phone', $branch->Phone) ?>">
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="IG">Instagram</label>
									<div>
										<input type="text" class="form-control" name="IG" id="IG" placeholder="" value="<?= set_value('IG', $branch->IG) ?>">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="YT">Youtube</label>
									<div>
										<input type="text" class="form-control" name="YT" id="YT" placeholder="" value="<?= set_value('YT', $branch->YT) ?>">
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="TW">Twitter</label>
									<div>
										<input type="text" class="form-control" name="TW" id="TW" placeholder="" value="<?= set_value('TW', $branch->TW) ?>">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="FB">Facebook</label>
									<div>
										<input type="text" class="form-control" name="FB" id="FB" placeholder="" value="<?= set_value('FB', $branch->FB) ?>">
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="LayananOperasionl">Alamat</label>
									<div>
										<?php
										$LayananOperasionl_Str = '&lt;b&gt;Jam Operasional Layanan&lt;/b&gt;&lt;br&gt;
&lt;i class=&quot;right-icon bx bx-chevrons-right&quot;&gt;&lt;/i&gt;Senin-Jumat 08.00 - 16.00 WIB&lt;br&gt;
&lt;i class=&quot;right-icon bx bx-chevrons-right&quot;&gt;&lt;/i&gt;Sabtu-Minggu 08.00 - 15.00 WIB&lt;br&gt;
&lt;i class=&quot;right-icon bx bx-chevrons-right&quot;&gt;&lt;/i&gt;Cuti Bersama dan Libur Nasional &lt;b&gt;Tutup&lt;/b&gt;&lt;br&gt;';
										$LayananOperasionl = $branch->LayananOperasionl ?? $LayananOperasionl_Str;

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
							<a href="javascript:void(0);" data-id="<?= branch_id() ?>" data-format=".jpg,.png" data-format-title="Format (JPG|PNG). Max 1 Files @ 1MB" data-field="Logo" data-title="Upload Logo" class="mb-2 mr-2 btn btn-warning upload-data">
								<i class="fa fa-edit"></i> Update Logo
							</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<?php
					$default = base_url('perpusnas.png');
					$file_image = (!empty($branch->Logo)) ? base_url('uploads/branch/' . $branch->Logo) : $default;
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
							<a href="javascript:void(0);" data-id="<?= branch_id() ?>" data-format=".jpg,.png" data-format-title="Format (JPG|PNG). Max 1 Files @ 1MB" data-field="CoverLetter" data-title="Upload Kop Surat" class="mb-2 mr-2 btn btn-warning upload-data">
								<i class="fa fa-edit"></i> Update Kop
							</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<?php
					$default = base_url('perpusnas.png');
					$file_image = (!empty($branch->CoverLetter)) ? base_url('uploads/branch/' . $branch->CoverLetter) : $default;
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
<?= $this->endSection('script') ?>