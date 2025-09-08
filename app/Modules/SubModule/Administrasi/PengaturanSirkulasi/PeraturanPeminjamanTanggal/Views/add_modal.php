<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Tambah Peraturan Peminjaman Tanggal
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_add" method="post" action="">
                <div class="modal-body">
                    <div id="frm_create_message"></div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="code">Tanggal Awal</label>
                                <div>
                                    <!-- <input required type="text" class="form-control" id="frm_add_DayIndex" name="DayIndex" placeholder="Day Index" value="" /> -->
                                    <input required type="date" class="form-control" id="frm_add_TanggalAwal" name="TanggalAwal" placeholder="Tanggal Awal" value="" />
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="code">Tanggal Akhir</label>
                                <div>
                                    <!-- <input required type="text" class="form-control" id="frm_add_DayIndex" name="DayIndex" placeholder="Day Index" value="" /> -->
                                    <input required type="date" class="form-control" id="frm_add_TanggalAkhir" name="TanggalAkhir" placeholder="Tanggal Akhir" value="" />
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="code">Maks Koleksi Dapat Dipinjam</label>
                                <div>
                                    <input required type="number" min="0" class="form-control" id="frm_add_MaxPinjamKoleksi"
                                        name="MaxPinjamKoleksi" placeholder="Maks Koleksi Dapat Dipinjam" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="name">Maks Lama Pinjam</label>
                                <div>
                                    <input required type="number" min="0" class="form-control" id="frm_add_MaxLoanDays"
                                        name="MaxLoanDays" placeholder="Maks. Lama Pinjam" value="" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="code">Jeda Peringatan peminjaman</label>
                                <div>
                                    <input required type="number" min="0" class="form-control" id="frm_add_WarningLoanDays"
                                        name="WarningLoanDays" placeholder="Jeda Peringatan Peminjaman" value="" />
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="code">Koleksi Yang Dapat Dipinjam</label>
                                <div>
								<select class="form-control select2" name="Category_id[]" multiple="multiple" tabindex="-1" aria-hidden="true" style="width:100%">
									<option value="">-Pilih-</option>
										<?php foreach (get_ref_table('collectioncategorys', 'ID, Name',null,'data') as $row): ?>
								<option value="<?=$row->ID?>"><?=$row->Name?></option>
							    <?php endforeach;?>
								</select>

                                </div>
                            </div>
                        </div>

                    </div>

					<div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="code">Maks Lama Perpanjangan</label>
                                <div>
                                    <input required type="number" min="0" class="form-control" id="frm_add_DayPerpanjang"
                                        name="DayPerpanjang" placeholder="Lama Perpanjangan" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="name">Maks Banyaknya perpanjang</label>
                                <div>
                                    <input required type="number" min="0" class="form-control" id="frm_add_CountPerpanjang"
                                        name="CountPerpanjang" placeholder="Maks. Lama Pinjam" value="" />
                                </div>
                            </div>
                        </div>
                    </div>

					<div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="code">Jumlah Denda</label>
                                <div>
                                    <input required type="number" min="0" class="form-control" id="frm_add_DendaPerTenor"
                                        name="DendaPerTenor" placeholder="Jumlah Denda" value="" />
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="code">Jenis Denda</label>
                                <div>
								<select class="form-control" name="DendaType"  tabindex="-1" aria-hidden="true" style="width:100%">
									<option value="">-Pilih-</option>
										<option value="Konstan">Denda</option>
										<option value="Berkelipatan">Berkelipatan</option>
								</select>

                                </div>
                            </div>
                        </div>

                    </div>

					
            
                   

					<div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="code">Satuan Tenor Denda</label>
                                <div>
                                    <input required type="number" min="0" class="form-control" id="frm_add_DendaTenorJumlah"
                                        name="DendaTenorJumlah" placeholder="Tenor Denda Denda" value="" />
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="code">Tenor Satuan</label>
                                <div>
								<select class="form-control" name="SuspendTenorSatuan"  tabindex="-1" aria-hidden="true" style="width:100%">
									<option value="">-Pilih-</option>
										<option value="Tanggal">Tanggal</option>
										<option value="Minggu">Minggu</option>
										<option value="Bulan">Bulan</option>
										<opttion value="Tahun">Tahun</option>
								</select>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="form-row">
                        
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label for="name">Pengali Tenor Denda</label>
                                <div>
                                    <input required type="number" class="form-control" id="frm_add_DendaPerTenorMultiply"
                                        name="DendaTenorMultipy" placeholder="Pengali Tenor Indah" value="" />
                                </div>
                            </div>
                        </div>
                    </div>

					<div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="code">Skorsing Tipe</label>
                                <div>
								<select required class="form-control" id="frm_add_DendaType" name="DendaType"
                                        placeholder="Denda Type" value="">
                                        <option value="">-- Pilih Denda --</option>
                                        <option value="Konstan">Konstan</option>
                                        <option value="Berkelipatan">Berkelipatan</option>
                                    </select>
								
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="name">Lama Skorsing</label>
                                <div>
								<input required type="number" class="form-control" id="frm_add_DaySuspend"
                                        name="DaySuspend" placeholder="Lama Skorsing" value="" />
                                </div>
                            </div>
                        </div>
                    </div>
					<div class="form-row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="code">Satuan Tenor Skorsing</label>
                                <div>
                                    <input required type="number" min="0" class="form-control" id="frm_add_SuspendTenorJumlah"
                                        name="SuspendTenorJumlah" placeholder="Tenor Skorsing" value="" />
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="code">Tenor Satuan</label>
                                <div>
								<select class="form-control" name="SuspendTenorSatuan"  tabindex="-1" aria-hidden="true" style="width:100%">
									<option value="">-Pilih-</option>
										<option value="Tanggal">Tanggal</option>
										<option value="Minggu">Minggu</option>
										<option value="Bulan">Bulan</option>
										<opttion value="Tahun">Tahun</option>
								</select>

                                </div>
                            </div>
                        </div>

                    </div>

					<div class="form-row">
                        
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label for="name">Pengali Tenor Skorsing</label>
                                <div>
                                    <input required type="number" class="form-control" id="frm_add_SuspendTenorMultiply"
                                        name="SuspendTenorMultipy" placeholder="Pengali Tenor Indah" value="" />
                                </div>
                            </div>
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
 $(document).ready(function() {
	 $('.select2').select2();
 });
</script>
<script>
$('#frm_add').submit(function(event) {
    event.preventDefault();

    var url = "<?=base_url('api/peraturan-peminjaman-tanggal/create')?>";
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
                    html: 'Peraturan Peminjaman Tanggal berhasil ditambah.',
                    type: 'success',
                    showConfirmButton: false,
                    timer: 5000,
                }).then(() => {
                    window.location.href = `<?=base_url('/master-peraturan-peminjaman-tanggal')?>`;
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
        .fail(function(res) {
            console.log(res);

            Swal.fire({
                title: 'Oups',
                text: 'Maaf, terjadi kesalahan. Coba beberapa saat lagi atau hubungi Admin',
                type: 'error',
                showConfirmButton: false,
                timer: 5000
            }).then(() => {
                $("#btnAdd").attr('disabled', false);
                $("#btnAdd").html('Simpan');
            });
        });

    return false;
});

$('#modal_create').on('hidden.bs.modal', function() {
    $(this).find('form').trigger('reset');
    $('#frm_add_message').html('');
});
</script>