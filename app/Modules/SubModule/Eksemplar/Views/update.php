<?php
$request = service('request');
$slug = $request->getGet('slug');
$select2 = select_two();
$nomor_manual = get_parameter('nomor-mode', 'manual') == 'manual';
$return_catalog = $request->getGet('catalog_id') ?? '';
$catalog_id = $eksemplar->Catalog_id;
$catalog = get_catalog($catalog_id);
$tanggal_pengadaan = date('Y-m-d', strtotime($eksemplar->TanggalPengadaan));
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
				<div>Eksemplar <?= ucwords(unslugify($slug)) ?>
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="breadcrumb-item"><a href="<?= base_url('eksemplar') ?>">Eksemplar</a></li>
						<li class="breadcrumb-item" aria-current="page">Ubah Eksemplar</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<form id="frm_edit" class="main-card mb-3 card" method="post" action="<?= base_url('eksemplar/edit/' . $eksemplar->ID); ?>">
		<div class="card-header"></div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? ''; ?></div>
			<?= get_message('message'); ?>

			<!-- Card Informasi Katalog -->
			<div class="card card-shadow mb-4">
				<div class="card-body">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2 col-md-6">
								<label for="CallNumber">No. Panggil</label>
								<input type="text" class="form-control" name="CallNumber" id="CallNumber" placeholder="" value="<?= $catalog->CallNumber ?>" readonly />
							</div>
							<div class="col-lg-2 col-md-6">
								<label>ID Katalog</label>
								<input type="text" class="form-control" id="Catalog_id2" placeholder="" value="<?= $catalog->ID ?>" readonly />
							</div>
							<div class="col-lg-8 col-md-12">
								<label>Judul Katalog</label>
								<input type="text" class="form-control" id="Title" placeholder="" value="<?= $catalog->Title ?>" readonly />
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Card Eksemplar -->
			<div class="card card-shadow mb-4">
				<div class="card-body">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2 col-md-6">
								<label>Is Drm*</label>
								<div>
									<div class="form-check">
										<input type="radio" class="form-check-input" name="ISDRM" id="is_drm_1" value="1"
											<?= isset($eksemplar->ISDRM) && $eksemplar->ISDRM == 1 ? 'checked' : '' ?> />
										<label class="form-check-label" for="is_drm_1">Yes</label>
									</div>
									<div class="form-check">
										<input type="radio" class="form-check-input" name="ISDRM" id="is_drm_0" value="0"
											<?= isset($eksemplar->ISDRM) && $eksemplar->ISDRM == 0 ? 'checked' : '' ?> />
										<label class="form-check-label" for="is_drm_0">No</label>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group" id="eksemplar">
						<label>Eksemplar</label>
						<div class="input-group">
							<div class="input-group-prepend"><span class="input-group-text">No. Induk: </span></div>
							<input type="text" class="form-control" name="NoInduk0" id="NoInduk_0" placeholder="" value="<?= $eksemplar->NoInduk ?>" />
							<div class="input-group-prepend"><span class="input-group-text">No. Barcode: </span></div>
							<input type="text" class="form-control" name="NomorBarcode0" id="NomorBarcode_0" placeholder="" value="<?= $eksemplar->NomorBarcode ?>" />
							<div class="input-group-prepend"><span class="input-group-text">No. RFID: </span></div>
							<input type="text" class="form-control" name="RFID0" id="RFID_0" placeholder="" value="<?= $eksemplar->RFID ?>" />
						</div>
					</div>
				</div>
			</div>

			<!-- Card Detail Pengadaan -->
			<div class="card card-shadow mb-4">
				<div class="card-body">
					<div class="form-group">
						<div class="row">

							<div class="col-lg-4 col-md-6 mt-3">
								<label for="TanggalPengadaan">Tanggal Pengadaan</label>
								<input type="date" class="form-control" name="TanggalPengadaan" id="TanggalPengadaan" value="<?= $tanggal_pengadaan ?>" />
							</div>

							<div class="col-lg-4 col-md-6 mt-3">
								<label for="Source_id">Jenis Sumber</label>
								<div class="select-wrapper">
									<select class="form-control" name="Source_id" id="Source_id" style="width:100%">
										<option value="">-Pilih-</option>
										<?php foreach (get_ref_table('collectionsources', 'ID, Code, Name', null, 'data') as $row) : ?>
											<option value="<?= $row->ID ?>" <?= $eksemplar->Source_id == $row->ID ? 'selected' : '' ?>>
												<?= $row->Code ?> <?= $row->Name ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>

							<div class="col-lg-4 col-md-6 mt-3">
								<label for="Partner_id">Nama Sumber</label>
								<div class="select-wrapper input-group">
									<select class="form-control" name="Partner_id" id="Partner_id" style="width:80%">
										<option value="">-Pilih-</option>
										<?php foreach (get_ref_table('partners', 'ID, Name', null, 'data') as $row) : ?>
											<option value="<?= $row->ID ?>" <?= $eksemplar->Partner_id == $row->ID ? 'selected' : '' ?>>
												<?= $row->Name ?>
											</option>
										<?php endforeach; ?>
									</select>
									<div class="input-group-append">
										<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalAddPartner">
											<i class="fa fa-plus"></i>
										</button>
										<button type="button" id="btnEditPartner" class="btn btn-warning" data-toggle="modal" data-target="#modalEditPartner" disabled>
											<i class="fa fa-edit"></i>
										</button>
									</div>
								</div>
							</div>

							<div class="col-lg-4 col-md-6 mt-3">
								<label for="Media_id">Bentuk Fisik</label>
								<div class="select-wrapper">
									<select class="form-control" name="Media_id" id="Media_id" style="width:100%">
										<option value="">-Pilih-</option>
										<?php foreach (get_ref_table('collectionmedias', 'ID, Code, Name', null, 'data') as $row) : ?>
											<option value="<?= $row->ID ?>" <?= $eksemplar->Media_id == $row->ID ? 'selected' : '' ?>>
												 <?= $row->Name ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>

							<div class="col-lg-4 col-md-6 mt-3">
								<label for="Category_id">Kategori</label>
								<div class="select-wrapper">
									<select class="form-control" name="Category_id" id="Category_id" style="width:100%">
										<option value="">-Pilih-</option>
										<?php foreach (get_ref_table('collectioncategorys', 'ID, Code, Name', null, 'data') as $row) : ?>
											<option value="<?= $row->ID ?>" <?= $eksemplar->Category_id == $row->ID ? 'selected' : '' ?>>
												<?= $row->Name ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>

							<div class="col-lg-4 col-md-6 mt-3">
								<label for="Rule_id">Akses</label>
								<div class="select-wrapper">
									<select class="form-control" name="Rule_id" id="Rule_id" style="width:100%">
										<option value="">-Pilih-</option>
										<?php foreach (get_ref_table('collectionrules', 'ID, Name', null, 'data') as $row) : ?>
											<option value="<?= $row->ID ?>" <?= $eksemplar->Rule_id == $row->ID ? 'selected' : '' ?>>
												<?= $row->Name ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>

							<div class="col-lg-4 col-md-6 mt-3">
								<label for="Location_Library_id">Lokasi Perpustakaan</label>
								<div class="select-wrapper">
									<select class="form-control selectx" name="Location_Library_id" id="Location_Library_id" tabindex="-1" aria-hidden="true" style="width:100%">
										<option value="">-Pilih-</option>
									</select>
								</div>
							</div>

							<div class="col-lg-4 col-md-6 mt-3">
								<label for="Location_id">Lokasi Ruang</label>
								<div class="select-wrapper">
									<select class="form-control selectx" name="Location_id" id="Location_id" tabindex="-1" aria-hidden="true" style="width:100%">
										<option value="">-Pilih-</option>
									</select>
								</div>
							</div>

							<div class="col-lg-4 col-md-6 mt-3">
								<label for="Status_id">Ketersediaan</label>
								<div class="select-wrapper">
									<select class="form-control" name="Status_id" id="Status_id" style="width:100%">
										<option value="">-Pilih-</option>
										<?php foreach (get_ref_table('collectionstatus', 'ID, Name', null, 'data') as $row) : ?>
											<option value="<?= $row->ID ?>" <?= $eksemplar->Status_id == $row->ID ? 'selected' : '' ?>>
												<?= $row->Name ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>

							<div class="col-lg-4 col-md-6 mt-3">
								<label for="Currency">Mata Uang</label>
								<div class="select-wrapper">
									<select class="form-control" name="Currency" id="Currency" style="width:100%">
										<option value="">-Pilih-</option>
										<?php foreach (get_ref_table('currency', 'Currency, Description', null, 'data') as $row) : ?>
											<option value="<?= $row->Currency ?>" <?= $eksemplar->Currency == $row->Currency ? 'selected' : '' ?>>
												<?= $row->Currency ?> - <?= $row->Description ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>

							<div class="col-lg-4 col-md-6 mt-3">
								<label for="Price">Harga</label>
								<input type="text" class="form-control" name="Price" id="Price" placeholder="" value="<?= $eksemplar->Price ?>" />
							</div>

							<div class="col-lg-4 col-md-6 mt-3">
								<label for="Price_type">Satuan Harga</label>
								<div class="select-wrapper">
									<?php
									$json = '[{"code": "Per eksemplar","name": "Per eksemplar"}, {"code": "Per jilid","name": "Per jilid"}]';
									$price_types = json_decode($json);
									?>
									<select class="form-control" name="PriceType" id="Price_type" style="width:100%">
										<option value="">-Pilih-</option>
										<?php foreach ($price_types as $row) : ?>
											<option value="<?= esc($row->code) ?>" <?= $eksemplar->PriceType == $row->code ? 'selected' : '' ?>>
												<?= esc($row->name) ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="ISOPAC">Tampil di OPAC</label><br>
				<input type="checkbox" class="apply-status" name="ISOPAC" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal" <?= $eksemplar->ISOPAC == 1 ? 'checked' : '' ?>>
			</div>

			<div class="table-responsive">
				<table class="table table-bordered table-striped mb-0">
					<tbody>
						<tr>
							<th scope="row">Dibuat Oleh</th>
							<td><?= $CreateBy ?></td>
						</tr>
						<tr>
							<th scope="row">Diperbarui Oleh</th>
							<td><?= $UpdateBy ?></td>
						</tr>
						<tr>
							<th scope="row">Dibuat Pada</th>
							<td><?= $eksemplar->CreateDate ?></td>
						</tr>
						<tr>
							<th scope="row">Diperbarui Pada</th>
							<td><?= $eksemplar->UpdateDate ?></td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="card-footer p-0 pt-3">
				<div class="form-group">
					<div class="form-check form-check-inline">
						<input type="hidden" name="Branch_id" id="Branch_id" value="<?= branch_id() ?>">
						<input type="hidden" name="Catalog_id" id="Catalog_id" value="<?= $catalog_id ?>">
						<input type="hidden" name="IsRedirect" value="0">
						<input class="form-check-input" type="checkbox" name="IsRedirect" value="1">
						<label class="form-check-label" for="IsRedirect">Tutup form setelah simpan</label>

						<?php if (!empty($return_catalog)) : ?>
							<input type="hidden" name="redirect" id="redirect" value="katalog/edit/<?= $catalog_id ?>?slug=eksemplar">
						<?php else : ?>
							<input type="hidden" name="redirect" id="redirect" value="eksemplar">
						<?php endif; ?>

						<button type="submit" class="btn btn-primary btn-lg mr-2 ml-2" name="submit">
							<i class="fa fa-save"></i> Simpan
						</button>
					</div>
				</div>
			</div>
		</div>
	</form>

	<?php if (!empty($return_catalog)) : ?>
		<a href="<?= base_url('katalog/edit/' . $catalog_id . '?slug=eksemplar') ?>" class="btn btn-secondary btn-lg mb-3">
			<i class="fa fa-chevron-left"></i> Kembali ke Katalog
		</a>
	<?php else : ?>
		<a href="<?= base_url('eksemplar') ?>" class="btn btn-secondary btn-lg mb-3">
			<i class="fa fa-list"></i> Kembali ke Daftar Eksemplar
		</a>
	<?php endif; ?>
