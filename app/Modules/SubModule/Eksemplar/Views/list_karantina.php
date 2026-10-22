<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
	.karantina-filter-label {
		margin: 0; color: #fff; background: #495057; border: 1px solid #495057;
		padding: .5rem .75rem; border-radius: .25rem 0 0 .25rem; white-space: nowrap;
	}
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-server icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Karantina Eksemplar
					<div class="page-title-subheading">Daftar semua Karantina Eksemplar</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('Eksemplar') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="breadcrumb-item">Eksemplar</li>
						<li class="breadcrumb-item" aria-current="page">Karantina Eksemplar</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<div class="main-card mb-3 card">
		<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Daftar Karantina Eksemplar
			<div class="btn-actions-pane-right actions-icon-btn">
			</div>
		</div>
		<!-- Panel Aksi Massal (sama dengan Daftar Eksemplar) -->
		<div class="card-footer">
			<div class="d-flex align-items-center flex-wrap" style="gap:6px; padding: 8px 0 4px 0;">
				<div class="input-group" style="width:320px; flex-shrink:0;">
					<div class="input-group-prepend">
						<label class="karantina-filter-label" for="action">Pilih Aksi</label>
					</div>
					<select class="form-control" id="action" name="action">
						<option value="">-- Pilih Aksi --</option>
						<option value="pulihkan">Pulihkan ke Daftar Eksemplar</option>
						<option value="hapus">Hapus Permanen</option>
					</select>
				</div>
				<button class="btn btn-primary" id="btnProcess" type="button" style="height:38px; padding: 0 16px; flex-shrink:0;">
					<i class="fa fa-check" aria-hidden="true"></i> Proses
				</button>
			</div>
		</div>

		<div class="card-body">
			<!-- Filter: Lokasi Perpustakaan, Lokasi Ruang, Bentuk Fisik -->
			<div class="d-flex align-items-center flex-wrap mb-3" style="gap:6px;">
				<div class="input-group" style="width:280px; flex-shrink:0;">
					<div class="input-group-prepend">
						<label class="karantina-filter-label" for="filter_location_library"><i class="fa fa-map-marker-alt" aria-hidden="true"></i> Lokasi Perpustakaan</label>
					</div>
					<select class="form-control" id="filter_location_library">
						<option value="">-- Semua Lokasi Perpustakaan --</option>
					</select>
				</div>

				<div class="input-group" style="width:280px; flex-shrink:0;">
					<div class="input-group-prepend">
						<label class="karantina-filter-label" for="filter_location_id"><i class="fa fa-door-open" aria-hidden="true"></i> Lokasi Ruang</label>
					</div>
					<select class="form-control" id="filter_location_id" disabled>
						<option value="">-- Pilih Lokasi Perpustakaan Dahulu --</option>
					</select>
				</div>

				<div class="input-group" style="width:280px; flex-shrink:0;">
					<div class="input-group-prepend">
						<label class="karantina-filter-label" for="filter_media_id"><i class="fa fa-cube" aria-hidden="true"></i> Bentuk Fisik</label>
					</div>
					<select class="form-control" id="filter_media_id">
						<option value="">-- Semua Bentuk Fisik --</option>
					</select>
				</div>

				<button class="btn btn-outline-secondary" id="btnResetFilter" type="button" style="height:38px; flex-shrink:0;">
					<i class="fa fa-undo" aria-hidden="true"></i> Reset Filter
				</button>
			</div>
			<p id="karantina_filter_error" class="text-danger" role="status" hidden>Pilihan filter gagal dimuat. Silakan muat ulang halaman.</p>

			<form name="form_items" id="form_items">
				<table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
					<thead>
						<tr>
							<th class="text-center" width="35">
								<input type="checkbox" class="check_data" title="Pilih Semua">
							</th>
							<th class="text-center" width="100">No. Barcode</th>
							<th class="text-center" width="100">Tanggal Pengadaan</th>
							<th class="text-center" width="100">No. Induk</th>
							<th class="text-center" width="">Data Bibliografis</th>
							<th class="text-center" width="">OPAC</th>
							<th class="text-center" width="">Karantina</th>
							<th class="text-center" width="80">Aksi</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</form>
		</div>
		<div class="card-footer">
			<div class="d-block">
				<button type="button" id="pulihkan_eksemplar" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Semua eksemplar yang terpilih"><i class="fa fa-undo"></i> Pulihkan ke Daftar Eksemplar</button> &nbsp;
				<button type="button" id="hapus_permanen" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Semua eksemplar yang terpilih"><i class="fa fa-trash"></i> Hapus Permanen</button>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
	var t;
	$(document).ready(function() {
		t = $('#tbl_data').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				"url": '<?php echo site_url('api/eksemplar/datatable/1') ?>',
				"data": function(d) {
					d.location_library_id = $('#filter_location_library').val();
					d.location_id = $('#filter_location_id').val();
					d.media_id = $('#filter_media_id').val();
				}
			},
			 dom: "<'row mb-2'<'col-md-6 col-sm-12 text-left'l><'col-md-6 col-sm-12 text-right'f>>" +
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
					data: 'ID',
					className: 'text-center',
					orderable: false
				},
				{
					data: 'NomorBarcode',
					className: 'text-left'
				},
				{
					data: 'TanggalPengadaan'
				},
				{
					data: 'NoInduk'
				},
				{
					data: 'Catalog_id'
				},
				{
					data: 'IsOPAC',
					className: 'text-center',
					orderable: false
				},
				{
					data:"IsQUARANTINE",
					className: 'text-center',
					orderable: false
				},
				{
					data: 'action',
					className: 'text-center',
					orderable: false
				},
			],
			"order": [
				[1, "asc"]
			],
			"drawCallback": function(data, type, full, meta) {
				var api = this.api();
				var data = api.rows().data();
				$('[data-toggle="tooltip"]').tooltip();
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
									type: 'success',
									showConfirmButton: false,
									timer: 5000,
								}).then(() => {});
							} else {
								Swal.fire({
									title: 'Gagal',
									text: res.message,
									type: 'error',
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
								type: 'error',
								showConfirmButton: false,
								timer: 5000
							}).then(() => {});
						});
				});
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

	$(".check_data").click(function() {
		$('#tbl_data input:checkbox').not(this).prop('checked', this.checked);
	});

	// === Filter (sumber opsi sama dengan Daftar Eksemplar) ===
	function loadFilterOptions($select, url) {
		return $.getJSON(url).then(function(payload) {
			var items = payload.data || payload;
			if (!Array.isArray(items)) {
				return $.Deferred().reject().promise();
			}
			$.each(items, function(_, item) {
				$select.append(new Option(item.name, item.code));
			});
		});
	}

	function resetRoomFilter() {
		$('#filter_location_id').empty().append(new Option('-- Pilih Lokasi Perpustakaan Dahulu --', '')).prop('disabled', true);
	}

	function reloadTable() {
		if (t) {
			t.ajax.reload();
		}
	}

	$.when(
		loadFilterOptions($('#filter_location_library'), '<?= site_url('api/eksemplar/locationlibrary') ?>'),
		loadFilterOptions($('#filter_media_id'), '<?= site_url('api/eksemplar/collectionmedias') ?>')
	).fail(function() {
		$('#karantina_filter_error').prop('hidden', false);
	});

	$('#filter_location_library').on('change', function() {
		var libraryId = $(this).val();
		resetRoomFilter();
		reloadTable();
		if (!libraryId) {
			return;
		}
		var $room = $('#filter_location_id').empty().append(new Option('-- Semua Lokasi Ruang --', ''));
		loadFilterOptions($room, '<?= site_url('api/eksemplar/locations') ?>/' + encodeURIComponent(libraryId))
			.done(function() {
				if ($('#filter_location_library').val() === libraryId) {
					$room.prop('disabled', false);
				}
			})
			.fail(function() {
				$('#karantina_filter_error').prop('hidden', false);
			});
	});
	$('#filter_location_id, #filter_media_id').on('change', reloadTable);
	$('#btnResetFilter').on('click', function() {
		$('#filter_location_library').val('');
		$('#filter_media_id').val('');
		resetRoomFilter();
		reloadTable();
	});

	// === Pilih Aksi + Proses ===
	$('#btnProcess').on('click', function() {
		var action = $('#action').val();
		if (action === 'pulihkan') {
			$('#pulihkan_eksemplar').trigger('click');
		} else if (action === 'hapus') {
			$('#hapus_permanen').trigger('click');
		} else {
			Swal.fire({
				title: 'Peringatan',
				text: 'Silahkan pilih aksi terlebih dahulu',
				type: 'warning',
				showConfirmButton: true
			});
		}
	});

	// Replace the existing button handlers with these

