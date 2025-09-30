<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Tambah Jenis Bahan
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="frm_add" method="post" action="<?= base_url('api/master-jenis-bahan/create') ?>">
				<div class="modal-body">
					<div id="frm_add_message"></div>

					<div class="form-row">
						<div class="col-lg-12 col-md-12">
							<div class="form-group">
								<label for="frm_add_Name">Jenis Bahan</label>
								<input required type="text" class="form-control" id="frm_add_Name" name="Name" placeholder="Jenis Bahan" />
							</div>
						</div>

						<div class="col-lg-6 col-md-6">
							<div class="form-group">
								<label for="frm_add_MaxPinjamKoleksi">Maks Koleksi Dapat Dipinjam</label>
								<input required type="number" min="0" class="form-control" id="frm_add_MaxPinjamKoleksi" name="MaxPinjamKoleksi" placeholder="Maks Koleksi" />
							</div>
						</div>
						<div class="col-lg-6 col-md-6">
							<div class="form-group">
								<label for="frm_add_MaxLoanDays">Maks Lama Pinjam</label>
								<input required type="number" min="0" class="form-control" id="frm_add_MaxLoanDays" name="MaxLoanDays" placeholder="Maks. Lama Pinjam" />
							</div>
						</div>
					</div>

					<div class="form-row">
						<div class="col-lg-6 col-md-6">
							<div class="form-group">
								<label for="frm_add_DayPerpanjang">Maks Lama Perpanjangan</label>
								<input required type="number" min="0" class="form-control" id="frm_add_DayPerpanjang" name="DayPerpanjang" placeholder="Maks Lama Perpanjangan" />
							</div>
						</div>
						<div class="col-lg-6 col-md-6">
							<div class="form-group">
								<label for="frm_add_CountPerpanjang">Maks Banyaknya Perpanjang</label>
								<input required type="number" min="0" class="form-control" id="frm_add_CountPerpanjang" name="CountPerpanjang" placeholder="Jumlah Maks Perpanjang" />
							</div>
						</div>
					</div>

					<div class="form-row">
						<div class="col-lg-6 col-md-6">
							<div class="form-group">
								<label for="frm_add_DendaType">Denda</label>
								<select required class="form-control" id="frm_add_DendaType" name="DendaType">
									<option value="">-- Pilih Denda --</option>
									<option value="Konstan">Konstan</option>
									<option value="Berkelipatan">Berkelipatan</option>
								</select>
							</div>
						</div>
						<div class="col-lg-6 col-md-6">
							<div class="form-group">
								<label for="frm_add_DendaPerTenor">Jumlah Denda</label>
								<input required type="number" min="0" class="form-control" id="frm_add_DendaPerTenor" name="DendaPerTenor" placeholder="Jumlah Denda" />
							</div>
						</div>
					</div>

					<div class="form-row">
						<div class="col-lg-6 col-md-6">
							<div class="form-group">
								<label for="frm_add_SuspendType">Suspend</label>
								<select required class="form-control" id="frm_add_SuspendType" name="SuspendType">
									<option value="">-- Pilih Suspend --</option>
									<option value="Konstan">Konstan</option>
									<option value="Berkelipatan">Berkelipatan</option>
								</select>
							</div>
						</div>
						<div class="col-lg-6 col-md-6">
							<div class="form-group">
								<label for="frm_add_DaySuspend">Lama Skorsing</label>
								<input required type="number" min="0" class="form-control" id="frm_add_DaySuspend" name="DaySuspend" placeholder="Lama Skorsing" />
							</div>
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
					<button type="submit" class="btn btn-primary" id="btnAdd">Simpan</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	$('#frm_add').submit(function(event) {
		event.preventDefault();
		var url = $(this).attr("action");
		var data_post = $(this).serializeArray();

		$("#btnAdd").html('<i class="fa fa-spinner fa-spin loading"></i> Mohon menunggu...');
		$("#btnAdd").attr('disabled', true);

		$.ajax({
				url: url,
				type: 'POST',
				data: data_post,
				dataType: 'json',
			})
			.done(function(res) {
				if (res.error == false) {
					Swal.fire({
						title: 'Berhasil',
						html: 'Jenis Bahan berhasil ditambah.',
						icon: 'success',
						showConfirmButton: false,
						timer: 5000,
					}).then(() => {
						window.location.href = `<?= base_url('master-jenis-bahan') ?>`;
					});
				} else {
					Swal.fire({
						title: 'Oups',
						text: res.message,
						icon: 'error',
						showConfirmButton: false,
						timer: 5000
					}).then(() => {
						$("#btnAdd").attr('disabled', false).html('Simpan');
					});
				}
			})
			.fail(function() {
				Swal.fire({
					title: 'Oups',
					text: 'Maaf, terjadi kesalahan. Coba beberapa saat lagi atau hubungi Admin',
					icon: 'error',
					showConfirmButton: false,
					timer: 5000
				}).then(() => {
					$("#btnAdd").attr('disabled', false).html('Simpan');
				});
			});
		return false;
	});

	$('#modal_create').on('hidden.bs.modal', function() {
		$(this).find('form').trigger('reset');
		$('#frm_add_message').html('');
	});
</script>
