<div class="main-card mb-3 card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
        <div><i class="header-icon lnr-list icon-gradient bg-plum-plate"></i> Tabel Data Artikel</div>

        <div class="d-flex align-items-center flex-wrap">
            <?php if (is_allowed('katalog/create_artikel')) : ?>
                <?php if (get_setting_parameter('FormEntriKatalog', is_profiling()) == 'Simple') : ?>
                    <a href="<?= base_url('katalog/create?rda=0') ?>" class="btn btn-success btn-sm mr-2">
                        <i class="fa fa-plus"></i> Tambah Artikel
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <form name="form_items" id="form_items">
                <table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center" width="35">
                                <input type="checkbox" class="check_data" title="Pilih Semua">
                            </th>

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
<script>
	var t;
	$(document).ready(function() {
		t = $('#tbl_data').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				"url": "<?php echo site_url('api/katalog/datatable_artikel') ?>",
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
			"columns": [{
					data: 'id',
					className: 'text-center',
					orderable: false
				},
				{
					data: 'Article_type',
					className: 'text-left'
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
				[0, "asc"]
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
	});
</script>
<?= $this->endSection('script'); ?>
