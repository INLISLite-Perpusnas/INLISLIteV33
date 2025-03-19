<div class="modal fade" id="modal_update" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Edit Mitra Perpustakaan
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_update" method="post" data-action="<?= base_url('api-mitra-perpustakaan/edit') ?>" data-id="">
				<div class="modal-body">
                    <div id="frm_create_message"></div>
					<div class="form-row">
						<div class="col-lg-3 col-md-4">
							<div class="form-group">
								<label for="Code">Kode Lokasi</label>
								<div>
									<input required type="text" class="form-control" id="frm_update_Code" name="Code" placeholder="Kode Lokasi" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-9 col-md-8">
							<div class="form-group">
								<label for="Name">Nama Lokasi</label>
								<div>
									<input required type="text" class="form-control" id="frm_update_Name" name="Name" placeholder="Nama Lokasi" value="" />
								</div>
							</div>
						</div>
					</div>

                    <div class="form-group">
                        <label for="Address">Alamat</label>
                        <div>
                            <textarea id="frm_update_Address" name="Address" placeholder="Alamat" rows="3" class="form-control autosize-input" style="min-height: 38px;"></textarea>
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
                $('#frm_update').attr("data-id", response.ID);
                $('#frm_update_Code').val(response.Code);
                $('#frm_update_Name').val(response.Name);
                $('#frm_update_Address').val(response.Address);
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
					html: 'Mitra Perpustakaan berhasil disimpan.',
					type: 'success',
					showConfirmButton: false,
					timer: 5000,
				}).then(() => {
					window.location.href = `<?=base_url('mitra-perpustakaan')?>`;
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