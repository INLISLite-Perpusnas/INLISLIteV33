<?php
$request = service('request');
$slug = $request->getGet('slug') ?? 'katalog_edit';
$actions = array(
	'cetak-label-a4-1' => 'Cetak Label A4-1 (Barcode + No. Panggil)',
	'cetak-label-a4-2' => 'Cetak Label A4-2 (Barcode + No. Panggil)',
	'cetak-label-a4-3' => 'Cetak Label A4-3 (Barcode + No. Panggil + 1 Warna)',
	'cetak-label-a4-4' => 'Cetak Label A4-4 (Barcode + No. Panggil + 1 Warna)',
	// 'cetak-label-a4-5' => 'Cetak Label A4-4 (Barcode + No. Panggil + 1 Warna)',
	// 'cetak-label-a4-5' => 'Cetak Label A4-4 (Barcode + No. Panggil + 1 Warna)',
	'tampil-opac' => 'Tampilkan di Opac',
	'karantina-eksemplar' => 'Karantina Eksemplar',
);
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
				<div><?= ($is_allowed ? 'Edit' : 'Detail') ?> Katalog
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="breadcrumb-item"><a href="<?= base_url('katalog') ?>">Katalog</a></li>
						<li class="active breadcrumb-item" aria-current="page">Edit</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav mb-3">
		<li class="nav-item">
			<?php if ($catalog->Worksheet_id == 4): ?> 
				<a class="nav-link <?= ($slug == 'katalog_edit') ? 'active' : '' ?>" href="<?= base_url('katalog/edit/' . $catalog->ID . '?rda=0') ?>">
					<span>KATALOG</span>
				</a>
			<?php else: ?>
				<a class="nav-link <?= ($slug == 'katalog_edit') ? 'active' : '' ?>" href="<?= base_url('katalog/edit/' . $catalog->ID ) ?>">
					<span>KATALOG</span>
				</a>
			<?php endif; ?>
		</li>

		<?php foreach (array('eksemplar', 'cover', 'konten_digital') as $group) : ?>
			<li class="nav-item">
				<a class="nav-link <?= ($slug == trim($group)) ? 'active' : '' ?>" href="<?= base_url('katalog/edit/' . $catalog->ID . '?slug=' . $group) ?>">
					<span><?= strtoupper(unslugify($group)) ?></span>
				</a>
			</li>
		<?php endforeach; ?>
    
    <?php if ($catalog->Worksheet_id == 4): ?> 
      <?php foreach (array('edisi_serial', 'artikel', 'konten_digital_artikel') as $group) : ?>
		  	<li class="nav-item">
		  		<a class="nav-link <?= ($slug == trim($group)) ? 'active' : '' ?>" href="<?= base_url('katalog/edit/' . $catalog->ID . '?slug=' . $group) ?>">
		  			<span><?= strtoupper(unslugify($group)) ?></span>
		  		</a>
		  	</li>
		  <?php endforeach; ?>
    <?php endif; ?>
	</ul>

	<form id="frm_edit" class="main-card mb-3 card" method="post" action="">
		<?= $this->include("Katalog\Views\slug\\$slug"); ?>
	</form>
	

	<a href="<?= base_url('katalog') ?>" class="btn btn-secondary btn-lg mb-3"><i class="fa fa-list mr-2"></i> Kembali ke Daftar Katalog</a>
</div>


<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= $this->include('Katalog\Views\add_script'); ?>

<script>
	//Preview pas photo yang di upload peserta
	function previewFile(input) {
		var file = $("input[type=file]").get(0).files[0];
		if (file) {
			var reader = new FileReader();
			reader.onload = function() {
				$("#previewImg").attr("src", reader.result);
			}
			reader.readAsDataURL(file);
		}
	}
	//-------------------------------------------------------------------
</script>


<?= $this->endSection('script'); ?>