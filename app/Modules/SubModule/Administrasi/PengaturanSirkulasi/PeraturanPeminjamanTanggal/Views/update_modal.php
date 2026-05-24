<div class="modal fade" id="modal_update" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Edit Peraturan Peminjaman Tanggal
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_update" method="post" data-action="<?= base_url('api/peraturan-peminjaman-tanggal/edit') ?>" data-ID="">
                <div class="modal-body">
                    <div id="frm_update_message"></div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_TanggalAwal">Tanggal Awal</label>
                                <input required type="date" class="form-control" id="frm_update_TanggalAwal" name="TanggalAwal" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_TanggalAkhir">Tanggal Akhir</label>
                                <input required type="date" class="form-control" id="frm_update_TanggalAkhir" name="TanggalAkhir" />
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_MaxPinjamKoleksi">Maks Koleksi Dapat Dipinjam</label>
                                <input required type="number" min="0" class="form-control" id="frm_update_MaxPinjamKoleksi" name="MaxPinjamKoleksi" placeholder="Maks Koleksi" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_MaxLoanDays">Maks Lama Pinjam</label>
                                <input required type="number" min="0" class="form-control" id="frm_update_MaxLoanDays" name="MaxLoanDays" placeholder="Maks Lama Pinjam" />
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_WarningLoanDueDay">Jeda Peringatan Peminjaman</label>
                                <input required type="number" min="0" class="form-control" id="frm_update_WarningLoanDueDay" name="WarningLoanDueDay" placeholder="Jeda Peringatan" />
                            </div>
                        </div>
                        
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_DayPerpanjang">Maks Lama Perpanjangan</label>
                                <input required type="number" min="0" class="form-control" id="frm_update_DayPerpanjang" name="DayPerpanjang" placeholder="Lama Perpanjangan" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_CountPerpanjang">Maks Banyaknya Perpanjang</label>
                                <input required type="number" min="0" class="form-control" id="frm_update_CountPerpanjang" name="CountPerpanjang" placeholder="Maks Perpanjang" />
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_DendaPerTenor">Jumlah Denda</label>
                                <input required type="number" min="0" class="form-control" id="frm_update_DendaPerTenor" name="DendaPerTenor" placeholder="Jumlah Denda" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_DendaType">Jenis Denda</label>
                                <select class="form-control" id="frm_update_DendaType" name="DendaType" style="width:100%">
                                    <option value="">-Pilih-</option>
                                    <?php foreach (get_ref_table('jenis_denda', 'ID, Name', null, 'data') as $row): ?>
                                        <option value="<?= $row->Name ?>"><?= $row->Name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_DendaTenorJumlah">Satuan Tenor Denda</label>
                                <input required type="number" min="0" class="form-control" id="frm_update_DendaTenorJumlah" name="DendaTenorJumlah" placeholder="Tenor Denda" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_DendaTenorSatuan">Tenor Satuan Denda</label>
                                <select class="form-control" id="frm_update_DendaTenorSatuan" name="DendaTenorSatuan" style="width:100%">
                                    <option value="">-Pilih-</option>
                                    <option value="Tanggal">Tanggal</option>
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
                                <label for="frm_update_DendaTenorMultiply">Pengali Tenor Denda</label>
                                <input required type="number" class="form-control" id="frm_update_DendaTenorMultiply" name="DendaTenorMultiply" placeholder="Pengali Tenor Denda" />
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_SuspendType">Skorsing Tipe</label>
                                <select class="form-control" id="frm_update_SuspendType" name="SuspendType" style="width:100%">
                                    <option value="">-- Pilih Skorsing --</option>
                                    <option value="Konstan">Konstan</option>
                                    <option value="Berkelipatan">Berkelipatan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_DaySuspend">Lama Skorsing</label>
                                <input required type="number" min="0" class="form-control" id="frm_update_DaySuspend" name="DaySuspend" placeholder="Lama Skorsing" />
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_SuspendTenorJumlah">Satuan Tenor Skorsing</label>
                                <input required type="number" min="0" class="form-control" id="frm_update_SuspendTenorJumlah" name="SuspendTenorJumlah" placeholder="Tenor Skorsing" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_SuspendTenorSatuan">Tenor Satuan Skorsing</label>
                                <select class="form-control" id="frm_update_SuspendTenorSatuan" name="SuspendTenorSatuan" style="width:100%">
                                    <option value="">-Pilih-</option>
                                    <option value="Tanggal">Tanggal</option>
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
                                <label for="frm_update_SuspendTenorMultiply">Pengali Tenor Skorsing</label>
                                <input required type="number" class="form-control" id="frm_update_SuspendTenorMultiply" name="SuspendTenorMultiply" placeholder="Pengali Tenor Skorsing" />
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="frm_update_SuspendMember"
                                        name="SuspendMember" value="1">
                                    <label class="custom-control-label" for="frm_update_SuspendMember">Suspend Member</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" name="submit" id="btnUpdate">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$('#modal_update').on('shown.bs.modal', function() {
    if (!$('#frm_update_Category_id').hasClass('select2-hidden-accessible')) {
        $('#frm_update_Category_id').select2({
            dropdownParent: $('#modal_update'),
            placeholder: '-Pilih-',
            allowClear: true,
        });
    }
});

