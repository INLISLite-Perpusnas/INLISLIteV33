<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Tambah Master Kelompok Umur
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_add" method="post" action="">
                <div class="modal-body">
                    <div id="frm_add_message"></div>
					<div class="form-row">
						<div class="col-lg-12">
							<div class="form-group">
								<label for="Nama">Master Kelompok Umur</label>
								<div>
									<input required type="number" class="form-control" id="frm_add_Nama" name="umur1" placeholder="Umur Batas Bawah" value="" /><br>
									<input required type="number" class="form-control" id="frm_add_Nama" name="umur2" placeholder="Umur Batas Atas" value="" /><br>
									<textarea required type="text" class="form-control" id="frm_add_Nama" name="keterangan" placeholder="Keterangan" value="">
								</textarea>

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
    $('#frm_add').submit(function(event) {
		event.preventDefault();

		var url = "<?=base_url('api/master-kelompok-umur/create')?>";
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
					'icon': 'success',
					html: 'Master Kelompok Umur Berhasil Ditambah.',
					type: 'success',
					showConfirmButton: false,
					timer: 5000,
				}).then(() => {
					window.location.href = `<?=base_url('master-kelompok-umur')?>`;
				});
			} else {				
				Swal.fire({
					title: 'Oups',
					icon: 'error',
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