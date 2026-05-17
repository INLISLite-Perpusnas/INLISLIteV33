<div class="modal fade" id="modal_upload" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Upload Foto
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form_upload" method="post" data-action="" data-id="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div id="form_upload_message"></div>
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label for="file_pendukung" class="">File Foto <span id="upload_title_span2"></span>*</label>
                                <div id="file_pendukung" class="dropzone"></div>
                                <div id="file_pendukung_listed"></div>
                                <div>
                                    <small class="info help-block"><span id="upload_data_format_title">Format (JPG|PNG). Max 1 Files @ 2MB</span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="upload_id" id="upload_id" value="">
                    <input type="hidden" name="upload_parent_id" id="upload_parent_id" value="">
                    <input type="hidden" name="upload_field" id="upload_field" value="">
                    <input type="hidden" name="upload_title" id="upload_title" value="">
                    <input type="hidden" name="upload_data_dropzone_url" id="upload_data_dropzone_url" value="<?= base_url('anggota/do_upload') ?>">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" id="submit_data" class="btn btn-primary" name="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?=$this->section('script');?>
<script>
	Dropzone.autoDiscover = false;
	$(document).ready(function() {
		$('#form_upload').submit(function(e) {
			e.preventDefault()
			var data_post = $(this).serializeArray();
			var id = $('#upload_id').val();
			var parent_id = $('#upload_parent_id').val();

			$('.loading').show()

			$.ajax({
					url: "<?= base_url('api/anggota/upload_file') ?>",
					type: 'POST',
					dataType: 'json',
					data: data_post,
				})
				.done(function(res) {
					console.log(res)
					if (res.status === 201) {
						$('#modal_upload').modal('hide');
						Swal.fire({
							title: 'Berhasil',
							text: 'Foto berhasil disimpan',
							icon: 'success',
							showConfirmButton: false,
							timer: 3000
						}).then(function() {
							if (typeof t !== 'undefined') t.ajax.reload(null, false);
						});
					} else {
						$('#form_upload_message').html(res.messages.error)
					}
				})
				.fail(function(res) {
					console.log(res)
					// $('#form_upload_message').html(res.responseJSON.messages.error)
				})
				.always(function() {
					$('.loading').hide()
				});

			return false;
		});

		// Event handler removed - modal initialization is now handled in list.php
		// This prevents duplicate initialization and event conflicts

		$(document).on('hidden.bs.modal','#modal_upload', function (e) {
			if (Dropzone.instances.length > 0) 
				Dropzone.instances.forEach(dz => dz.destroy())

			$('#form_upload_message').html('');
		});
	});
</script>
<?=$this->endSection('script');?>