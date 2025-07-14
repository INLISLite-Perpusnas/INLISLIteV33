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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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

                    <?php if (is_member('admin') || is_member('sa_pus')) : ?>
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

                    <?php if (is_member('admin')) : ?>
                <div class="position-relative form-group">
                    <label for="search_perpus">Cari NPP atau Nama Perpustakaan</label>
                    <div class="input-group">
                        <input type="text" id="search_perpus" class="form-control" placeholder="Ketik NPP atau Nama...">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" id="btn_search_perpus">Cari</button>
                        </div>
                    </div>
                    <div id="search_results" class="list-group mt-2"></div>
                    <input type="hidden" name="branch_id" id="branch_id" value="<?= $user->branch_id ?>">
                    <input type="hidden" name="perpus_npp" id="perpus_npp">
                <input type="hidden" name="perpus_nama" id="perpus_nama">
                <input type="hidden" name="perpus_alamat" id="perpus_alamat">
                <input type="hidden" name="perpus_email" id="perpus_email">
                <input type="hidden" name="perpus_jenis" id="perpus_jenis">

                </div>
                <?php endif ?>



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
    var is_profile = '<?= $is_profile ?>';
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
                        title: 'Success',
                        text: 'Profil User berhasil disimpan',
                        type: 'success',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    setTimeout(function() {
                        if (is_profile == true) {
                            window.location.href = '<?= base_url('user/profile') ?>';
                        } else {
                            window.location.href = '<?= base_url('user/detail/' . $user->id) ?>';
                        }

                    }, 2000);
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('search_perpus');
    var searchButton = document.getElementById('btn_search_perpus');
    var resultsContainer = document.getElementById('search_results');

    searchButton.addEventListener('click', function() {
        var keyword = searchInput.value.trim();
        resultsContainer.innerHTML = '';

        if (keyword.length < 5) {
            alert('Masukkan minimal 5 karakter untuk pencarian.');
            return;
        }

        fetch('<?= env('FLASK_API_BASEURL') ?>/perpustakaan?q=' + encodeURIComponent(keyword))
            .then(response => response.json())
            .then(data => {
                if (data.data && data.data.length > 0) {
                    data.data.forEach(function(item) {
                        var el = document.createElement('a');
                        el.href = '#';
                        el.className = 'list-group-item list-group-item-action';
                        el.textContent = 'NPP: ' + item.npp + ' | Nama: ' + item.nama;
                       el.addEventListener('click', function(e) {
                        e.preventDefault();
                        document.getElementById('branch_id').value = item.id;
                        searchInput.value = item.npp + ' - ' + item.nama;
                        resultsContainer.innerHTML = '';

                        // set field lain
                        document.getElementById('perpus_npp').value = item.npp;
                        document.getElementById('perpus_jenis').value = item.jenis;
                        document.getElementById('perpus_nama').value = item.nama;
                        document.getElementById('perpus_alamat').value = item.alamat || '';
                        document.getElementById('perpus_email').value = item.email || '';
                    });

                        resultsContainer.appendChild(el);
                    });
                } else {
                    var noData = document.createElement('div');
                    noData.className = 'list-group-item';
                    noData.textContent = 'Tidak ada data ditemukan.';
                    resultsContainer.appendChild(noData);
                }
            })
            .catch(err => {
                console.error('Error:', err);
                var errorEl = document.createElement('div');
                errorEl.className = 'list-group-item text-danger';
                errorEl.textContent = 'Terjadi kesalahan saat pencarian.';
                resultsContainer.appendChild(errorEl);
            });
    });
});

</script>


