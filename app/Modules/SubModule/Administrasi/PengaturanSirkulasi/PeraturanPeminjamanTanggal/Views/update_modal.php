<div class="modal fade" id="modal_update" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Edit Peraturan Peminjaman Hari
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_update" method="post" data-action="<?= base_url('api/peraturan-peminjaman-hari/edit') ?>" data-ID="">
                <div class="modal-body">
                    <div id="frm_create_message"></div>

                    <div class="form-row">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label for="frm_update_DayIndex">Day Index</label>
                                <div>
                                    <select id="frm_update_DayIndex" name="DayIndex" class="form-control" required>
                                        <option value="">-Pilih Hari-</option>
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
                                <label for="frm_update_MaxPinjamKoleksi">Maks Koleksi Dapat Dipinjam</label>
                                <div>
                                    <input required type="number" class="form-control" id="frm_update_MaxPinjamKoleksi"
                                        name="MaxPinjamKoleksi" placeholder="Maks Koleksi" min="0" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_MaxLoanDays">Maks Lama Pinjam (Hari)</label>
                                <div>
                                    <input required type="number" class="form-control" id="frm_update_MaxLoanDays"
                                        name="MaxLoanDays" placeholder="Maks Lama Pinjam" min="0" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_WarningLoanDueDay">Jeda Peringatan Peminjaman (Hari)</label>
                                <div>
                                    <input required type="number" class="form-control" id="frm_update_WarningLoanDueDay"
                                        name="WarningLoanDueDay" placeholder="Jeda Peringatan" min="0" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_Category_id">Koleksi Yang Dapat Dipinjam</label>
                                <div>
                                    <select class="form-control select2" id="frm_update_Category_id" name="Category_id[]" multiple="multiple" style="width:100%">
                                        <?php foreach (get_ref_table('collectioncategorys', 'ID, Name', null, 'data') as $row): ?>
                                            <option value="<?=$row->ID?>"><?=$row->Name?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_DayPerpanjang">Maks Lama Perpanjangan (Hari)</label>
                                <div>
                                    <input required type="number" class="form-control" id="frm_update_DayPerpanjang"
                                        name="DayPerpanjang" placeholder="Lama Perpanjangan" min="0" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_CountPerpanjang">Maks Banyaknya Perpanjang</label>
                                <div>
                                    <input required type="number" class="form-control" id="frm_update_CountPerpanjang"
                                        name="CountPerpanjang" placeholder="Maks Perpanjang" min="0" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_DendaPerTenor">Jumlah Denda</label>
                                <div>
                                    <input required type="number" class="form-control" id="frm_update_DendaPerTenor"
                                        name="DendaPerTenor" placeholder="Jumlah Denda" step="0.01" min="0" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_DendaType">Jenis Denda</label>
                                <div>
                                    <select class="form-control" id="frm_update_DendaType" name="DendaType" style="width:100%">
                                        <option value="">-Pilih-</option>
                                        <option value="Konstan">Konstan</option>
                                        <option value="Berkelipatan">Berkelipatan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_DendaTenorJumlah">Satuan Tenor Denda</label>
                                <div>
                                    <input required type="number" class="form-control" id="frm_update_DendaTenorJumlah"
                                        name="DendaTenorJumlah" placeholder="Tenor Denda" min="0" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_DendaTenorSatuan">Tenor Satuan Denda</label>
                                <div>
                                    <select class="form-control" id="frm_update_DendaTenorSatuan" name="DendaTenorSatuan" style="width:100%">
                                        <option value="">-Pilih-</option>
                                        <option value="Hari">Hari</option>
                                        <option value="Minggu">Minggu</option>
                                        <option value="Bulan">Bulan</option>
                                        <option value="Tahun">Tahun</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label for="frm_update_DendaTenorMultiply">Pengali Tenor Denda</label>
                                <div>
                                    <input required type="number" class="form-control" id="frm_update_DendaTenorMultiply"
                                        name="DendaTenorMultiply" placeholder="Pengali Tenor Denda" step="0.01" min="0" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_SuspendType">Skorsing Tipe</label>
                                <div>
                                    <select class="form-control" id="frm_update_SuspendType" name="SuspendType" style="width:100%">
                                        <option value="">-- Pilih Skorsing --</option>
                                        <option value="Konstan">Konstan</option>
                                        <option value="Berkelipatan">Berkelipatan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_DaySuspend">Lama Skorsing (Hari)</label>
                                <div>
                                    <input required type="number" class="form-control" id="frm_update_DaySuspend"
                                        name="DaySuspend" placeholder="Lama Skorsing" min="0" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_SuspendTenorJumlah">Satuan Tenor Skorsing</label>
                                <div>
                                    <input required type="number" class="form-control" id="frm_update_SuspendTenorJumlah"
                                        name="SuspendTenorJumlah" placeholder="Tenor Skorsing" min="0" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="frm_update_SuspendTenorSatuan">Tenor Satuan Skorsing</label>
                                <div>
                                    <select class="form-control" id="frm_update_SuspendTenorSatuan" name="SuspendTenorSatuan" style="width:100%">
                                        <option value="">-Pilih-</option>
                                        <option value="Hari">Hari</option>
                                        <option value="Minggu">Minggu</option>
                                        <option value="Bulan">Bulan</option>
                                        <option value="Tahun">Tahun</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label for="frm_update_SuspendTenorMultiply">Pengali Tenor Skorsing</label>
                                <div>
                                    <input required type="number" class="form-control" id="frm_update_SuspendTenorMultiply"
                                        name="SuspendTenorMultiply" placeholder="Pengali Tenor Skorsing" step="0.01" min="0" />
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
$(document).ready(function() {
    // Initialize select2 if available
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            placeholder: "Pilih kategori...",
            allowClear: true,
            width: '100%'
        });
    }

    // Handle show data button click
    $("body").on("click", ".show-data", function() {
        var url = $(this).attr('data-href');
        
        // Show loading indicator
        var loadingHtml = '<div id="loading-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); display: flex; align-items: center; justify-content: center; z-index: 1000;"><div class="text-center"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><br><small class="text-muted mt-2">Memuat data...</small></div></div>';
        $('#modal_update .modal-body').css('position', 'relative').append(loadingHtml);
        
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            timeout: 10000, // 10 second timeout
            success: function(response) {
                console.log('Response data:', response);
                
                // Remove loading overlay
                $('#loading-overlay').remove();
                
                if (response && typeof response === 'object') {
                    // Set the form data-ID attribute
                    $('#frm_update').attr("data-ID", response.ID || '');
                    
                    // Populate all form fields with fallback to empty string
                    $('#frm_update_DayIndex').val(response.DayIndex || '');
                    $('#frm_update_MaxPinjamKoleksi').val(response.MaxPinjamKoleksi || '');
                    $('#frm_update_MaxLoanDays').val(response.MaxLoanDays || '');
                    $('#frm_update_WarningLoanDueDay').val(response.WarningLoanDueDay || response.WarningLoanDueDay || '');
                    $('#frm_update_DayPerpanjang').val(response.DayPerpanjang || '');
                    $('#frm_update_CountPerpanjang').val(response.CountPerpanjang || '');
                    $('#frm_update_DendaPerTenor').val(response.DendaPerTenor || '');
                    $('#frm_update_DendaTenorJumlah').val(response.DendaTenorJumlah || '');
                    $('#frm_update_DendaTenorMultiply').val(response.DendaTenorMultiply || response.DendaTenorMultiply || '');
                    $('#frm_update_DaySuspend').val(response.DaySuspend || '');
                    $('#frm_update_SuspendTenorJumlah').val(response.SuspendTenorJumlah || '');
                    $('#frm_update_SuspendTenorMultiply').val(response.SuspendTenorMultiply || '');
                    
                    // Handle select fields
                    $('#frm_update_DendaType').val(response.DendaType || '');
                    $('#frm_update_DendaTenorSatuan').val(response.DendaTenorSatuan || '');
                    $('#frm_update_SuspendType').val(response.SuspendType || '');
                    $('#frm_update_SuspendTenorSatuan').val(response.SuspendTenorSatuan || '');
                    
                    // Handle multiple select for categories
                    if (response.Category_id) {
                        var categoryIds = Array.isArray(response.Category_id) ? response.Category_id : [response.Category_id];
                        $('#frm_update_Category_id').val(categoryIds);
                        
                        // Trigger change event for select2
                        if (typeof $.fn.select2 !== 'undefined') {
                            $('#frm_update_Category_id').trigger('change');
                        }
                    } else {
                        $('#frm_update_Category_id').val([]);
                        if (typeof $.fn.select2 !== 'undefined') {
                            $('#frm_update_Category_id').trigger('change');
                        }
                    }
                    
                    // Show the modal
                    $('#modal_update').modal('show');
                } else {
                    console.error('Invalid response format:', response);
                    Swal.fire({
                        title: 'Error',
                        text: 'Format data tidak valid. Silakan coba lagi.',
                        type: 'error',
                        showConfirmButton: true,
                        timer: 5000
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });
                
                $('#loading-overlay').remove();
                
                var errorMessage = 'Gagal memuat data.';
                if (xhr.status === 404) {
                    errorMessage = 'Data tidak ditemukan.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Terjadi kesalahan server.';
                } else if (status === 'timeout') {
                    errorMessage = 'Koneksi timeout. Silakan coba lagi.';
                }
                
                Swal.fire({
                    title: 'Error',
                    text: errorMessage + ' Silakan coba lagi.',
                    type: 'error',
                    showConfirmButton: true,
                    timer: 5000
                });
            }
        });
    });

    // Reset form when modal is hidden
    $('#modal_update').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $('#frm_create_message').html('');
        
        // Reset select2 if using it
        if (typeof $.fn.select2 !== 'undefined') {
            $('#frm_update_Category_id').val(null).trigger('change');
        }
        
        // Reset form data-ID
        $('#frm_update').attr('data-ID', '');
    });

    // Handle form submission
    $('#frm_update').submit(function(event) {
        event.preventDefault();
        
        var formId = $(this).attr('data-ID');
        if (!formId) {
            Swal.fire({
                title: 'Error',
                text: 'ID tidak ditemukan. Silakan tutup modal dan coba lagi.',
                type: 'error',
                showConfirmButton: true
            });
            return false;
        }
        
        var url = $(this).data('action') + '/' + formId;
        var formData = new FormData(this);
        
        // Debug: log form data
        console.log('Form ID:', formId);
        console.log('Submit URL:', url);
        
        // Show loading state
        $("#btnUpdate").html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
        $("#btnUpdate").attr('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            timeout: 30000, // 30 second timeout
            success: function(res) {
                console.log('Update response:', res);

                if (res && res.error === false) {
                    Swal.fire({
                        title: 'Berhasil',
                        text: res.message || 'Data berhasil disimpan.',
                        type: 'success',
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(() => {
                        $('#modal_update').modal('hide');
                        
                        // Reload datatable if available
                        if (typeof t !== 'undefined' && t.ajax) {
                            t.ajax.reload(null, false); // Keep current page
                        } else if (typeof table !== 'undefined' && table.ajax) {
                            table.ajax.reload(null, false);
                        } else {
                            // Fallback: reload page
                            window.location.reload();
                        }
                    });
                } else {
                    var errorMsg = (res && res.message) ? res.message : 'Terjadi kesalahan saat menyimpan data';
                    
                    Swal.fire({
                        title: 'Gagal',
                        text: errorMsg,
                        type: 'error',
                        showConfirmButton: true,
                        timer: 5000
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Update error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });

                var errorMessage = 'Terjadi kesalahan saat menyimpan.';
                if (status === 'timeout') {
                    errorMessage = 'Koneksi timeout. Silakan coba lagi.';
                } else if (xhr.status === 422) {
                    errorMessage = 'Data yang dikirim tidak valid.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Terjadi kesalahan server.';
                }

                Swal.fire({
                    title: 'Error',
                    text: errorMessage,
                    type: 'error',
                    showConfirmButton: true,
                    timer: 5000
                });
            },
            complete: function() {
                // Reset button state
                $("#btnUpdate").attr('disabled', false);
                $("#btnUpdate").html('Simpan');
            }
        });
        
        return false;
    });
});
</script>