<div class="modal fade" id="modal_update" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Edit Jenis Bahan
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
                                <label for="code">Day Index</label>
                                <div>
                                    <!-- <input required type="text" class="form-control" id="frm_update_DayIndex" name="DayIndex" placeholder="Day Index" value="" /> -->
                                    <select id="frm_update_DayIndex" name="DayIndex" class="form-control">
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
                                <label for="code">Maks Koleksi Dapat Dipinjam</label>
                                <div>
                                    <input required type="text" class="form-control" id="frm_update_MaxPinjamKoleksi"
                                        name="MaxPinjamKoleksi" placeholder="Kode Jenis Bahan" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="name">Maks Lama Pinjam</label>
                                <div>
                                    <input required type="text" class="form-control" id="frm_update_MaxLoanDays"
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
                                    <input required type="text" class="form-control" id="frm_update_WarningLoanDays"
                                        name="WarningLoanDays" placeholder="Jeda Peringatan Peminjaman" value="" />
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="code">koleksi Yang Dapat Dipinjam</label>
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
                                    <input required type="text" class="form-control" id="frm_update_DayPerpanjang"
                                        name="DayPerpanjang" placeholder="Lama Perpanjangan" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="name">Maks Banyaknya perpanjang</label>
                                <div>
                                    <input required type="text" class="form-control" id="frm_update_CountPerpanjang"
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
                                    <input required type="text" class="form-control" id="frm_update_DendaPerTenor"
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
                                    <input required type="text" class="form-control" id="frm_update_DendaTenorJumlah"
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
										<option value="Hari">Hari</option>
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
                                    <input required type="number" class="form-control" id="frm_update_DendaPerTenorMultiply"
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
								<select required class="form-control" id="frm_update_DendaType" name="DendaType"
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
								<input required type="number" class="form-control" id="frm_update_DaySuspend"
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
                                    <input required type="text" class="form-control" id="frm_update_SuspendTenorJumlah"
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
										<option value="Hari">Hari</option>
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
                                    <input required type="number" class="form-control" id="frm_update_SuspendTenorMultiply"
                                        name="SuspendTenorMultipy" placeholder="Pengali Tenor Indah" value="" />
                                </div>
                            </div>
                        </div>
                    </div>
                   
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" name="submit" id="btnUpdate">Simpan</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
	$("body").on("click", ".show-data", function() {
		var url = $(this).attr('data-href');
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            success: function(response) {
                $('#frm_update').attr("data-ID", response.ID);
				$('#frm_update_DayIndex').val(response.DayIndex);
				$('#frm_update_MaxPinjamKoleksi').val(response.MaxPinjamKoleksi);
				$('#frm_update_MaxLoanDays').val(response.MaxLoanDays);
				$('#frm_update_DayPerpanjang').val(response.DayPerpanjang);
				$('#frm_update_CountPerpanjang').val(response.CountPerpanjang);
				$('#frm_update_DendaType').val(response.DendaType);
				$('#frm_update_DendaTenorJumlah').val(response.DendaTenorJumlah);
				$('#frm_update_DendaTenorSatuan').val(response.DendaTenorSatuan);
				$('#frm_update_DendaPerTenor').val(response.DendaPerTenor);
				$('#frm_update_DendaTenorMultiply').val(response.DendaTenorMultiply);
				$('#frm_update_SuspendMember').val(response.SuspendMember);
				$('#frm_update_WarningLoanDueDay').val(response.WarningLoanDueDay);
				$('#frm_update_SuspendType').val(response.SuspendType);
				$('#frm_update_SuspendTenorJumlah').val(response.SuspendTenorJumlah);
				$('#frm_update_SuspendTenorSatuan').val(response.SuspendTenorSatuan);
				$('#frm_update_DaySuspend').val(response.DaySuspend);
				$('#frm_update_SuspendTenorMultiply').val(response.SuspendTenorMultiply);
				$('#frm_update_DayPerpanjang').val(response.DayPerpanjang); $('#frm_update').attr("data-ID", response.ID);
                $('#frm_update_MaxPinjamKoleksi').val(response.MaxPinjamKoleksi);
                $('#frm_update_MaxLoanDays').val(response.MaxLoanDays);
                $('#frm_update_DayPerpanjang').val(response.DayPerpanjang);
                $('#frm_update_CountPerpanjang').val(response.CountPerpanjang);
                $('#frm_update_DendaType').val(response.DendaType);
                $('#frm_update_DendaPerTenor').val(response.DendaPerTenor);
                $('#frm_update_SuspendType').val(response.SuspendType);
                $('#frm_update_DaySuspend').val(response.DaySuspend);
                $('#modal_update').modal('show');
            }
        });
	});

    $('#modal_update').on('hidden.bs.modal', function() {
        $(this).find('form').trigger('reset');
        $('#frm_update_message').html('');
    });

    $('#frm_update').submit(function(event) {
        event.preventDefault();
		var url = $(this).data('action') + '/' + $(this).data('id');
		var data_post = $(this).serializeArray();

		$("#btnUpdate").html('<i class="fa fa-spinner fa-spin loading"></i> Mohon menunggu...');
		$("#btnUpdate").attr('disabled', true);

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
					html: 'Jenis Bahan berhasil disimpan.',
					type: 'success',
					showConfirmButton: false,
					timer: 5000,
				}).then(() => {
					window.location.href = `<?=base_url('master-jenis-bahan')?>`;
				});
			} else {				
				Swal.fire({
					title: 'Oups',
					text: res.message,
					type: 'error',
					showConfirmButton: false,
					timer: 5000
				}).then(() => {
					$("#btnUpdate").attr('disabled', false);
					$("#btnUpdate").html('Simpan');
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
				$("#btnUpdate").attr('disabled', false);
				$("#btnUpdate").html('Simpan');
			});
		});
		
		return false;
    });
</script>