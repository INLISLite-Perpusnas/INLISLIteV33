<div class="modal fade" id="modal_update" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Edit Jenis Bahan
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="frm_update" method="post" data-action="<?= base_url('api/master-jenis-bahan/edit') ?>" data-ID="">
				<div class="modal-body">
					<div id="frm_create_message"></div>
					<div class="form-row">
						<div class="col-lg-12 col-md-12">
							<div class="form-group">
								<label for="Name">Jenis Bahan</label>
								<div>
									<input required type="text" class="form-control" id="frm_update_Name" name="Name" placeholder="Jenis Bahan" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6">
							<div class="form-group">
								<label for="code">Maks Koleksi Dapat Dipinjam</label>
								<div>
									<input required type="number" min="0" class="form-control" id="frm_update_MaxPinjamKoleksi" name="MaxPinjamKoleksi" placeholder="Kode Jenis Bahan" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6">
							<div class="form-group">
								<label for="name">Maks Lama Pinjam</label>
								<div>
									<input required type="number" min="0" class="form-control" id="frm_update_MaxLoanDays" name="MaxLoanDays" placeholder="Maks. Lama Pinjam" value="" />
								</div>
							</div>
						</div>
					</div>
					<div class="form-row">
						<div class="col-lg-6 col-md-6">
							<div class="form-group">
								<label for="code">Maks Lama Perpanjangan</label>
								<div>
									<input required type="number" min="0" class="form-control" id="frm_update_DayPerpanjang" name="DayPerpanjang" placeholder="Kode Jenis Bahan" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6">
							<div class="form-group">
								<label for="name">Maks Banyaknya perpanjang</label>
								<div>
									<input required type="number" min="0" class="form-control" id="frm_update_CountPerpanjang" name="CountPerpanjang" placeholder="Maks. Lama Pinjam" value="" />
								</div>
							</div>
						</div>
					</div>

					<div class="form-row">
						<div class="col-lg-6 col-md-6">
							<div class="form-group">
								<label for="code">Denda</label>
								<div>
									<select required class="form-control" id="frm_update_DendaType" name="DendaType" placeholder="Denda Type" value="">
										<option value="">-- Pilih Denda --</option>
										<option value="Konstan">Konstan</option>
										<option value="Berkelipatan">Berkelipatan</option>
									</select>
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6">
							<div class="form-group">
								<label for="name">Jumlah Denda</label>
								<div>
									<input required type="number" min="0" class="form-control" id="frm_update_DendaPerTenor" name="DendaPerTenor" placeholder="Jumlah Denda" value="" />
								</div>
							</div>
						</div>
					</div>

					<div class="form-row">
						<div class="col-lg-6 col-md-6">
							<div class="form-group">
								<label for="code">Suspend</label>
								<div>
									<select required class="form-control" id="frm_update_SuspendType" name="SuspendType" placeholder="Suspend Type" value="">
										<option value="">-- Pilih Denda --</option>
										<option value="Konstan">Konstan</option>
										<option value="Berkelipatan">Berkelipatan</option>
									</select>
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6">
							<div class="form-group">
								<label for="name">Lama Skorsing</label>
								<div>
									<input required type="number" min="0" class="form-control" id="frm_update_DaySuspend" name="DaySuspend" placeholder="Lama Skorsing" value="" />
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
				$('#frm_update').attr("data-ID", response.ID);
				$('#frm_update_Name').val(response.Name);
				$('#frm_update_MaxPinjamKoleksi').val(response.MaxPinjamKoleksi);
				$('#frm_update_MaxLoanDays').val(response.MaxLoanDays);
				$('#frm_update_DayPerpanjang').val(response.DayPerpanjang);
				$('#frm_update_CountPerpanjang').val(response.CountPerpanjang);
				$('#frm_update_DendaType').val(response.DendaType);
				$('#frm_update_DendaPerTenor').val(response.DendaPerTenor);
				$('#frm_update_SuspendType').val(response.SuspendType);
				$('#frm_update_DaySuspend').val(response.DaySuspend);
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
						html: 'Jenis Bahan berhasil disimpan.',
						type: 'success',
						showConfirmButton: false,
						timer: 5000,
					}).then(() => {
						window.location.href = `<?= base_url('master-jenis-bahan') ?>`;
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