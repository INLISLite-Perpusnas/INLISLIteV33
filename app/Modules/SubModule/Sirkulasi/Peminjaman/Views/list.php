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
				<div>Peminjaman
					<div class="page-title-subheading">Daftar semua Peminjaman</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('peminjaman') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="active breadcrumb-item" aria-current="page">Peminjaman</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<div class="main-card mb-3 card">
		<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Daftar Peminjaman
			<div class="btn-actions-pane-right actions-icon-btn">
				<?php if (is_allowed('sirkulasi-peminjaman/create')) : ?>
					<!-- <a data-toggle="modal" data-target="#modal_create" href="javascript:void(0);" class="btn btn-success" title="Tambah"><i class="fa fa-plus"></i> Peminjaman</a> -->
					<a href="<?= base_url('sirkulasi-peminjaman/create') ?>" class="btn btn-success" title="Tambah"><i class="fa fa-plus"></i> Peminjaman</a>
				<?php endif; ?>
			</div>
		</div>
		<div class="card-body">
			<table style="width: 100%;" id="tbl_data" class="table table-hover table-bordered">
				<thead class="bg-night-sky text-light">
					<tr>
						<th class="text-center" width="35">No</th>
						<th class="text-center" width="100">No. Barcode</th>
						<th class="text-center">Penerbit / Judul</th>
						<th class="text-center" width="100">Tgl. Pinjam/ Jatuh Tempo</th>
						<th class="text-center">Hari Terlambat</th>
						<th class="text-center" width="120">Lokasi Perpustakaan</th>
						<th class="text-center" width="100">Updated Date</th>
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
<?= $this->include('Peminjaman\Views\add_modal'); ?>
<?= $this->include('Peminjaman\Views\update_modal'); ?>
<script>
    // Group berdasarkan kolom CollectionLoan_id (yang sudah di-edit di controller berisi info member)
    var groupColumn = 7; 
    var t;
    $(document).ready(function() {
        t = $('#tbl_data').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": '<?php echo site_url('api/sirkulasi-peminjaman/datatable/' . $slug) ?>',
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
                { data: 'no', className: 'text-center', orderable: false }, // 0
                { data: 'NomorBarcode' },                                  // 1
                { data: 'Title' },                                         // 2
                { data: 'LoanDate', className: 'text-center' },            // 3
                { data: 'LateDays', className: 'text-center' },            // 4
                { data: 'LocationLibrary' },                               // 5
                { data: 'UpdateDate', className: 'text-center' },          // 6
                { data: 'CollectionLoan_id', visible: false },             // 7 (Untuk Grouping)
                { data: 'ID', visible: false },                            // 8
                { data: 'Fullname', visible: false },                      // 9
                { data: 'DueDate', visible: false },                       // 10
                { data: 'Publisher', visible: false },                     // 11
            ],
            "columnDefs": [
                {
                    targets: [0, 7, 8, 9, 10, 11],
                    searchable: false
                },
                {
                    targets: [0, 2, 3, 4, 7],
                    orderable: false
                }
            ],
            "order": [
                [7, "desc"] // Urutkan berdasarkan grup ID transaksi terbaru
            ],
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

    // Fungsi klik pada grup untuk sorting (Opsional)
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