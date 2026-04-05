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
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Tambah - Kata Sandang
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_create" method="post" action="">
                <div class="modal-body">
                    <div id="frm_create_message"></div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tag">Tag*</label>
                                <div>
                                    <input required type="text" class="form-control" id="frm_create_tag" name="tag" placeholder="Tag" value="<?= set_value('tag', '245'); ?>" readonly />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="length">Jumlah Karakter*</label>
                                <div>
                                    <input required type="number" class="form-control" id="frm_create_length" name="length" placeholder="Jumlah Karakter" value="<?= set_value('length', -1); ?>" required />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name">Nama Kata Sandang*</label>
                        <div>
                            <input required type="text" class="form-control" id="frm_create_name" name="name" placeholder="Nama Kata Sandang" value="<?= set_value('name'); ?>" required />
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('App.btn.close') ?></button>
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
                url: '<?= base_url('api/master-kata-sandang/create') ?>',
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
                        text: 'Kata Sandang gagal disimpan',
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