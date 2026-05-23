<div class="modal fade" id="modal_create2" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Tambah Libur Panjang
				</h5>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="frm_add2" method="post" action="">
				<div class="modal-body">
					<div id="frm_add_message"></div>
					<div class="form-row">
						<div class="col-lg-6">
							<div class="form-group">
								<label for="Nama">Tanggal Awal</label>
								<div>
									<input required type="date" class="form-control" id="frm_add_StartDates" name="StartDates" placeholder="Tanggal Awal" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="Nama">Tanggal Akhir</label>
								<div>
									<input required type="date" class="form-control" id="frm_add_EndDates" name="EndDates" placeholder="Tanggal Akhir" value="" />
								</div>
							</div>
						</div>
					</div>

					<div class="form-row">
						<div class="col-lg-12">
							<div class="form-group">
								<label for="Nama">Nama Libur</label>
								<div>
									<input required type="text" class="form-control" id="frm_add_Names" name="Names" placeholder="Nama" value="" />
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
					<button type="submit" class="btn btn-primary" name="submit" id="btnAdd">Simpan</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	$('#frm_add2').submit(function(event) {
		event.preventDefault();

		var url = "<?= base_url('api/master-hari-libur/createliburpanjang') ?>";
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
						html: 'Hari Libur berhasil ditambah.',
						type: 'success',
						showConfirmButton: false,
						timer: 5000,
					}).then(() => {
						window.location.href = `<?= base_url('master-hari-libur') ?>`;
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