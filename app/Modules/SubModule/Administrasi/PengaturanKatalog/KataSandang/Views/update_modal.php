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
    $('#tbl_data').on('click', '.show-data', function() {
        var url = $(this).attr('data-href');
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            success: function(response) {
                $('#frm_edit').attr("data-id", response.ID);
                $('#frm_edit_tag').val(response.Tag);
                $('#frm_edit_name').val(response.Name);
                $('#frm_edit_length').val(response.JumlahKarakter);

                $('#modal_edit').modal('show');
            }
        });
    });

    $('#modal_edit').on('hidden.bs.modal', function(event) {
        $('#frm_edit_message').html('');
    });

    $('#modal_edit').on('shown.bs.modal', function(event) {
                // event.preventDefault();    });

                $('#frm_edit').submit(function(event) {
                    event.preventDefault();
                    var data_post = $(this).serializeArray();
                    var url = $(this).data('action') + '/' + $(this).data('id');

                    $('.loading').show();

                    $.ajax({
                            url: url,
                            type: 'POST',
                            dataType: 'json',
                            data: data_post,
                        })
                        .done(function(res) {
                            if (!res.error) {
                                Swal.fire({
                                    title: 'Success',
                                    text: 'Kata Sandang berhasil disimpan',
                                    type: 'success',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'KataSandang gagal disimpan',
                                    type: 'warning',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }

                            setTimeout(function() {
                                window.location.href = '<?= base_url('master-kata-sandang') ?>';
                            }, 2000);
                        })
                        .fail(function(res) {
                            $('#frm_edit_message').html(res);
                        })
                        .always(function() {
                            $('.loading').hide();
                        });

                    return false;
                });
</script>