</div>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>

<!-- Modal Add Partner -->
<div class="modal fade" id="modalAddPartner" tabindex="-1" role="dialog" aria-labelledby="modalAddPartnerLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalAddPartnerLabel">Tambah Nama Sumber</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="formAddPartner">
					<div class="form-group">
						<label for="partnerName">Nama</label>
						<input type="text" class="form-control" id="partnerName" name="Name" required>
					</div>
					<div class="form-group">
						<label for="partnerAddress">Alamat</label>
						<textarea class="form-control" id="partnerAddress" name="Address" rows="3"></textarea>
					</div>
					<div class="form-group">
						<label for="partnerPhone">Telepon</label>
						<input type="text" class="form-control" id="partnerPhone" name="Phone">
					</div>
					<div class="form-group">
						<label for="partnerFax">Fax</label>
						<input type="text" class="form-control" id="partnerFax" name="Fax">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
				<button type="button" class="btn btn-primary" id="btnSavePartner">Simpan</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Edit Partner -->
<div class="modal fade" id="modalEditPartner" tabindex="-1" role="dialog" aria-labelledby="modalEditPartnerLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalEditPartnerLabel">Edit Nama Sumber</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="formEditPartner">
					<input type="hidden" id="editPartnerId" name="ID">
					<div class="form-group">
						<label for="editPartnerName">Nama</label>
						<input type="text" class="form-control" id="editPartnerName" name="Name" required>
					</div>
					<div class="form-group">
						<label for="editPartnerAddress">Alamat</label>
						<textarea class="form-control" id="editPartnerAddress" name="Address" rows="3"></textarea>
					</div>
					<div class="form-group">
						<label for="editPartnerPhone">Telepon</label>
						<input type="text" class="form-control" id="editPartnerPhone" name="Phone">
					</div>
					<div class="form-group">
						<label for="editPartnerFax">Fax</label>
						<input type="text" class="form-control" id="editPartnerFax" name="Fax">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
				<button type="button" class="btn btn-primary" id="btnUpdatePartner">Simpan</button>
			</div>
		</div>
	</div>
