<?php
$request = service('request');
?>
<div class="modal fade" id="modal_content" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="title_content">
					${Tag} - ${Name}
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="card-body">
				<form name="form_content" id="form_content">
					<input type="hidden" name="field_id_content" id="field_id_content" value="">
					<input type="hidden" name="dom_id_content" id="dom_id_content" value="">
					<table style="width: 100%;" id="tbl_content" class="table table-hover table-striped table-bordered" style="z-index:1000">
						<thead>
							<tr>
								<th width="40" class="text-center">Kode</th>
								<th class="text-center">Nama</th>
								<th class="text-center">Isi</th>
							</tr>
						</thead>
					</table>
				</form>
			</div>
			<div class="card-footer">
				<button type="button" class="btn btn-primary copy-content">OK</button>
			</div>
		</div>
	</div>
</div>

<script>
	$("body").on("click", ".copy-content", function() {
		var inputElements = document.getElementsByClassName("input-content");
		var html = ``;
		var field = 0;

		for (var i = 0; i < inputElements.length; i++) {
			var input = inputElements[i];
			var code = input.getAttribute("code");
			var value = input.value;
			field = input.getAttribute("field");

			if (value.length > 0) {
				html += `$${code} ${value} `;
			}
		}

		var dom_id = $('#dom_id_content').val();
		$(`#${dom_id}`).val(html);
		$('#modal_content').modal('hide');
	});


	var t_modal
	$('#modal_content').on('shown.bs.modal', function(event) {
		var field_id = $('#field_id_content').val();
		var url = `<?= base_url('api/katalog/get_field_content') ?>/${field_id}`;

		t_modal = $('#tbl_content').DataTable({
			"processing": true,
			"retrieve": true,
			"serverSide": true,
			"ajax": {
				"url": url,
			},
			"dom": "<'row'<'col-md-6 col-sm-8 col-xs-12 text-left'><'col-md-6 col-sm-4 col-xs-12 d-none d-sm-block text-right'>>" +
				"<'row'<'col-md-12'tr>>" +
				"<'row'<'col-md-6 col-sm-12'><'col-md-6 col-sm-12 text-right'>>",
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
					data: 'Code'
				},
				{
					data: 'Name'
				},
				{
					data: 'Input'
				},
			],
			"order": [
				['0', 'asc']
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

	$('#modal_content').on('hidden.bs.modal', function() {
		if (t_modal !== undefined) {
			t_modal.destroy(); // Destroy the DataTable instance
		}
	});
</script>