$('#pulihkan_eksemplar').click(function() {
    var form = $('#form_items');
    var checkboxes = form.find('input[name="ID[]"]:checked');
    
    if (checkboxes.length === 0) {
        Swal.fire({
            title: 'Peringatan',
            text: 'Silahkan pilih minimal satu eksemplar',
            type: 'warning',
            showConfirmButton: true
        });
        return false;
    }
    
    // Create a hidden form to submit
    var formSubmit = $('<form>', {
        'method': 'post',
        'action': '<?= base_url('eksemplar/pulihkan_eksemplar') ?>'
    });
    
    // Add all selected IDs to the form
    checkboxes.each(function() {
        formSubmit.append($('<input>', {
            'type': 'hidden',
            'name': 'ID[]',
            'value': $(this).val()
        }));
    });
    
    // Add CSRF token if needed
    <?php if (csrf_token() !== null): ?>
    formSubmit.append($('<input>', {
        'type': 'hidden',
        'name': '<?= csrf_token() ?>',
        'value': '<?= csrf_hash() ?>'
    }));
    <?php endif; ?>
    
    Swal.fire({
        title: 'Anda yakin?',
        html: "Semua eksemplar yang terpilih akan dipulihkan <br>ke Daftar Eksemplar",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#dd6b55',
        confirmButtonText: '<?= lang('App.btn.yes') ?>',
        cancelButtonText: '<?= lang('App.btn.no') ?>'
    }).then((result) => {
        if (result.value) {
            // Append the form to the body and submit it
            $('body').append(formSubmit);
            formSubmit.submit();
        }
    });
    return false;
});

