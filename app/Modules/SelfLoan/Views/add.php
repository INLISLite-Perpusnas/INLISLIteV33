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
				<div><b>Peminjaman Mandiri</b>
					<div class="page-title-subheading font-weight-bold"><?= $kode_lokasi ?> - <?= $lokasi_ruang ?></div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url() ?>"><i class="fa fa-home"></i> Beranda</a></li>
						<li class="active breadcrumb-item" aria-current="page">Peminjaman Mandiri</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-4 col-md-6 col-sm-12">
			<form method="get" action="<?= base_url($prefix . '/peminjaman-mandiri') ?>">
				<div class="select-wrapper input-group mb-3">
					<input type="text" class="form-control form-control-lg" name="member_no" id="member_no" placeholder="Nomor Angggota">
					<div class="input-group-append">
						<button class="btn btn-shadow btn bg-corporate-primary2 text-white" type="submit"><i class="fa fa-search"></i> Cari</button>
					</div>
				</div>
			</form>
		</div>
		<?php if (empty($member_no)) : ?>
			<div class="col-md-12">
				<div class="alert alert-warning alert-dismissible fade show" role="alert">
					<button type="button" class="close" aria-label="Close"><span aria-hidden="true">×</span></button>
					Pilih Nomor Anggota terlebih dahulu.
				</div>
			</div>
		<?php endif; ?>
	</div>

	<?php if (!empty($member_no)) : ?>
		<div class="row">
			<div class="col-lg-12">
				<?= view('Member\Views\member_profile', array('member' => $member ?? '', 'jenis_anggota' => $jenis_anggota ?? [])) ?>
			</div>
		</div>

		<div class="row">
			<?php $today = new \DateTime(); ?>
			<?php $end_date = new \DateTime($member->EndDate); ?>
			<?php if ($today > $end_date) : ?>
				<div class="col-lg-12">
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<b>Tanggal berlaku keanggotaan anda sudah berakhir.</b>
					</div>
				</div>
			<?php else : ?>
				<?php if ($loan_count >= $loan_limit) : ?>
					<div class="col-lg-12">
						<div class="alert alert-warning alert-dismissible fade show" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<b>Total Daftar Peminjaman sudah mencapai Limit Jumlah Peminjaman.</b>
						</div>
					</div>
				<?php else : ?>
					<div class="col-lg-12">
						<?= view('SelfLoan\Views\cart_form', array('member' => $member ?? '', 'jenis_anggota' => $jenis_anggota ?? [])) ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<div class="col-lg-12">
				<?= view('SelfLoan\Views\loan_table', array('member' => $member ?? '', 'jenis_anggota' => $jenis_anggota ?? [])) ?>
			</div>
		</div>
	<?php endif; ?>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
	$('#empty_cart').click(function() {
		var url = "<?= base_url('sirkulasi/peminjaman/cart_destroy') ?>";
		console.log(url);

		Swal.fire({
			title: 'Anda yakin?',
			html: "Semua koleksi akan dihapus <br>dari Troli Peminjaman",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#dd6b55',
			confirmButtonText: '<?= lang('App.btn.yes') ?>',
			cancelButtonText: '<?= lang('App.btn.no') ?>'
		}).then((result) => {
			if (result.value) {
				window.location.href = url;
			}
		});
		return false;
	});
</script>
<?= $this->endSection('script'); ?>