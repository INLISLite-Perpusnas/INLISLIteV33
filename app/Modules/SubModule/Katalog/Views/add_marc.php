<?php
$request = service('request');
$slug = $request->getGet('slug') ?? 'katalog_add_marc';
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
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-note icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Tambah Katalog
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="breadcrumb-item"><a href="<?= base_url('katalog') ?>">Katalog</a></li>
						<li class="breadcrumb-item" aria-current="page">Tambah</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<div class="main-card mb-3 card">
		<form id="frm_create" class="col-md-12 mx-auto" method="post" action="<?= base_url('katalog/create_marc') ?>" onsubmit="return validateForm()">
			<?= $this->include("Katalog\Views\slug\\$slug"); ?>
		</form>
	</div>
</div>


<?= $this->endSection('page'); ?>

<?= $this->section('script') ?>
<?= $this->include('Katalog\Views\modal_indicator1'); ?>
<?= $this->include('Katalog\Views\modal_indicator2'); ?>
<?= $this->include('Katalog\Views\modal_content'); ?>
<script>
    $(document).ready(function() {
        <?php if (session()->getFlashdata('swal_icon')) : ?>
            Swal.fire({
                type: '<?= session()->getFlashdata('swal_icon') ?>', // gunakan 'icon' jika SweetAlert2 versi terbaru
                title: '<?= session()->getFlashdata('swal_title') ?>',
                html: '<?= session()->getFlashdata('swal_html') ?? session()->getFlashdata('swal_text') ?>',
                showConfirmButton: false,
                timer: 3000,
                icon:'error'
            });
        <?php endif; ?>
    });
</script>
<script>
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

		$(document).on('click', '.btn-value', function() {
			var id = $(this).data('id');
			var field_id = $(this).data('field_id');
			var tag = $(this).data('tag');
			var name = $(this).data('name');

			$('#title_content').html(`Tag ${tag} - ${name} `);
			$('#field_id_content').val(id);
			$('#dom_id_content').val(field_id);
			$('#modal_content').modal('show');
		});


		const getTags = async (url, dom, selected = false, label = '-All-') => {
			await axios.get(url)
				.then(res => {
					// console.log(res)
					$(dom).html('<option value="">Loading...</option>');
					var output = '<option value="">' + label + '</option>';
					$.each(res.data, function(key, val) {
						output += '<option value="' + val.code + '" data-text="' + val.name + '">[' + val.tag + '] ' + val.name + '</option>';
					});

					$(dom).html(output);
					$(dom).select2({
						width: "resolve"
					});

					if (selected) {
						$(dom).val(selected);
					}
				})
				.catch(err => {
					console.log(err)
				});
		}


		$(".worksheet-btn-load").click(function() {
			var worksheet_id = $("#worksheet_id").val();
			var url = `<?= base_url('katalog/create_marc') ?>?worksheet_id=${worksheet_id}&fullscreen=1`;
			window.location.href = url;
		});

		var worksheet_id = $("#worksheet_id").val();
		var url = `<?= base_url('api/katalog/get_all_tags') ?>/${worksheet_id}`;
		getTags(url, `#field_id`, false, `-Pilih-`);

		$.ajax({
			url: url,
			type: 'GET',
			dataType: 'json',
			success: function(response) {
				// console.log(response);
				$('#field_id').find('option:selected').remove();
				// $(".select2").trigger('update');
			},
			error: function(xhr, status, error) {
				console.error('API call error:', status, error);
			}
		});

		$(document).on('click', '.marc-btn-remove', function() {
			var field_id = $(this).data('field_id');
			var url = "<?= base_url('api/katalog/remove_from_session') ?>" + "/" + field_id;

			var $this = $(this); // Capture the context

			$.ajax({
				url: url,
				type: 'GET',
				dataType: 'json',
				success: function(response) {
					console.log(response);
					var row = $this.closest('tr');
					row.remove();

					var tag = $('<option>', {
						value: response.ID,
						text: '[' + response.Tag + '] ' + response.Name
					});
					$('#field_id').append(tag);

					return false;
				},
				error: function(xhr, status, error) {
					console.error('API call error:', status, error);
				}
			});
		});

		$(".marc-btn-add").click(function() {
			var index = Date.now();
			var field_id = $('#field_id').val();
			var url = "<?= base_url('api/katalog/add_to_session') ?>" + "/" + field_id;
			$.ajax({
				url: url,
				type: 'GET',
				dataType: 'json',
				success: function(response) {
					console.log(response);

					if (response.Repeatable < 1) {
						$('#field_id').find('option:selected').remove();
					}

					$('#marc-tbody').append(`
						<tr class="rm-row">
							<td class="text-center">
								<button type="button" class="btn btn-danger marc-btn-remove" data-field_id="` + response.ID + ` "><i class="fa fa-trash"></i></button>
							</td>
							<td>
								<input type="hidden" name="index0[]" value="` + index + `">
								<input type="hidden" name="Index[` + response.Tag + `][]" value="Index_` + response.Tag + `_` + index + `">
								` + response.Tag + ` 
							</td>
							<td>
								` + response.Name + ` 
							</td>
							<td>
								<div class="input-group">
									<input type="text" class="form-control" name="Indicator1[` + response.Tag + `][]" id="Indicator1_` + response.Tag + `_` + index + `"  placeholder="" value="#" />
									<div class="input-group-append">
										<button class="btn btn bg-primary text-white btn-indicator1" data-id="` + response.ID + `" data-field_id="Indicator1_` + response.Tag + `_` + index + `" data-tag="` + response.Tag + `" data-name="` + response.Name + `" type="button"><i class="fa fa-list"></i></button>
									</div>
								</div>
							</td>
							<td>
								<div class="input-group">
									<input type="text" class="form-control" name="Indicator2[` + response.Tag + `][]" id="Indicator2_` + response.Tag + `_` + index + `"  placeholder="" value="#" />
									<div class="input-group-append">
										<button class="btn btn bg-primary text-white btn-indicator2" data-id="` + response.ID + `" data-field_id="Indicator2_` + response.Tag + `_` + index + `" data-tag="` + response.Tag + `" data-name="` + response.Name + `" type="button"><i class="fa fa-list"></i></button>
									</div>
								</div>  
							</td>
							<td>
								<div class="input-group">
									<input type="text" class="form-control" name="Value[` + response.Tag + `][]" id="Value_` + response.Tag + `_` + index + `"  placeholder="" value="$a" />
									<div class="input-group-append">
										<button class="btn btn bg-primary text-white btn-value" data-id="` + response.ID + `" data-field_id="Value_` + response.Tag + `_` + index + `" data-tag="` + response.Tag + `" data-name="` + response.Name + `" type="button"><i class="fa fa-list"></i></button>
									</div>
								</div>
							</td>
						</tr>
					`);
					window.scrollTo({
						left: 0,
						top: document.body.scrollHeight,
						behavior: "smooth"
					});
				},
				error: function(xhr, status, error) {
					console.error('API call error:', status, error);
				}
			});
		});
	});
</script>
<?= $this->endSection('script') ?>