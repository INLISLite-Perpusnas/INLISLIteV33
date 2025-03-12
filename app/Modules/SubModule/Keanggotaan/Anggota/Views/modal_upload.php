<div class="modal fade" id="modal_upload" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Upload Foto
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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
						Swal.fire({
							title: 'Success',
							text: 'File berhasil disimpan',
							type: 'success',
							showConfirmButton: false,
							timer: 3000
						})

						setTimeout(function() {
							window.location.href = "<?= base_url('anggota') ?>";
						}, 2000)
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

		$(document).on('show.bs.modal','#modal_upload', function (e) {
			var defaultDropzoneUrl = "<?= base_url('anggota/do_upload') ?>";
			var defaultUrl = "<?= base_url('api/anggota/upload_file') ?>";
			var defaultFormat = ".png, .jpg, .jpeg";
			var defaultFile = 1;
			var defaultRedirect = "<?= base_url('anggota') ?>";
			var defaultFormatTitle = "Format (JPG|PNG). Max 1 Files @ 2MB";

			var this_ = e.relatedTarget;
			var id = $(this_).attr('data-id');
			var parent_id = $(this_).attr('data-parent');
			var field = $(this_).attr('data-field');
			var title = $(this_).attr('data-title');

			$('#form_upload').attr("data-id", id);
			$('#form_upload').attr("data-field", field);
			$('#form_upload').attr("data-title", title);

			var data_dropzone_url = $(this_).attr('data-dropzone-url');
			if(data_dropzone_url) {
				$('#upload_data_dropzone_url').val(data_dropzone_url);	
			} else {
				$('#upload_data_dropzone_url').val(defaultDropzoneUrl);	
			}
			console.log('upload_data_dropzone_url: ' + $('#upload_data_dropzone_url').val());

			var data_url = $(this_).attr('data-url');
			if(data_url) {
				$('#upload_data_url').val(data_url);	
			} else {
				$('#upload_data_url').val(defaultUrl);	
			}
			console.log('upload_data_url: ' + $('#upload_data_url').val());

			var data_format = $(this_).attr('data-format');
			if(data_format) {
				defaultFormat = data_format;
				$('#upload_data_format').val(data_format);
			} else {
				$('#upload_data_format').val(defaultFormat);
			}
			console.log('upload_data_format: ' + $('#upload_data_format').val());

			var data_file = $(this_).attr('data-file');
			if(data_file) {
				defaultFile = data_file;
				$('#upload_data_file').val(data_file);
			} else {
				$('#upload_data_file').val(defaultFile);
			}
			console.log('upload_data_file: ' + $('#upload_data_file').val());

			var data_redirect = $(this_).attr('data-redirect');
			if(data_redirect) {
				$('#upload_data_redirect').val(data_redirect);
			} else {
				$('#upload_data_redirect').val(defaultRedirect);
			}
			console.log('upload_data_redirect: ' + $('#upload_data_redirect').val());

			var data_format_title = $(this_).attr('data-format-title');
			if(data_format_title) {
				$('#upload_data_format_title').html(data_format_title);	
			} else {
				$('#upload_data_format_title').html(defaultFormatTitle);	
			}

			$('#modal_upload').modal('show');
			$('#upload_id').val(id);
			$('#upload_parent_id').val(parent_id);
			$('#upload_field').val(field);
			$('#upload_title').val(title);
			$('#upload_title_span').html(title);
				
			setDropzone('file_pendukung', 'anggota', '.png,.jpg,.jpeg', 1, 10);
		});

		$(document).on('hidden.bs.modal','#modal_upload', function (e) {
			if (Dropzone.instances.length > 0) 
				Dropzone.instances.forEach(dz => dz.destroy())

			$('#form_upload_message').html('');
		});
	});
</script>
<?=$this->endSection('script');?>