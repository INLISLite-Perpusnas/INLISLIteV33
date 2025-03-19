<div class="modal fade" id="modal_update" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Edit Master fakultas
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_update" method="post" data-action="<?= base_url('api/jurusan/edit') ?>" data-id="">
				<div class="modal-body">
                    <div id="frm_create_message"></div>

					<div class="form-row">
						<div class="col-lg-12">
							<div class="form-group">
								<label for="Nama">Nama Fakultas</label>
								<div>
								<select required name="id_fakultas" id="frm_update_id_fakultas" class="form-control">
										<option value="">Pilih Fakultas</option>
										<?php foreach (get_ref_table('master_fakultas', 'id, Nama',null,'data') as $row): ?>
								           <option data-date="<?= $row->Nama ?>" value="<?=$row->id?>" <?=set_select('id_fakultas',$row->id)?>><?=$row->Nama?></option>
							                <?php endforeach;?>

									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="form-row">
						<div class="col-lg-12">
							<div class="form-group">
								<label for="Nama">Nama Jursan</label>
								<div>
									<input required type="text" class="form-control" id="frm_update_Nama" name="Nama" placeholder="Master fakultas" value="" />
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
                $('#frm_update_Nama').val(response.Nama);
                $('#frm_update_id_fakultas').val(response.id_fakultas);
                $('#frm_update_address').val(response.address);
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
					html: 'Fakultas siswa berhasil disimpan.',
					type: 'success',
					showConfirmButton: false,
					timer: 5000,
				}).then(() => {
					window.location.href = `<?=base_url('master-fakultas')?>`;
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