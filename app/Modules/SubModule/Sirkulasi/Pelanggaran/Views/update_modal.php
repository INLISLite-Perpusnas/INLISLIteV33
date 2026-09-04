<div class="modal fade" id="modal_update" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">

			<!-- Header -->
			<div class="modal-header bg-danger text-white">
				<h5 class="modal-title text-white">
					<i class="fa fa-pencil-square-o"></i> Edit Data Pelanggaran
				</h5>
				<button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<form id="frm_update" method="post" data-action="<?= base_url('api/sirkulasi-pelanggaran/edit') ?>" data-id="">
				<div class="modal-body">
					<div id="frm_update_message"></div>

					<!-- Info Buku & Anggota -->
					<div class="alert alert-warning" role="alert">
						<h6 id="info_update_title" class="font-weight-bold mb-1">-</h6>
						<div class="small">
							<span class="mr-3"><i class="fa fa-barcode"></i> <span id="info_update_barcode">-</span></span>
							<span><i class="fa fa-user"></i> <span id="info_update_member">-</span></span>
						</div>
					</div>

					<!-- Form Pelanggaran -->
					<div class="form-group">
						<label for="frm_update_JenisPelanggaran_id" class="font-weight-bold">
							Jenis Pelanggaran <span class="text-danger">*</span>
						</label>
						<select id="frm_update_JenisPelanggaran_id" name="JenisPelanggaran_id" class="form-control" required>
							<option value="">-- Pilih Jenis Pelanggaran --</option>
							<?php foreach (get_table('jenis_pelanggaran', 'ID, JenisPelanggaran', null, 'data') as $row) : ?>
								<option value="<?= $row->ID ?>"><?= $row->JenisPelanggaran ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="form-group">
						<label for="frm_update_JenisDenda_id" class="font-weight-bold">
							Jenis Denda <span class="text-danger">*</span>
						</label>
						<select id="frm_update_JenisDenda_id" name="JenisDenda_id" class="form-control" required>
							<option value="">-- Pilih Jenis Denda --</option>
							<?php foreach (get_table('jenis_denda', 'ID, Name', null, 'data') as $row) : ?>
								<option value="<?= $row->ID ?>"><?= $row->Name ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="form-row">
						<div class="col-md-6 form-group">
							<label for="frm_update_JumlahDenda" class="font-weight-bold">
								Jumlah Denda (Rp) <span class="text-danger">*</span>
							</label>
							<input type="number" id="frm_update_JumlahDenda" name="JumlahDenda" class="form-control" min="0" step="500" placeholder="0" required>
						</div>

						<div class="col-md-6 form-group">
							<label for="frm_update_JumlahSuspend" class="font-weight-bold">
								Jumlah Suspend (Hari)
							</label>
							<input type="number" id="frm_update_JumlahSuspend" name="JumlahSuspend" class="form-control" min="0" value="0" placeholder="0">
						</div>
					</div>

				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">
						Batal
					</button>
					<button type="submit" name="submit" id="btnUpdate" class="btn btn-danger">
						<i class="fa fa-save"></i> Simpan Perubahan
					</button>
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
				$('#info_update_title').text(response.Title || '-');
				$('#info_update_barcode').text(response.NomorBarcode || '-');
				$('#info_update_member').text((response.Fullname || '-') + (response.MemberNo ? ' (' + response.MemberNo + ')' : ''));

				$('#frm_update_JenisPelanggaran_id').val(response.JenisPelanggaran_id);
				$('#frm_update_JenisDenda_id').val(response.JenisDenda_id);
				$('#frm_update_JumlahDenda').val(response.JumlahDenda || 0);
				$('#frm_update_JumlahSuspend').val(response.JumlahSuspend || 0);

				$('#modal_update').modal('show');
			},
			error: function() {
				Swal.fire({
					title: 'Error',
					text: 'Gagal mengambil data pelanggaran',
					type: 'error'
				});
			}
		});
	});

	$('#modal_update').on('hidden.bs.modal', function() {
		$(this).find('form').trigger('reset');
		$('#frm_update_message').html('');
	});

	$('#frm_update').submit(function(event) {
		event.preventDefault();
		var url = $(this).data('action') + '/' + $(this).attr('data-id');
		var data_post = $(this).serializeArray();

		$("#btnUpdate").html('<i class="fa fa-spinner fa-spin loading"></i> Menyimpan...');
		$("#btnUpdate").attr('disabled', true);

		$.ajax({
				url: url,
				type: 'POST',
				data: data_post,
			})
			.done(function(res) {
				$("#btnUpdate").attr('disabled', false);
				$("#btnUpdate").html('<i class="fa fa-save"></i> Simpan Perubahan');

				if (res.error == false) {
					$('#modal_update').modal('hide');
					Swal.fire({
						title: 'Berhasil',
						text: res.message || 'Pelanggaran berhasil diperbarui.',
						type: 'success',
						showConfirmButton: false,
						timer: 2000,
					});

					if (typeof t !== 'undefined') {
						t.ajax.reload(null, false);
					} else {
						window.location.reload();
					}
				} else {
					Swal.fire({
						title: 'Gagal',
						text: res.message || 'Terjadi kesalahan saat menyimpan',
						type: 'error'
					});
				}
			})
			.fail(function(res) {
				$("#btnUpdate").attr('disabled', false);
				$("#btnUpdate").html('<i class="fa fa-save"></i> Simpan Perubahan');

				Swal.fire({
					title: 'Oups',
					text: 'Maaf, terjadi kesalahan. Coba beberapa saat lagi atau hubungi Admin',
					type: 'error'
				});
			});

		return false;
	});
</script>