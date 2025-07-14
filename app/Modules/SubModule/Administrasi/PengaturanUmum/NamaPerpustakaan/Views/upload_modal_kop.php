<!-- Modal Upload Logo - HTML sama seperti sebelumnya -->
<div class="modal fade" id="modal_upload_logokop" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload"></i> Upload Kop Laporan
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_upload_kop" enctype="multipart/form-data">
                <?=csrf_field()?>
                <input type="hidden" name="category" value="kop">
                <div class="modal-body">
                    <div id="upload_message_kop"></div>
                    
                    <div class="form-group">
                        <label>Kop Laporan Saat Ini:</label>
                        <div id="current_kop_preview" class="text-center mb-3">
                            <img id="current_kop_img" src="" alt="Current Kop" 
                                 class="img-thumbnail" style="max-width: 100%; max-height: 150px; display: none;">
                            <div id="no_kop_message" class="text-muted">
                                <i class="fas fa-image fa-3x"></i>
                                <p>Belum ada kop laporan</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="kop_file">Pilih File Kop Laporan*</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="kop_file" name="kop_file" 
                                   accept="image/jpeg,image/jpg,image/png,image/gif">
                            <label class="custom-file-label" for="kop_file">Pilih file...</label>
                        </div>
                        <small class="form-text text-muted">
                            Format: JPG, JPEG, PNG, GIF. Maksimal: 2MB
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Preview:</label>
                        <div id="upload_preview_kop" class="text-center" style="display: none;">
                            <img id="preview_img_kop" src="" alt="Preview" 
                                 class="img-thumbnail" style="max-width: 100%; max-height: 150px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_delete_kop" class="btn btn-danger mr-auto" style="display: none;">
                        <i class="fas fa-trash"></i> Hapus Kop Laporan
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


    // ========================================================
    // LOGIKA UNTUK MODAL UPLOAD KOP (#modal_upload_logokop)
    // ========================================================
    
    // Load kop saat ini ketika modal kop dibuka
    $('#modal_upload_logokop').on('show.bs.modal', function() {
        loadCurrentKop();
    });

    // Handler untuk input file kop
    $('#kop_file').on('change', function() {
        var file = this.files[0];
        if (file) {
            $(this).next('.custom-file-label').text(file.name);
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview_img_kop').attr('src', e.target.result);
                $('#upload_preview_kop').show();
            };
            reader.readAsDataURL(file);
        } else {
            $(this).next('.custom-file-label').text('Pilih file...');
            $('#upload_preview_kop').hide();
        }
    });

    // Submit form upload kop
    $('#frm_upload_kop').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        if (!$('#kop_file')[0].files[0]) {
            showMessage('#upload_message_kop', 'error', 'Silakan pilih file terlebih dahulu');
            return;
        }

        $.ajax({
            // PASTIKAN URL INI SESUAI DENGAN ROUTE API ANDA UNTUK UPLOAD KOP
            url: '<?= base_url('api/master-nama-perpustakaan/logo-upload') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json'
        })
        .done(function(response) {
            if (response.status === 200) {
                showMessage('#upload_message_kop', 'success', response.message);
                loadCurrentKop(); // Muat ulang kop
                location.reload(); // Muat ulang halaman
            } else {
                showMessage('#upload_message_kop', 'error', response.message);
            }
        })
        .fail(function() {
            showMessage('#upload_message_kop', 'error', 'Terjadi kesalahan saat upload.');
        });
    });

    // Hapus kop
    $('#btn_delete_kop').on('click', function() {
        if (confirm('Apakah Anda yakin ingin menghapus kop laporan?')) {
            $.ajax({
                // PASTIKAN URL INI SESUAI DENGAN ROUTE API ANDA UNTUK HAPUS KOP
                url: '<?= base_url('api/master-nama-perpustakaan/logo/delete') ?>',
                type: 'DELETE',
                dataType: 'json'
            })
            .done(function(response) {
                if (response.status === 200) {
                    showMessage('#upload_message_kop', 'success', response.message);
                    loadCurrentKop();
                    location.reload();
                } else {
                    showMessage('#upload_message_kop', 'error', response.message);
                }
            });
        }
    });

    // Fungsi untuk memuat kop saat ini
    function loadCurrentKop() {
        // PASTIKAN URL INI SESUAI DENGAN ROUTE API ANDA UNTUK MENDAPATKAN KOP
        $.getJSON('<?= base_url('api/master-nama-perpustakaan/logo/current') ?>', function(response) {
            if (response.status === 200 && response.data.exists) {
                $('#current_kop_img').attr('src', response.data.url).show();
                $('#no_kop_message').hide();
                $('#btn_delete_kop').show();
            } else {
                $('#current_kop_img').hide();
                $('#no_kop_message').show();
                $('#btn_delete_kop').hide();
            }
        });
    }


    // ========================================================
    // FUNGSI BANTUAN (HELPER)
    // ========================================================
    
    // Fungsi untuk menampilkan pesan (alert)
    function showMessage(selector, type, message) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
        
        $(selector).html(
            '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                '<i class="fas fa-' + icon + '"></i> ' + message +
                '<button type="button" class="close" data-dismiss="alert">' +
                    '<span aria-hidden="true">&times;</span>' +
                '</button>' +
            '</div>'
        );
    }

</script>