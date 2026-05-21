<div class="modal fade" id="modal_upload_img" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Upload File - <span id="upload_title_span"></span>
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_upload" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div id="frm_upload_message"></div>
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label for="file_pendukung" class="">File <span id="upload_title_span2"></span>*</label>
                                <input type="file" class="form-control" name="file_pendukung[]" id="file_pendukung" accept="image/*" multiple>
                                <small class="form-text text-muted">
                                    Format yang diperbolehkan: JPG, JPEG, PNG, GIF. Maksimal ukuran per file: 2MB
                                </small>
                                <!-- Preview area -->
                                <div id="preview_container" class="mt-3" style="display: none;">
                                    <label>Preview:</label>
                                    <div id="image_preview" class="d-flex flex-wrap gap-2"></div>
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
                    <input type="hidden" name="upload_data_url" id="upload_data_url" value="">
                    <input type="hidden" name="upload_data_redirect" id="upload_data_redirect" value="">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('App.btn.close') ?></button>
                    <button type="submit" class="btn btn-primary" name="submit"><?= lang('App.btn.save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    #image_preview img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border: 2px solid #ddd;
        border-radius: 5px;
        margin-right: 10px;
        margin-bottom: 10px;
    }
    
    .preview-item {
        position: relative;
        display: inline-block;
    }
    
    .preview-item .remove-preview {
        position: absolute;
        top: -8px;
        right: 2px;
        background: red;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 16px;
        border: 2px solid white;
    }
</style>

<script>
    var defaultUrl = "<?= base_url('api/user/upload_file') ?>";
    var defaultRedirect = "<?= base_url('user/profile') ?>";

    // Preview images when selected
    $('#file_pendukung').on('change', function(e) {
        var files = e.target.files;
        var previewContainer = $('#preview_container');
        var imagePreview = $('#image_preview');
        
        imagePreview.empty();
        
        if (files.length > 0) {
            previewContainer.show();
            
            Array.from(files).forEach(function(file, index) {
                if (file.type.match('image.*')) {
                    var reader = new FileReader();
                    
                    reader.onload = function(e) {
                        var previewItem = $('<div class="preview-item"></div>');
                        var img = $('<img>').attr('src', e.target.result);
                        var removeBtn = $('<span class="remove-preview" data-index="' + index + '">&times;</span>');
                        
                        previewItem.append(img);
                        previewItem.append(removeBtn);
                        imagePreview.append(previewItem);
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
        } else {
            previewContainer.hide();
        }
    });

    // Remove preview (note: actual file removal requires DataTransfer API)
    $(document).on('click', '.remove-preview', function() {
        $(this).parent('.preview-item').remove();
        if ($('#image_preview').children().length === 0) {
            $('#preview_container').hide();
            $('#file_pendukung').val('');
        }
    });

    $('.upload-data').click(function() {
        var id = $(this).attr('data-id');
        var parent_id = $(this).attr('data-parent');
        var field = $(this).attr('data-field');
        var title = $(this).attr('data-title');

        $('#frm_upload').attr("data-id", id);
        $('#frm_upload').attr("data-field", field);
        $('#frm_upload').attr("data-title", title);

        var data_url = $(this).attr('data-url');
        if (data_url) {
            $('#upload_data_url').val(data_url);
        } else {
            $('#upload_data_url').val(defaultUrl);
        }

        var data_redirect = $(this).attr('data-redirect');
        if (data_redirect) {
            $('#upload_data_redirect').val(data_redirect);
        } else {
            $('#upload_data_redirect').val(defaultRedirect);
        }

        $('#modal_upload_img').modal('show');
        $('#upload_id').val(id);
        $('#upload_parent_id').val(parent_id);
        $('#upload_field').val(field);
        $('#upload_title').val(title);
        $('#upload_title_span').html(title);
        $('#upload_title_span2').html(title);
    });

    $('#frm_upload').submit(function(event) {
        event.preventDefault();
        
        var formData = new FormData(this);
        
        // Validasi client-side
        var files = $('#file_pendukung')[0].files;
        if (files.length === 0) {
            Swal.fire({
                title: 'Error',
                text: 'Silakan pilih file untuk diupload',
                icon: 'error'
            });
            return false;
        }

        // Validasi ukuran file (2MB)
        var maxSize = 2 * 1024 * 1024; // 2MB in bytes
        var invalidFiles = [];
        
        for (var i = 0; i < files.length; i++) {
            if (files[i].size > maxSize) {
                invalidFiles.push(files[i].name);
            }
        }
        
        if (invalidFiles.length > 0) {
            Swal.fire({
                title: 'Error',
                text: 'File berikut melebihi ukuran maksimal 2MB: ' + invalidFiles.join(', '),
                icon: 'error'
            });
            return false;
        }

        $('.loading').show();

        $.ajax({
            url: $('#upload_data_url').val(),
            type: 'POST',
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status === 201) {
                    window.location.href = $('#upload_data_redirect').val();
                } else {
                    $('#frm_upload_message').html('<div class="alert alert-danger">' + res.messages.error + '</div>');
                }
            },
            error: function(xhr) {
                console.log(xhr);
                var errorMsg = 'Terjadi kesalahan saat upload file';
                if (xhr.responseJSON && xhr.responseJSON.messages && xhr.responseJSON.messages.error) {
                    errorMsg = xhr.responseJSON.messages.error;
                }
                $('#frm_upload_message').html('<div class="alert alert-danger">' + errorMsg + '</div>');
            },
            complete: function() {
                $('.loading').hide();
            }
        });

        return false;
    });

    $('#modal_upload_img').on('hidden.bs.modal', function() {
        $(this).find('form').trigger('reset');
        $('#frm_upload_message').html('');
        $('#preview_container').hide();
        $('#image_preview').empty();
    });
</script>