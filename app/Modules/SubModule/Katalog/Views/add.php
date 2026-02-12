<?php
$request = service('request');
$slug = $request->getGet('slug') ?? 'katalog_add';
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
					<i class="pe-7s-note icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Tambah Katalog
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="breadcrumb-item"><a href="<?= base_url('katalog') ?>">Katalog</a></li>
						<li class="active breadcrumb-item" aria-current="page">Tambah</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<form id="frm_create" class="main-card mb-3 card" method="post" action="<?= base_url('katalog/create'); ?>">
		<?= $this->include("Katalog\Views\slug\\$slug"); ?>
	</form>

	<a href="<?= base_url('katalog') ?>" class="btn btn-secondary btn-lg mb-3"><i class="fa fa-list mr-2"></i> Kembali ke Daftar Katalog</a>
</div>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= $this->include('Katalog\Views\add_script'); ?>
<script>
	const nomorInput = document.getElementById('nomorInput');
	const ddcInput = document.querySelector('input[name="DeweyNo"]');
	const pengarangInput = document.querySelector('input[name="judul[c]"]');
	const judulInput = document.querySelector('input[name="judul[a]"]');

	nomorInput.addEventListener('click', function() {

    const ddcValue = (ddcInput.value || '').trim();
    const pengarangValue = (pengarangInput.value || '').trim();
    const judulValue = (judulInput.value || '').trim();

    const concatenatedValue =
        ddcValue.substring(0, 3).toUpperCase() + " " +
        pengarangValue.substring(0, 3).toUpperCase() + " " +
        judulValue.substring(0, 1).toUpperCase();

    nomorInput.value = concatenatedValue;
});

</script>
<?= $this->endSection('script'); ?>