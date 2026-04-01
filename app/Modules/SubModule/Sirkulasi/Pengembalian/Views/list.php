<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
	tr.group,
	tr.group:hover {
		background-color: #F0F3F5 !important;
	}

	dl {
		display: grid;
		grid-template-columns: max-content auto;
	}

	dt {
		grid-column-start: 1;
		width: 100px;
		font-weight: normal;
	}

	dd {
		grid-column-start: 2;
	}
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-refresh-2 icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Pengembalian
					<div class="page-title-subheading">Daftar semua Pengembalian</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('pengembalian') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="breadcrumb-item" aria-current="page">Pengembalian</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	

	<div class="main-card mb-3 card">
		<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Daftar Pengembalian
			<div class="btn-actions-pane-right actions-icon-btn">
				<?php if (is_allowed('sirkulasi-pengembalian/create')) : ?>
					<!-- <a data-toggle="modal" data-target="#modal_create" href="javascript:void(0);" class="btn btn-success" title="Tambah"><i class="fa fa-plus"></i> Pengembalian</a> -->
					<a href="<?= base_url('sirkulasi-pengembalian/create') ?>" class="btn btn-success" title="Tambah"><i class="fa fa-plus"></i> Pengembalian</a>
				<?php endif; ?>
			</div>
		</div>
		<div class="card-body">
			<?= get_message('message'); ?>
			<table style="width: 100%;" id="tbl_data" class="table table-hover table-bordered">
				<thead class="bg-night-sky text-light">
					<tr>
						<th class="text-center" width="35">No</th>
						<th class="text-center" width="100">No. Barcode</th>
						<th class="text-center">Penerbit / Judul</th>
						<th class="text-center" width="100">Tgl. Pinjam/ Jatuh Tempo</th>
						<th class="text-center">Tgl. Kembali</th>
						<th class="text-center">Hari Terlambat</th>
						<th class="text-center" width="120">Lokasi Perpustakaan</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    // Index 7 adalah CollectionLoan_id yang berisi info Member & No Transaksi
    var groupColumn = 7; 
    var t;

    $(document).ready(function() {
        t = $('#tbl_data').DataTable({
            "processing": true,
            "serverSide": true,
            "scrollCollapse": true,
            "scrollX": true,
            "ajax": {
                "url": '<?php echo site_url('api/sirkulasi-pengembalian/datatable/' . $slug) ?>',
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
            "columns": [
                { data: 'no', className: 'text-center', orderable: false }, // 0
                { data: 'NomorBarcode' },                                  // 1
                { data: 'Title' },                                         // 2
                { data: 'LoanDate', className: 'text-center' },            // 3
                { data: 'ActualReturn', className: 'text-center' },        // 4
                { data: 'LateDays', className: 'text-center' },            // 5
                { data: 'LocationLibrary' },                               // 6
                { data: 'CollectionLoan_id', visible: false },             // 7 (Untuk Grouping)
                { data: 'Fullname', visible: false },                      // 8
                { data: 'DueDate', visible: false },                       // 9
                { data: 'Publisher', visible: false },                     // 10
            ],
            "columnDefs": [
                {
                    targets: [0, 7, 8, 9, 10],
                    searchable: false
                },
                {
                    targets: [0, 2, 3, 5, 6],
                    orderable: false
                }
            ],
            "order": [[groupColumn, "desc"]],
            "drawCallback": function(settings) {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;

                api.column(groupColumn, { page: 'current' })
                    .data()
                    .each(function(group, i) {
                        if (last !== group) {
                            $(rows).eq(i).before(
                                '<tr class="group"><td colspan="7">' + group + '</td></tr>'
                            );
                            last = group;
                        }
                    });
            },
            "initComplete": function(settings, json) {
                var $searchInput = $('div.dataTables_filter input');
                $searchInput.unbind();
                $searchInput.bind('keyup', function(e) {
                    if (e.keyCode == 13) {
                        t.search(this.value).draw();
                    }
                });
            }
        });
    });

    // Handle klik pada baris grup untuk sorting
    $('#tbl_data tbody').on('click', 'tr.group', function() {
        var currentOrder = t.order()[0];
        if (currentOrder[0] === groupColumn && currentOrder[1] === 'asc') {
            t.order([groupColumn, 'desc']).draw();
        } else {
            t.order([groupColumn, 'asc']).draw();
        }
    });
</script>
<?= $this->endSection('script'); ?>