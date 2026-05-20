<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i>
                    Tambah User
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
                            <div class="position-relative form-group">
                                <label for="username">Username*</label>
                                <div>
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="first_name">Nama Depan</label>
                                <div>
                                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Nama Depan" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="last_name">Nama Belakang</label>
                                <div>
                                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Nama Belakang" value="" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="email">Email*</label>
                                <div>
                                    <input type="text" class="form-control" id="email" name="email" placeholder="Email" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="phone">No. Telepon</label>
                                <div>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="No. Telepon" value="" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" />
                                <small class="info help-block"><?= lang('User.info.update.password') ?> </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="pass_confirm">Konfirmasi Password</label>
                                <div>
                                    <input type="password" class="form-control" id="pass_confirm" name="pass_confirm" placeholder="Konfirmasi Password" />
                                </div>
                            </div>
                        </div>
                    </div>

                 
                    <div class="position-relative form-group">
                        <label>Role*</label>
                        <div>
                            <?php foreach ($groups as $group) : ?>
                                <div class="custom-checkbox custom-control custom-control-inline">
                                    <input type="checkbox"
                                           id="create_groups<?= $group->id ?>"
                                           name="groups[]"
                                           value="<?= $group->id ?>"
                                           class="custom-control-input">
                                    <label class="custom-control-label" for="create_groups<?= $group->id ?>">
                                        <?= $group->name ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                  
                    <div class="position-relative form-group">
                        <label>Akses Lokasi Perpustakaan</label>
                        <select class="form-control" multiple="multiple" name="location_library_ids[]" id="frm_create_LocationLibrary_id" style="width: 100%;">
                            <?php foreach (get_ref_table('location_library', 'ID, Code, Name', null, 'data') as $row) : ?>
                                <option value="<?= $row->ID ?>"><?= $row->Code ?> <?= $row->Name ?></option>
                            <?php endforeach; ?>
                        </select>
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
    $(document).ready(function() {
        $('#modal_create').on('shown.bs.modal', function() {
            if (!$('#frm_create_LocationLibrary_id').hasClass('select2-hidden-accessible')) {
                $('#frm_create_LocationLibrary_id').select2({
                    placeholder: "Pilih Akses Lokasi",
                    allowClear: true,
                    dropdownParent: $('#modal_create')
                });
            }
        });

        $('#modal_create').on('hidden.bs.modal', function() {
            $(this).find('form').trigger('reset');
            if ($('#frm_create_LocationLibrary_id').hasClass('select2-hidden-accessible')) {
                $('#frm_create_LocationLibrary_id').select2('destroy');
            }
            $('#frm_create_message').html('');
        });
    });

    $('#frm_create').submit(function(event) {
        event.preventDefault();
        var data_post = $(this).serializeArray();

        $('.loading').show();

        $.ajax({
                url: '<?= base_url('api/user/create') ?>',
                type: 'POST',
                dataType: 'json',
                data: data_post,
            })
            .done(function(res) {
                console.log(res);
                if (res.status === 201) {
                    Swal.fire({
                        title: 'Berhasil',
                        text: 'Tambah User berhasil disimpan',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 3000
                    }).then(function() {
                        window.location.href = '<?= base_url('user?slug=' . $slug) ?>';
                    });
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
</script>