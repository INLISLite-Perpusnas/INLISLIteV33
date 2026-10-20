<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Tambah Parameter
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_create" method="post" action="">
                <div class="modal-body">
                    <div id="frm_create_message"></div>
                    <div class="form-group">
                        <label for="frm_create_name">Nama Parameter*</label>
                        <input required maxlength="50" type="text" class="form-control" id="frm_create_name" name="Name" placeholder="Nama Parameter" value="<?= set_value('Name'); ?>" />
                    </div>
                    <div class="form-group">
                        <label for="frm_create_value">Nilai Parameter</label>
                        <div>
                            <textarea class="form-control" id="frm_create_value" name="Value" placeholder="Nilai Parameter" rows="3"><?= set_value('Value'); ?></textarea>
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
    $('#frm_create').submit(function(event) {
        event.preventDefault();
        var data_post = $(this).serializeArray();

        $('.loading').show()

        $.ajax({
                url: '<?= base_url('api/parameter/create') ?>',
                type: 'POST',
                dataType: 'json',
                data: data_post,
            })
            .done(function(res) {
                if (!res.error) {
                    Swal.fire({
                        title: 'Success',
                        text: 'Parameter berhasil disimpan',
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
                $('#frm_create_message').html(res)
            })
            .always(function() {
                $('.loading').hide()
            });

        return false;
    });

    $('#modal_create').on('hidden.bs.modal', function() {
        $('#frm_create_message').html('');
    });

    $('#modal_create').on('shown.bs.modal', function(e) {
        //
    });
</script>