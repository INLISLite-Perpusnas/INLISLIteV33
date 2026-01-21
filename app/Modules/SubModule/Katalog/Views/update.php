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
<style>
.btn-header-argon {
    margin-left: 8px; /* jarak antar tombol */
}
.btn-header-argon.active {
    color: #182d52;
    font-weight: 600;
}

.btn-header-argon.active::after {
    content: "";
    position: absolute;
    left: 10px;
    right: 10px;
    bottom: -4px;
    height: 3px;
    background: #5e72e4;
    border-radius: 3px;
}
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">


	<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav mb-3 flex-nowrap">
		<li class="nav-item">
	<div class="d-flex flex-wrap gap-2 mb-3">

    <!-- KATALOG -->
    <?php if ($catalog->Worksheet_id == 4): ?> 
        <a href="<?= base_url('katalog/edit/' . $catalog->ID . '?rda=0') ?>"
           class="btn-header-argon <?= ($slug == 'katalog_edit') ? 'active' : '' ?>">
            <span>KATALOG</span>
        </a>
    <?php else: ?>
        <a style="padding-left: 10px;" href="<?= base_url('katalog/edit/' . $catalog->ID) ?>"
           class="btn-header-argon <?= ($slug == 'katalog_edit') ? 'active' : '' ?>">
            <span>KATALOG</span>
        </a>
    <?php endif; ?>

    <!-- TAB UMUM -->
    <?php foreach (['eksemplar', 'cover', 'konten_digital'] as $group): ?>
        <a href="<?= base_url('katalog/edit/' . $catalog->ID . '?slug=' . $group) ?>"
           class="btn-header-argon <?= ($slug == $group) ? 'active' : '' ?>">
            <span><?= strtoupper(unslugify($group)) ?></span>
        </a>
    <?php endforeach; ?>

    <!-- TAB KHUSUS WORKSHEET 4 -->
    <?php if ($catalog->Worksheet_id == 4): ?>
        <?php foreach (['edisi_serial', 'artikel', 'konten_digital_artikel'] as $group): ?>
            <a href="<?= base_url('katalog/edit/' . $catalog->ID . '?slug=' . $group) ?>"
               class="btn-header-argon <?= ($slug == $group) ? 'active' : '' ?>">
                <span><?= strtoupper(unslugify($group)) ?></span>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

	</ul>

  <?php if ($slug !== 'edisi_serial'): ?>
    <form id="frm_edit" class="main-card mb-3 card" method="post" action="">
      <?= $this->include("Katalog\Views\slug\\$slug"); ?>
    </form>
  <?php else: ?>
    <div id="frm_edit">
      <?= $this->include("Katalog\Views\slug\\$slug"); ?>
    </div>
  <?php endif; ?>

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