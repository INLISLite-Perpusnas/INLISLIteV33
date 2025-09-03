<?php
$request = service('request');
$slug = $request->getGet('slug');
$select2 = select_two();
$nomor_manual = get_parameter('nomor-mode', 'manual') == 'manual';
$return_catalog = $request->getGet('catalog_id') ?? '';
$catalog_id = $eksemplar->Catalog_id;
$catalog = get_catalog($catalog_id);
$tanggal_pengadaan = date('Y-m-d', strtotime($eksemplar->TanggalPengadaan));
// dd($eksemplar);
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
						<li class="active breadcrumb-item" aria-current="page">Ubah Eksemplar</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<form id="frm_edit" class="main-card mb-3 card" method="post" action="<?= base_url('eksemplar/edit/' . $eksemplar->ID); ?>">
		<div class="card-header">
			
		</div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? ''; ?></div>
			<?= get_message('message'); ?>
			<div class="card card-shadow mb-4">
				<div class="card-body">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2 col-md-6">
								<label for="groups">No. Panggil</label>
								<div>
									<input type="text" class="form-control" name="CallNumber" id="CallNumber" placeholder="" value="<?= $catalog->CallNumber ?>" readonly />
								</div>
							</div>
							<div class=" col-lg-2 col-md-6">
								<label for="groups">ID Katalog</label>
								<div>
									<input type="text" class="form-control" id="Catalog_id2" placeholder="" value="<?= $catalog->ID ?>" readonly />
								</div>
							</div>
							<div class="col-lg-8 col-md-12">
								<label for="groups">Judul Katalog</label>
								<div>
									<input type="text" class="form-control" id="Title" placeholder="" value="<?= $catalog->Title ?>" readonly />
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="card card-shadow mb-4">
				<div class="card-body">
					<div class="form-group">
							<div class="row">
                            <div class="col-lg-2 col-md-6">
                                <label for="is_drm">Is Drm*</label>
                                <div>
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" name="ISDRM" id="is_drm_1"
                                            value="1"
                                            <?= isset($eksemplar->ISDRM) && $eksemplar->ISDRM == 1 ? 'checked' : '' ?> />
                                        <label class="form-check-label" for="is_drm_1">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" name="ISDRM" id="is_drm_0"
                                            value="0"
                                            <?= isset($eksemplar->ISDRM) && $eksemplar->ISDRM == 0 ? 'checked' : '' ?> />
                                        <label class="form-check-label" for="is_drm_0">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
				    </div>
					<div class="form-group" id="eksemplar">
						<label for="varchar">Eksemplar</label>
						<div>
							<div class="form input-group" title="Eksemplar">
								<div class="input-group-prepend"><span class="input-group-text">No. Induk: </span></div>
								<input type="text" class="form-control" name="NoInduk0" id="NoInduk_0" placeholder="" value="<?= $eksemplar->NoInduk ?>" />
								<div class="input-group-prepend">
									<span class="input-group-text">No. Barcode: </span>
								</div>
								<input type="text" class="form-control" name="NomorBarcode0" id="NomorBarcode_0" placeholder="" value="<?= $eksemplar->NomorBarcode ?>" />
								<div class="input-group-prepend">
									<span class="input-group-text">No. RFID: </span>
								</div>
								<input type="text" class="form-control" name="RFID0" id="RFID_0" placeholder="" value="<?= $eksemplar->RFID ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="card card-shadow mb-4">
				<div class="card-body">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-4 col-md-6">
								<label for="TanggalPengadaan">Tanggal Pengadaan</label>
								<div>
									<input type="date" class="form-control" name="TanggalPengadaan" id="TanggalPengadaan" placeholder="" value="<?= $tanggal_pengadaan ?>" />
								</div>
							</div>
							<div class="col-lg-4 col-md-6">
								<label for="groups">Jenis Sumber</label>
								<div class="select-wrapper">
									<select class="form-control selectx" name="Source_id" id="Source_id" tabindex="-1" aria-hidden="true" style="width:100%">
									</select>
								</div>
							</div>
							<div class="col-lg-4 col-md-6">
								<label for="groups">Nama Sumber</label>
								<div class="select-wrapper">
									<select class="form-control selectx" name="Partner_id" id="Partner_id" tabindex="-1" aria-hidden="true" style="width:100%">
									</select>
								</div>
							</div>
							<div class="col-lg-4 col-md-6">
								<label for="groups">Bentuk Fisik</label>
								<div class="select-wrapper">
									<select class="form-control selectx" name="Media_id" id="Media_id" tabindex="-1" aria-hidden="true" style="width:100%">
									</select>
								</div>
							</div>
							<div class="col-lg-4 col-md-6">
								<label for="groups">Kategori</label>
								<div class="select-wrapper">
									<select class="form-control selectx" name="Category_id" id="Category_id" tabindex="-1" aria-hidden="true" style="width:100%">
									</select>
								</div>
							</div>
							<div class="col-lg-4 col-md-6">
								<label for="groups">Akses</label>
								<div class="select-wrapper">
									<select class="form-control selectx" name="Rule_id" id="Rule_id" tabindex="-1" aria-hidden="true" style="width:100%">
									</select>
								</div>
							</div>
							<div class="col-lg-4 col-md-6">
								<label for="groups">Lokasi Perpustakaan</label>
								<div class="select-wrapper">
									<select class="form-control selectx" name="Location_Library_id" id="Location_Library_id" tabindex="-1" aria-hidden="true" style="width:100%">
										<option value="">-Pilih-</option>
									</select>
								</div>
							</div>
							<div class="col-lg-4 col-md-6">
								<label for="groups">Lokasi Ruang</label>
								<div class="select-wrapper">
									<select class="form-control selectx" name="Location_id" id="Location_id" tabindex="-1" aria-hidden="true" style="width:100%">
										<option value="">-Pilih-</option>
									</select>
								</div>
							</div>
							<div class="col-lg-4 col-md-6">
								<label for="groups">Ketersediaan</label>
								<div class="select-wrapper">
									<select class="form-control selectx" name="Status_id" id="Status_id" tabindex="-1" aria-hidden="true" style="width:100%">
									</select>
								</div>
							</div>
							<div class="col-lg-4 col-md-6">
								<label for="groups">Mata Uang</label>
								<div class="select-wrapper">
									<select class="form-control selectx" name="Currency_id" id="Currency_id" tabindex="-1" aria-hidden="true" style="width:100%">
									</select>
								</div>
							</div>
							<div class="col-lg-4 col-md-6">
								<label for="groups">Harga</label>
								<div>
									<input type="text" class="form-control" name="Price" id="Price" placeholder="" value="<?= $eksemplar->Price ?>" />
								</div>
							</div>
							<div class="col-lg-4 col-md-6">
								<label for="groups">Satuan Harga</label>
								<div class="select-wrapper">
									<select class="form-control selectx" name="PriceType" id="Price_type" tabindex="-1" aria-hidden="true" style="width:100%">
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<div>
					<label for="ISOPAC">Tampil di OPAC </label><br>
					<input type="checkbox" class="apply-status" name="ISOPAC" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal" <?= $eksemplar->ISOPAC == 1 ? 'checked' : '' ?>>
				</div>
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


							<button type="submit" class="btn btn-primary btn-lg mr-2 ml-2" name="submit"><i class="fa fa-save"></i> Simpan</button>
						</div>
				
				</div>
			</div>
		</div>
	</form>

	<?php if (!empty($return_catalog)) : ?>
		<a href="<?= base_url('katalog/edit/' . $catalog_id . '?slug=eksemplar') ?>" class="btn btn-secondary btn-lg mb-3"><i class="fa fa-chevron-left"></i> Kembali ke Katalog</a>
	<?php else : ?>
		<a href="<?= base_url('eksemplar') ?>" class="btn btn-secondary btn-lg mb-3"><i class="fa fa-list"></i> Kembali ke Daftar Eksemplar</a>
	<?php endif; ?>
