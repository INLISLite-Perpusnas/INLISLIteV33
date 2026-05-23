<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Tambah Lokasi Ruang
				</h5>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="frm_add" method="post" action="">
				<div class="modal-body">
					<div id="frm_add_message"></div>
					<div class="form-row">
						<div class="col-lg-3 col-md-4">
							<div class="form-group">
								<label for="Code">Kode Ruang</label>
								<div>
									<input required type="text" class="form-control" id="frm_add_Code" name="Code" placeholder="Kode Ruang" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-9 col-md-8">
							<div class="form-group">
								<label for="Name">Nama Ruang</label>
								<div>
									<input required type="text" class="form-control" id="frm_add_Name" name="name" placeholder="Nama Ruang" value="" />
								</div>
							</div>
						</div>
					</div>

					<div class="position-relative form-group">
						<label>Lokasi Perpustakaan</label>
						<select class="form-control" name="LocationLibrary_id" id="frm_add_LocationLibrary_id" tabindex="-1" aria-hidden="true">
							<?php foreach (get_ref_table('location_library', 'ID, Code, Name', null, 'data') as $row) : ?>
								<option value="<?= $row->ID ?>"><?= $row->Code ?> <?= $row->Name ?></option>
							<?php endforeach; ?>
						</select>
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
	function makeid(length) {
		var result = '';
		var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		var charactersLength = characters.length;
		for (var i = 0; i < length; i++) {
			result += characters.charAt(Math.floor(Math.random() * charactersLength));
		}
		return result;
	}

	$('#frm_add').submit(function(event) {
		event.preventDefault();

		var url = "<?= base_url('api-lokasi-ruang/create') ?>";
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
						html: 'Lokasi Ruang berhasil ditambah.',
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

	$('#modal_create').on('shown.bs.modal', function() {
		var code = makeid(6);
		$('#frm_add_Code').val(code);
	});

	$('#modal_create').on('hidden.bs.modal', function() {
		$(this).find('form').trigger('reset');
		$('#frm_add_message').html('');
	});
</script>