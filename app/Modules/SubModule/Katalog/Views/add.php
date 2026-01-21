<?php
$request = service('request');
$slug = $request->getGet('slug') ?? 'katalog_add';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">


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
		const ddcValue = ddcInput.value;

		const pengarangValue = pengarangInput.value;
		const judulValue = judulInput.value;

		const concatenatedValue = ddcValue.substring(0, 3).toUpperCase() + " " + pengarangValue.substring(0, 3) + " " + judulValue.substring(0, 1);
		nomorInput.value = concatenatedValue;
	});
</script>
<?= $this->endSection('script'); ?>