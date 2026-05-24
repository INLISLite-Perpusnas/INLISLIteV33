<div class="modal fade" id="modal_upload_img" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Upload Cover
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_upload" method="post" data-action="" data-id="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div id="frm_upload_message"></div>
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label for="upload_file" class=""><span id="upload_title_file"></span>*</label>
                                <div id="upload_file" class="dropzone"></div>
                                <div id="upload_file_listed"></div>
                                <div>
                                    <small class="info help-block"><span id="upload_data_format_title"></span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="upload_id" id="upload_id" value="">
                    <input type="hidden" name="upload_ref_id" id="upload_ref_id" value="">
                    <input type="hidden" name="upload_field" id="upload_field" value="">
                    <input type="hidden" name="upload_title" id="upload_title" value="">

                    <input type="hidden" name="upload_data_dropzone_url" id="upload_data_dropzone_url" value="">
                    <input type="hidden" name="upload_data_url" id="upload_data_url" value="">
                    <input type="hidden" name="upload_data_format" id="upload_data_format" value="">
                    <input type="hidden" name="upload_data_max_files" id="upload_data_max_files" value="">
                    <input type="hidden" name="upload_data_max_size" id="upload_data_max_size" value="">
                    <input type="hidden" name="upload_data_redirect_url" id="upload_data_redirect_url" value="">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('App.btn.close') ?></button>
                    <button type="submit" class="btn btn-primary" name="submit"><?= lang('App.btn.save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('.upload-data').click(function() {
        Dropzone.autoDiscover = false;

        $('#upload_title_header').html($(this).attr('data-title-header'));
        $('#upload_title_file').html($(this).attr('data-title-file'));

        $('#upload_data_format_title').html($(this).attr('data-format-title'));

        $('#upload_id').val($(this).attr('data-id'));
        console.log('upload_id: ' + $(this).attr('data-id'));

        $('#upload_ref_id').val($(this).attr('data-ref-id'));
        console.log('upload_ref_id: ' + $(this).attr('data-ref-id'));

        $('#upload_field').val($(this).attr('data-field'));
        console.log('upload_field: ' + $(this).attr('data-field'));

        $('#upload_title').val($(this).attr('data-title'));
        console.log('upload_title: ' + $(this).attr('data-title'));

        $('#upload_data_dropzone_url').val($(this).attr('data-dropzone-url'));
        console.log('upload_data_dropzone_url: ' + $('#upload_data_dropzone_url').val());

        $('#upload_data_url').val($(this).attr('data-upload-url'));
        console.log('upload_data_url: ' + $('#upload_data_url').val());

        $('#upload_data_format').val($(this).attr('data-format'));
        console.log('upload_data_format: ' + $('#upload_data_format').val());

        $('#upload_data_max_files').val($(this).attr('data-max-files'));
        console.log('upload_data_max_files: ' + $('#upload_data_max_files').val());

        $('#upload_data_max_size').val($(this).attr('data-max-size'));
        console.log('upload_data_max_size: ' + $('#upload_data_max_size').val());

        $('#upload_data_redirect_url').val($(this).attr('data-redirect-url'));
        console.log('upload_data_redirect_url: ' + $('#upload_data_redirect_url').val());

        $('#modal_upload_img').modal('show');

        setDropzone('upload_file', 'user', $('#upload_data_format').val(), $('#upload_data_max_files').val(), $('#upload_data_max_size').val());
    });

    $('#frm_upload').submit(function(event) {
        event.preventDefault()
        var data_post = $(this).serializeArray();
        var id = $('#upload_id').val();
        var ref_id = $('#upload_ref_id').val();

        $('.loading').show()

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
                        window.location.href = $('#upload_data_redirect_url').val();
                    }, 2000)
                } else {
                    $('#frm_upload_message').html(res.messages.error)
                }
            })
            .fail(function(res) {
                console.log(res)
                // $('#frm_upload_message').html(res.responseJSON.messages.error)
            })
            .always(function() {
                $('.loading').hide()
            });

        return false;
    });

    $('#modal_upload_img').on('hidden.bs.modal', function() {
        $(this).find('form').trigger('reset');
        $('#frm_upload_message').html('');
        upload_file = null;
        upload_file.disable();
    });

    $('#modal_upload_img').on('shown.bs.modal', function(e) {
        //
    });
</script>