</div>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
	getData(`<?= base_url('api/eksemplar/collectionsources') ?>`, `#Source_id`, `<?= $eksemplar->Source_id ?>`, `-Pilih-`);
	getData(`<?= base_url('api/eksemplar/collectionpartners') ?>`, `#Partner_id`, `<?= $eksemplar->Partner_id ?>`, `-Pilih-`);
	getData(`<?= base_url('api/eksemplar/collectionrules') ?>`, `#Rule_id`, `<?= $eksemplar->Rule_id ?>`, `-Pilih-`);
	getData(`<?= base_url('api/eksemplar/collectionmedias') ?>`, `#Media_id`, `<?= $eksemplar->Media_id ?>`, `-Pilih-`);
	getData(`<?= base_url('api/eksemplar/collectioncategory') ?>`, `#Category_id`, `<?= $eksemplar->Category_id ?>`, `-Pilih-`);
	getData(`<?= base_url('api/eksemplar/collectionstatus') ?>`, `#Status_id`, `<?= $eksemplar->Status_id ?>`, `-Pilih-`);
	getData(`<?= base_url('api/eksemplar/locationlibrary') ?>`, `#Location_Library_id`, `<?= $eksemplar->Location_Library_id ?>`, `-Pilih-`);
	getData(`<?= base_url('api/eksemplar/locations/' . $eksemplar->Location_Library_id) ?>`, `#Location_id`, <?= $eksemplar->Location_id ?>, `-Pilih-`);
	getData(`<?= base_url('api/eksemplar/collectioncurrency') ?>`, `#Currency_id`, `<?= $eksemplar->Currency ?>`, `-Pilih-`);
	getData(`<?= base_url('api/eksemplar/collectionpricetype') ?>`, `#Price_type`, `<?= $eksemplar->PriceType ?>`, `-Pilih-`);
	$('#Location_Library_id').change(function(e) {
		var Location_Library_id = $("#Location_Library_id option:selected").val();
		getData(`<?= base_url('api/eksemplar/locations') ?>/${Location_Library_id}`, `#Location_id`, <?= $eksemplar->Location_id ?>, `-Pilih-`);
	});
</script>
<?= $this->include('Eksemplar\Views\modal_katalog'); ?>
<script>
</script>
<?= $this->endSection('script'); ?>