<div class="main-card mb-3 card">
	<div class="card-header d-flex justify-content-between align-items-center flex-wrap">
		<div><i class="header-icon lnr-list icon-gradient bg-plum-plate"></i> Tabel Data Artikel</div>

		<div class="d-flex align-items-center flex-wrap">
			<?php if (is_allowed('katalog/create_artikel')) : ?>
				<?php if (get_setting_parameter('FormEntriKatalog', is_profiling()) == 'Simple') : ?>
					<button type="button" class="btn btn-success btn-sm mr-2" data-toggle="modal" data-target="#modalTambahArtikel">
						<i class="fa fa-plus"></i> Tambah Artikel
					</button>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<form name="form_items" id="form_items">
				<table style="width: 100%;" id="tbl_artikel" class="table table-hover table-striped table-bordered">
					<thead>
						<tr>
							<!-- <th class="text-center" width="35">
                                <input type="checkbox" class="check_data" title="Pilih Semua">
                            </th> -->

							<th class="text-center">No</th>
							<th class="text-center">Title</th>
							<th class="text-center">Kreator</th>
							<th class="text-center">Kontributor</th>
							<th class="text-center">Halaman Awal</th>
							<th class="text-center">Halaman</th>
							<th class="text-center">Subjek</th>
							<th class="text-center">Edisi Serial</th>
							<th class="text-center">Tanggal Terbit Edisi Serial</th>
							<th class="text-center">Tampilkan di OPAC</th>
							<th class="text-center" width="80">Aksi</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</form>
		</div>
	</div>
</div>

<?= $this->section('script'); ?>
<div class="modal fade" id="modalTambahArtikel" tabindex="-1" role="dialog" aria-labelledby="modalTambahArtikelLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<form id="formTambahArtikel" method="post" action="<?= base_url('katalog/create_artikel') ?>">
				<div class="modal-header">
					<h5 class="modal-title" id="modalTambahArtikelLabel">Tambah Artikel</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">
					<input type="hidden" name="catalog_id" value="<?= $catalog->ID ?>">

					<div class="form-group">
						<label for="edisi_serial">Edisi Serial</label>
						<select class="form-control" name="edisi_serial" id="edisi_serial_select" required>
							<option value="">-- Pilih Edisi Serial --</option>
						</select>
					</div>

					<div class="form-group">
						<label for="title">Judul</label>
						<input type="text" class="form-control" name="title" required>
					</div>

					<div class="form-group">
						<label for="creator">Kreator</label>
						<div id="creator-wrapper">
							<div class="input-group mb-2">
								<input type="text" class="form-control creator-input" name="creator[]">
								<div class="input-group-append">
									<button type="button" class="btn btn-success btn-add-creator"><i class="fa fa-plus"></i></button>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="contributor">Kontributor</label>
						<div id="contributor-wrapper">
							<div class="input-group mb-2">
								<input type="text" class="form-control contributor-input" name="contributor[]">
								<div class="input-group-append">
									<button type="button" class="btn btn-success btn-add-contributor"><i class="fa fa-plus"></i></button>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="start_page">Halaman Awal</label>
						<input type="text" class="form-control" name="start_page">
					</div>

					<div class="form-group">
						<label for="pages">Halaman</label>
						<input type="text" class="form-control" name="pages">
					</div>

					<div class="form-group">
						<label for="subject">Subjek</label>
						<div id="subject-wrapper">
							<div class="input-group mb-2">
								<input type="text" class="form-control subject-input" name="subject[]">
								<div class="input-group-append">
									<button type="button" class="btn btn-success btn-add-subject"><i class="fa fa-plus"></i></button>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="tanggal_terbit">Tanggal Terbit</label>
						<input type="date" class="form-control" name="tanggal_terbit">
					</div>

					<div class="form-group">
						<label for="isopac">Tampilkan di OPAC</label><br>
						<input type="checkbox" name="isopac" value="1" checked> Ya
					</div>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
					<button type="submit" class="btn btn-primary">Simpan</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php if (session()->getFlashdata('success')) : ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				icon: 'success',
				title: 'Berhasil!',
				text: '<?= session()->getFlashdata('success') ?>',
				showConfirmButton: false,
				timer: 2000
			});
		});
	</script>
