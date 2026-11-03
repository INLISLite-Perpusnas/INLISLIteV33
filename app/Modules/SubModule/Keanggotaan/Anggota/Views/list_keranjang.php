<?php
$request = service('request'); ?>
<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-id icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Keranjang Anggota
					<div class="page-title-subheading">Daftar semua Keranjang Anggota </div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
						<li class="breadcrumb-item">Keanggotaan</li>
						<li class="breadcrumb-item ">Keranjang Anggota</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<div class="main-card mb-3 card">
		<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Keranjang Anggota
			<div class="btn-actions-pane-right actions-icon-btn">

			</div>
		</div>

		<div class="card-body">
			<?php if ($is_sekolah) : ?>
				<!-- Filter: Kelas (khusus perpustakaan sekolah), sama dengan Daftar Anggota -->
				<div class="d-flex align-items-center flex-wrap mb-3" style="gap:6px;">
					<div class="input-group" style="width:280px; flex-shrink:0;">
						<div class="input-group-prepend">
							<span class="btn btn-secondary"><i class="fa fa-filter" aria-hidden="true"></i> Kelas</span>
						</div>
						<select class="form-control" id="filter_kelas_id" aria-label="Filter kelas">
							<option value="">-- Semua Kelas --</option>
							<?php foreach ($kelas_list as $row) : ?>
								<option value="<?= $row->id ?>"><?= esc($row->namakelassiswa) ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<button class="btn btn-outline-secondary" id="btnResetFilterKelas" type="button" style="height:38px; flex-shrink:0;">
						<i class="fa fa-undo" aria-hidden="true"></i> Reset Filter
					</button>
				</div>
			<?php endif; ?>

			<?php if ($is_perguruan_tinggi) : ?>
				<!-- Filter: Fakultas & Jurusan (khusus perguruan tinggi), sama dengan Daftar Anggota -->
				<div class="d-flex align-items-center flex-wrap mb-3" style="gap:6px;">
					<div class="input-group" style="width:280px; flex-shrink:0;">
						<div class="input-group-prepend">
							<span class="btn btn-secondary"><i class="fa fa-filter" aria-hidden="true"></i> Fakultas</span>
						</div>
						<select class="form-control" id="filter_fakultas_id" aria-label="Filter fakultas">
							<option value="">-- Semua Fakultas --</option>
							<?php foreach ($fakultas_list as $row) : ?>
								<option value="<?= $row->id ?>"><?= esc($row->Nama) ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="input-group" style="width:280px; flex-shrink:0;">
						<div class="input-group-prepend">
							<span class="btn btn-secondary"><i class="fa fa-filter" aria-hidden="true"></i> Jurusan</span>
						</div>
						<select class="form-control" id="filter_jurusan_id" aria-label="Filter jurusan">
							<option value="">-- Semua Jurusan --</option>
							<?php foreach ($jurusan_list as $row) : ?>
								<option value="<?= $row->id ?>"><?= esc($row->Nama) ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<button class="btn btn-outline-secondary" id="btnResetFilterFakultasJurusan" type="button" style="height:38px; flex-shrink:0;">
						<i class="fa fa-undo" aria-hidden="true"></i> Reset Filter
					</button>
				</div>
			<?php endif; ?>

			<form name="form_items" id="form_items">
				<div class="d-block mb-3 pb-3 border-bottom">
				<button type="button" id="pulihkan_keranjang" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="Semua anggota yang terpilih"><i class="fa fa-undo"></i> Pulihkan dari Keranjang</button>
				<button type="button" id="print_kartu" class="btn btn-primary ml-2" data-toggle="tooltip" data-placement="top" title="Print kartu anggota yang terpilih"><i class="fa fa-print" aria-hidden="true"></i> Print Kartu</button>
				<button type="button" id="aktifkan_online" class="btn btn-success ml-2" data-toggle="tooltip" data-placement="top" title="Aktifkan akses online untuk anggota yang terpilih"><i class="fa fa-globe" aria-hidden="true"></i> Aktifkan Online</button>
				</div>

				<table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
					<thead>
						<tr>
							<th class="text-center" width="35">
								<input type="checkbox" class="check_data" title="Pilih Semua">
							</th>
							<th>Nama Anggota</th>
							<th>No. Anggota</th>
							<th>Tgl. Register</th>
							<th>Tgl. Berakhir</th>
							<th>Aksi</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>

<?= $this->include('Anggota\Views\modal_upload'); ?>
<?= $this->include('Anggota\Views\modal_camera'); ?>
<script>
	Dropzone.autoDiscover = false;
</script>

