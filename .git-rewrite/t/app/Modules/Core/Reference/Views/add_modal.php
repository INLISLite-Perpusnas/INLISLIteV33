<?php
$baseModel = new \Base\Models\BaseModel();
$request = service('request');

$menu_id = $request->getGet('menu_id') ?? 0;
?>

<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Tambah Referensi
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form_create" method="post" action="">
                <div class="modal-body">
                    <div id="create_message"></div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="menu_id">Kategori</label>
                                <div>
                                    <select class="form-control" name="menu_id" id="create_menu_id" tabindex="-1" aria-hidden="true">
                                        <option value="0"></option>
                                        <?= display_menu_option(3, 0, 0, $menu_id, true) ?>
                                    </select>
                                    <small class="info help-block text-muted">Kategori Referensi</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="name">Label*</label>
                                <div>
                                    <input type="text" class="form-control" id="create_name" name="name" placeholder="Label" value="<?= set_value('name'); ?>" />
                                    <small class="info help-block text-muted">Label Referensi</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Keterangan</label>
                        <div>
                            <textarea id="description" name="create_description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description') ?></textarea>
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
    $('#form_create').submit(function(event) {
        event.preventDefault()
        var data_post = $(this).serializeArray()

        $('.loading').show()

        $.ajax({
                url: '<?= base_url('api/reference/create?menu_id=' . $menu_id) ?>',
                type: 'POST',
                dataType: 'json',
                data: data_post,
            })
            .done(function(res) {
                console.log(res)

                if (res.status === 201) {
                    if (res.error == null) {
                        Swal.fire({
                            title: 'Success',
                            text: 'Referensi berhasil ditambah',
                            type: 'success',
                            showConfirmButton: false,
                            timer: 3000
                        })
                    }

                    setTimeout(function() {
                        window.location.href = '<?= base_url('reference?menu_id=' . $menu_id) ?>';
                    }, 2000)
                } else {
                    $('#create_message').html(res.messages.error)
                }
            })
            .fail(function(res) {
                console.log(res)
                $('#create_message').html(res.responseJSON.messages.error)
            })
            .always(function() {
                $('.loading').hide()
            });

        return false;
    });

    $('#modal_create').on('hidden.bs.modal', function() {
        $(this).find('form').trigger('reset');
        $('#create_message').html('');
    });

    $('#modal_create').on('shown.bs.modal', function(e) {
        $('#create_name').focus();
    });
</script>