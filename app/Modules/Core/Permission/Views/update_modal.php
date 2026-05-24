<?php /** @var string $parent_menus */ $parent_menus = $parent_menus ?? ''; ?>
<div class="modal fade" id="modal_edit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Edit Permission
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_edit" method="post" data-action="<?= base_url('api/permission/edit') ?>" data-id="">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col">
                            <label for="name">Nama Method</label>
                            <div>
                                <input type="text" class="form-control" id="frm_edit_name" name="name" placeholder="" value="<?= set_value('name'); ?>" />
                            </div>
                        </div>
                        <div class="form-group col">
                            <label for="route">Route</label>
                            <div>
                                <input type="text" class="form-control" id="frm_edit_route" name="route" placeholder="" value="<?= set_value('route'); ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col">
                            <label for="menu">Menu</label>
                            <div>
                                <select class="form-control" name="menu" id="frm_edit_menu" tabindex="-1">
                                    <?= $parent_menus ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Keterangan</label>
                        <div>
                            <textarea id="frm_edit_description" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description'); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('.show-data').click(function() {
        var url = $(this).attr('data-href');
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            success: function(response) {
                $('#frm_edit').attr("data-id", response.id);
                $('#frm_edit_name').val(response.name);
                $('#frm_edit_route').val(response.route);
                $('#frm_edit_description').val(response.description);

                // Cari option berdasarkan data-name yang match dengan response.menu
                $('#frm_edit_menu option').each(function() {
                    if ($(this).data('name') && $(this).data('name').trim() === response.menu.trim()) {
                        $('#frm_edit_menu').val($(this).val());
                        return false;
                    }
                });

                $('#modal_edit').modal('show');
            }
        });
    });

    $('#modal_edit').on('hidden.bs.modal', function() {
        $(this).find('form').trigger('reset');
    });

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
                console.log(res)
                if (res.status === 201) {
                    Swal.fire({
                        title: 'Berhasil',
                        text: 'Permission berhasil diubah.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    setTimeout(function() {
                        window.location.href = '<?= base_url('permission') ?>';
                    }, 2000);
                } else {
                    Swal.fire({
                        title: 'Gagal',
                        text: res.messages.error,
                        icon: 'error',
                        showConfirmButton: true
                    });
                }
            })
            .fail(function(res) {
                console.log(res);
                let errorMsg = 'Terjadi kesalahan pada server';
                if(res.responseJSON && res.responseJSON.messages && res.responseJSON.messages.error) {
                    errorMsg = res.responseJSON.messages.error;
                }
                Swal.fire({
                    title: 'Gagal',
                    text: errorMsg,
                    icon: 'error',
                    showConfirmButton: true
                });
            })
            .always(function() {
                $('.loading').hide();
                $('html, body').animate({
                    scrollTop: $(document).height()
                }, 2000);
            });

        return false;
    });

    $('#frm_edit_name').on('keyup', function(e) {
        $(this).val($(this).val().toLowerCase().replace(/\s/g, ''));
        if (e.which == 32) {
            return false;
        }
    });
</script>