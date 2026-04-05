<!-- Modal Tambah -->
<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Master Kelas Besar</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form_create" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kdKelas">Kode Kelas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kdKelas" name="kdKelas" maxlength="3" required>
                                <small class="form-text text-muted">Maksimal 3 karakter</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="warna">Warna</label>
                                <div class="input-group">
                                    <input type="color" class="form-control" id="warna" name="warna" style="width: 60px;">
                                    <input type="text" class="form-control" id="warna_text" placeholder="#000000" maxlength="7">
                                </div>
                                <small class="form-text text-muted">Pilih warna untuk kelas ini</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="namakelas">Nama Kelas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="namakelas" name="namakelas" maxlength="255" required>
                                <small class="form-text text-muted">Maksimal 255 karakter</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Sinkronisasi color picker dan text input
    $('#warna').on('change', function() {
        $('#warna_text').val($(this).val());
    });
    
    $('#warna_text').on('input', function() {
        var color = $(this).val();
        if (/^#[0-9A-F]{6}$/i.test(color)) {
            $('#warna').val(color);
        }
    });

    // Submit form create
    $('#form_create').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        // Clear previous error states
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        // Disable submit button
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: '<?= base_url('master-kelas-besar/create') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#modal_create').modal('hide');
                    $('#form_create')[0].reset();
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
                        text: 'Terjadi kesalahan saat menyimpan data',
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
    $('#modal_create').on('hidden.bs.modal', function() {
        $('#form_create')[0].reset();
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    });
});
</script>