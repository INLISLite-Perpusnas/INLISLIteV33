<?php
$request = service('request');
$slug = $request->getGet('slug') ?? 'profile';
$slug_title = ucwords(strtolower($slug));
$label = $slug_title;

$label = $slug_title;

if ($slug == 'profile') {
	$label = 'Profil';
	$label_title = 'Profil Saya';
}

if ($slug == 'loan') {
	$label = 'Peminjaman';
	$label_title = 'Daftar Peminjaman';
}

if ($slug == 'reserve') {
	$label = 'Pemesanan';
	$label_title = 'Daftar Pemesanan';
}

if ($slug == 'request') {
	$label = 'Usulan';
	$label_title = 'Daftar Usulan';
}

if ($slug == 'extend') {
	$label = 'Perpanjangan';
	$label_title = 'Daftar Perpanjangan';
}

$member_no = user()->username;
$member = get_member($member_no);
$anggota = get_member($member_no);
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>

<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-user icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Keanggotaan
					<div class="page-title-subheading"><?= $label_title ?></div>
				</div>
			</div>
			<div class="page-title-actions">
				<?= view('Anggota\Views\online\nav_bread', array('slug' => $slug, 'slug_title' => $slug_title, 'label' => $label, 'label_title' => $label_title)) ?>
			</div>
		</div>
	</div>

	<?= view('Anggota\Views\online\nav_list', array('member_no' => $member_no, 'slug' => $slug, 'slug_title' => $slug_title, 'label' => $label, 'label_title' => $label_title)) ?>

	<div class="row">
		<div class="col-lg-3 d-none d-lg-block">
			<?= view('Anggota\Views\online\member_profile', array('member' => $member)) ?>
		</div>
		<div class="col-lg-9">
			<?= view('Anggota\Views\online\section\\' . $slug, array('anggota' => $anggota, 'is_anggota' => true, 'member' => $member, 'member_no' => $member_no, 'slug' => $slug, 'slug_title' => $slug_title, 'label' => $label, 'label_title' => $label_title)) ?>
		</div>
	</div>
</div>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= view('Anggota\Views\online\section\\' . $slug . '_script', array('member' => $member, 'slug' => $slug, 'slug_title' => $slug_title, 'label' => $label, 'label_title' => $label_title)) ?>
<?= $this->endSection('script'); ?>