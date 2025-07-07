<?php
$db=db_connect('data');
$request = service('request');
$slug = $request->getGet('slug');
$select2 = select_two();
$nomor_manual = get_parameter('nomor-mode', 'manual') == 'manual';
$branch_id = user()->branch_id ?? $request->getGet('branch_id');
$return_catalog = $request->getGet('catalog_id') ?? '';
$catalog_id = $request->getGet('catalog_id') ?? '';
$worksheet_id=$db->table('catalogs')->where('ID', $catalog_id)->get()->getRow()->Worksheet_id ?? '';

$catalog = get_catalog($catalog_id);
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
    <?php if (!empty($catalog_id)) : ?>
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
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i>
                                Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('page') ?>">Katalog</a></li>
                        <li class="breadcrumb-item">Edit</li>
                        <li class="active breadcrumb-item" aria-current="page">Eksemplar</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <?php else : ?>
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
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i>
                                Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('eksemplar') ?>">Eksemplar</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Tambah Eksemplar</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <form id="frm_create" class="main-card mb-3 card" method="post"
        action="<?= base_url('eksemplar/create?slug=' . $slug); ?>">
        <div class="card-header">
            <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Tambah Eksemplar
            <div class="btn-actions-pane-right actions-icon-btn">
                <?php if (is_allowed('eksemplar/create')) : ?>
                <a data-toggle="modal" data-target="#modal_katalog" href="javascript:void(0);" class="btn btn-success"
                    title="Daftar Katalog"><i class="fa fa-check-square"></i> Pilih Judul Katalog</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-body">
            <div id="infoMessage"><?= $message ?? ''; ?></div>
            <?= get_message('message'); ?>

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
                                            <?= isset($catalog->ISDRM) && $catalog->ISDRM == 1 ? 'checked' : '' ?> />
                                        <label class="form-check-label" for="is_drm_1">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" name="ISDRM" id="is_drm_0"
                                            value="0"
                                            <?= isset($catalog->ISDRM) && $catalog->ISDRM == 0 ? 'checked' : '' ?> />
                                        <label class="form-check-label" for="is_drm_0">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-lg-2 col-md-6">
                                <label for="groups">No. Panggil</label>
                                <div>
                                    <input type="text" class="form-control" name="CallNumber" id="CallNumber"
                                        placeholder="" value="<?= $catalog->CallNumber ?? '' ?>" readonly />
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label for="groups">ID Katalog</label>
                                <div>
                                    <input type="text" class="form-control" id="Catalog_id2" placeholder=""
                                        value="<?= $catalog->ID ?? '' ?>" readonly />
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-12">
                                <label for="groups">Judul Katalog</label>
                                <div>
                                    <input type="text" class="form-control" id="Title" placeholder=""
                                        value="<?= $catalog->Title ?? '' ?>" readonly />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-shadow mb-4">
                <div class="card-body">
                    <div class="form-group">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="varchar">Jumlah Eksemplar</label>
                                <div class="form input-group" title="Eksemplar">
                                    <input type="text" class="form-control" name="JumlahEksemplar" id="JumlahEksemplar"
                                        placeholder="" value='<?= set_value('JumlahEksemplar', '1') ?>' />
                                  
                                </div>
                            </div>
                        </div>
                        <div class="row" id="eksemplar-container">

                        </div>
                    </div>

                    <?php if ($nomor_manual) : ?>
                    <div class="form-group" id="eksemplar">
                        <label for="varchar">Eksemplar</label>
                        <div>
                            <div class="form input-group" title="Eksemplar">
                                <div class="input-group-prepend"><span class="input-group-text">No. Induk: </span></div>
                                <input type="text" class="form-control" name="NoInduk0" id="NoInduk_0" placeholder=""
                                    value="" />
                                <div class="input-group-prepend">
                                    <span class="input-group-text">No. Barcode: </span>
                                </div>
                                <input type="text" class="form-control" name="NomorBarcode0" id="NomorBarcode_0"
                                    placeholder="" value="" />
                                <div class="input-group-prepend">
                                    <span class="input-group-text">No. RFID: </span>
                                </div>
                                <input type="text" class="form-control" name="RFID0" id="RFID_0" placeholder=""
                                    value="" />
                                <div class="input-group-append">
                                    <span class="add-eksemplar btn btn-outline-secondary"><i
                                            class="fa fa-plus"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card card-shadow mb-4">
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <label for="TanggalPengadaan">Tanggal Pengadaan *</label>
                                <div>
                                    <input type="date" 
                                           class="form-control <?= (isset($validation) && $validation->getError('TanggalPengadaan')) ? 'is-invalid' : '' ?>" 
                                           name="TanggalPengadaan"
                                           id="TanggalPengadaan" 
                                           placeholder="" 
                                           value="<?= set_value('TanggalPengadaan') ?>" />
                                    <?php if (isset($validation) && $validation->getError('TanggalPengadaan')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('TanggalPengadaan') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label for="groups">Jenis Sumber *</label>
                                <div class="select-wrapper">
                                    <select class="form-control selectx <?= (isset($validation) && $validation->getError('Source_id')) ? 'is-invalid' : '' ?>" 
                                            name="Source_id" 
                                            id="Source_id" 
                                            tabindex="-1"
                                            aria-hidden="true" 
                                            style="width:100%"
                                            data-selected="<?= set_value('Source_id') ?>">
                                    </select>
                                    <?php if (isset($validation) && $validation->getError('Source_id')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('Source_id') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label for="groups">Nama Sumber *</label>
                                <div class="select-wrapper input-group">
                                    <select class="form-control selectx <?= (isset($validation) && $validation->getError('Partner_id')) ? 'is-invalid' : '' ?>" 
                                            name="Partner_id" 
                                            id="Partner_id" 
                                            tabindex="-1"
                                            aria-hidden="true" 
                                            style="width:80%"
                                            data-selected="<?= set_value('Partner_id') ?>">
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalAddPartner">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                        <button type="button" id="btnEditPartner" class="btn btn-warning" data-toggle="modal" data-target="#modalEditPartner" disabled>
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </div>
                                    <?php if (isset($validation) && $validation->getError('Partner_id')): ?>
                                        <div class="invalid-feedback d-block"><?= $validation->getError('Partner_id') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label for="groups">Bentuk Fisik *</label>
                                <div class="select-wrapper">
                                    <select class="form-control selectx <?= (isset($validation) && $validation->getError('Media_id')) ? 'is-invalid' : '' ?>" 
                                            name="Media_id" 
                                            id="Media_id" 
                                            tabindex="-1"
                                            aria-hidden="true" 
                                            style="width:100%"
                                            data-selected="<?= set_value('Media_id') ?>">
                                    </select>
                                    <?php if (isset($validation) && $validation->getError('Media_id')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('Media_id') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label for="groups">Kategori *</label>
                                <div class="select-wrapper">
                                    <select class="form-control selectx <?= (isset($validation) && $validation->getError('Category_id')) ? 'is-invalid' : '' ?>" 
                                            name="Category_id" 
                                            id="Category_id"
                                            tabindex="-1" 
                                            aria-hidden="true" 
                                            style="width:100%"
                                            data-selected="<?= set_value('Category_id') ?>">
                                    </select>
                                    <?php if (isset($validation) && $validation->getError('Category_id')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('Category_id') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label for="groups">Akses</label>
                                <div class="select-wrapper">
                                    <select class="form-control selectx" 
                                            name="Rule_id" 
                                            id="Rule_id" 
                                            tabindex="-1"
                                            aria-hidden="true" 
                                            style="width:100%"
                                            data-selected="<?= set_value('Rule_id') ?>">
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label for="groups">Lokasi Perpustakaan</label>
                                <div class="select-wrapper">
                                    <select class="form-control selectx" 
                                            name="Location_Library_id"
                                            id="Location_Library_id" 
                                            tabindex="-1" 
                                            aria-hidden="true" 
                                            style="width:100%"
                                            data-selected="<?= set_value('Location_Library_id') ?>">
                                        <option value="">-Pilih-</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label for="groups">Lokasi Ruang</label>
                                <div class="select-wrapper">
                                    <select class="form-control selectx" 
                                            name="Location_id" 
                                            id="Location_id"
                                            tabindex="-1" 
                                            aria-hidden="true" 
                                            style="width:100%"
                                            data-selected="<?= set_value('Location_id') ?>">
                                        <option value="">-Pilih-</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label for="groups">Ketersediaan</label>
                                <div class="select-wrapper">
                                    <select class="form-control selectx" 
                                            name="Status_id" 
                                            id="Status_id" 
                                            tabindex="-1"
                                            aria-hidden="true" 
                                            style="width:100%"
                                            data-selected="<?= set_value('Status_id') ?>">
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label for="groups">Mata Uang</label>
                                <div class="select-wrapper">
                                    <select class="form-control selectx" 
                                            name="Currency_id" 
                                            id="Currency_id"
                                            tabindex="-1" 
                                            aria-hidden="true" 
                                            style="width:100%"
                                            data-selected="<?= set_value('Currency_id') ?>">
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label for="groups">Harga</label>
                                <div>
                                    <input type="text" 
                                           class="form-control" 
                                           name="Price" 
                                           id="Price" 
                                           placeholder=""
                                           value="<?= set_value('Price') ?>" />
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label for="groups">Satuan Harga</label>
                                <div class="select-wrapper">
                                    <select class="form-control selectx" 
                                            name="PriceType" 
                                            id="Price_type" 
                                            tabindex="-1"
                                            aria-hidden="true" 
                                            style="width:100%"
                                            data-selected="<?= set_value('PriceType') ?>">
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
                    <input type="checkbox" class="apply-status" name="ISOPAC" data-toggle="toggle"
                        data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal">
                </div>
            </div>

            <div class="card-footer p-0 pt-3">
                <div class="form-group">
                    <div class="form-check form-check-inline">
                        <input type="hidden" name="Branch_id" id="Branch_id" value="<?= $branch_id ?>">
                        <input type="hidden" name="Catalog_id" id="Catalog_id" value="<?= $catalog_id ?>">
                        <input type="hidden" name="worksheet_id" id="worksheet_id" value="<?= $worksheet_id ?>">

                        <input type="hidden" name="IsRedirect" value="1">

                        <?php if (!empty($return_catalog)) : ?>
                        <input type="hidden" name="redirect" id="redirect"
                            value="katalog/edit/<?= $catalog_id ?>?slug=eksemplar">
                        <?php else : ?>
                        <input type="hidden" name="redirect" id="redirect" value="eksemplar">
                        <?php endif; ?>

                        <button type="submit" class="btn btn-primary btn-lg mr-2" name="submit"><i
                                class="fa fa-save"></i> Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php if (!empty($return_catalog)) : ?>
    <a href="<?= base_url('katalog/edit/' . $catalog_id . '?slug=eksemplar') ?>"
        class="btn btn-secondary btn-lg mb-3"><i class="fa fa-chevron-left"></i> Kembali ke Katalog</a>
    <?php else : ?>
    <a href="<?= base_url('eksemplar') ?>" class="btn btn-secondary btn-lg mb-3"><i class="fa fa-list"></i> Kembali ke
        Daftar Eksemplar</a>
    <?php endif; ?>
</div>
<!-- modal -->
 


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
getData(`<?= base_url('api/eksemplar/collectionsources') ?>`, `#Source_id`, false, `-Pilih-`);
getData(`<?= base_url('api/eksemplar/collectionpartners') ?>`, `#Partner_id`, false, `-Pilih-`);
getData(`<?= base_url('api/eksemplar/collectionrules') ?>`, `#Rule_id`, false, `-Pilih-`);
getData(`<?= base_url('api/eksemplar/collectionmedias') ?>`, `#Media_id`, false, `-Pilih-`);
getData(`<?= base_url('api/eksemplar/collectioncategory') ?>`, `#Category_id`, false, `-Pilih-`);
getData(`<?= base_url('api/eksemplar/collectionstatus') ?>`, `#Status_id`, false, `-Pilih-`);
getData(`<?= base_url('api/eksemplar/locationlibrary') ?>`, `#Location_Library_id`, false, `-Pilih-`);
getData(`<?= base_url('api/eksemplar/collectioncurrency') ?>`, `#Currency_id`, false, `-Pilih-`);
getData(`<?= base_url('api/eksemplar/collectionpricetype') ?>`, `#Price_type`, false, `-Pilih-`);

$('#Location_Library_id').change(function(e) {
    var Location_Library_id = $("#Location_Library_id option:selected").val();
    getData(`<?= base_url('api/eksemplar/locations') ?>/${Location_Library_id}`, `#Location_id`, false,
        `-Pilih-`);
});

$('#generate-eksemplar-number').click(function() {
    var count = $("#JumlahEksemplar").val();
    var html = '';
    $.getJSON(`<?= base_url('api/eksemplar/eksemplar_number') ?>/${count}`, function(data) {
        var items = [];
        $.each(data.data, function(idx, val) {
            idx++;
            var NoInduk = val.NoInduk;
            var NomorBarcode = val.NomorBarcode;
            var RFID = val.RFID;

            html += `
					<div class="col-md-12">
						<div class="form input-group" title="Eksemplar">
							<div class="input-group-prepend"><span class="input-group-text">No. Induk: </span></div>
							<input type="text" class="form-control" name="NoInduk[${idx}]" placeholder="" value="${NoInduk}" />
							<div class="input-group-prepend">
								<span class="input-group-text">No. Barcode: </span>
							</div>
							<input type="text" class="form-control" name="NomorBarcode[${idx}]" placeholder="" value="${NomorBarcode}" />
							<div class="input-group-prepend">
								<span class="input-group-text">No. RFID: </span>
							</div>
							<input type="text" class="form-control" name="RFID[${idx}]" placeholder="" value="${RFID}" />
						</div>
					</div>
				`;
        });

        // Gunakan variabel html sesuai kebutuhan Anda
        console.log(html);
        $('#eksemplar-container').html(html);
    });
});
</script>
<script>
    // Handle Partner functionality
$(document).ready(function() {
    // Enable edit button when a partner is selected
    $('#Partner_id').on('change', function() {
        var selectedValue = $(this).val();
        if (selectedValue && selectedValue !== '') {
            $('#btnEditPartner').prop('disabled', false);
        } else {
            $('#btnEditPartner').prop('disabled', true);
        }
    });

    // Add new partner
    $('#btnSavePartner').on('click', function() {
        var formData = $('#formAddPartner').serialize();
        
        $.ajax({
            url: '<?= base_url('api/eksemplar/add_partner') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success message
                    toastr.success('Data berhasil disimpan');
                    
                    // Close the modal
                    $('#modalAddPartner').modal('hide');
                    
                    // Reset form
                    $('#formAddPartner')[0].reset();
                    
                    // Refresh partners dropdown
                    getData(`<?= base_url('api/eksemplar/collectionpartners') ?>`, `#Partner_id`, false, `-Pilih-`);
                    
                    // Select the newly added partner
                    setTimeout(function() {
                        $('#Partner_id').val(response.data.ID).trigger('change');
                    }, 500);
                } else {
                    toastr.error(response.message || 'Terjadi kesalahan saat menyimpan data');
                }
            },
            error: function(xhr, status, error) {
                toastr.error('Terjadi kesalahan: ' + error);
            }
        });
    });

    // Load partner data for editing
    $('#btnEditPartner').on('click', function() {
        var partnerId = $('#Partner_id').val();
        if (!partnerId) return;

        $.ajax({
            url: '<?= base_url('api/eksemplar/get_partner') ?>/' + partnerId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var partner = response.data;
                    $('#editPartnerId').val(partner.ID);
                    $('#editPartnerName').val(partner.Name);
                    $('#editPartnerAddress').val(partner.Address);
                    $('#editPartnerPhone').val(partner.Phone);
                    $('#editPartnerFax').val(partner.Fax);
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

    // Update partner data
    $('#btnUpdatePartner').on('click', function() {
        var formData = $('#formEditPartner').serialize();
        var partnerId = $('#editPartnerId').val();
        
        $.ajax({
            url: '<?= base_url('api/eksemplar/update_partner') ?>/' + partnerId,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success message
                    toastr.success('Data berhasil diperbarui');
                    
                    // Close the modal
                    $('#modalEditPartner').modal('hide');
                    
                    // Refresh partners dropdown
                    getData(`<?= base_url('api/eksemplar/collectionpartners') ?>`, `#Partner_id`, false, `-Pilih-`);
                    
                    // Keep the updated partner selected
                    setTimeout(function() {
                        $('#Partner_id').val(partnerId).trigger('change');
                    }, 500);
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
<?= $this->include('Eksemplar\Views\add_script'); ?>
<?= $this->include('Eksemplar\Views\modal_katalog'); ?>
<script>
</script>
<?= $this->endSection('script'); ?>