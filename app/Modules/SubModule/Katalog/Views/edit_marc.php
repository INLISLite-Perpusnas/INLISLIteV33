<?php
$request = service('request');
$slug = $request->getGet('slug') ?? 'katalog_edit_marc';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
	.tox.tox-tinymce.tox-fullscreen {
		z-index: 1050;
		top: 60px !important;
		left: 85px !important;
		width: calc(100% - 85px) !important;
	}
</style>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-note icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Edit Katalog
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="breadcrumb-item"><a href="<?= base_url('katalog') ?>">Katalog</a></li>
						<li class="active breadcrumb-item" aria-current="page">Edit</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<div class="main-card mb-3 card">
		<form id="frm_edit" class="col-md-12 mx-auto" method="post" action="<?= base_url('katalog/edit_marc/' . $catalog->ID) ?>" onsubmit="return validateForm()">
			<?= $this->include("Katalog\Views\slug\\$slug"); ?>
		</form>
	</div>
</div>

<?= $this->endSection('page'); ?>

<?= $this->section('script') ?>
<?= $this->include('Katalog\Views\modal_indicator1'); ?>
<?= $this->include('Katalog\Views\modal_indicator2'); ?>
<?= $this->include('Katalog\Views\modal_content'); ?>
<?= $this->include('Katalog\Views\modal_fixed'); ?>
<?= $this->include('Katalog\Views\marc_script'); ?>
<script>
	// Dipanggil oleh onsubmit form: judul (245 $a) wajib diisi, tombol Update dikunci agar tidak terkirim dobel.
	function validateForm() {
		var hasTitle = false;
		$('#frm_edit input[name^="Value[245]"]').each(function() {
			var match = /\$a([^$]*)/.exec(this.value || '');
			if (match && match[1].trim() !== '') {
				hasTitle = true;
			}
		});
		if (!hasTitle) {
			Swal.fire({
				title: 'Validasi Gagal',
				text: 'Tag 245 (Pernyataan Judul) subruas $a wajib diisi.',
				icon: 'warning',
				type: 'warning'
			});
			return false;
		}
		$('#frm_edit button[type="submit"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-2"></i>Menyimpan...');
		return true;
	}

	$(document).ready(function() {
		$('.simple-btn-switch').on('click', function() {
			var url = $(this).data('href');
			Swal.fire({
				title: 'Anda yakin?',
				html: "Form Sederhana tidak mengakomodir format Metadata MARC secara penuh",
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
		});

		$(document).on('click', '.btn-indicator1', function() {
			var id = $(this).data('id');
			var field_id = $(this).data('field_id');
			var tag = $(this).data('tag');
			var name = $(this).data('name');

			$('#title_indicator1').html(`Indikator 1 - Tag ${tag} ${name} `);
			$('#field_id_indicator1').val(id);
			$('#dom_id_indicator1').val(field_id);
			$('#modal_indicator1').modal('show');
		});

		$(document).on('click', '.btn-indicator2', function() {
			var id = $(this).data('id');
			var field_id = $(this).data('field_id');
			var tag = $(this).data('tag');
			var name = $(this).data('name');

			$('#title_indicator2').html(`Indikator 2 - Tag ${tag} ${name} `);
			$('#field_id_indicator2').val(id);
			$('#dom_id_indicator2').val(field_id);
			$('#modal_indicator2').modal('show');
		});

		initMarcForm(<?= $catalog->ID ?>);

		$(".worksheet-btn-load").click(function() {
			var worksheet_id = $("#worksheet_id").val();
			var catalog_id = <?= $catalog->ID ?>;
			var url = `<?= base_url('katalog/edit_marc') ?>/${catalog_id}?worksheet_id=${worksheet_id}&fullscreen=1`;
			window.location.href = url;
		});
	});
</script>
<?= $this->endSection('script') ?>