$("body").on("click", ".show-data", function() {
    var url = $(this).attr('data-href');

    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('API response:', response);

            var data = response.data !== undefined ? response.data : response;

            $('#frm_update').attr('data-ID', data.ID || '');

            $('#frm_update_TanggalAwal').val(data.TanggalAwal ? data.TanggalAwal.substring(0, 10) : '');
            $('#frm_update_TanggalAkhir').val(data.TanggalAkhir ? data.TanggalAkhir.substring(0, 10) : '');
            $('#frm_update_MaxPinjamKoleksi').val(data.MaxPinjamKoleksi || '');
            $('#frm_update_MaxLoanDays').val(data.MaxLoanDays || '');
            $('#frm_update_WarningLoanDueDay').val(data.WarningLoanDueDay || data.WarningLoanDueDay || '');
            $('#frm_update_DayPerpanjang').val(data.DayPerpanjang || '');
            $('#frm_update_CountPerpanjang').val(data.CountPerpanjang || '');
            $('#frm_update_DendaPerTenor').val(data.DendaPerTenor || '');
            $('#frm_update_DendaTenorJumlah').val(data.DendaTenorJumlah || '');
            $('#frm_update_DendaTenorMultiply').val(data.DendaTenorMultiply || '');
            $('#frm_update_DaySuspend').val(data.DaySuspend || '');
            $('#frm_update_SuspendTenorJumlah').val(data.SuspendTenorJumlah || '');
            $('#frm_update_SuspendTenorMultiply').val(data.SuspendTenorMultiply || '');

            $('#frm_update_DendaType').val(data.DendaType || '');
            $('#frm_update_DendaTenorSatuan').val(data.DendaTenorSatuan || '');
            $('#frm_update_SuspendType').val(data.SuspendType || '');
            $('#frm_update_SuspendTenorSatuan').val(data.SuspendTenorSatuan || '');

            // Populate checkbox SuspendMember (bit field: 1 = checked)
            $('#frm_update_SuspendMember').prop('checked', data.SuspendMember == 1);

            var categoryIds = Array.isArray(data.Category_id) ? data.Category_id : [];
            if ($('#frm_update_Category_id').hasClass('select2-hidden-accessible')) {
                $('#frm_update_Category_id').val(categoryIds).trigger('change');
            } else {
                $('#frm_update_Category_id').val(categoryIds);
            }

            $('#modal_update').modal('show');
        },
        error: function(xhr) {
            var errorMessage = xhr.status === 404 ? 'Data tidak ditemukan.' : 'Terjadi kesalahan server. Silakan coba lagi.';
            Swal.fire({ title: 'Error', text: errorMessage, icon: 'error', showConfirmButton: true });
        }
    });
});

$('#modal_update').on('hidden.bs.modal', function() {
    $(this).find('form')[0].reset();
    $('#frm_update').attr('data-ID', '');
    $('#frm_update_Category_id').val(null).trigger('change');
});

$('#frm_update').submit(function(event) {
    event.preventDefault();

    var formId = $(this).attr('data-ID');
    if (!formId) {
        Swal.fire({ title: 'Error', text: 'ID tidak ditemukan. Silakan tutup modal dan coba lagi.', icon: 'error' });
        return false;
    }

    var url = $(this).data('action') + '/' + formId;
    $("#btnUpdate").html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);

    $.ajax({
        url: url,
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            if (res && res.error === false) {
                Swal.fire({ title: 'Berhasil', text: res.message, icon: 'success', showConfirmButton: false, timer: 2000 })
                    .then(() => {
                        $('#modal_update').modal('hide');
                        if (typeof t !== 'undefined' && t.ajax) { t.ajax.reload(null, false); } else { window.location.reload(); }
                    });
            } else {
                Swal.fire({ title: 'Gagal', text: res.message || 'Terjadi kesalahan', icon: 'error', showConfirmButton: true });
            }
        },
        error: function(xhr) {
            Swal.fire({ title: 'Error', text: xhr.status === 500 ? 'Terjadi kesalahan server.' : 'Terjadi kesalahan saat menyimpan.', icon: 'error', showConfirmButton: true });
        },
        complete: function() {
            $("#btnUpdate").attr('disabled', false).html('Simpan');
        }
    });

    return false;
});
</script>
