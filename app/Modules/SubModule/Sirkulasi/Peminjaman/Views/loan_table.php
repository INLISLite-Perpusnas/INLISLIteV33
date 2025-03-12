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
</script>
<?= $this->endSection('script'); ?>