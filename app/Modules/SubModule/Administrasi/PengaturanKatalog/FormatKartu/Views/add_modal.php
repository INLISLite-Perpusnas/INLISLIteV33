<?php
$baseModel = new \Base\Models\BaseModel();
$request = service('request');

$slug = $request->getGet('slug') ?? '';
?>

<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Tambah - Format Kartu
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_create" method="post" action="">
                <div class="modal-body">
                    <div id="frm_create_message"></div>
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Nama*</label>
                                <div>
                                    <input required type="text" class="form-control" name="name" placeholder="Nama" value="<?= set_value('name'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="width">Panjang*</label>
                                <div>
                                    <input required type="text" class="form-control" name="width" placeholder="Panjang" value="<?= set_value('width'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="height">Lebar*</label>
                                <div>
                                    <input required type="text" class="form-control" name="height" placeholder="Lebar" value="<?= set_value('height'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="font_name">Nama Font*</label>
                                <div>
                                    <input required type="text" class="form-control" name="font_name" placeholder="Lebar" value="<?= set_value('font_name'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="font_size">Ukuran Font*</label>
                                <div>
                                    <input required type="text" class="form-control" name="font_size" placeholder="Panjang" value="<?= set_value('font_size'); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="format_text">Format (Ada Tag 1XX) </label>
                        <div>
                            <textarea name="format_text" placeholder="Format (Ada Tag 1XX)" rows="5" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('format_text') ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="format_text_no_author">Format (Tidak Ada Tag 1XX) </label>
                        <div>
                            <textarea name="format_text_no_author" placeholder="Format (Tidak Ada Tag 1XX)" rows="5" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('format_text_no_author') ?></textarea>
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
                url: '<?= base_url('api/master-format-kartu/create') ?>',
                type: 'POST',
                dataType: 'json',
                data: data_post,
            })
            .done(function(res) {
                if (!res.error) {
                    Swal.fire({
                        title: 'Success',
                        text: 'Format Kartu berhasil disimpan',
                        type: 'success',
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Format Kartu gagal disimpan',
                        type: 'warning',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }

                setTimeout(function() {
                    window.location.href = '<?= base_url('master-format-kartu') ?>';
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