<?php
$request = service('request');
?>
<div class="modal fade" id="modal_indicator1" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="title_indicator1">
					Indicator 1 - ${Tag} ${Name}
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="card-body">
				<form name="form_indicator1" id="form_indicator1">
					<input type="hidden" name="field_id_indicator1" id="field_id_indicator1" value="">
					<input type="hidden" name="dom_id_indicator1" id="dom_id_indicator1" value="">
					<table style="width: 100%;" id="tbl_indicator1" class="table table-hover table-striped table-borderless" style="z-index:1000">
						<thead>
							<tr>
								<th width="100" class="text-center"></th>
								<th class="text-center">Kode</th>
								<th class="text-center">Nama</th>
							</tr>
						</thead>
					</table>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	$("body").on("click", ".choose-data1", function() {
		var html = $(this).data('value');
		var dom_id = $('#dom_id_indicator1').val();
		$(`#${dom_id}`).val(html);
		$('#modal_indicator1').modal('hide');
	});

	var t_modal;
	$('#modal_indicator1').on('shown.bs.modal', function() {
		var field_id = $('#field_id_indicator1').val();
		var url = `<?= base_url('api/katalog/get_field_indicator1') ?>/${field_id}`;

		t_modal = $('#tbl_indicator1').DataTable({
			"processing": true,
			"retrieve": true,
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
					data: 'action',
					className: 'text-center',
					orderable: false
				},
				{
					data: 'Code',
					className: 'text-center'
				},
				{
					data: 'Name'
				},
			],
			"order": [
				['1', 'asc']
			],
			"drawCallback": function(data, type, full, meta) {
				var api = this.api();
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

	$('#modal_indicator1').on('hidden.bs.modal', function() {
		if (t_modal !== undefined) {
			t_modal.destroy(); // Destroy the DataTable instance
		}
	});
</script>