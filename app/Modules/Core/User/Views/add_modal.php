<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i>
                    Tambah User
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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

                    <div class="form-row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Role* </label>
                                <div class="select-wrapper">
                                    <input type="text" class="form-control" name="group" id="group" value="<?= $slug ?>" readonly>
                                </div>
                                <small class="help-block with-errors"></small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Jenis Admin </label>
                                <div class="select-wrapper">
                                    <select class="form-control select2" id="is_branch" name="is_branch" style="width:100%">
                                        <?php if (in_array($slug, ['admin', 'sa_prov', 'sa_kabkot'])) : ?>
                                            <option value="0" selected>Admin Wilayah</option>
                                        <?php endif; ?>
                                        <option value="1">Admin Lembaga</option>
                                    </select>
                                </div>
                                <small class="help-block with-errors"></small>
                            </div>
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
    $(document).ready(function() {
        $('#modal_create').on('shown.bs.modal', function() {
            $('.select2').select2({
                width: '100%', // Adjust as needed
                placeholder: "Pilih",
                allowClear: true,
                dropdownParent: $('#modal_create') // Append the dropdown to the modal
            });
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
                console.log(res)
                if (res.status === 201) {
                    Swal.fire({
                        title: 'Success',
                        text: 'Tambah User berhasil',
                        type: 'success',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    setTimeout(function() {
                        window.location.href = '<?= base_url('user?slug=' . $slug) ?>';
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
</script>

<script>
    let npp_provinsi_id = '<?= user()->npp_provinsi_id ?>';
    getData(`<?= base_url('api/region/province') ?>`, `#provinsi_id`, npp_provinsi_id, 'Pilih');

    <?php if (in_array($slug, ['sa_kabkot'])) : ?>
        if (npp_provinsi_id) {
            getData(`<?= base_url('api/region/city') ?>/${npp_provinsi_id}.`, `#kabkota_id`, false, 'Pilih');
        }

        $('#provinsi_id').change(function(e) {
            var provinsi_id = $(this).val();
            if (!provinsi_id) {
                $('#kabkota_id').html('<option value="">Pilih</option>');
            } else {
                getData(`<?= base_url('api/region/city') ?>/${provinsi_id}.`, `#kabkota_id`, false, 'Pilih');

                $('#kabkota_id').select2({
                    width: '100%', // Adjust as needed
                    placeholder: "Pilih",
                    allowClear: true,
                    dropdownParent: $('#modal_create') // Append the dropdown to the modal
                });
            }
        });
    <?php endif; ?>

    $("#is_branch").change(function() {
        if ($(this).val() == 0) {
            $(".is_branch").hide();
        } else {
            $(".is_branch").show();
        }
    });
</script>