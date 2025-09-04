<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Tambah Tujuan Kunjungan
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_add" method="post" action="">
                <div class="modal-body">
                    <div id="frm_add_message"></div>
                    <div class="form-row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="Name">Kode</label>
                                <div>
                                    <input required type="text" class="form-control" id="frm_add_Code" name="Code" placeholder="Code" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label>Tujuan Kunjungan</label>
                            <div>
                                <input required type="text" class="form-control" id="frm_add_TujuanKunjungan" name="TujuanKunjungan" placeholder="TujuanKunjungan" value="" />
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label for="frm_add_Membership">Membership</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="frm_add_Member" name="Member">
                                <label class="form-check-label" for="frm_add_Member">Member</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="frm_add_NonMember" name="NonMember">
                                <label class="form-check-label" for="frm_add_NonMember">NonMember</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="frm_add_Rombongan" name="Rombongan">
                                <label class="form-check-label" for="frm_add_Rombongan">Rombongan</label>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" name="submit" id="btnAdd">Simpan</button>
                    </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#frm_add').submit(function(event) {
        event.preventDefault();

        var url = "<?= base_url('api/tujuan-kunjungan/create') ?>";
        var data_post = $(this).serializeArray();

        $("#btnAdd").html('<i class="fa fa-spinner fa-spin loading"></i> Mohon menunggu...');
        $("#btnAdd").attr('disabled', true);

        $.ajax({
                url: url,
                type: 'POST',
                data: data_post,
            })
            .done(function(res) {
                console.log(res)

                if (res.error == false) {
                    Swal.fire({
                        title: 'Berhasil',
                        html: 'Tujuan Kunjungan berhasil ditambah.',
                        type: 'success',
                        showConfirmButton: false,
                        timer: 5000,
                    }).then(() => {
                        window.location.href = `<?= base_url('master-tujuan-kunjungan') ?>`;
                    });
                } else {
                    Swal.fire({
                        title: 'Oups',
                        text: res.message,
                        type: 'error',
                        showConfirmButton: false,
                        timer: 5000
                    }).then(() => {
                        $("#btnAdd").attr('disabled', false);
                        $("#btnAdd").html('Simpan');
                    });
                }
            })
            .fail(function(xhr) {
                let res = xhr.responseJSON;
                let title = 'Oups';
                let html = 'Maaf, terjadi kesalahan. Coba beberapa saat lagi atau hubungi Admin';

                if (res && res.message) {
                    let errors = Object.values(res.message)
                        .map(value => `<div>${value}</div>`)
                        .join('');
                    html = `<p style="text-align:center">${errors}</p>`;
                }

                Swal.fire({
                    title: title,
                    html: html,
                    type: 'error',
                    showConfirmButton: false,
                    timer: 5000
                }).then(() => {
                    $("#btnAdd").attr('disabled', false).html('Simpan');
                });
            });

        return false;
    });

    $('#modal_create').on('hidden.bs.modal', function() {
        $(this).find('form').trigger('reset');
        $('#frm_add_message').html('');
    });
</script>