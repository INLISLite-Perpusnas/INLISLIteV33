<div class="modal fade" id="modal_update" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Edit Pelanggaran
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="frm_update" method="post" data-action="<?= base_url('api/sirkulasi-pelanggaran/edit') ?>" data-id="">
				<div class="modal-body">
					<div id="frm_update_message"></div>
					<div class="form-row">
						<div class="col-lg-6">
							<div class="form-group">
								<label for="pelanggaran">Pelanggaran</label>
								<div>
									<input required type="text" class="form-control" id="frm_update_pelanggaran" name="pelanggaran" placeholder="Pelanggaran" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="MasaBerlakuAnggota">Masa Berlaku</label>
								<div>
									<input required type="text" class="form-control" id="frm_update_MasaBerlakuAnggota" name="MasaBerlakuAnggota" placeholder="Masa Berlaku" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="BiayaPendaftaran">Biaya Pendaftaran</label>
								<div>
									<input required type="text" class="form-control" id="frm_update_BiayaPendaftaran" name="BiayaPendaftaran" placeholder="Biaya Pendaftaran" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="BiayaPerpanjangan">Biaya Perpanjangan</label>
								<div>
									<input required type="text" class="form-control" id="frm_update_BiayaPerpanjangan" name="BiayaPerpanjangan" placeholder="Biaya Perpanjangan" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="MaxLoanDays">Maksimal Pinjam Koleksi</label>
								<div>
									<input required type="text" class="form-control" id="frm_update_MaxLoanDays" name="MaxLoanDays" placeholder="Maksimal Pinjam Koleksi" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="UploadDokumenKeanggotaanOnline">Upload Dokumen Pelanggaran</label>
								<div>
									<select class="form-control" id="frm_update_UploadDokumenKeanggotaanOnline" name="UploadDokumenKeanggotaanOnline">
										<option value="1">Ya</option>
										<option value="0">Tdk</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
					<button type="submit" class="btn btn-primary" name="submit" id="btnUpdate">Simpan</button>
				</div>
			</form>

		</div>
	</div>
</div>

<script>
	$("body").on("click", ".show-data", function() {
		var url = $(this).attr('data-href');
		$.ajax({
			url: url,
			type: 'get',
			dataType: 'json',
			success: function(response) {
				$('#frm_update').attr("data-id", response.id);
				$('#frm_update_pelanggaran').val(response.pelanggaran);
				$('#frm_update_MasaBerlakuAnggota').val(response.MasaBerlakuAnggota);
				$('#frm_update_BiayaPendaftaran').val(response.BiayaPendaftaran);
				$('#frm_update_BiayaPerpanjangan').val(response.BiayaPerpanjangan);
				$('#frm_update_MaxLoanDays').val(response.MaxLoanDays);
				$('#frm_update_UploadDokumenKeanggotaanOnline').val(response.UploadDokumenKeanggotaanOnline);
				$('#modal_update').modal('show');
			}
		});
	});

	$('#modal_update').on('hidden.bs.modal', function() {
		$(this).find('form').trigger('reset');
		$('#frm_update_message').html('');
	});

	$('#frm_update').submit(function(event) {
		event.preventDefault();
		var url = $(this).data('action') + '/' + $(this).data('id');
		var data_post = $(this).serializeArray();

		$("#btnUpdate").html('<i class="fa fa-spinner fa-spin loading"></i> Mohon menunggu...');
		$("#btnUpdate").attr('disabled', true);

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
						html: 'Pelanggaran berhasil disimpan.',
						type: 'success',
						showConfirmButton: false,
						timer: 5000,
					}).then(() => {
						window.location.href = `<?= base_url('pelanggaran') ?>`;
					});
				} else {
					Swal.fire({
						title: 'Oups',
						text: res.message,
						type: 'error',
						showConfirmButton: false,
						timer: 5000
					}).then(() => {
						$("#btnUpdate").attr('disabled', false);
						$("#btnUpdate").html('Simpan');
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
					$("#btnUpdate").attr('disabled', false);
					$("#btnUpdate").html('Simpan');
				});
			});

		return false;
	});
</script>