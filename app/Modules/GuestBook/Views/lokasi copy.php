<?php
$request = \Config\Services::request();
$request->getUri()->setSilent();
$slug    = $request->getGet('slug') ?? '';
?>
<?= $this->extend('App\Views\layout\opac\layout'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('content'); ?>
<!-- Start Page Title Area -->
<div class="page-title-area">
	<div class="container">
		<div class="page-title-content">
			<h2>Pengaturan Lokasi Ruang</h2>
			<?= (isset($branch->Name) ? ('<h3>' . $branch->Name . '</h3>') : '') ?>
		</div>
	</div>
</div>
<!-- End Page Title Area -->

<!-- Start Content -->
<section class="main-contact-area contact-info-area contact-info-three offer-area" style="background-color: #ffffff;">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="contact-wrap contact-pages mb-0">
					<div class="contact-form contact-form-mb">
						<form id="frm_register" method="post">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label>Kode Perpustakaan</label>
										<input type="text" name="Code" id="Code" class="form-control" required data-error="Kode kosong" placeholder="">
										<small class="help-block with-errors">Contoh: ABC1230101 </small>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>&nbsp;</label>
										<button type="button" class="default-btn" id="btnCheck" style="margin-left:0; background-color:#336899;">Cek Kode</button>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label>Mitra Perpustakaan</label>
										<input type="text" name="Branch" id="Branch" class="form-control">
										<input type="hidden" name="Branch_id" id="Branch_id" value="<?= $branch->ID ?? '' ?>" class="form-control">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Lokasi Perpustakaan</label>
										<input type="text" name="LocationLibrary" id="LocationLibrary" class="form-control">
										<input type="hidden" name="LocationLibrary_id" id="LocationLibrary_id" class="form-control">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Lokasi Ruang</label>
										<input type="text" name="Location" id="Location" class="form-control">
										<input type="hidden" name="Location_id" id="Location_id" class="form-control">
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="reset" class="default-btn" style="margin-left:0; background-color:#337788;">Reset Form</button>
								<button type="submit" class="default-btn disabled" style="margin-right:0; background-color:#04063C;">Simpan Pengaturan</button>
							</div>
						</form>
					</div>
				</div>
				<div>
				</div>
			</div>
			
</section>
<!-- End Content -->

<?= $this->endsection(); ?>

<?= $this->section('script'); ?>
<script>
	$("#btnCheck").click(function() {
		$("#btnCheck").html('<i class="fa fa-spinner fa-spin loading"></i> Mohon menunggu...');
		$("#btnCheck").attr('disabled', true);

		var url = "<?= base_url('api/lokasi-ruang/check') ?>";
		var code = $("#Code").val();

		$.ajax({
				url: `${url}/${code}`,
			})
			.done(function(res) {
				$('#Branch').val(res.Branch_name);
				$('#Branch_id').val(res.Branch_id);

				$('#LocationLibrary').val(res.LocationLibrary_name);
				$('#LocationLibrary_id').val(res.LocationLibrary_id);

				$('#Location').val(res.Name);
				$('#Location_id').val(res.ID);

				$("#btnCheck").attr('disabled', false);
				$("#btnCheck").html('Cek Kode');
			})
			.fail(function(res) {
				console.log(res);

				Swal.fire({
					title: 'Oups',
					text: 'Maaf, terjadi kesalahan. Coba beberapa saat lagi atau hubungi Admin',
					type: 'error',
					showConfirmButton: false,
					timer: 5000
				}).then(() => {
					$("#btnCheck").attr('disabled', false);
					$("#btnCheck").html('Cek Kode');
				});
			});

		return false;
	});

	$("#frm_register").submit(function(e) {
    e.preventDefault(); // Prevent the form from submitting the traditional way

    var locationId = $("#Location_id").val();

    // Simpan Location_id sebagai cookie (misal expired dalam 7 hari)
    document.cookie = "Location_id=" + locationId + "; path=/; expires=" + new Date(new Date().getTime() + 7*24*60*60*1000).toUTCString();

    // Redirect ke halaman /buku-tamu
	window.location.href = "<?= base_url('/buku-tamu') ?>";


    return false;
});
</script>
<?= $this->endsection(); ?>