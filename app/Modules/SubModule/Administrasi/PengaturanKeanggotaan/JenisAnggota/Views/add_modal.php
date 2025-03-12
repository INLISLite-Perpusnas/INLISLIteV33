<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Tambah Jenis Anggota
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_add" method="post" action="">
                <div class="modal-body">
                    <div id="frm_add_message"></div>
					<div class="form-row">
					<div class="col-lg-6">
							<div class="form-group">
								<label for="jenisanggota">Jenis Anggota</label>
								<div>
									<input required type="text" class="form-control" id="frm_add_jenisanggota" name="jenisanggota" placeholder="Jenis Anggota" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="MasaBerlakuAnggota">Masa Berlaku</label>
								<div>
									<input required type="text" class="form-control" id="frm_add_MasaBerlakuAnggota" name="MasaBerlakuAnggota" placeholder="Masa Berlaku" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="BiayaPendaftaran">Biaya Pendaftaran</label>
								<div>
									<input required type="text" class="form-control" id="frm_add_BiayaPendaftaran" name="BiayaPendaftaran" placeholder="Biaya Pendaftaran" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="BiayaPerpanjangan">Biaya Perpanjangan</label>
								<div>
									<input required type="text" class="form-control" id="frm_add_BiayaPerpanjangan" name="BiayaPerpanjangan" placeholder="Biaya Perpanjangan" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="MaxLoanDays">Maksimal Pinjam Koleksi</label>
								<div>
									<input required type="text" class="form-control" id="frm_add_MaxLoanDays" name="MaxLoanDays" placeholder="Maksimal Pinjam Koleksi" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="UploadDokumenKeanggotaanOnline">Upload Dokumen Peminjaman</label>
								<div>
									<select class="form-control" id="frm_add_UploadDokumenKeanggotaanOnline" name="UploadDokumenKeanggotaanOnline">
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
                    <button type="submit" class="btn btn-primary" name="submit" id="btnAdd">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#frm_add').submit(function(event) {
		event.preventDefault();

		var url = "<?=base_url('api/jenis-anggota/create')?>";
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
					html: 'Jenis Anggota berhasil ditambah.',
					type: 'success',
					showConfirmButton: false,
					timer: 5000,
				}).then(() => {
					window.location.href = `<?=base_url('jenis-anggota')?>`;
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