<?php
$db = db_connect();
$request = service('request');
$slug = $request->getGet('slug');
$select2 = select_two();
$nomor_manual = get_parameter('nomor-mode', 'manual') == 'manual';
$branch_id = user()->branch_id ?? $request->getGet('branch_id');
$return_catalog = $request->getGet('catalog_id') ?? '';
$catalog_id = $request->getGet('catalog_id') ?? '';
$worksheet_id = $db->table('catalogs')->where('ID', $catalog_id)->get()->getRow()->Worksheet_id ?? '';

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
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a>
                            </li>
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
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a>
                            </li>
                            <li class="breadcrumb-item"><a href="<?= base_url('eksemplar') ?>">Eksemplar</a></li>
                            <li class="breadcrumb-item">Tambah Eksemplar</li>
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
                    <a data-bs-toggle="modal" data-bs-target="#modal_katalog" data-toggle="modal" data-target="#modal_katalog" href="javascript:void(0);"
                        class="btn btn-success" title="Daftar Katalog">
                        <i class="fa fa-check-square"></i> Pilih Judul Katalog
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-body">
            <div id="infoMessage"><?= $message ?? ''; ?></div>
            <?= get_message('message'); ?>

            <!-- Card Informasi Katalog -->
            <div class="card card-shadow mb-4">
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-2 col-md-6">
                                <label>Is Drm*</label>
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
                                <label>No. Panggil</label>
                                <input type="text" class="form-control" name="CallNumber" id="CallNumber"
                                    placeholder="" value="<?= $catalog->CallNumber ?? '' ?>" readonly />
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label>ID Katalog</label>
                                <input type="text" class="form-control" id="Catalog_id2"
                                    placeholder="" value="<?= $catalog->ID ?? '' ?>" readonly />
                            </div>
                            <div class="col-lg-8 col-md-12">
                                <label>Judul Katalog</label>
                                <input type="text" class="form-control" id="Title"
                                    placeholder="" value="<?= $catalog->Title ?? '' ?>" readonly />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Jumlah & Input Eksemplar -->
            <div class="card card-shadow mb-4">
                <div class="card-body">
                    <div class="form-group">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="JumlahEksemplar">Jumlah Eksemplar</label>
                                <input type="number" class="form-control" name="JumlahEksemplar"
                                    id="JumlahEksemplar" min="1" placeholder="1"
                                    value="<?= set_value('JumlahEksemplar', '1') ?>" />
                            </div>
                        </div>

                        <!-- Container input eksemplar (manual & otomatis) -->
                        <div class="row" id="eksemplar-container"></div>
                    </div>
                </div>
            </div>

            <!-- Card Detail Pengadaan -->
            <div class="card card-shadow mb-4">
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 mt-3">
                                <label for="TanggalPengadaan">Tanggal Pengadaan *</label>
                                <input type="date"
                                    class="form-control <?= (isset($validation) && $validation->getError('TanggalPengadaan')) ? 'is-invalid' : '' ?>"
                                    name="TanggalPengadaan" id="TanggalPengadaan"
                                    value="<?= set_value('TanggalPengadaan') ?>" />
                                <?php if (isset($validation) && $validation->getError('TanggalPengadaan')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('TanggalPengadaan') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-lg-4 col-md-6 mt-3">
                                <label for="Source_id">Jenis Sumber *</label>
                                <div class="select-wrapper">
                                    <select class="form-control <?= (isset($validation) && $validation->getError('Source_id')) ? 'is-invalid' : '' ?>"
                                        name="Source_id" id="Source_id" style="width:100%">
                                        <option value="">-Pilih-</option>
                                        <?php foreach (get_ref_table('collectionsources', 'ID, Code, Name', null, 'data') as $row) : ?>
                                            <option value="<?= $row->ID ?>" <?= set_value('Source_id') == $row->ID ? 'selected' : '' ?>>
                                                <?= $row->Code ?> <?= $row->Name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($validation) && $validation->getError('Source_id')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('Source_id') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 mt-3">
                                <label for="Partner_id">Nama Sumber *</label>
                                <div class="select-wrapper input-group">
                                    <select class="form-control <?= (isset($validation) && $validation->getError('Partner_id')) ? 'is-invalid' : '' ?>"
                                        name="Partner_id" id="Partner_id" style="width:80%">
                                        <option value="">-Pilih-</option>
                                        <?php foreach (get_ref_table('partners', 'ID, Name', null, 'data') as $row) : ?>
                                            <option value="<?= $row->ID ?>" <?= set_value('Partner_id') == $row->ID ? 'selected' : '' ?>>
                                                <?= $row->Name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary"
                                            data-toggle="modal" data-target="#modalAddPartner">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                        <button type="button" id="btnEditPartner" class="btn btn-warning"
                                            data-toggle="modal" data-target="#modalEditPartner" disabled>
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </div>
                                    <?php if (isset($validation) && $validation->getError('Partner_id')): ?>
                                        <div class="invalid-feedback d-block"><?= $validation->getError('Partner_id') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 mt-3">
                                <label for="Media_id">Bentuk Fisik *</label>
                                <div class="select-wrapper">
                                    <select class="form-control <?= (isset($validation) && $validation->getError('Media_id')) ? 'is-invalid' : '' ?>"
                                        name="Media_id" id="Media_id" style="width:100%">
                                        <option value="">-Pilih-</option>
                                        <?php foreach (get_ref_table('collectionmedias', 'ID, Code, Name', null, 'data') as $row) : ?>
                                            <option value="<?= $row->ID ?>" <?= set_value('Media_id') == $row->ID ? 'selected' : '' ?>>
                                                <?= $row->Code ?> <?= $row->Name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($validation) && $validation->getError('Media_id')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('Media_id') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 mt-3">
                                <label for="Category_id">Kategori *</label>
                                <div class="select-wrapper">
                                    <select class="form-control <?= (isset($validation) && $validation->getError('Category_id')) ? 'is-invalid' : '' ?>"
                                        name="Category_id" id="Category_id" style="width:100%">
                                        <option value="">-Pilih-</option>
                                        <?php foreach (get_ref_table('collectioncategorys', 'ID, Code, Name', null, 'data') as $row) : ?>
                                            <option value="<?= $row->ID ?>" <?= set_value('Category_id') == $row->ID ? 'selected' : '' ?>>
                                                <?= $row->Code ?> <?= $row->Name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($validation) && $validation->getError('Category_id')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('Category_id') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 mt-3">
                                <label for="Rule_id">Akses</label>
                                <div class="select-wrapper">
                                    <select class="form-control" name="Rule_id" id="Rule_id" style="width:100%">
                                        <option value="">-Pilih-</option>
                                        <?php foreach (get_ref_table('collectionrules', 'ID, Name', null, 'data') as $row) : ?>
                                            <option value="<?= $row->ID ?>" <?= set_value('Rule_id') == $row->ID ? 'selected' : '' ?>>
                                                <?= $row->Name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 mt-3">
                                <label for="Location_Library_id">Lokasi Perpustakaan</label>
                                <div class="select-wrapper">
                                    <select class="form-control selectx"
                                        name="Location_Library_id" id="Location_Library_id"
                                        tabindex="-1" aria-hidden="true" style="width:100%"
                                        data-selected="<?= set_value('Location_Library_id') ?>">
                                        <option value="">-Pilih-</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 mt-3">
                                <label for="Location_id">Lokasi Ruang</label>
                                <div class="select-wrapper">
                                    <select class="form-control selectx"
                                        name="Location_id" id="Location_id"
                                        tabindex="-1" aria-hidden="true" style="width:100%"
                                        data-selected="<?= set_value('Location_id') ?>">
                                        <option value="">-Pilih-</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 mt-3">
                                <label for="Status_id">Ketersediaan</label>
                                <div class="select-wrapper">
                                    <select class="form-control" name="Status_id" id="Status_id" style="width:100%">
                                        <option value="">-Pilih-</option>
                                        <?php foreach (get_ref_table('collectionstatus', 'ID,Name', null, 'data') as $row) : ?>
                                            <option value="<?= $row->ID ?>" <?= set_value('Status_id') == $row->ID ? 'selected' : '' ?>>
                                                <?= $row->Name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 mt-3">
                                <label for="Currency_id">Mata Uang</label>
                                <div class="select-wrapper">
                                    <select class="form-control" name="Currency" id="Currency" style="width:100%">
                                        <option value="">-Pilih-</option>
                                        <?php foreach (get_ref_table('currency', 'Currency, Description', null, 'data') as $row) : ?>
                                            <option value="<?= $row->Currency ?>" <?= set_value('Currency') == $row->Currency ? 'selected' : '' ?>>
                                                <?= $row->Currency ?> - <?= $row->Description ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 mt-3">
                                <label for="Price">Harga</label>
                                <input type="text" class="form-control" name="Price" id="Price"
                                    placeholder="" value="<?= set_value('Price') ?>" />
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
                                            <option value="<?= esc($row->code) ?>" <?= set_value('PriceType') == $row->code ? 'selected' : '' ?>>
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
                <input type="checkbox" class="apply-status" name="ISOPAC"
                    data-toggle="toggle" data-onstyle="success"
                    data-on="Ya" data-off="Tdk" data-size="normal">
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

                        <button type="submit" class="btn btn-primary btn-lg mr-2" name="submit">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php if (!empty($return_catalog)) : ?>
        <a href="<?= base_url('katalog/edit/' . $catalog_id . '?slug=eksemplar') ?>"
            class="btn btn-secondary btn-lg mb-3">
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
    // Inisialisasi select2 dengan AJAX
    getData(`<?= base_url('api/eksemplar/locationlibrary') ?>`, `#Location_Library_id`, false, `-Pilih-`);

    // Lokasi Ruang berdasarkan Lokasi Perpustakaan
    $('#Location_Library_id').change(function() {
        var Location_Library_id = $("#Location_Library_id option:selected").val();
        getData(`<?= base_url('api/eksemplar/locations') ?>/${Location_Library_id}`, `#Location_id`, false, `-Pilih-`);
    });
</script>

<script>
    $(document).ready(function() {

        // =====================================================
        // Ambil mode dari PHP
        // =====================================================
        var isManual = <?= $NomorInduk == 'True' ? 'true' : 'false'; ?>;

        // =====================================================
        // Fungsi render input MANUAL
        // =====================================================
        function renderManualInputs(jumlah) {
            var html = '';
            for (var i = 0; i < jumlah; i++) {
                html += `
                    <div class="col-md-12 mb-2" id="eksemplar-row-${i}">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text fw-bold">Eksemplar ${i + 1}</span>
                            </div>
                            <div class="input-group-prepend">
                                <span class="input-group-text">No. Induk</span>
                            </div>
                            <input type="text" class="form-control"
                                name="NoInduk${i}" id="NoInduk_${i}"
                                placeholder="No. Induk" value="" />
                            <div class="input-group-prepend">
                                <span class="input-group-text">No. Barcode</span>
                            </div>
                            <input type="text" class="form-control"
                                name="NomorBarcode${i}" id="NomorBarcode_${i}"
                                placeholder="No. Barcode" value="" />
                            <div class="input-group-prepend">
                                <span class="input-group-text">No. RFID</span>
                            </div>
                            <input type="text" class="form-control"
                                name="RFID${i}" id="RFID_${i}"
                                placeholder="No. RFID" value="" />
                        </div>
                    </div>
                `;
            }
            $('#eksemplar-container').html(html);
        }

        // =====================================================
        // Fungsi render input OTOMATIS (generate dari API)
        // =====================================================
        function renderOtomatisInputs(jumlah) {
            $.getJSON(`<?= base_url('api/eksemplar/eksemplar_number') ?>/${jumlah}`, function(data) {
                var html = '';
                $.each(data.data, function(idx, val) {
                    html += `
                        <div class="col-md-12 mb-2" id="eksemplar-row-${idx}">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text fw-bold">Eksemplar ${idx + 1}</span>
                                </div>
                                <div class="input-group-prepend">
                                    <span class="input-group-text">No. Induk</span>
                                </div>
                                <input type="text" class="form-control"
                                    name="NoInduk${idx}" id="NoInduk_${idx}"
                                    placeholder="No. Induk" value="${val.NoInduk}" readonly />
                                <div class="input-group-prepend">
                                    <span class="input-group-text">No. Barcode</span>
                                </div>
                                <input type="text" class="form-control"
                                    name="NomorBarcode${idx}" id="NomorBarcode_${idx}"
                                    placeholder="No. Barcode" value="${val.NomorBarcode}" readonly />
                                <div class="input-group-prepend">
                                    <span class="input-group-text">No. RFID</span>
                                </div>
                                <input type="text" class="form-control"
                                    name="RFID${idx}" id="RFID_${idx}"
                                    placeholder="No. RFID" value="${val.RFID}" readonly />
                            </div>
                        </div>
                    `;
                });
                $('#eksemplar-container').html(html);
            });
        }

        // =====================================================
        // Inisialisasi awal saat halaman load
        // =====================================================
        var initialJumlah = parseInt($('#JumlahEksemplar').val()) || 1;
        if (isManual) {
            renderManualInputs(initialJumlah);
        }
        // Jika otomatis, biarkan kosong dulu —
        // user bisa trigger sendiri atau bisa uncomment baris berikut:
        // else { renderOtomatisInputs(initialJumlah); }

        // =====================================================
        // Event: JumlahEksemplar berubah
        // =====================================================
        $('#JumlahEksemplar').on('input change', function() {
            var jumlah = parseInt($(this).val()) || 0;
            if (jumlah < 1) {
                $('#eksemplar-container').html('');
                return;
            }
            if (isManual) {
                renderManualInputs(jumlah);
            }
            // Jika otomatis tidak perlu render di sini
            // karena controller yang generate via generateNomorBarcode()
        });

        // =====================================================
        // Partner: enable tombol edit jika ada pilihan
        // =====================================================
        $('#Partner_id').on('change', function() {
            $('#btnEditPartner').prop('disabled', !$(this).val());
        });

        // =====================================================
        // Add Partner
        // =====================================================
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
                        getData(`<?= base_url('api/eksemplar/collectionpartners') ?>`, `#Partner_id`, false, `-Pilih-`);
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

        // =====================================================
        // Load data Edit Partner
        // =====================================================
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

        // =====================================================
        // Update Partner
        // =====================================================
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
                        getData(`<?= base_url('api/eksemplar/collectionpartners') ?>`, `#Partner_id`, false, `-Pilih-`);
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
    $(document).ready(function() {
        <?php if (session()->getFlashdata('swal_icon')) : ?>
            Swal.fire({
                icon: '<?= session()->getFlashdata('swal_icon') ?>',
                title: '<?= session()->getFlashdata('swal_title') ?>',
                html: '<?= session()->getFlashdata('swal_html') ?? session()->getFlashdata('swal_text') ?>',
                showConfirmButton: true,
            });
        <?php endif; ?>
    });
</script>

<?= $this->endSection('script'); ?>