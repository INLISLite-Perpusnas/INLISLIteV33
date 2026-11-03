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
/* Tombol tab edit katalog (KATALOG, EKSEMPLAR, COVER, ...): biru #2d659e,
   tab aktif berwarna penuh, tab lain bergaris biru */
.catalog-edit-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 20px;
}

.catalog-edit-tabs .btn-header-argon {
    display: inline-flex;
    align-items: center;
    height: 40px;
    margin: 0;
    padding: 0 18px !important;
    font-size: 14px;
    font-weight: 600;
    color: #2d659e;
    text-decoration: none;
    background: #fff;
    border: 1px solid #2d659e;
    border-radius: 8px;
    transform: none;
    transition: background-color .15s ease, color .15s ease;
}

.catalog-edit-tabs .btn-header-argon:hover {
    color: #2d659e;
    background: #eaf1f8;
    border-color: #2d659e;
    transform: none;
}

.catalog-edit-tabs .btn-header-argon.active,
.catalog-edit-tabs .btn-header-argon.active:hover {
    color: #fff;
    background: #2d659e;
}

/* Tombol "Tambah File" di tab Cover & Konten Digital */
.content-wrapper .btn.btn-catalog-add {
    color: #fff;
    background-color: #2d659e;
    border-color: #2d659e;
}

.content-wrapper .btn.btn-catalog-add:hover {
    color: #fff;
    background-color: #24527f;
    border-color: #24527f;
}

.content-wrapper .btn.btn-catalog-add:focus-visible {
    outline: 2px solid #2d659e;
    outline-offset: 2px;
}

.catalog-edit-tabs .btn-header-argon:focus-visible {
    outline: 2px solid #2d659e;
    outline-offset: 2px;
}
</style>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">


	<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav mb-3 flex-nowrap">
		<li class="nav-item">
	<div class="catalog-edit-tabs">

    <!-- KATALOG -->
    <?php if ($catalog->Worksheet_id == 4): ?> 
        <a href="<?= base_url('katalog/edit/' . $catalog->ID . '?rda=0') ?>"
           class="btn-header-argon <?= ($slug == 'katalog_edit') ? 'active' : '' ?>">
            <span>KATALOG</span>
        </a>
    <?php else: ?>
        <a href="<?= base_url('katalog/edit/' . $catalog->ID) ?>"
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
    <form id="frm_edit" class="main-card mb-3 card" method="post" action="<?= base_url('katalog/edit/' . $catalog->ID) ?>">
        <?= csrf_field() ?>
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
    $(document).ready(function() {
        <?php if (session()->getFlashdata('swal_icon')) : ?>
            Swal.fire({
                type: '<?= session()->getFlashdata('swal_icon') ?>', // gunakan 'icon' jika SweetAlert2 versi terbaru
                title: '<?= session()->getFlashdata('swal_title') ?>',
                html: '<?= session()->getFlashdata('swal_html') ?? session()->getFlashdata('swal_text') ?>',
                showConfirmButton: false,
                timer: 3000,
                icon:'success'
            });
        <?php endif; ?>
    });
</script>

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

<script>
    const nomorInput = document.querySelector('input[name="CallNumber[]"]');
    const ddcInput = document.querySelector('input[name="DeweyNo"]');
    const pengarangInput = document.querySelector('input[name="judul[c]"]');
    const judulInput = document.querySelector('input[name="judul[a]"]');

    if (nomorInput && ddcInput && pengarangInput && judulInput) {
        nomorInput.addEventListener('click', function() {
            const ddcValue = (ddcInput.value || '').trim();
            const pengarangValue = (pengarangInput.value || '').trim();
            const judulValue = (judulInput.value || '').trim();

            const concatenatedValue =
                ddcValue + " " +
                pengarangValue.substring(0, 3).toUpperCase() + " " +
                judulValue.substring(0, 1).toLowerCase();

            nomorInput.value = concatenatedValue;
        });
    }
</script>

<?= $this->endSection('script'); ?>