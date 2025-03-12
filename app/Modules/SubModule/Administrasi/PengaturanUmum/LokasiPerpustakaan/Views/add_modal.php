<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Tambah Lokasi Perpustakaan
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="frm_add" method="post" action="">
				<div class="modal-body">
					<div class="form-row">
						<div class="col-lg-3 col-md-4">
							<div class="form-group">
								<label for="Code">Kode Lokasi</label>
								<div>
									<input required type="text" class="form-control" id="frm_add_Code" name="Code" placeholder="Kode Lokasi" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-9 col-md-8">
							<div class="form-group">
								<label for="Name">Nama Lokasi</label>
								<div>
									<input required type="text" class="form-control" id="frm_add_Name" name="Name" placeholder="Nama Lokasi" value="" />
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="Address">Alamat</label>
						<div>
							<textarea id="frm_add_Address" name="Address" placeholder="Alamat" rows="3" class="form-control autosize-input" style="min-height: 38px;"></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
					<button type="submit" class="btn btn-primary" name="submit" id="btnAdd">Simpan</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	$('#frm_add').submit(function(event) {
		event.preventDefault();

		var url = "<?= base_url('api-lokasi-perpustakaan/create') ?>";
		var data_post = $(this).serializeArray();

		$("#btnAdd").html('<i class="fa fa-spinner fa-spin loading"></i> Mohon menunggu...');
		$("#btnAdd").attr('disabled', true);

		$.ajax({
				url: url,
				type: 'POST',
				data: data_post,
			})
			.done(function(res) {
				console.log(res)

				if (res.error == false) {
					Swal.fire({
						title: 'Berhasil',
						html: 'Lokasi Perpustakaan berhasil ditambah.',
						type: 'success',
						showConfirmButton: false,
						timer: 5000,
					}).then(() => {
						window.location.href = `<?= base_url('master-lokasi-perpustakaan') ?>`;
					});
				} else {
					Swal.fire({
						title: 'Oups',
						text: res.message,
						type: 'error',
						showConfirmButton: false,
						timer: 5000
					}).then(() => {
						$("#btnAdd").attr('disabled', false);
						$("#btnAdd").html('Simpan');
					});
				}
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
					$("#btnAdd").attr('disabled', false);
					$("#btnAdd").html('Simpan');
				});
			});

		return false;
	});

	$('#modal_create').on('hidden.bs.modal', function() {
		$(this).find('form').trigger('reset');
		$('#frm_add_message').html('');
	});
</script>