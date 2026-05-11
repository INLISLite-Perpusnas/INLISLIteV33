<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Tambah Peraturan Peminjaman Hari
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_add" method="post" action="">
                <div class="modal-body">
                    <div id="frm_create_message"></div>

                    <div class="form-row">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label for="code">Day Index</label>
                                <div>
                                    <select id="frm_add_DayIndex" name="DayIndex" class="form-control">
                                        <option value="1">Senin</option>
                                        <option value="2">Selasa</option>
                                        <option value="3">Rabu</option>
                                        <option value="4">Kamis</option>
                                        <option value="5">Jumat</option>
                                        <option value="6">Sabtu</option>
                                        <option value="7">Minggu</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label>Maks Koleksi Dapat Dipinjam</label>
                                <input required type="number" min="0" class="form-control" id="frm_add_MaxPinjamKoleksi"
                                    name="MaxPinjamKoleksi" placeholder="Maks Koleksi Dapat Dipinjam" value="" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label>Maks Lama Pinjam</label>
                                <input required type="number" min="0" class="form-control" id="frm_add_MaxLoanDays"
                                    name="MaxLoanDays" placeholder="Maks. Lama Pinjam" value="" />
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label>Jeda Peringatan Peminjaman</label>
                                <input required type="number" min="0" class="form-control" id="frm_add_WarningLoanDays"
                                    name="WarningLoanDays" placeholder="Jeda Peringatan Peminjaman" value="" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label>Koleksi Yang Dapat Dipinjam</label>
                                <!-- ✅ Hapus tabindex="-1" dan aria-hidden="true" -->
                                <select class="form-control select2-koleksi" name="Category_id[]" multiple="multiple" style="width:100%">
                                    <option value="">-Pilih-</option>
                                    <?php foreach (get_ref_table('collectioncategorys', 'ID, Name', null, 'data') as $row): ?>
                                        <option value="<?= $row->ID ?>"><?= $row->Name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label>Maks Lama Perpanjangan</label>
                                <input required type="number" min="0" class="form-control" id="frm_add_DayPerpanjang"
                                    name="DayPerpanjang" placeholder="Lama Perpanjangan" value="" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label>Maks Banyaknya Perpanjang</label>
                                <input required type="number" min="0" class="form-control" id="frm_add_CountPerpanjang"
                                    name="CountPerpanjang" placeholder="Maks. Banyak Perpanjang" value="" />
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label>Jumlah Denda</label>
                                <input required type="number" min="0" class="form-control" id="frm_add_DendaPerTenor"
                                    name="DendaPerTenor" placeholder="Jumlah Denda" value="" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label>Jenis Denda</label>
                                <select class="form-control" name="DendaType" style="width:100%">
                                    <option value="">-Pilih-</option>
                                    <option value="Konstan">Denda</option>
                                    <option value="Berkelipatan">Berkelipatan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label>Satuan Tenor Denda</label>
                                <input required type="number" min="0" class="form-control" id="frm_add_DendaTenorJumlah"
                                    name="DendaTenorJumlah" placeholder="Tenor Denda" value="" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label>Tenor Satuan Denda</label>
                                <!-- ✅ Perbaiki typo <opttion> → <option> -->
                                <select class="form-control" name="DendaTenorSatuan" style="width:100%">
                                    <option value="">-Pilih-</option>
                                    <option value="Hari">Hari</option>
                                    <option value="Minggu">Minggu</option>
                                    <option value="Bulan">Bulan</option>
                                    <option value="Tahun">Tahun</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label>Pengali Tenor Denda</label>
                                <input required type="number" class="form-control" id="frm_add_DendaPerTenorMultiply"
                                    name="DendaTenorMultipy" placeholder="Pengali Tenor Denda" value="" />
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label>Skorsing Tipe</label>
                                <select required class="form-control" id="frm_add_SkorsingType" name="SkorsingType" style="width:100%">
                                    <option value="">-- Pilih Tipe --</option>
                                    <option value="Konstan">Konstan</option>
                                    <option value="Berkelipatan">Berkelipatan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label>Lama Skorsing</label>
                                <input required type="number" class="form-control" id="frm_add_DaySuspend"
                                    name="DaySuspend" placeholder="Lama Skorsing" value="" />
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label>Satuan Tenor Skorsing</label>
                                <input required type="number" min="0" class="form-control" id="frm_add_SuspendTenorJumlah"
                                    name="SuspendTenorJumlah" placeholder="Tenor Skorsing" value="" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label>Tenor Satuan Skorsing</label>
                                <!-- ✅ Perbaiki typo <opttion> → <option> -->
                                <select class="form-control" name="SuspendTenorSatuan" style="width:100%">
                                    <option value="">-Pilih-</option>
                                    <option value="Hari">Hari</option>
                                    <option value="Minggu">Minggu</option>
                                    <option value="Bulan">Bulan</option>
                                    <option value="Tahun">Tahun</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label>Pengali Tenor Skorsing</label>
                                <input required type="number" class="form-control" id="frm_add_SuspendTenorMultiply"
                                    name="SuspendTenorMultipy" placeholder="Pengali Tenor Skorsing" value="" />
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" name="submit" id="btnAdd">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // ✅ Inisialisasi Select2 saat modal dibuka, bukan di document.ready
    $('#modal_create').on('shown.bs.modal', function() {
        $('.select2-koleksi').select2({
            dropdownParent: $('#modal_create'),
            width: '100%',
            placeholder: '-Pilih Koleksi-',
            allowClear: true
        });
    });

    // ✅ Reset form & Select2 saat modal ditutup
    $('#modal_create').on('hidden.bs.modal', function() {
        $(this).find('form').trigger('reset');
        $('#frm_create_message').html('');
        $('.select2-koleksi').val(null).trigger('change');
    });

    // ✅ Submit form
    $('#frm_add').submit(function(event) {
        event.preventDefault();

        var url = "<?= base_url('api/peraturan-peminjaman-hari/create') ?>";
        var data_post = $(this).serializeArray();

        $("#btnAdd").html('<i class="fa fa-spinner fa-spin"></i> Mohon menunggu...');
        $("#btnAdd").attr('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: data_post,
        })
        .done(function(res) {
            console.log(res);

            if (res.error == false) {
                Swal.fire({
                    title: 'Berhasil',
                    html: 'Peraturan Peminjaman Hari berhasil ditambah.',
                    icon: 'success', // ✅ ganti type → icon
                    showConfirmButton: false,
                    timer: 5000,
                }).then(() => {
                    window.location.href = `<?= base_url('/master-peraturan-peminjaman-hari') ?>`;
                });
            } else {
                Swal.fire({
                    title: 'Oups',
                    text: res.message,
                    icon: 'error', // ✅ ganti type → icon
                    showConfirmButton: false,
                    timer: 5000
                }).then(() => {
                    $("#btnAdd").attr('disabled', false);
                    $("#btnAdd").html('Simpan');
                });
            }
        })
        .fail(function(res) {
            console.log(res);

            Swal.fire({
                title: 'Oups',
                text: 'Maaf, terjadi kesalahan. Coba beberapa saat lagi atau hubungi Admin.',
                icon: 'error', // ✅ ganti type → icon
                showConfirmButton: false,
                timer: 5000
            }).then(() => {
                $("#btnAdd").attr('disabled', false);
                $("#btnAdd").html('Simpan');
            });
        });

        return false;
    });
</script>