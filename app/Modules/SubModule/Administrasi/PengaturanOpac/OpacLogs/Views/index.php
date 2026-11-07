<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ina-skin.css') . '?v=' . filemtime(FCPATH . 'assets/css/ina-skin.css') ?>">
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon"><i class="pe-7s-server icon-gradient bg-strong-bliss"></i></div>
                <div>Log Pencarian OPAC
                    <div class="page-title-subheading">Atur pencatatan aktivitas pencarian pada katalog daring.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
                        <li class="breadcrumb-item">Administrasi</li>
                        <li class="breadcrumb-item">Pengaturan Opac</li>
                        <li class="breadcrumb-item">Log Pencarian OPAC</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <?= get_message('message') ?>
    <div class="row">
        <div class="col-md-6">
            <div class="main-card mb-3 card">
                <div class="card-header"><i class="header-icon lnr-pencil icon-gradient bg-plum-plate"></i> Pengaturan Log Pencarian</div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('master-opac-logs/index') ?>">
                        <?= csrf_field() ?>
                        <fieldset class="position-relative form-group">
                            <legend class="col-form-label">Catat pencarian OPAC *</legend>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="opac_log" id="opac_log_true" value="TRUE" <?= $opac_log === 'TRUE' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="opac_log_true">Ya</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="opac_log" id="opac_log_false" value="FALSE" <?= $opac_log !== 'TRUE' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="opac_log_false">Tidak</label>
                            </div>
                        </fieldset>
                        <p class="text-muted mb-3">Jika diaktifkan, setiap pencarian di OPAC akan dicatat dan dapat ditinjau melalui menu Histori Pencarian.</p>
                        <?php if (!$log_table_exists): ?>
                            <div class="alert alert-warning">Tabel histori pencarian belum tersedia. Jalankan migrasi database sebelum mengaktifkan pencatatan.</div>
                        <?php endif; ?>
                        <?php if (is_allowed('master-opac-logs/edit')): ?>
                            <button type="submit" class="btn btn-catalog-add">Simpan</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<?php if (session()->getFlashdata('opac_log_saved')): ?>
<script>
    Swal.fire({
        imageUrl: <?= json_encode(base_url('assets/img/data-berhasil-disimpan.png'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
        imageWidth: 240,
        imageHeight: 180,
        imageAlt: 'Ilustrasi data berhasil disimpan',
        title: 'Berhasil',
        text: 'Pengaturan log pencarian OPAC berhasil disimpan.',
        confirmButtonText: 'Tutup'
    });
</script>
<?php endif; ?>
<?= $this->endSection('script') ?>
