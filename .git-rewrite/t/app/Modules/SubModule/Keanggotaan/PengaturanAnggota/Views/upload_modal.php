<div class="modal fade" id="modal_upload_img" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Upload File -Artikel
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_upload" method="post" data-action="" data-id="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div id="frm_upload_message"></div>
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label for="file_pendukung" class="">File <span id="upload_title_span2"></span>*</label>
                                <div id="file_pendukung" class="dropzone"></div>
                                <div id="file_pendukung_listed"></div>
                                <div>
                                    <small class="info help-block"><span id="upload_data_format_title"></span></small>
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

                    <input type="hidden" name="upload_data_dropzone_url" id="upload_data_dropzone_url" value="">
                    <input type="hidden" name="upload_data_url" id="upload_data_url" value="">
                    <input type="hidden" name="upload_data_format" id="upload_data_format" value="">
                    <input type="hidden" name="upload_data_file" id="upload_data_file" value="">
                    <input type="hidden" name="upload_data_redirect" id="upload_data_redirect" value="">

                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal"><?= lang('App.btn.close') ?></button>
                    <button type="submit" class="btn btn-primary" name="submit"><?= lang('App.btn.save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var defaultDropzoneUrl = "<?= base_url('page/do_upload') ?>";
var defaultUrl = "<?= base_url('api/page/upload_file') ?>";
var defaultFormat = "application/pdf";
var defaultFile = 1;
var defaultRedirect = "<?= base_url('page') ?>";
var defaultFormatTitle = "Format (PDF). Max 10MB";

$('.upload-data').click(function() {
    Dropzone.autoDiscover = false;
    var id = $(this).attr('data-id');
    var parent_id = $(this).attr('data-parent');
    var field = $(this).attr('data-field');
    var title = $(this).attr('data-title');

    $('#frm_upload').attr("data-id", id);
    $('#frm_upload').attr("data-field", field);
    $('#frm_upload').attr("data-title", title);

    console.log(id)
    console.log(field)
    console.log(title)

    var data_dropzone_url = $(this).attr('data-dropzone-url');
    if (data_dropzone_url) {
        $('#upload_data_dropzone_url').val(data_dropzone_url);
    } else {
        $('#upload_data_dropzone_url').val(defaultDropzoneUrl);
    }
    console.log('upload_data_dropzone_url: ' + $('#upload_data_dropzone_url').val());

    var data_url = $(this).attr('data-url');
    if (data_url) {
        $('#upload_data_url').val(data_url);
    } else {
        $('#upload_data_url').val(defaultUrl);
    }
    console.log('upload_data_url: ' + $('#upload_data_url').val());

    var data_format = $(this).attr('data-format');
    if (data_format) {
        defaultFormat = data_format;
        $('#upload_data_format').val(data_format);
    } else {
        $('#upload_data_format').val(defaultFormat);
    }
    console.log('upload_data_format: ' + $('#upload_data_format').val());

    var data_file = $(this).attr('data-file');
    if (data_file) {
        defaultFile = data_file;
        $('#upload_data_file').val(data_file);
    } else {
        $('#upload_data_file').val(defaultFile);
    }
    console.log('upload_data_file: ' + $('#upload_data_file').val());

    var data_redirect = $(this).attr('data-redirect');
    if (data_redirect) {
        $('#upload_data_redirect').val(data_redirect);
    } else {
        $('#upload_data_redirect').val(defaultRedirect);
    }
    console.log('upload_data_redirect: ' + $('#upload_data_redirect').val());

    var data_format_title = $(this).attr('data-format-title');
    if (data_format_title) {
        $('#upload_data_format_title').html(data_format_title);
    } else {
        $('#upload_data_format_title').html(defaultFormatTitle);
    }

    $('#modal_upload_img').modal('show');
    $('#upload_id').val(id);
    $('#upload_parent_id').val(parent_id);
    $('#upload_field').val(field);
    $('#upload_title').val(title);
    $('#upload_title_span').html(title);

    setDropzone('file_pendukung', 'page', $('#upload_data_format').val(), $('#upload_data_file').val(), 10);
});

$('#frm_upload').submit(function(event) {
    event.preventDefault()
    var data_post = $(this).serializeArray();
    var id = $('#upload_id').val();
    var parent_id = $('#upload_parent_id').val();

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
                    window.location.href = $('#upload_data_redirect').val();
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
    file_pendukung = null;
    file_pendukung.disable();
});

$('#modal_upload_img').on('shown.bs.modal', function(e) {
    //
});
</script>