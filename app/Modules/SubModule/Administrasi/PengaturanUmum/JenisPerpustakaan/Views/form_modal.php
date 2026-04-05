<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					<i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> <span id="frm_form_title"></span>
				</h5>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="frm_form" method="post" data-action="<?= base_url('api-jenis-perpustakaan/form') ?>" data-id="1">
				<div class="modal-body">
					<table style="width: 100%;" id="tbl_form" class="table table-hover table-striped table-bordered">
						<thead>
							<tr>
								<th class="text-center" width="35">No</th>
								<th class="text-center">Nama Field</th>
								<th class="text-center">Mandatory</th>
								<th class="text-center">Aktif</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	var f;
	$("body").on("click", ".show-form", function() {
		var url = $(this).attr('data-href');
		var id = $(this).attr('data-id');
		var name = $(this).attr('data-name');
		$('#frm_form_title').html('Form Anggota - ' + name);

		$(document).ready(function() {
			f = $('#tbl_form').DataTable({
				"processing": true,
				"serverSide": true,
				"ajax": {
					"url": url,
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
						data: 'field_name'
					},
					{
						data: 'mandatory',
						className: 'text-center'
					},
					{
						data: 'active',
						className: 'text-center'
					},
				],
				"columnDefs": [{
					targets: [0, 3],
					searchable: false,
					orderable: false
				}, ],
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

		$('#modal_form').modal('show');
	});

	$('#modal_form').on('hidden.bs.modal', function() {
		f.destroy();
	});
</script>