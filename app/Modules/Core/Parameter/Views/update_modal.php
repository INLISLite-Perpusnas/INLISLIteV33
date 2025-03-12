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
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="category">Kategori</label>
                                <div>
                                    <select class="form-control" name="category" id="frm_category" tabindex="-1" aria-hidden="true">
                                        <?php foreach (get_ref('ref-parameter', 'slug') as $row) : ?>
                                            <option value="<?= $row->name ?>"><?= $row->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="name">Nama Parameter*</label>
                                <div>
                                    <input required type="text" class="form-control" id="frm_edit_name" name="name" placeholder="Nama Parameter" value="<?= set_value('name'); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="value">Nilai Parameter*</label>
                        <div>
                            <input required type="text" class="form-control" id="frm_edit_value" name="value" placeholder="Nilai Parameter" value="<?= set_value('value'); ?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Keterangan</label>
                        <div>
                            <textarea id="frm_edit_description" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description') ?></textarea>
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
                $('#frm_edit').attr("data-id", response.id);
                $('#frm_edit_name').val(response.name);
                $('#frm_edit_value').val(response.value);
                $('#frm_edit_description').val(response.description);
                $('#frm_category').val(response.category);

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