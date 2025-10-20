<div class="mb-3 card">
	<div class="card-header-tab card-header">
		<div class="card-header-title">
			<i class="header-icon lnr-list icon-gradient bg-success"> </i>
			Daftar Peminjaman
		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table style="width: 100%;" id="tbl_loan" class="table table-hover table-bordered">
				<thead class="bg-night-sky text-light">
					<tr>
						<th class="text-center" width="35">No</th>
						<th class="text-center" width="150">No. Transaksi /<br>No. Barcode</th>
						<th class="text-center">Penerbit / Judul</th>
						<th class="text-center" width="100">Tgl. Pinjam /<br>Jatuh Tempo</th>
						<th class="text-center">Hari Terlambat</th>
						<th class="text-center" width="100">Updated Date</th>
						<th class="text-center" width="100">Aksi</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>



<?= $this->section('script'); ?>
<!-- Modal Pelanggaran -->
<div class="modal fade" id="modalViolation" tabindex="-1" role="dialog" aria-labelledby="modalViolationLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="modalViolationLabel">
                    <i class="pe-7s-attention"></i> Tambah Pelanggaran
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formViolation" class="text-left">
                    <!-- Info Peminjaman -->
                    <div class="alert alert-warning" role="alert">
                        <strong>Informasi Peminjaman:</strong>
                        <div class="mt-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <small>
                                        <strong>No. Transaksi:</strong> <span id="info-loan-id">-</span><br>
                                        <strong>No. Barcode:</strong> <span id="info-barcode">-</span>
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <small>
                                        <strong>Judul Buku:</strong> <span id="info-title">-</span><br>
                                        <strong>Hari Terlambat:</strong> <span class="badge badge-danger" id="info-latedays">0 hari</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Fields -->
                    <input type="hidden" name="collection_loan_item_id" id="collection_loan_item_id">
                    <input type="hidden" name="collection_loan_id" id="collection_loan_id">
                    <input type="hidden" name="member_id" id="member_id">
                    <input type="hidden" name="collection_id" id="collection_id">
                    <input type="hidden" name="late_days" id="late_days">

                    <!-- Form Fields -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jenis_pelanggaran_id">Jenis Pelanggaran <span class="text-danger">*</span></label>
                                <select class="form-control" id="jenis_pelanggaran_id" name="jenis_pelanggaran_id" required>
                                    <option value="">-- Pilih Jenis Pelanggaran --</option>
                                    <?php foreach (get_table('jenis_pelanggaran', 'ID, JenisPelanggaran', null, 'data') as $row) : ?>
                                        <option value="<?= $row->ID ?>"><?= $row->JenisPelanggaran ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jenis_denda_id">Jenis Denda <span class="text-danger">*</span></label>
                                <select class="form-control" id="jenis_denda_id" name="jenis_denda_id" required>
                                    <option value="">-- Pilih Jenis Denda --</option>
                                    <?php foreach (get_table('jenis_denda', 'ID, Name', null, 'data') as $row) : ?>
                  <option value="<?= $row->ID ?>" <?= set_select('ID', $row->ID) ?>><?= $row->Name ?></option>
                <?php endforeach; ?>
                        </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jumlah_denda">Jumlah Denda per Hari (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="jumlah_denda" name="jumlah_denda" value="0" min="0" required>
                                <small class="form-text text-muted">Denda akan dikalikan dengan hari keterlambatan</small>
                            </div>
                            <div class="alert alert-info">
                                <strong>Total Denda:</strong> Rp <span id="total_denda_display">0</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jumlah_suspend">Jumlah Hari Suspend</label>
                                <input type="number" class="form-control" id="jumlah_suspend" name="jumlah_suspend" value="0" min="0">
                                <small class="form-text text-muted">Kosongkan atau isi 0 jika tidak ada suspend</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="catatan">Catatan</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Catatan tambahan (opsional)"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="pe-7s-close"></i> Batal
                </button>
                <button type="button" class="btn btn-warning" id="btnSaveViolation">
                    <i class="pe-7s-diskette"></i> Simpan Pelanggaran
                </button>
            </div>
        </div>
    </div>
