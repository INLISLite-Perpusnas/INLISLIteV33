<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Tambah Kategori
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_create" method="post" action="">
                <div class="modal-body">
                    <div id="frm_create_message"></div>
                    <div class="form-row">
                        <div class="col-md-8">
                            <div class="position-relative form-group">
                                <label for="name">Nama*</label>
                                <div>
                                    <input type="text" class="form-control" id="frm_create_name" name="name" placeholder="Nama" value="<?= set_value('name'); ?>" />
                                    <small class="info help-block text-muted">Nama Kategori Menu</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="sort">Urutan</label>
                                <div>
                                    <input type="number" class="form-control" id="frm_create_sort" name="sort" placeholder="Urutan" value="<?= set_value('sort') ?>" />
                                    <small class="info help-block text-muted">Urutan Kategori Menu</small>
                                </div>
                            </div>
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
        event.preventDefault()
        var data_post = $(this).serializeArray()

        $('.loading').show()

        $.ajax({
                url: '<?= base_url('api/menu/category_create') ?>',
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
                            text: 'Kategori Menu berhasil ditambah',
                            type: 'success',
                            showConfirmButton: false,
                            timer: 3000
                        })
                    }

                    setTimeout(function() {
                        window.location.href = '<?= base_url('menu') ?>';
                    }, 2000)
                } else {
                    $('#frm_create_message').html(res.messages.error)
                }
            })
            .fail(function(res) {
                console.log(res)
                $('#frm_create_message').html(res.responseJSON.messages.error)
            })
            .always(function() {
                $('.loading').hide()
            });

        return false;
    });

    $('#modal_create').on('hidden.bs.modal', function() {
        $(this).find('form').trigger('reset');
        $('#frm_create_message').html('');
    });

    $('#modal_create').on('shown.bs.modal', function(e) {
        //
    });
</script>