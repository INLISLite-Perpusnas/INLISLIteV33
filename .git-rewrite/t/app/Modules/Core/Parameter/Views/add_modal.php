<?php
$baseModel = new \Base\Models\BaseModel();
$request = service('request');

$menu_id = $request->getGet('menu_id') ?? 0;
$slug = $request->getGet('slug') ?? '';
?>

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
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="category">Kategori</label>
                                <div>
                                    <select class="form-control" name="category" id="category" tabindex="-1" aria-hidden="true">
                                        <?php foreach (get_ref('ref-parameter', 'slug') as $row) : ?>
                                            <option value="<?= $row->name ?>" <?= (slugify($row->name) == $slug) ? 'selected' : '' ?>><?= $row->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="name">Nama Parameter*</label>
                                <div>
                                    <input required type="text" class="form-control" id="frm_create_name" name="name" placeholder="Nama Parameter" value="<?= set_value('name'); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="value">Nilai Parameter*</label>
                        <div>
                            <input required type="text" class="form-control" id="frm_create_value" name="value" placeholder="Nilai Parameter" value="<?= set_value('value'); ?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Keterangan</label>
                        <div>
                            <textarea id="frm_create_description" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description') ?></textarea>
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
        console.log(data_post);

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