<?php endif; ?>
<script>
	var tb;
	$(document).ready(function() {
		tb = $('#tbl_artikel').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				"url": "<?php echo site_url('api/katalog/datatable_artikel') ?>",
				"data": {
					catalog_id: <?= $catalog->ID ?>
				},
				"type": "POST",
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
			"columns": [
				// {
				// 	data: 'Article_type',
				// 	className: 'text-left'
				// },
				{
					data: 'no'
				},
				{
					data: 'Title'
				},
				{
					data: 'Creator'
				},
				{
					data: 'Contributor'
				},
				{
					data: 'StartPage'
				},
				{
					data: 'Pages'
				},
				{
					data: 'Subject'
				},
				{
					data: 'EDISISERIAL'
				},
				{
					data: 'TANGGAL_TERBIT_EDISI_SERIAL'
				},
				{
					data: 'ISOPAC'
				},
				{
					data: 'action',
					className: 'text-center',
					orderable: false
				},
			],
			"order": [
				[0, "desc"]
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
		});

		$('button[data-target="#modalTambahArtikel"]').on('click', function() {
			$('#modalTambahArtikelLabel').text('Tambah Artikel');
			loadEdisiSerialOptions(<?= $catalog->ID ?>);
		});

		$(document).ready(function() {
			// Add field handlers
			$('.btn-add-creator').click(function() {
				$('#creator-wrapper').append(`
			<div class="input-group mb-2">
				<input type="text" class="form-control creator-input" name="creator[]">
				<div class="input-group-append">
					<button type="button" class="btn btn-danger btn-remove"><i class="fa fa-trash"></i></button>
				</div>
			</div>
		`);
			});

			$('.btn-add-contributor').click(function() {
				$('#contributor-wrapper').append(`
			<div class="input-group mb-2">
				<input type="text" class="form-control contributor-input" name="contributor[]">
				<div class="input-group-append">
					<button type="button" class="btn btn-danger btn-remove"><i class="fa fa-trash"></i></button>
				</div>
			</div>
		`);
			});

			$('.btn-add-subject').click(function() {
				$('#subject-wrapper').append(`
			<div class="input-group mb-2">
				<input type="text" class="form-control subject-input" name="subject[]">
				<div class="input-group-append">
					<button type="button" class="btn btn-danger btn-remove"><i class="fa fa-trash"></i></button>
				</div>
			</div>
		`);
			});

			$(document).on('click', '.btn-remove', function() {
				$(this).closest('.input-group').remove();
			});

			$('#formTambahArtikel').submit(function(e) {
				e.preventDefault();

				let creators = $('.creator-input').map(function() {
					return $(this).val().trim();
				}).get().filter(Boolean).join(', ');

				let contributors = $('.contributor-input').map(function() {
					return $(this).val().trim();
				}).get().filter(Boolean).join(', ');

				let subjects = $('.subject-input').map(function() {
					return $(this).val().trim();
				}).get().filter(Boolean).join(', ');

				$('#formTambahArtikel input[name="creator_final"], input[name="contributor_final"], input[name="subject_final"]').remove();

				$(this).append(`<input type="hidden" name="creator_final" value="${creators}">`);
				$(this).append(`<input type="hidden" name="contributor_final" value="${contributors}">`);
				$(this).append(`<input type="hidden" name="subject_final" value="${subjects}">`);

				$('.creator-input, .contributor-input, .subject-input').removeAttr('name');

				var id = $('input[name="id"]').val();
				if (id) {
					$(this).attr('action', '<?= base_url('katalog/edit_artikel/') ?>' + id);
				} else {
					$(this).attr('action', '<?= base_url('katalog/create_artikel') ?>');
				}

				this.submit();
			});

		});

		$(document).on('click', '.btn-edit-artikel', function() {
			var id = $(this).data('id');

			var form = $('#formTambahArtikel')[0];
			form.reset();
			$('#creator-wrapper .input-group:not(:first)').remove();
			$('#contributor-wrapper .input-group:not(:first)').remove();
			$('#subject-wrapper .input-group:not(:first)').remove();

			$('#formTambahArtikel input[name="creator_final"], input[name="contributor_final"], input[name="subject_final"], input[name="id"]').remove();

			$('#formTambahArtikel').append('<input type="hidden" name="id" value="' + id + '">');

			$.ajax({
				url: '<?= base_url('api/katalog/get_artikel') ?>/' + id,
				type: 'GET',
				success: function(res) {
					if (!res.error) {
						var data = res.data;

						$('input[name="id"]').val(id);
						$('input[name="title"]').val(data.Title);

						function fillMultiInput(wrapperId, values) {
							values = values ? values.split(',').map(v => v.trim()) : [];
							var firstInput = $(wrapperId + ' .input-group:first input');
							firstInput.val(values[0] || '');

							for (var i = 1; i < values.length; i++) {
								var newInput = `<div class="input-group mb-2">
                            <input type="text" class="form-control" value="${values[i]}" name="">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger btn-remove"><i class="fa fa-trash"></i></button>
                            </div>
                        </div>`;
								$(wrapperId).append(newInput);
							}
						}

						fillMultiInput('#creator-wrapper', data.Creator);
						fillMultiInput('#contributor-wrapper', data.Contributor);
						fillMultiInput('#subject-wrapper', data.Subject);

						$('input[name="start_page"]').val(data.StartPage);
						$('input[name="pages"]').val(data.Pages);
						loadEdisiSerialOptions(<?= $catalog->ID ?>, data.EDISISERIAL);
						$('input[name="tanggal_terbit"]').val(data.TANGGAL_TERBIT_EDISI_SERIAL);

						$('input[name="isopac"]').prop('checked', data.ISOPAC == 1);

						$('#modalTambahArtikelLabel').text('Edit Artikel');
						$('#modalTambahArtikel').modal('show');
					} else {
						Swal.fire('Error', res.message, 'error');
					}
				},
				error: function() {
					Swal.fire('Error', 'Failed to fetch article data', 'error');
				}
			});
		});

		$(document).on('click', '.btn-delete-artikel', function() {
			var id = $(this).data('id');
			Swal.version
			Swal.fire({
				title: '<?= lang('App.swal.are_you_sure') ?>',
				text: "<?= lang('App.swal.can_not_be_restored') ?>",
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#dd6b55',
				confirmButtonText: '<?= lang('App.btn.yes') ?>',
				cancelButtonText: '<?= lang('App.btn.no') ?>',
			}).then((result) => {
				if (result.value) {
					$.ajax({
						url: '<?= base_url('katalog/delete_artikel') ?>/' + id,
						type: 'POST',
						success: function(res) {
							if (!res.error) {
								Swal.fire('Berhasil', res.message, 'success');
								tb.ajax.reload();
							} else {
								Swal.fire('Gagal', res.message, 'error');
							}
						},
						error: function() {
							Swal.fire('Error', 'Terjadi kesalahan saat menghapus artikel.', 'error');
						}
					});
				}
			});
		});

		function loadEdisiSerialOptions(catalogId, selected = '') {
			$.ajax({
				url: '<?= base_url('api/katalog/get-edisi-serial') ?>/' + catalogId,
				type: 'GET',
				success: function(res) {
					if (!res.error) {
						let options = '<option value="">-- Pilih Edisi Serial --</option>';
						res.data.forEach(function(item) {
							let selectedAttr = (item.no_edisi_serial === selected) ? 'selected' : '';
							options += `<option value="${item.no_edisi_serial}" ${selectedAttr}>${item.no_edisi_serial}</option>`;
						});
						$('#edisi_serial_select').html(options);
					}
				},
				error: function() {
					console.error('Failed to load edisi serial options');
				}
			});
		}
	});
</script>
<?= $this->endSection('script'); ?>