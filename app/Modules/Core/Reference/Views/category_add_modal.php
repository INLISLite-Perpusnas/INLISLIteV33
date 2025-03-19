<?php
$baseModel = new \Base\Models\BaseModel();
$request = service('request');


$slug = 'reference-menu';
?>

<div class="modal fade" id="category_modal_create" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Tambah Kategori
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="category_form" method="post" action="">
                <div class="modal-body">
                    <div id="category_message"></div>
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label for="parent">Parent</label>
                                <div>
                                    <select class="form-control" name="parent" id="category_parent" tabindex="-1" aria-hidden="true">
                                        <option value="0"></option>
                                        <?= display_menu_option(3, 0) ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label for="name">Nama Kategori*</label>
                                <div>
                                    <input type="text" class="form-control" id="category_name" name="name" placeholder="Nama Kategori" value="<?= set_value('name'); ?>" />
                                </div>
                            </div>
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
    $('#category_form').submit(function(event) {
        event.preventDefault()
        var data_post = $(this).serializeArray()

        $('.loading').show()

        $.ajax({
                url: '<?= base_url('api/menu/create') ?>',
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
                            text: 'Kategori Referensi berhasil ditambah',
                            type: 'success',
                            showConfirmButton: false,
                            timer: 3000
                        })
                    }

                    setTimeout(function() {
                        window.location.href = '<?= base_url('reference') ?>?menu_id=' + res.data;
                    }, 2000)
                } else {
                    $('#category_message').html(res.messages.error)
                }
            })
            .fail(function(res) {
                console.log(res)
                $('#category_message').html(res.responseJSON.messages.error)
            })
            .always(function() {
                $('.loading').hide()
            });

        return false;
    });

    $('#category_modal_create').on('hidden.bs.modal', function() {
        $(this).find('form').trigger('reset');
        $('#category_message').html('');
    });

    $('#category_modal_create').on('shown.bs.modal', function(e) {
        $('#category_name').focus();
    });
</script>