</div>
<script>
    $(document).ready(function() {
        <?php if (session()->getFlashdata('swal_icon')) : ?>
            Swal.fire({
                type: '<?= session()->getFlashdata('swal_icon') ?>', // gunakan 'icon' jika SweetAlert2 versi terbaru
                title: '<?= session()->getFlashdata('swal_title') ?>',
                html: '<?= session()->getFlashdata('swal_html') ?? session()->getFlashdata('swal_text') ?>',
                showConfirmButton: false,
                timer: 3000,
                icon:'success'
            });
        <?php endif; ?>
    });
</script>
<script>
	// Lokasi Perpustakaan & Ruang tetap pakai getData (AJAX)
	getData(`<?= base_url('api/eksemplar/locationlibrary') ?>`, `#Location_Library_id`, `<?= $eksemplar->Location_Library_id ?>`, `-Pilih-`);
	getData(`<?= base_url('api/eksemplar/locations/') ?><?= $eksemplar->Location_Library_id ?>`, `#Location_id`, <?= $eksemplar->Location_id ?>, `-Pilih-`);

	$('#Location_Library_id').change(function() {
		var Location_Library_id = $("#Location_Library_id option:selected").val();
		getData(`<?= base_url('api/eksemplar/locations') ?>/${Location_Library_id}`, `#Location_id`, null, `-Pilih-`);
	});

	$(document).ready(function() {

		// Enable tombol edit partner jika ada pilihan
		$('#Partner_id').on('change', function() {
			$('#btnEditPartner').prop('disabled', !$(this).val());
		});
		// Trigger on load jika sudah ada value
		if ($('#Partner_id').val()) {
			$('#btnEditPartner').prop('disabled', false);
		}

		// Add Partner
		$('#btnSavePartner').on('click', function() {
			$.ajax({
				url: '<?= base_url('api/eksemplar/add_partner') ?>',
				type: 'POST',
				data: $('#formAddPartner').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						toastr.success('Data berhasil disimpan');
						$('#modalAddPartner').modal('hide');
						$('#formAddPartner')[0].reset();
						// Reload options Partner_id
						var currentVal = response.data.ID;
						$.getJSON('<?= base_url('api/eksemplar/collectionpartners') ?>', function(data) {
							var opts = '<option value="">-Pilih-</option>';
							$.each(data.data, function(i, row) {
								opts += `<option value="${row.ID}" ${row.ID == currentVal ? 'selected' : ''}>${row.Name}</option>`;
							});
							$('#Partner_id').html(opts).trigger('change');
						});
					} else {
						toastr.error(response.message || 'Terjadi kesalahan saat menyimpan data');
					}
				},
				error: function(xhr, status, error) {
					toastr.error('Terjadi kesalahan: ' + error);
				}
			});
		});

		// Load data Edit Partner
		$('#btnEditPartner').on('click', function() {
			var partnerId = $('#Partner_id').val();
			if (!partnerId) return;
			$.ajax({
				url: '<?= base_url('api/eksemplar/get_partner') ?>/' + partnerId,
				type: 'GET',
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						var p = response.data;
						$('#editPartnerId').val(p.ID);
						$('#editPartnerName').val(p.Name);
						$('#editPartnerAddress').val(p.Address);
						$('#editPartnerPhone').val(p.Phone);
						$('#editPartnerFax').val(p.Fax);
					} else {
						toastr.error('Gagal mengambil data partner');
						$('#modalEditPartner').modal('hide');
					}
				},
				error: function(xhr, status, error) {
					toastr.error('Terjadi kesalahan: ' + error);
					$('#modalEditPartner').modal('hide');
				}
			});
		});

		// Update Partner
		$('#btnUpdatePartner').on('click', function() {
			var partnerId = $('#editPartnerId').val();
			$.ajax({
				url: '<?= base_url('api/eksemplar/update_partner') ?>/' + partnerId,
				type: 'POST',
				data: $('#formEditPartner').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						toastr.success('Data berhasil diperbarui');
						$('#modalEditPartner').modal('hide');
						// Reload options Partner_id
						$.getJSON('<?= base_url('api/eksemplar/collectionpartners') ?>', function(data) {
							var opts = '<option value="">-Pilih-</option>';
							$.each(data.data, function(i, row) {
								opts += `<option value="${row.ID}" ${row.ID == partnerId ? 'selected' : ''}>${row.Name}</option>`;
							});
							$('#Partner_id').html(opts).trigger('change');
						});
					} else {
						toastr.error(response.message || 'Terjadi kesalahan saat memperbarui data');
					}
				},
				error: function(xhr, status, error) {
					toastr.error('Terjadi kesalahan: ' + error);
				}
			});
		});
	});
</script>

<?= $this->include('Eksemplar\Views\modal_katalog'); ?>
<?= $this->endSection('script'); ?>