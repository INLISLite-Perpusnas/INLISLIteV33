<?php
$prefix = '';
if (isset($branch->slug) && $branch->slug != null) {
	$prefix = $branch->slug . '/';
} else if (isset($branch->Name) && trim($branch->Name) != '') {
	$prefix = str_slugify($branch->Name) . '/';
}

$request = \Config\Services::request();
$request->uri->setSilent();
$cookie_location = cookie_location();
$mitra_perpustakaan = $cookie_location->Branch_name ?? '';
$lokasi_perpustakaan = $cookie_location->LocationLibrary_name ?? '';
$alamat_perpustakaan = $cookie_location->LocationLibrary_address ?? '';
$lokasi_ruang = $cookie_location->Name ?? '';
$kode_lokasi = $cookie_location->Code ?? '';
$slug = $request->getGet('slug') ?? 'anggota';
?>

<?= $this->extend(config('Core')->layout_landing); ?>
<?= $this->section('style'); ?>
<style>
	.card-horizontal {
		display: flex;
		flex: 1 1 auto;
	}

	tr.group,
	tr.group:hover {
		background-color: #F0F3F5 !important;
	}

	dl {
		display: grid;
		grid-template-columns: max-content auto;
	}

	dt {
		grid-column-start: 1;
		width: 100px;
		font-weight: normal;
	}

	dd {
		grid-column-start: 2;
	}

	#nav_profile li a.active {
		font-weight: bold !important;
		color: white !important;
	}
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('branch'); ?>
<div class="row">
	<div class="col-md-12">
		<h5><b><?= $lokasi_perpustakaan ?></b></h5>
		<?= $alamat_perpustakaan ?>
	</div>
</div>
<?= $this->endSection('branch'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-refresh-2 icon-gradient bg-strong-bliss"></i>
				</div>
				<div><b>Buku Tamu</b>
					<div class="page-title-subheading font-weight-bold"><?= $kode_lokasi ?> - <?= $lokasi_ruang ?></div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url() ?>"><i class="fa fa-home"></i> Beranda</a></li>
						<li class="breadcrumb-item">Buku Tamu</li>
						<li class="active breadcrumb-item" aria-current="page">Bukan Anggota</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
		<li class="nav-item">
			<a class="nav-link" href="<?= base_url($prefix . 'buku-tamu') ?>">
				<span>Anggota</span>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link active" href="<?= base_url($prefix . 'buku-tamu/non_anggota') ?>">
				<span>Bukan Anggota </span>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="<?= base_url($prefix . 'buku-tamu/rombongan') ?>">
				<span>Rombongan</span>
			</a>
		</li>
	</ul>

	<div class="main-card mb-3 card">
		<div class="card-header">
			<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Buku Tamu - Bukan Anggota
		</div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? ''; ?></div>
			<?= get_message('message'); ?>

			<form id="frm_create" class="col-md-12 mx-auto" method="post" action="">
				<div class="form-row">
					<div class="col-md-12">
						<div class="position-relative form-group">
							<label for="Nama">Nama Pengunjung*</label>
							<div>
								<input type="text" class="form-control" name="Nama" id="Nama" placeholder="" value="<?= set_value('Nama'); ?>" />
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label>Pekerjaan*</label>
							<select class="form-control" name="Profesi_id" id="Profesi_id" tabindex="-1" aria-hidden="true">
								<?php foreach (get_ref_table('master_pekerjaan', 'id, pekerjaan', null, 'data') as $row) : ?>
									<option value="<?= $row->id ?>"><?= $row->pekerjaan ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label>Pendidikan Terakhir*</label>
							<select class="form-control" name="PendidikanTerakhir_id" id="PendidikanTerakhir_id" tabindex="-1" aria-hidden="true">
								<?php foreach (get_ref_table('master_pendidikan', 'id, Nama', null, 'data') as $row) : ?>
									<option value="<?= $row->id ?>"><?= $row->Nama ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label>Jenis Kelamin*</label>
							<select class="form-control" name="JenisKelamin_id" id="JenisKelamin_id" tabindex="-1" aria-hidden="true">
								<?php foreach (get_ref_table('jenis_kelamin', 'ID ,Name', 'active=1', 'data') as $row) : ?>
									<option value="<?= $row->ID ?>"><?= $row->Name ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="Alamat">Alamat Sesuai Identitas </label>
					<div>
						<textarea id="Alamat" name="Alamat" placeholder=" " rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('Alamat') ?></textarea>
					</div>
				</div>

				<div class="form-group">
					<button type="submit" class="btn btn-primary" name="submit"><i class="fa fa-save"></i> Simpan Buku Tamu</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= $this->endSection('script'); ?>