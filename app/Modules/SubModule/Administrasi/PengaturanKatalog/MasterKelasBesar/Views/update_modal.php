<!-- Modal Update -->
<div class="modal fade" id="modal_update" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Master Kelas Besar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form_update" method="post">
                <input type="hidden" id="update_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="update_kdKelas">Kode Kelas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="update_kdKelas" name="kdKelas" maxlength="3" required>
                                <small class="form-text text-muted">Maksimal 3 karakter</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="update_warna">Warna</label>
                                <div class="input-group">
                                    <input type="color" class="form-control" id="update_warna" name="warna" style="width: 60px;">
                                    <input type="text" class="form-control" id="update_warna_text" placeholder="#000000" maxlength="7">
                                </div>
                                <small class="form-text text-muted">Pilih warna untuk kelas ini</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="update_namakelas">Nama Kelas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="update_namakelas" name="namakelas" maxlength="255" required>
                                <small class="form-text text-muted">Maksimal 255 karakter</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Sinkronisasi color picker dan text input untuk update
    $('#update_warna').on('change', function() {
        $('#update_warna_text').val($(this).val());
    });
    
    $('#update_warna_text').on('input', function() {
        var color = $(this).val();
        if (/^#[0-9A-F]{6}$/i.test(color)) {
            $('#update_warna').val(color);
        }
    });

    // Show update modal
    $('body').on('click', '.show-data', function() {
        var url = $(this).data('href');
        
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#update_id').val(response.ID);
                $('#update_kdKelas').val(response.kdKelas);
                $('#update_namakelas').val(response.namakelas);
                $('#update_warna').val(response.warna || '#000000');
                $('#update_warna_text').val(response.warna || '#000000');
                
                $('#modal_update').modal('show');
            },
            error: function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal mengambil data',
                    type: 'error'
                });
            }
        });
    });

    // Submit form update
    $('#form_update').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        var id = $('#update_id').val();
        
        // Clear previous error states
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        // Disable submit button
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Mengupdate...');
        
        $.ajax({
            url: '<?= base_url('master-kelas-besar/update/') ?>' + id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#modal_update').modal('hide');
                    $('#form_update')[0].reset();
                    t.ajax.reload();
                    
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        type: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                var response = xhr.responseJSON;
                if (response && response.messages) {
                    // Display validation errors
                    $.each(response.messages, function(field, message) {
                        var input = $('[name="' + field + '"]');
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(message);
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mengupdate data',
                        type: 'error'
                    });
                }
            },
            complete: function() {
                // Re-enable submit button
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Reset form when modal is closed
    $('#modal_update').on('hidden.bs.modal', function() {
        $('#form_update')[0].reset();
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    });
});
</script>