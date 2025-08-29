<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Tambah Permission
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_create" method="post" action="">
                <div class="modal-body">
                    <div id="frm_create_message"></div>
                    <div class="form-row">
                        <div class="form-group col">
                            <label for="menu">Nama Method</label>
                            <div>
                                <input type="text" class="form-control" id="name" name="name" placeholder="" value="<?= set_value('name'); ?>" />
                            </div>
                        </div>
                        <div class="form-group col">
                            <label for="route">Route</label>
                            <div>
                                <input type="text" class="form-control" id="route" name="route" placeholder="" value="<?= set_value('route'); ?>" />
                            </div>
                        </div>
                    </div>
                       <div class="form-row">
                        <div class="form-group col">
                            <label for="menu">Menu</label>
                            <div>
                                <select class="form-control" name="menu" id="menu" tabindex="-1">
                                    <?= $parent_menus ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Keterangan</label>
                        <div>
                            <textarea id="description" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('group_description'); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#frm_create').submit(function(event) {
        event.preventDefault();
        var data_post = $(this).serializeArray();

        $('.loading').show();

        $.ajax({
                url: '<?= base_url('api/permission/create') ?>',
                type: 'POST',
                dataType: 'json',
                data: data_post,
            })
            .done(function(res) {
                console.log(res)
                if (res.status === 200) {
                    Swal.fire({
                        title: 'Success',
                        text: 'Permission berhasil disimpan',
                        type: 'success',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    setTimeout(function() {
                        window.location.href = '<?= base_url('permission') ?>';
                    }, 2000);
                } else {
                    $('#frm_create_message').html(res.messages.error);
                }
            })
            .fail(function(res) {
                console.log(res);
                $('#frm_create_message').html(res.responseJSON.messages.error);
            })
            .always(function() {
                $('.loading').hide();
                $('html, body').animate({
                    scrollTop: $(document).height()
                }, 2000);
            });

        return false;
    });

    $('#modal_create').on('hidden.bs.modal', function() {
        $(this).find('form').trigger('reset');
        $('#frm_create_message').html('');
    });

    $('#name').on('keyup', function(e) {
        $(this).val($(this).val().toLowerCase().replace(/\s/g, ''));
        if (e.which == 32) {
            return false;
        }
    });
</script>