<script>
	var t;
	$(document).ready(function() {
		t = $('#tbl_data').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				"url": '<?php echo site_url('api/anggota/datatable/1'); ?>',
				"data": function(d) {
					d.kelas_id = $('#filter_kelas_id').val();
					d.fakultas_id = $('#filter_fakultas_id').val();
					d.jurusan_id = $('#filter_jurusan_id').val();
				}
			},
			   "dom": "<'row mb-2'<'col-md-6 col-sm-12 text-left'l><'col-md-6 col-sm-12 text-right'f>>" +
       "<'row'<'col-md-12'tr>>" +
       "<'row mt-2'<'col-md-5 col-sm-12 text-left'i><'col-md-7 col-sm-12 d-flex justify-content-end'p>>",
            
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
					data: 'cid',
					className: 'text-center',
					orderable: false,
					searchable: false
				},
				{
					data: 'FullName'
				},
				{
					data: 'MemberNo'
				},
				{
					data: 'RegisterDate',
					className: 'text-center'
				},
				{
					data: 'EndDate',
					className: 'text-center'
				},
				{
					data: 'action',
					className: 'text-center',
					searchable: false,
					orderable: false
				},
			],
			"order": [
				[2, "asc"]
			],
			"drawCallback": function(data, type, full, meta) {
				var api = this.api();
				var data = api.rows().data();

				$('.apply-status').bootstrapToggle();

				$(".apply-status").on('change', function() {
					var url = $(this).attr('data-href');
					var field = $(this).attr('data-field');
					var value = $(this).is(':checked');
					var data_post = 'field=' + field + '&value=' + value;

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
									html: res.message,
									icon: 'success',
									showConfirmButton: false,
									timer: 5000,
								}).then(() => {});
							} else {
								Swal.fire({
									title: 'Gagal',
									text: res.message,
									icon: 'error',
									showConfirmButton: false,
									timer: 5000
								}).then(() => {});
							}
						})
						.fail(function(res) {
							console.log(res);

							Swal.fire({
								title: 'Oups',
								text: 'Maaf, terjadi kesalahan. Coba beberapa saat lagi atau hubungi Admin',
								icon: 'error',
								showConfirmButton: false,
								timer: 5000
							}).then(() => {});
						});
				});

				$.each(data, function(i, row) {
					$("#lazy" + row.id).Lazy();
				});

				$('.image-link').magnificPopup({
					type: 'image'
				});
			},
			"initComplete": function(settings, json) {
				// var $searchInput = $('div.dataTables_filter input');
				// $searchInput.unbind();
				// $searchInput.bind('keyup', function(e) {
				// 	if(e.keyCode == 13){
				// 		if(this.value.length == 0){
				// 			t.search('').draw();
				// 		}

				// 		if(this.value.length >= 3){
				// 			t.search( this.value ).draw();
				// 		}
				// 	} 
				// });
			}
		});
	});

	$(".check_data").click(function() {
		$('#tbl_data input:checkbox').not(this).prop('checked', this.checked);
	});

	// Filter Kelas / Fakultas & Jurusan (sama dengan Daftar Anggota)
	$('#filter_kelas_id, #filter_fakultas_id, #filter_jurusan_id').on('change', function() {
		t.ajax.reload();
	});
	$('#btnResetFilterKelas').on('click', function() {
		$('#filter_kelas_id').val('');
		t.ajax.reload();
	});
	$('#btnResetFilterFakultasJurusan').on('click', function() {
		$('#filter_fakultas_id').val('');
		$('#filter_jurusan_id').val('');
		t.ajax.reload();
	});

	$('#pulihkan_keranjang').click(function() {
		var form = $('#form_items');
		var serialize_bulk = form.serialize();
		var url = "<?= base_url('anggota/pulihkan_keranjang') ?>" + '?' + serialize_bulk;
		console.log(serialize_bulk);
		console.log(url);

		Swal.fire({
			title: 'Anda yakin?',
			html: "Semua anggota yang terpilih akan dipulihkan ke daftar anggota",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#dd6b55',
			confirmButtonText: '<?= lang('App.btn.yes') ?>',
			cancelButtonText: '<?= lang('App.btn.no') ?>'
		}).then((result) => {
			if (result.isConfirmed) {
				window.location.href = url;
			}
		});
		return false;
	});

	

