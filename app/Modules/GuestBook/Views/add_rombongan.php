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
						<li class="active breadcrumb-item" aria-current="page">Rombongan</li>
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
			<a class="nav-link" href="<?= base_url($prefix . 'buku-tamu/non_anggota') ?>">
				<span>Bukan Anggota </span>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link active" href="<?= base_url($prefix . 'buku-tamu/rombongan') ?>">
				<span>Rombongan</span>
			</a>
		</li>
	</ul>

	<div class="main-card mb-3 card">
		<div class="card-header">
			<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Buku Tamu - Rombongan
		</div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? ''; ?></div>
			<?= get_message('message'); ?>

			<form id="frm_create" class="col-md-12 mx-auto" method="post" action="">
    <div class="form-row">
        <div class="col-md-12">
            <h5>Instansi/Lembaga</h5>
        </div>
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label for="Nama">Nama Instansi/Lembaga*</label>
                <div>
                    <input type="text" class="form-control" name="AsalInstansi" id="AsalInstansi" placeholder="" value="<?= set_value('AsalInstansi'); ?>" />
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label for="Nama">Nama Ketua Rombongan*</label>
                <div>
                    <input type="text" class="form-control" name="NamaKetua" id="NamaKetua" placeholder="" value="<?= set_value('NamaKetua'); ?>" />
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label for="Nama">Telepon Instansi/Lembaga</label>
                <div>
                    <input type="text" class="form-control" name="TeleponInstansi" id="TeleponInstansi" placeholder="" value="<?= set_value('Nama'); ?>" />
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label for="Nama">Email Instansi/Lembaga</label>
                <div>
                    <input type="text" class="form-control" name="EmailInstansi" id="EmailInstansi" placeholder="" value="<?= set_value('EmailInstansi'); ?>" />
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label for="Nama">Alamat Instansi/Lembaga</label>
                <div>
                    <textarea id="frm_create_description" name="AlamatInstansi" placeholder="AlamatInstansi" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description') ?></textarea>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <hr>
            <h5>Personil</h5>
        </div>
        <div class="col-md-6">
            <div class="position-relative form-group">
                <label for="Nama">Jumlah Rombongan</label>
                <div>
                    <input type="text" class="form-control" name="CountPersonel" id="CountPersonel" placeholder="" value="<?= set_value('CountPersonel'); ?>" readonly />
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="position-relative form-group">
                <label for="Nama">Jumlah Laki-laki</label>
                <div>
                    <input type="text" class="form-control" name="CountLaki" id="CountLaki" placeholder="" value="<?= set_value('CountLaki'); ?>" oninput="updateTotalPersonel()" />
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="position-relative form-group">
                <label for="Nama">Jumlah Perempuan</label>
                <div>
                    <input type="text" class="form-control" name="CountPerempuan" id="CountPerempuan" placeholder="" value="<?= set_value('CountPerempuan'); ?>" oninput="updateTotalPersonel()" />
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <hr>
            <h5>Pekerjaan</h5>
        </div>
        <div class="col-md-3">
            <div class="position-relative form-group">
                <label for="Nama">Jumlah Pelajar</label>
                <div>
                    <input type="text" class="form-control" name="CountPelajar" id="CountPelajar" placeholder="" value="<?= set_value('CountPelajar'); ?>" oninput="updateTotalPersonel()" />
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="position-relative form-group">
                <label for="Nama">Jumlah Guru</label>
                <div>
                    <input type="text" class="form-control" name="CountGuru" id="CountGuru" placeholder="" value="<?= set_value('CountGuru'); ?>" oninput="updateTotalPersonel()" />
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="position-relative form-group">
                <label for="Nama">Jumlah PNS</label>
                <div>
                    <input type="text" class="form-control" name="CountPNS" id="CountPNS" placeholder="" value="<?= set_value('CountPNS'); ?>" oninput="updateTotalPersonel()" />
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="position-relative form-group">
                <label for="Nama">Jumlah Pegawai Swasta</label>
                <div>
                    <input type="text" class="form-control" name="CountPSwasta" id="CountPSwasta" placeholder="" value="<?= set_value('CountPSwasta'); ?>" oninput="updateTotalPersonel()" />
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="position-relative form-group">
                <label for="Nama">Jumlah Peneliti</label>
                <div>
                    <input type="text" class="form-control" name="CountPeneliti" id="CountPeneliti" placeholder="" value="<?= set_value('CountPeneliti'); ?>" oninput="updateTotalPersonel()" />
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="position-relative form-group">
                <label for="Nama">Jumlah Dosen</label>
                <div>
                    <input type="text" class="form-control" name="CountDosen" id="CountDosen" placeholder="" value="<?= set_value('CountDosen'); ?>" oninput="updateTotalPersonel()" />
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="position-relative form-group">
                <label for="Nama">Jumlah Pensiunan</label>
                <div>
                    <input type="text" class="form-control" name="CountPensiunan" id="CountPensiunan" placeholder="" value="<?= set_value('CountPensiunan'); ?>" oninput="updateTotalPersonel()" />
                </div>
            </div>
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
<script>
    function updateTotalPersonel() {
        var countLaki = parseInt(document.getElementById('CountLaki').value) || 0;
        var countPerempuan = parseInt(document.getElementById('CountPerempuan').value) || 0;
        // var countPelajar = parseInt(document.getElementById('CountPelajar').value) || 0;
        // var countGuru = parseInt(document.getElementById('CountGuru').value) || 0;
        // var countPNS = parseInt(document.getElementById('CountPNS').value) || 0;
        // var countPSwasta = parseInt(document.getElementById('CountPSwasta').value) || 0;
        // var countPeneliti = parseInt(document.getElementById('CountPeneliti').value) || 0;
        // var countDosen = parseInt(document.getElementById('CountDosen').value) || 0;
        // var countPensiunan = parseInt(document.getElementById('CountPensiunan').value) || 0;

        var totalPersonel = countLaki + countPerempuan; 

        document.getElementById('CountPersonel').value = totalPersonel;
    }
</script>
<?= $this->endSection('script'); ?>