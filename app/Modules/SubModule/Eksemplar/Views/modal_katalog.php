<?php
$request = service('request');
$member_no = $request->getGet('member_no');
?>
<div class="modal fade" id="modal_katalog" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					<i class="header-icon lnr-list icon-gradient bg-success"> </i> Daftar Katalog
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="card-body">
				<form name="form_items" id="form_items">
					<table style="width: 100%;" id="tbl_katalog" class="table table-hover table-striped table-borderless" style="z-index:1000">
						<thead class="bg-night-sky text-light">
							<tr>
								<th class="text-center" width="80">
									#
								</th>
								<th class="text-center" width="100">ISBN</th>
								<th class="text-center">Judul / Penerbit / No. Panggil</th>
								<th class="text-center" width="80">Eksemplar</th>
							</tr>
						</thead>
					</table>
				</form>
			</div>
			<div class="modal-footer">
				<div class="d-block">

				</div>
			</div>
		</div>
	</div>
</div>

<script>
	var t_modal
	$('#modal_katalog').on('shown.bs.modal', function() {
		t_modal = $('#tbl_katalog').DataTable({
			"processing": true,
			"retrieve": true,
			"serverSide": true,
			"ajax": {
				"url": '<?php echo site_url('api/eksemplar/katalog') ?>',
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
					data: 'ID',
					className: 'text-center',
					orderable: false
				},
				{
					data: 'ISBN'
				},
				{
					data: 'Title'
				},
				{
					data: 'Eksemplar',
					className: 'text-right'
				},
				{
					data: 'Publisher',
					visible: false
				},
				{
					data: 'CallNumber',
					visible: false
				},
			],
			"order": [
				['0', 'desc']
			],
			"drawCallback": function(data, type, full, meta) {
				var api = this.api();
				$('[data-toggle="tooltip"]').tooltip();
			},
			"initComplete": function(settings, json) {
				var $searchInput = $('div.dataTables_filter input');
				$searchInput.unbind();
				$searchInput.bind('keyup', function(e) {
					if (this.value.length == 0) {
						t_modal.search('').draw();
					}

					if (this.value.length >= 3) {
						t_modal.search(this.value).draw();
					}
				});
			}
		});
	});

	$("body").on("click", "#tbl_katalog tbody tr .btnAddtoForm", function(e) {
		e.preventDefault();
		var id = $(this).data('id');
		var callnumber = $(this).data('callnumber');
		var title = $(this).data('title');

		console.log(id);
		console.log(callnumber);
		console.log(title);

		$('#modal_katalog').modal('hide');

		$('#Catalog_id').val(id);
		$('#Catalog_id2').val(id);
		$('#CallNumber').val(callnumber);
		$('#Title').val(title);


	});
</script>