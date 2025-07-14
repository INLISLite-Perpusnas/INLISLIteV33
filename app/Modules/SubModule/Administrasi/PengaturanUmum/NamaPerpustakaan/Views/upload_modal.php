<!-- Modal Upload Logo - HTML sama seperti sebelumnya -->
<div class="modal fade" id="modal_upload_logo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload"></i> Upload Logo
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_upload_logo" enctype="multipart/form-data">
                <?=csrf_field()?>
                <input type="hidden" name="category" value="logo">
                <div class="modal-body">
                    <div id="upload_message"></div>
                    
                    <!-- Current Logo Preview -->
                    <div class="form-group">
                        <label>Logo Saat Ini:</label>
                        <div id="current_logo_preview" class="text-center mb-3">
                            <img id="current_logo_img" src="" alt="Current Logo" 
                                 class="img-thumbnail" style="max-width: 200px; max-height: 150px; display: none;">
                            <div id="no_logo_message" class="text-muted">
                                <i class="fas fa-image fa-3x"></i>
                                <p>Belum ada logo</p>
                            </div>
                        </div>
                    </div>

                    <!-- File Input -->
                    <div class="form-group">
                        <label for="logo_file">Pilih File Logo*</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="logo_file" name="logo_file" 
                                   accept="image/jpeg,image/jpg,image/png,image/gif">
                            <label class="custom-file-label" for="logo_file">Pilih file...</label>
                        </div>
                        <small class="form-text text-muted">
                            Format: JPG, JPEG, PNG, GIF. Maksimal: 2MB
                        </small>
                    </div>

                    <!-- Preview Upload -->
                    <div class="form-group">
                        <label>Preview:</label>
                        <div id="upload_preview" class="text-center" style="display: none;">
                            <img id="preview_img" src="" alt="Preview" 
                                 class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_delete_logo" class="btn btn-danger mr-auto" style="display: none;">
                        <i class="fas fa-trash"></i> Hapus Logo
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
$(document).ready(function() {
    // Load current logo when modal opens
    $('#modal_upload_logo').on('show.bs.modal', function() {
        loadCurrentLogo();
    });

    // File input change handler
    $('#logo_file').on('change', function() {
        var file = this.files[0];
        if (file) {
            // Update label
            $('.custom-file-label').text(file.name);
            
            // Show preview
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview_img').attr('src', e.target.result);
                $('#upload_preview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('.custom-file-label').text('Pilih file...');
            $('#upload_preview').hide();
        }
    });

    // Form submission
    $('#frm_upload_logo').on('submit', function(e) {
        e.preventDefault();
        
        var file = $('#logo_file')[0].files[0];
        if (!file) {
            showMessage('error', 'Silakan pilih file terlebih dahulu');
            return;
        }

        var formData = new FormData();
        formData.append('logo_file', file);

        $('#loading_overlay').show();
        $('#upload_message').html('');

        $.ajax({
            // URL YANG SUDAH DISESUAIKAN DENGAN ROUTE BARU
            url: '<?= base_url('api/master-nama-perpustakaan/logo-upload') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json'
        })
        .done(function(response) {
            if (response.status === 200) {
                showMessage('success', response.message);
                loadCurrentLogo();
                
                // Reset form
                $('#frm_upload_logo')[0].reset();
                $('.custom-file-label').text('Pilih file...');
                $('#upload_preview').hide();
                
                // Close modal after 2 seconds
                setTimeout(function() {
                    $('#modal_upload_logo').modal('hide');
                }, 2000);
            } else {
                showMessage('error', response.message);
                if (response.errors) {
                    var errorList = '<ul>';
                    for (var field in response.errors) {
                        errorList += '<li>' + response.errors[field] + '</li>';
                    }
                    errorList += '</ul>';
                    showMessage('error', 'Validation Error:' + errorList);
                }
            }
        })
        .fail(function(xhr) {
            var errorMsg = 'Terjadi kesalahan saat upload';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            showMessage('error', errorMsg);
        })
        .always(function() {
            $('#loading_overlay').hide();
        });
    });

    // Delete logo
    $('#btn_delete_logo').on('click', function() {
        if (confirm('Apakah Anda yakin ingin menghapus logo?')) {
            $('#loading_overlay').show();
            
            $.ajax({
                // URL YANG SUDAH DISESUAIKAN DENGAN ROUTE BARU
                url: '<?= base_url('api/master-nama-perpustakaan/logo/delete') ?>',
                type: 'DELETE',
                dataType: 'json'
            })
            .done(function(response) {
                if (response.status === 200) {
                    showMessage('success', response.message);
                    loadCurrentLogo();
                } else {
                    showMessage('error', response.message);
                }
            })
            .fail(function(xhr) {
                showMessage('error', 'Terjadi kesalahan saat menghapus logo');
            })
            .always(function() {
                $('#loading_overlay').hide();
            });
        }
    });

    // Load current logo
    function loadCurrentLogo() {
        $.ajax({
            // URL YANG SUDAH DISESUAIKAN DENGAN ROUTE BARU
            url: '<?= base_url('api/master-nama-perpustakaan/logo/current') ?>',
            type: 'GET',
            dataType: 'json'
        })
        .done(function(response) {
            if (response.status === 200 && response.data.exists) {
                $('#current_logo_img').attr('src', response.data.url).show();
                $('#no_logo_message').hide();
                $('#btn_delete_logo').show();
            } else {
                $('#current_logo_img').hide();
                $('#no_logo_message').show();
                $('#btn_delete_logo').hide();
            }
        })
        .fail(function() {
            $('#current_logo_img').hide();
            $('#no_logo_message').show();
            $('#btn_delete_logo').hide();
        });
    }

    // Show message helper
    function showMessage(type, message) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
        
        $('#upload_message').html(
            '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                '<i class="fas fa-' + icon + '"></i> ' + message +
                '<button type="button" class="close" data-dismiss="alert">' +
                    '<span aria-hidden="true">&times;</span>' +
                '</button>' +
            '</div>'
        );
    }
});
</script>