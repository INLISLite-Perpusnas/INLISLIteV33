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
						<li class="breadcrumb-item" aria-current="page">Buku Tamu</li>
						<li class="active breadcrumb-item" aria-current="page">Anggota</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
		<li class="nav-item">
			<a class="nav-link active" href="<?= base_url($prefix . 'buku-tamu') ?>">
				<span>Anggota</span>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="<?= base_url($prefix . 'buku-tamu/non_anggota') ?>">
				<span>Bukan Anggota </span>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="<?= base_url($prefix . 'buku-tamu/rombongan') ?>">
				<span>Rombongan</span>
			</a>
		</li>
	</ul>

	<?php if (empty($member)) : ?>
		<div class="row">
			<div class="col-lg-4 col-md-6 col-sm-12">
				<form method="get" action="<?= base_url($prefix . 'buku-tamu') ?>">
					<div class="select-wrapper input-group mb-3">
						<input type="text" class="form-control form-control-lg" name="member_no" id="member_no" placeholder="Nomor Angggota">
						<div class="input-group-append">
							<button class="btn btn-shadow btn bg-corporate-primary2 text-white" type="submit"><i class="fa fa-search"></i> Cari</button>
						</div>
					</div>
				</form>
			</div>
			<div class="col-md-12">
				<div class="alert alert-warning alert-dismissible fade show" role="alert">
					<button type="button" class="close" aria-label="Close"><span aria-hidden="true">×</span></button>
					Pilih Nomor Anggota terlebih dahulu.
				</div>
			</div>
		</div>
	<?php else : ?>
		<div class="row">
			<div class="col-lg-12">
				<div class="alert alert-info alert-dismissible fade show" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<b>Selamat Datang <?= $member->Fullname ?></b>
				</div>
			</div>
			<div class="col-lg-12">
				<?= view('Member\Views\member_profile', array('member' => $member ?? '', 'jenis_anggota' => $jenis_anggota ?? [])) ?>
			</div>
			<div class="col-lg-12">
				<form method="post" method="post" action="">
					<div class="select-wrapper input-group mb-3">
						<input type="hidden" name="member_no" value="<?= $member->MemberNo ?>">
						<button class="btn btn-primary btn-lg text-white" type="submit"><i class="fa fa-save"></i> Simpan Buku Tamu</button>
					</div>
				</form>
			</div>
		</div>

	<?php endif; ?>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= $this->endSection('script'); ?>