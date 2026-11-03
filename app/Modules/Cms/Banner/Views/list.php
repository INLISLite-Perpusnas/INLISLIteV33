<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-table.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-table.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div>Banner <?= ucwords(unslugify($slug)) ?>
					<div class="page-title-subheading">Kelola daftar banner perpustakaan.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Beranda</a></li>
						<li class="active breadcrumb-item" aria-current="page">Banner</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<?= get_message('message'); ?>

	<div class="ina-table-card">
		<div class="ina-table-toolbar">
			<div class="ina-search">
				<i class="fas fa-search" aria-hidden="true"></i>
				<input type="search" id="banner-search" placeholder="Cari judul banner" aria-label="Cari judul banner" autocomplete="off">
			</div>
			<div class="ina-table-actions">
				<a href="<?= base_url('cms/banner/create?slug=' . $slug) ?>" class="ina-btn-primary">
					<i class="fas fa-plus" aria-hidden="true"></i> Tambah Banner
				</a>
			</div>
		</div>
		<table id="tbl_pages" class="ina-table">
			<thead>
				<tr>
					<th>Judul</th>
					<th>Kategori</th>
					<th>Keterangan</th>
					<th class="ina-num">Urutan</th>
					<th>Status</th>
					<th class="ina-col-action">Aksi</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
	$(document).ready(function() {
		<?php if (session()->getFlashdata('swal_icon')) : ?>
			Swal.fire({
				type: '<?= session()->getFlashdata('swal_icon') ?>', // gunakan 'icon' jika SweetAlert2 versi terbaru
				title: '<?= session()->getFlashdata('swal_title') ?>',
				html: '<?= session()->getFlashdata('swal_html') ?? session()->getFlashdata('swal_text') ?>',
				showConfirmButton: false,
				timer: 3000,
				icon: 'success'
			});
		<?php endif; ?>
	});
</script>
<script>
	var t;
	$(document).ready(function() {
		t = $('#tbl_pages').DataTable({
			"processing": true,
			"serverSide": true,
			"autoWidth": false,
			"ajax": {
				"url": '<?php echo site_url('api/banner/datatable/' . $slug) ?>',
			},
			// Pencarian memakai input sendiri di toolbar; jumlah baris/info/paginasi di bawah
			"dom": "<'row'<'col-md-12 table-responsive'tr>>" +
				"<'ina-table-footer'lip>",
			"pageLength": 30,
			"lengthMenu": [[10, 30, 50, 100], [10, 30, 50, 100]],
			"pagingType": "simple_numbers",
			"language": {
				"lengthMenu": "Tampilkan _MENU_ baris",
				"info": "_START_–_END_ dari _TOTAL_ banner",
				"infoEmpty": "Tidak ada banner",
				"infoFiltered": "(disaring dari _MAX_)",
				"zeroRecords": "Banner tidak ditemukan",
				"emptyTable": "Belum ada banner",
				"processing": "Memuat data...",
				"paginate": {
					"next": "<i class='fa fa-chevron-right' aria-hidden='true'></i><span class='sr-only'>Berikutnya</span>",
					"previous": "<i class='fa fa-chevron-left' aria-hidden='true'></i><span class='sr-only'>Sebelumnya</span>"
				}
			},
			"columns": [{
					data: 'title'
				},
				{
					data: 'category'
				},
				{
					data: 'description',
					orderable: false
				},
				{
					data: 'sort',
					className: 'ina-num'
				},
				{
					data: 'active'
				},
				{
					data: 'action',
					orderable: false,
					className: 'ina-col-action'
				},
			],
			"order": [],
			"drawCallback": function() {
				if ($.fn.magnificPopup) {
					$('.image-link').magnificPopup({
						type: 'image'
					});
				}
			}
		});

		// Enter untuk mencari; kosongkan untuk menampilkan semua lagi
		$('#banner-search').on('keyup', function(e) {
			if (e.keyCode == 13) {
				t.search(this.value).draw();
			}
		}).on('input search', function() {
			if (this.value === '') {
				t.search('').draw();
			}
		});
	});

	$("body").on("click", ".remove-data", function() {
		var url = $(this).attr('data-href');
		Swal.fire({
			title: '<?= lang('App.swal.are_you_sure') ?>',
			text: "<?= lang('App.swal.can_not_be_restored') ?>",
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