$('#hapus_permanen').click(function() {
    var form = $('#form_items');
    var checkboxes = form.find('input[name="ID[]"]:checked');
    
    if (checkboxes.length === 0) {
        Swal.fire({
            title: 'Peringatan',
            text: 'Silahkan pilih minimal satu eksemplar',
            type: 'warning',
            showConfirmButton: true
        });
        return false;
    }
    
    // Create a hidden form to submit
    var formSubmit = $('<form>', {
        'method': 'post',
        'action': '<?= base_url('eksemplar/hapus_permanen') ?>'
    });
    
    // Add all selected IDs to the form
    checkboxes.each(function() {
        formSubmit.append($('<input>', {
            'type': 'hidden',
            'name': 'ID[]',
            'value': $(this).val()
        }));
    });
    
    // Add CSRF token if needed
    <?php if (csrf_token() !== null): ?>
    formSubmit.append($('<input>', {
        'type': 'hidden',
        'name': '<?= csrf_token() ?>',
        'value': '<?= csrf_hash() ?>'
    }));
    <?php endif; ?>
    
    Swal.fire({
        title: 'Anda yakin?',
        html: "Semua eksemplar yang terpilih akan dihapus permanen.<br><?= lang('App.swal.can_not_be_restored') ?>",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#dd6b55',
        confirmButtonText: '<?= lang('App.btn.yes') ?>',
        cancelButtonText: '<?= lang('App.btn.no') ?>'
    }).then((result) => {
        if (result.value) {
            // Append the form to the body and submit it
            $('body').append(formSubmit);
            formSubmit.submit();
        }
    });
    return false;
});
</script>
<?= $this->endSection('script'); ?>