<div class="modal fade" id="modal_upload" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> <span id="upload_title_span"></span>
                </h5>
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> -->
            </div>
            <form id="frm_upload" method="post" data-action="" data-id="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div id="frm_upload_message"></div>
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label for="upload_data_file" class="">File <span id="upload_title_span2"></span>*</label>
                                <div id="upload_data_file" class="dropzone"></div>
                                <div id="upload_data_file_listed"></div>
                                <div>
                                    <small class="info help-block"><span id="upload_data_sub_title"></span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="upload_id" id="upload_id" value="">
                    <input type="hidden" name="upload_parent_id" id="upload_parent_id" value="">
                    <input type="hidden" name="upload_field" id="upload_field" value="">
                    <input type="hidden" name="upload_format" id="upload_format" value="">
                    <input type="hidden" name="upload_title" id="upload_title" value="">

                    <input type="hidden" name="upload_data_dropzone_url" id="upload_data_dropzone_url" value="">
                    <input type="hidden" name="upload_data_url" id="upload_data_url" value="">
                    <input type="hidden" name="upload_data_redirect" id="upload_data_redirect" value="">

                    <button type="button" class="btn btn-secondary btnClose" data-dismiss="modal"><?= lang('App.btn.close') ?></button>
                    <button type="submit" class="btn btn-primary btnSubmit" name="submit"><span class="loading">Simpan</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#frm_upload').submit(function(event) {
        event.preventDefault()
        var data_post = $(this).serializeArray();
        var id = $('#upload_id').val();
        var parent_id = $('#upload_parent_id').val();

        $('.btnClose').prop("disabled", true);
		$('.btnSubmit').prop("disabled", true);
        $('.loading').html('<i class="fa fa-spinner fa-spin"></i> Loading...');
        $.ajax({
                url: $('#upload_data_url').val(),
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
                        window.location.href = $('#upload_data_redirect').val();
                    }, 2000)
                } else {
					Swal.fire({
						title: 'Oups',
						text: 'File gagal disimpan, silakan coba beberapa saat lagi!',
						type: 'warning',
						showConfirmButton: false,
						timer: 3000
					})
                    $('#frm_upload_message').html(res.messages.error);
                }
            })
            .fail(function(res) {
                console.log(res);
				Swal.fire({
					title: 'Oups',
					text: 'File gagal disimpan, silakan coba beberapa saat lagi!',
					type: 'error',
					showConfirmButton: false,
					timer: 3000
				})
            })
            .always(function() {
				$('.btnClose').prop("disabled", false);
				$('.btnSubmit').prop("disabled", false);
        		$('.loading').html('Simpan');
            });

        return false;
    });
</script>
<script>
	$("body").on("click", ".upload-data", function() {
		Dropzone.autoDiscover = false;
		var id = $(this).attr('data-id');
		var parent_id = $(this).attr('data-parent');
		var field = $(this).attr('data-field');
		var title = $(this).attr('data-title');
		var sub_title = $(this).attr('data-sub_title');
		var dz_url = $(this).attr('data-dz_url');
		var url = $(this).attr('data-url');
		var redirect = $(this).attr('data-redirect');
		var controller = $(this).attr('data-controller');
		var format = $(this).attr('data-format');
		var count = $(this).attr('data-count');
		var max = $(this).attr('data-max');
		
		$('#frm_upload').attr("data-id", id);
		$('#frm_upload').attr("data-field", field);
		$('#frm_upload').attr("data-title", title);
		$('#upload_data_dropzone_url').val(dz_url);
		$('#upload_data_url').val(url);
		$('#upload_data_redirect').val(redirect);
		$('#upload_data_sub_title').html(sub_title);

		$('#modal_upload').modal({backdrop: 'static', keyboard: false, show: true});

		$('#upload_id').val(id);
		$('#upload_parent_id').val(parent_id);
		$('#upload_field').val(field);
		$('#upload_format').val(format);
		$('#upload_title').val(title);
		$('#upload_title_span').html(title);

		console.log('parent_id: ' + parent_id);
		console.log('id: ' + id);
		console.log('field: ' + field);
		console.log('url: ' + url);
		console.log('dz_url: ' + dz_url);
		console.log('controller: ' + controller);
		console.log('title: ' + title);
		console.log('sub_title: ' + sub_title);
		console.log('redirect: ' + redirect);
		console.log('format: ' + format);
		console.log('count: ' + count);
		console.log('max: ' + max);

		setDropzone('upload_data_file', controller, format, count, max);
	});
</script> 