<div class="modal fade" id="modal_update" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Edit Master Kelompok Umur
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_update" method="post" data-action="<?= base_url('api/master-kelompok-umur/edit') ?>" data-id="">
				<div class="modal-body">
                    <div id="frm_create_message"></div>
					<div class="form-row">
						<div class="col-lg-12">
							<div class="form-group">
								<label for="Nama">Master kelompok umur</label>
								<div>
									<input required type="text" class="form-control" id="frm_update_Umur1" name="umur1" placeholder="Umur Batas Bawah" value="" /><br>
									<input required type="text" class="form-control" id="frm_update_Umur2" name="umur2" placeholder="Umur Batas Atas" value="" /><br>
									<textarea required type="text" class="form-control" id="frm_update_Keterangan" name="Keterangan" placeholder="Keterangan" value=""></textarea>
								</div>
							</div>
						</div>
					</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
				$('#frm_update_Umur1').val(response.umur1);
				$('#frm_update_Umur2').val(response.umur2);
				$('#frm_update_Keterangan').val(response.Keterangan);
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
					'icon': 'success',
					html: 'Master Kelompok Umur berhasil diubah.',
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