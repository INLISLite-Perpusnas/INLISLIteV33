<?php
$request = service('request'); ?>

<?= $this->extend('App\Views\layout\main'); ?>
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

<?= $this->section('page'); ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-refresh-2 icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Sirkulasi
					<div class="page-title-subheading">Peminjaman</div>
				</div>
			</div>
			<div class="page-title-actions">
				<form method="get" action="<?= base_url('sirkulasi-peminjaman/create') ?>">
					<div class="select-wrapper input-group mb-3">
						<select class="form-control select2" name="member_no" id="member_no" style="min-width:360px">
							<option value="">Nomor Anggota</option>
							<?php foreach (get_ref_table('members', 'MemberNo, Fullname', 'MemberNo IS NOT NULL', 'data') as $row) : ?>
								<option value="<?= $row->MemberNo ?>" <?= $member_no == $row->MemberNo ? 'selected' : '' ?>><?= $row->MemberNo ?> <?= $row->Fullname ?></option>
							<?php endforeach; ?>
						</select>
						<div class="input-group-append">
							<button class="btn btn-shadow btn bg-corporate-primary2 text-white" type="submit"><i class="fa fa-check-circle"></i> Pilih</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<?php if (empty($member_no)) : ?>
		<div class="row">
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
						<?= view('Peminjaman\Views\cart_form', array('member' => $member ?? '', 'jenis_anggota' => $jenis_anggota ?? [])) ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<div class="col-lg-12">
				<?= view('Peminjaman\Views\loan_table', array('member' => $member ?? '', 'jenis_anggota' => $jenis_anggota ?? [])) ?>
			</div>
		</div>
	<?php endif; ?>


</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
	$('.select2').select2({
		theme: "bootstrap4"
	});
	$('#empty_cart').click(function() {
		var url = "<?= base_url('sirkulasi-peminjaman/cart_destroy') ?>";
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