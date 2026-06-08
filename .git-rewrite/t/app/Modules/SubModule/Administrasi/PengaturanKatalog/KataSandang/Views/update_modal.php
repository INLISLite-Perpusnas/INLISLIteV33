<?php
$request = service('request');

?>

<div class="modal fade" id="modal_edit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Form Edit - Kata Sandang
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_edit" method="post" data-action="<?= base_url('api/master-kata-sandang/edit') ?>" data-id="">
                <div class="modal-body">
                    <div id="frm_edit_message"></div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tag">Tag*</label>
                                <div>
                                    <input required type="text" class="form-control" id="frm_edit_tag" name="tag" placeholder="Tag" value="<?= set_value('tag', '245'); ?>" readonly />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="length">Jumlah Karakter*</label>
                                <div>
                                    <input required type="number" class="form-control" id="frm_edit_length" name="length" placeholder="Jumlah Karakter" value="<?= set_value('length', -1); ?>" required />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name">Nama Kata Sandang*</label>
                        <div>
                            <input required type="text" class="form-control" id="frm_edit_name" name="name" placeholder="Nama Kata Sandang" value="<?= set_value('name'); ?>" required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= lang('App.btn.close') ?></button>
                    <button type="submit" class="btn btn-primary" name="submit"><?= lang('App.btn.save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Pastikan event handler didaftarkan setelah DOM ready
    $(document).on('click', '.show-data', function(e) {
        e.preventDefault();
        
        var url = $(this).attr('data-href');
        console.log('URL being called:', url); // Debug log
        
        // Show loading indicator
        $('.loading').show();
        
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            timeout: 10000, // 10 second timeout
            success: function(response) {
                console.log('Response received:', response); // Debug log
                
                // Set form data-id
                $('#frm_edit').attr("data-id", response.ID);
                
                // Populate form fields
                $('#frm_edit_tag').val(response.Tag);
                $('#frm_edit_name').val(response.Name);
                $('#frm_edit_length').val(response.JumlahKarakter);
                
                // Show modal
                $('#modal_edit').modal('show');
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });
                
                Swal.fire({
                    title: 'Error',
                    text: 'Gagal memuat data: ' + error,
                    type: 'error',
                    confirmButtonText: 'OK'
                });
            },
            complete: function() {
                $('.loading').hide();
            }
        });
    });
    
    // Modal event handlers
    $('#modal_edit').on('hidden.bs.modal', function(event) {
        $('#frm_edit_message').html('');
        $('#frm_edit')[0].reset(); // Reset form
        $('#frm_edit').removeAttr('data-id'); // Clear data-id
    });

    // Form submit handler
    $('#frm_edit').on('submit', function(event) {
        event.preventDefault();
        
        var formId = $(this).data('id');
        if (!formId) {
            Swal.fire({
                title: 'Error',
                text: 'ID tidak ditemukan',
                type: 'error'
            });
            return false;
        }
        
        var data_post = $(this).serializeArray();
        var url = $(this).data('action') + '/' + formId;
        
        console.log('Submitting to:', url); // Debug log
        console.log('Data:', data_post); // Debug log

        $('.loading').show();

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: data_post,
            success: function(res) {
                if (!res.error) {
                    Swal.fire({
                        title: 'Success',
                        text: 'Kata Sandang berhasil disimpan',
                        type: 'success',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    
                    setTimeout(function() {
                        $('#modal_edit').modal('hide');
                        t.ajax.reload(); // Reload datatable instead of redirect
                    }, 2000);
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: res.messages || 'Kata Sandang gagal disimpan',
                        type: 'error'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log('Submit Error:', xhr.responseText);
                Swal.fire({
                    title: 'Error',
                    text: 'Terjadi kesalahan: ' + error,
                    type: 'error'
                });
            },
            complete: function() {
                $('.loading').hide();
            }
        });
        
        return false;
    });
});
</script>