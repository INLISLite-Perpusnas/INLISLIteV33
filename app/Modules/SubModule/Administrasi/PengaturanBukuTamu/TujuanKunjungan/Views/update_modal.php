<div class="modal fade" id="modal_update" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Edit Tujuan Kunjungan
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="frm_update" method="post" data-action="<?= base_url('api/tujuan-kunjungan/edit') ?>" data-id="">
				<div class="modal-body">
					<div id="frm_create_message"></div>
					<div class="form-row">
						<div class="col-lg-12">
							<div class="form-group">
								<label for="Name">Kode</label>
								<div>
									<input required type="text" class="form-control" id="frm_update_Code" name="Code" placeholder="Code" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-12">
							<label>Tujuan Kunjungan</label>
							<div>
								<input required type="text" class="form-control" id="frm_update_TujuanKunjungan" name="TujuanKunjungan" placeholder="TujuanKunjungan" value="" />
							</div>
						</div>
						<div class="col-lg-12">
							<label for="frm_update_Membership">Membership</label><br>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="frm_update_Member" name="Member">
								<label class="form-check-label" for="frm_update_Member">Member</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="frm_update_NonMember" name="NonMember">
								<label class="form-check-label" for="frm_update_NonMember">NonMember</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="frm_update_Rombongan" name="Rombongan">
								<label class="form-check-label" for="frm_update_Rombongan">Rombongan</label>
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
				$('#frm_update').attr("data-id", response.ID);
				$('#frm_update_Code').val(response.Code); // Change frm_add_Code to frm_update_Code
				$('#frm_update_TujuanKunjungan').val(response.TujuanKunjungan); // Change frm_add_TujuanKunjungan to frm_update_TujuanKunjungan
				// Update the checkboxes based on the response
				if (response.Member) {
					$('#frm_update_Member').prop('checked', true);
				} else {
					$('#frm_update_Member').prop('checked', false);
				}
				if (response.NonMember) {
					$('#frm_update_NonMember').prop('checked', true);
				} else {
					$('#frm_update_NonMember').prop('checked', false);
				}
				if (response.Rombongan) {
					$('#frm_update_Rombongan').prop('checked', true);
				} else {
					$('#frm_update_Rombongan').prop('checked', false);
				}
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
						html: 'Tujuan Kunjungan berhasil diubah.',
						type: 'success',
						showConfirmButton: false,
						timer: 5000,
					}).then(() => {
						window.location.href = `<?= base_url('master-tujuan-kunjungan') ?>`;
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