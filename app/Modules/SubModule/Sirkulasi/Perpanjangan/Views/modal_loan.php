<?php
$request = service('request');
$member_no = $request->getGet('member_no') ?? '';
?>
<div class="modal fade" id="modal_loan" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					<i class="header-icon lnr-list icon-gradient bg-success"> </i> Daftar Peminjaman
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="card-body">
				<form name="form_items" id="form_items">
					<table style="width: 100%;" id="tbl_extend" class="table table-hover table-striped table-borderless" style="z-index:1000">
						<thead class="bg-night-sky text-light">
							<tr>
								<th class="text-center" width="35">
									<input type="checkbox" class="check_return" title="Pilih Semua">
								</th>
								<th class="text-center">No. Barcode</th>
								<th class="text-center">Penerbit / Judul</th>
								<th class="text-center">Hari Terlambat</th>
							</tr>
						</thead>
					</table>
				</form>
			</div>
			<div class="modal-footer">
				<div class="d-block">
					<button type="button" id="add_to_cart" class="btn btn-success" data-toggle="tooltip" data-placement="left" title="Tambah ke Troli Perpanjangan"><i class="fa fa-plus"></i> Tambah ke Troli</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	var t_modal
	$('#modal_loan').on('shown.bs.modal', function() {
		t_modal = $('#tbl_extend').DataTable({
			"processing": true,
			"retrieve": true,
			"serverSide": true,
			"ajax": {
				"url": '<?php echo site_url('api/sirkulasi-perpanjangan/loan_datatable') ?>',
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
					data: 'NomorBarcode'
				},
				{
					data: 'Title'
				},
				{
					data: 'LateDays',
					className: 'text-center'
				},
				{
					data: 'Publisher',
					visible: false
				},
			],
			"order": [
				['1', 'asc']
			],
			"drawCallback": function(data, type, full, meta) {
				$('[data-toggle="tooltip"]').tooltip();
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

	$(".check_return").click(function() {
		$('#tbl_extend input:checkbox').not(this).prop('checked', this.checked);
	});

	$('#add_to_cart').click(function() {
		var form = $('#form_items');
		var serialize_bulk = form.serialize();
		var url = "<?= base_url('sirkulasi-perpanjangan/cart_insert') ?>" + '?' + serialize_bulk;
		console.log(serialize_bulk);
		console.log(url);

		Swal.fire({
			title: 'Anda yakin?',
			html: "Semua koleksi yang terpilih akan ditambah <br>ke Troli Pengembalian",
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
</script>