// Print Kartu & Aktifkan Online: sama dengan Daftar Anggota (anggota/list.php)
    // Event handler untuk tombol Print Kartu
    $('#print_kartu').click(function() {
        // Coba beberapa selector berbeda untuk menangkap checkbox
        var checkedBoxes = $('#tbl_data input[type="checkbox"]:checked').not('.check_data');

        if (checkedBoxes.length === 0) {
            Swal.fire({
                title: 'Peringatan',
                text: 'Silakan pilih minimal satu anggota untuk dicetak kartunya',
                icon: 'warning',
                showConfirmButton: true,
                confirmButtonText: 'OK'
            });
            return false;
        }

        // Collect all selected IDs
        var selectedIds = [];
        checkedBoxes.each(function() {
            var checkboxValue = $(this).val();
            var checkboxName = $(this).attr('name');
            var checkboxId = $(this).attr('id');

            if (checkboxValue && checkboxValue !== 'on') {
                selectedIds.push(checkboxValue);
            }
        });

        // Konfirmasi sebelum print
        Swal.fire({
            title: 'Konfirmasi Print Kartu',
            html: `Akan mencetak kartu untuk <strong>${selectedIds.length}</strong> anggota yang dipilih.<br><br>
                <div style="text-align:left;display:inline-block">
                  <label style="margin-right:18px;cursor:pointer">
                    <input type="radio" name="swal_orientation" value="landscape" checked> Landscape
                  </label>
                  <label style="cursor:pointer">
                    <input type="radio" name="swal_orientation" value="portrait"> Portrait
                  </label>
                </div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#dd6b55',
            confirmButtonText: 'Ya, Print Kartu',
            cancelButtonText: 'Batal',
            preConfirm: () => {
                const sel = document.querySelector('input[name="swal_orientation"]:checked');
                return sel ? sel.value : 'landscape';
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim data ke controller
                printKartuAnggota(selectedIds, result.value || 'landscape');
            }
        });

        return false;
    });

    // Fungsi untuk mengirim data ke controller print kartu
    function printKartuAnggota(ids, orientation) {
        // Method 1: Kirim via POST dengan form hidden
        var form = $('<form></form>');
        form.attr('method', 'post');
        form.attr('action', '<?= base_url('anggota/multipleprint') ?>');
        form.attr('target', '_blank'); // Buka di tab baru

        // Tambahkan CSRF token jika ada
        <?php if (csrf_token()): ?>
            form.append('<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />');
        <?php endif; ?>

        // Orientasi kartu (landscape / portrait)
        form.append('<input type="hidden" name="orientation" value="' + (orientation === 'portrait' ? 'portrait' : 'landscape') + '" />');

        // Tambahkan IDs sebagai array
        $.each(ids, function(index, value) {
            form.append('<input type="hidden" name="member_ids[]" value="' + value + '" />');
        });

        // Append form ke body dan submit
        $('body').append(form);
        form.submit();
        form.remove();
    }

    // Event handler untuk tombol Aktifkan Online
    $('#aktifkan_online').click(function() {
        var checkedBoxes = $('#tbl_data input[type="checkbox"]:checked').not('.check_data');
        
        if (checkedBoxes.length === 0) {
            Swal.fire({
                title: 'Peringatan',
                text: 'Silakan pilih minimal satu anggota untuk diaktifkan secara online',
                icon: 'warning',
                showConfirmButton: true,
                confirmButtonText: 'OK'
            });
            return false;
        }
        
        // Collect all selected IDs
        var selectedIds = [];
        checkedBoxes.each(function() {
            var checkboxValue = $(this).val();
            if (checkboxValue && checkboxValue !== 'on') {
                selectedIds.push(checkboxValue);
            }
        });
        
        // Konfirmasi sebelum aktifkan
        Swal.fire({
            title: 'Konfirmasi Aktivasi Online',
            html: `Akan mengaktifkan akses online untuk <strong>${selectedIds.length}</strong> anggota yang dipilih.<br><br>
                   <small class="text-muted">Username akan sama dengan No. Anggota. Password acak hanya ditampilkan sekali setelah proses selesai.</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#dd6b55',
            confirmButtonText: 'Ya, Aktifkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim data ke controller
                aktifkanAnggotaOnline(selectedIds);
            }
        });
        
        return false;
    });

    // Token CSRF diperbarui dari tiap respons karena token di-regenerate setelah dipakai
    var aktivasiCsrfHash = '<?= csrf_hash() ?>';

    // Fungsi untuk mengirim data aktivasi online ke controller
    function aktifkanAnggotaOnline(ids) {
        // Tampilkan loading
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang mengaktifkan anggota secara online',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: '<?= base_url('anggota/aktifkan_online') ?>',
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': aktivasiCsrfHash,
                member_ids: ids
            },
            dataType: 'json'
        })
        .done(function(response) {
            if (response.csrf) {
                aktivasiCsrfHash = response.csrf;
            }
            if (response.error === false) {
                Swal.fire({
                    title: 'Berhasil!',
                    html: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Reload datatable
                    t.ajax.reload(null, false);
                    // Uncheck all checkboxes
                    $('#tbl_data input:checkbox').prop('checked', false);
                });
            } else {
                Swal.fire({
                    title: 'Gagal',
                    html: response.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            var res = jqXHR.responseJSON || {};
            if (res.csrf) {
                aktivasiCsrfHash = res.csrf;
            }
            Swal.fire({
                title: 'Oops!',
                text: res.message || 'Terjadi kesalahan saat memproses data. Muat ulang halaman lalu coba lagi.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    }


	$("body").on("click", ".remove-data", function() {
		var url = $(this).attr('data-href');
		console.log(url);
		Swal.fire({
			title: '<?= lang('App.swal.are_you_sure') ?>',
			text: "<?= lang('App.swal.can_not_be_restored') ?>",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#dd6b55',
			confirmButtonText: '<?= lang('App.btn.yes') ?>',
			cancelButtonText: '<?= lang('App.btn.no') ?>'
		}).then((result) => {
			if (result.isConfirmed) {
				window.location.href = url;
			}
		});
		return false;
	});
</script>

<?= $this->endSection('script'); ?>