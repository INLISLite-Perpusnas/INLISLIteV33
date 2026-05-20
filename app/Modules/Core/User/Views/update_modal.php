<?php
$branch = get_ref_single('branchs', 'ID=' . $user->branch_id, 'data');
?>
<div class="modal fade" id="modal_edit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Edit Profil User
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_edit" method="post" data-action="<?= base_url('api/user/edit/' . $user->id) ?>">
                <div class="modal-body">
                    <div id="frm_edit_message"></div>
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label for="username">Username*</label>
                                <div>
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?= $user->username ?: ''; ?>" <?= (is_member('admin') ? 'readonly' : 'readonly') ?> />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="first_name">Nama Depan</label>
                                <div>
                                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Nama Depan" value="<?= $user->first_name ?: ''; ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="last_name">Nama Belakang</label>
                                <div>
                                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Nama Belakang" value="<?= $user->last_name ?: ''; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="email">Email*</label>
                                <div>
                                    <input type="text" class="form-control" id="email" name="email" placeholder="Email" value="<?= $user->email ?: ''; ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                <label for="phone">No. Telepon</label>
                                <div>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="No. Telepon" value="<?= $user->phone ?: ''; ?>" />
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

                    <?php if (is_member('admin')) : ?>
                        <div class="position-relative form-group">
                            <label for="groups">Role*</label>
                            <div>
                                <?php foreach ($groups as $group) : ?>
                                    <div class="custom-checkbox custom-control custom-control-inline">
                                        <input type="checkbox" id="groups<?= $group->id ?>" name="groups[]" value="<?= $group->id ?>" class="custom-control-input" <?= (in_array($group->name, $currentGroups)) ? 'checked="checked"' : '' ?>>
                                        <label class="custom-control-label" for="groups<?= $group->id ?>"><?= $group->name ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif ?>

                    <div class="position-relative form-group">
                        <label>Akses Lokasi Perpustakaan</label>
                        <select class="form-control select2" multiple="multiple" name="location_library_ids[]" id="frm_edit_LocationLibrary_id" style="width: 100%;">
                            <?php
                            $selected_locations = !empty($user->location_ids) ? explode(',', $user->location_ids) : [];

                            foreach (get_ref_table('location_library', 'ID, Code, Name', null, 'data') as $row) :
                                // Cek apakah ID lokasi saat ini ada di dalam array $selected_locations
                                $is_selected = in_array($row->ID, $selected_locations) ? 'selected' : '';
                            ?>
                                <option value="<?= $row->ID ?>" <?= $is_selected ?>><?= $row->Code ?> <?= $row->Name ?></option>
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
    var is_profile = '<?= $is_profile ?>';

    $(document).ready(function() {
        // Inisialisasi Select2 untuk lokasi perpustakaan
        $('#frm_edit_LocationLibrary_id').select2({
            placeholder: "Pilih Akses Lokasi",
            allowClear: true,
            // dropdownParent wajib di-set ke modal agar search box select2 bisa diklik saat di dalam modal
            dropdownParent: $('#modal_edit')
        });
    });

    $('#frm_edit').submit(function(event) {
        event.preventDefault();
        var data_post = $(this).serializeArray();
        var url = $(this).data('action');

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
                        text: 'Profil User berhasil disimpan',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 3000
                    }).then(function() {
                        if (is_profile == true) {
                            window.location.href = '<?= base_url('user/profile') ?>';
                        } else {
                            window.location.href = '<?= base_url('user/detail/' . $user->id) ?>';
                        }
                    });
                } else {
                    $('#frm_edit_message').html(res.messages.error);
                }
            })
            .fail(function(res) {
                console.log(res);
                $('#frm_edit_message').html(res.responseJSON.messages.error);
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