</div>
<script>
	var groupColumn = 8;
	var t;
	$(document).ready(function() {
		t = $('#tbl_loan').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				"url": '<?php echo site_url('api/sirkulasi-peminjaman/loan_datatable/' . $member_no ?? '') ?>',
			},
			"dom": "<'row'<'col-md-6 col-sm-8 col-xs-12 text-left'f><'col-md-6 col-sm-4 col-xs-12 d-none d-sm-block text-right'p>>" +
				"<'row'<'col-md-12'tr>>" +
				"<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12 text-right'i>>",
			"pagingType": "full_numbers",
			"oLanguage": {
				"sSearch": "<i class='fa fa-search'></i> _INPUT_",
				"sLengthMenu": "_MENU_",
				"oPaginate": {
					"sNext": "<i class='fa fa-chevron-right'></i>",
					"sPrevious": "<i class='fa fa-chevron-left'></i>",
					"sLast": "<i class='fa fa-chevron-double-right'></i>",
					"sFirst": "<i class='fa fa-chevron-double-left'></i>",
				}
			},
			"columns": [{
					data: 'no',
					className: 'text-center',
					orderable: false
				},
				{
					data: 'NomorBarcode'
				},
				{
					data: 'Title'
				},
				{
					data: 'LoanDate',
					className: 'text-center'
				},
				{
					data: 'LateDays',
					className: 'text-center'
				},
				{
					data: 'UpdateDate'
				},
				{
					data: 'action',
					className: 'text-center',
					orderable: false
				},
				{
					data: 'CollectionLoan_id',
					visible: false
				},
				{
					data: 'ID',
					visible: false
				},
				{
					data: 'Fullname',
					visible: false
				},
				{
					data: 'DueDate',
					visible: false
				},
				{
					data: 'Publisher',
					visible: false
				},
			],
			"columnDefs": [{
					targets: [0, 6],
					searchable: false
				},
				{
					targets: [0, 2, 3, 4, 5, 6],
					orderable: false
				},
				{
					targets: groupColumn,
					visible: false
				},
			],
			"order": [
				[5, "desc"]
			],
			"drawCallback": function(data, type, full, meta) {
				$('[data-toggle="tooltip"]').tooltip();

				var api = this.api();
				var data = api.rows().data();
			},
			"initComplete": function(settings, json) {
				var $searchInput = $('div.dataTables_filter input');
				$searchInput.unbind();
				$searchInput.bind('keyup', function(e) {
					if (e.keyCode == 13) {
						if (this.value.length == 0) {
							t.search('').draw();
						}

						if (this.value.length >= 3) {
							t.search(this.value).draw();
						}
					}
				});
			}
		});
	});

	$("body").on("click", ".remove-data", function() {
		var url = $(this).attr('data-href');
		console.log(url);
		Swal.fire({
			title: '<?= lang('App.swal.are_you_sure') ?>',
			html: "<?= lang('App.swal.can_not_be_restored') ?>",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#dd6b55',
			confirmButtonText: '<?= lang('App.btn.yes') ?>',
			cancelButtonText: '<?= lang('App.btn.no') ?>'
		}).then((result) => {
			if (result.value) {
				window.location.href = url;
			}
		});
		return false;
	});

	$("body").on("click", ".return-data", function() {
		var url = $(this).attr('data-href');
		console.log(url);
		Swal.fire({
			title: '<?= lang('App.swal.are_you_sure') ?>',
			html: "Koleksi yang dipinjam akan diproses ke <br>daftar pengembalian",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#dd6b55',
			confirmButtonText: '<?= lang('App.btn.yes') ?>',
			cancelButtonText: '<?= lang('App.btn.no') ?>'
		}).then((result) => {
			if (result.value) {
				window.location.href = url;
			}
		});
		return false;
	});


	 // TAMBAHKAN KODE INI
    // Event listener untuk tombol 'Tambah Pelanggaran'
    $('body').on('click', '.add-violation', function() {
        // Ambil data dari atribut data-* tombol yang di-klik
        var loanItemId = $(this).data('id');
        var loanId = $(this).data('loan-id');
        var barcode = $(this).data('barcode');
        var title = $(this).data('title');
        var lateDays = $(this).data('late-days');
		var memberId = $(this).data('member-id');
		var collectionId = $(this).data('collection-id');
        
        // --- Isi informasi di header modal ---
        $('#info-loan-id').text(loanId);
        $('#info-barcode').text(barcode);
        $('#info-title').text(title);
        $('#info-latedays').text(lateDays + ' hari');
        
        // --- Isi hidden input di dalam form ---
        $('#collection_loan_item_id').val(loanItemId);
        $('#collection_loan_id').val(loanId);
		$('#member_id').val(memberId);
		$('#collection_id').val(collectionId);
        $('#late_days').val(lateDays);
        
        // --- (Opsional) Reset form setiap kali modal dibuka ---
        $('#formViolation')[0].reset();
        $('#total_denda_display').text('0');

        // Tampilkan modal secara manual
        // Baris ini tidak wajib jika Anda sudah menambahkan data-toggle="modal", 
        // tapi ini adalah praktik yang baik untuk memastikan modal muncul
        $('#modalViolation').modal('show');
    });

    // (Opsional) Tambahkan event listener untuk menghitung total denda
    $('#jumlah_denda').on('keyup change', function() {
        var dendaPerHari = $(this).val();
        var hariTerlambat = $('#late_days').val();
        
        if (dendaPerHari === '' || isNaN(dendaPerHari)) {
            dendaPerHari = 0;
        }

        var totalDenda = parseInt(dendaPerHari) * parseInt(hariTerlambat);
        $('#total_denda_display').text(totalDenda.toLocaleString('id-ID')); // Format sebagai Rupiah
    });
	// Handler untuk simpan pelanggaran
$('#btnSaveViolation').on('click', function() {
    var form = $('#formViolation');
    
    // Validasi form
    if (!form[0].checkValidity()) {
        form[0].reportValidity();
        return;
    }
    
    var formData = form.serialize();
    
    Swal.fire({
        title: 'Konfirmasi',
        text: "Anda yakin ingin menambahkan pelanggaran ini?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f8c43a',
        cancelButtonColor: '#dd6b55',
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.value) {
            // Tampilkan loading
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                onBeforeOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Kirim data ke server
            $.ajax({
                url: '<?= base_url('api/sirkulasi-pengembalian/save_violation') ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message || 'Pelanggaran berhasil disimpan',
                            type: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            $('#modalViolation').modal('hide');
                            t.ajax.reload(); // Reload datatable
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: response.message || 'Terjadi kesalahan',
                            type: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan pada server',
                        type: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
});


	
</script>
<?= $this->endSection('script'); ?>