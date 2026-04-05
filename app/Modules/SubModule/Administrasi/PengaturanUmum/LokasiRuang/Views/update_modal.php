<div class="modal fade" id="modal_update" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Edit Lokasi Ruang
				</h5>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="frm_update" method="post" data-action="<?= base_url('api-lokasi-ruang/edit') ?>" data-id="">
				<div class="modal-body">
					<div id="frm_create_message"></div>
					<div class="form-row">
						<div class="col-lg-3 col-md-3">
							<div class="form-group">
								<label for="Code">Kode Ruang</label>
								<div>
									<input required type="text" class="form-control" id="frm_update_Code" name="Code" placeholder="Kode Ruang" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-2 col-md-2">
							<div class="form-group">
								<label for="Code">&nbsp;</label>
								<div>
									<button type="button" id="btnGenerate" class="btn btn-secondary btn-block mt-1">Generate</button>
								</div>
							</div>
						</div>
						<div class="col-lg-7 col-md-7">
							<div class="form-group">
								<label for="Name">Nama Ruang</label>
								<div>
									<input required type="text" class="form-control" id="frm_update_Name" name="Name" placeholder="Nama Ruang" value="" />
								</div>
							</div>
						</div>
					</div>

					<div class="position-relative form-group">
						<label>Lokasi Perpustakaan</label>
						<select class="form-control" name="LocationLibrary_id" id="frm_update_LocationLibrary_id" tabindex="-1" aria-hidden="true">
							<?php foreach (get_ref_table('location_library', 'ID, Code, Name','Branch_id = ' . user()->branch_id ?? '', 'data') as $row) : ?>
								<option value="<?= $row->ID ?>"><?= $row->Code ?> <?= $row->Name ?></option>
							<?php endforeach; ?>
						</select>
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
	function makeid(length) {
		var result = '';
		var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		var charactersLength = characters.length;
		for (var i = 0; i < length; i++) {
			result += characters.charAt(Math.floor(Math.random() * charactersLength));
		}
		return result;
	}

	$('#btnGenerate').click(function() {
		var code = makeid(6);
		$('#frm_update_Code').val(code);
	});

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
				$('#frm_update_LocationLibrary_id').val(response.LocationLibrary_id);
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
						html: 'Lokasi Ruang berhasil disimpan.',
						type: 'success',
						showConfirmButton: false,
						timer: 5000,
					}).then(() => {
						window.location.href = `<?= base_url('master-lokasi-ruang') ?>`;
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