<div class="modal fade" id="modal_edit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Edit Parameter
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_edit" method="post" data-action="<?= base_url('api/parameter/edit') ?>" data-id="">
                <div class="modal-body">
                    <div id="frm_edit_message"></div>
                    <div class="form-group">
                        <label for="frm_edit_name">Nama Parameter*</label>
                        <input required maxlength="50" type="text" class="form-control" id="frm_edit_name" name="Name" placeholder="Nama Parameter" value="<?= set_value('Name'); ?>" />
                    </div>
                    <div class="form-group">
                        <label for="frm_edit_value">Nilai Parameter</label>
                        <div>
                            <textarea class="form-control" id="frm_edit_value" name="Value" placeholder="Nilai Parameter" rows="3"><?= set_value('Value'); ?></textarea>
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
    $('#tbl_params').on('click', '.show-data', function() {
        var url = $(this).attr('data-href');
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            success: function(response) {
                $('#frm_edit').attr("data-id", response.ID);
                $('#frm_edit_name').val(response.Name);
                $('#frm_edit_value').val(response.Value);

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
                            text: 'Parameter berhasil diubah',
                            type: 'success',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'Parameter gagal diubah',
                            type: 'warning',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }

                    setTimeout(function() {
                        window.location.href = '<?= base_url('parameter') ?>';
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
    });
</script>