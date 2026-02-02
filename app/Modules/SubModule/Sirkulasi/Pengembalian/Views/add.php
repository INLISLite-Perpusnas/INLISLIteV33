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
					<div class="page-title-subheading">Pengembalian</div>
				</div>
			</div>
				<div class="page-title-actions">
				<form id="form-pilih-member" method="get" action="<?= base_url('sirkulasi-pengembalian/create') ?>">
					<div class="select-wrapper input-group mb-3">
						<select class="form-control select2" name="member_no" id="member_no"
							style="min-width:360px" onchange="this.form.submit()">
							<option value="">Nomor Anggota</option>
							<?php foreach (get_ref_table('members', 'MemberNo, Fullname', 'MemberNo IS NOT NULL', 'data') as $row) : ?>
								<option value="<?= $row->MemberNo ?>" <?= $member_no == $row->MemberNo ? 'selected' : '' ?>>
									<?= $row->MemberNo ?> <?= $row->Fullname ?>
								</option>
							<?php endforeach; ?>
						</select>
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
		<div class="row mb-3">
			<div class="col-lg-12">
				<?= view('Member\Views\member_profile', array('member' => $member, 'jenis_anggota' => $jenis_anggota)) ?>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<?= view('Peminjaman\Views\loan_table', array('member_no' => $member_no ?? '')) ?>
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

		// Otomatis submit saat pilihan berubah (untuk Select2)
	$('#member_no').on('select2:select', function(e) {
		$('#form-pilih-member').submit();
	});
</script>
<?= $this->endSection('script'); ?>