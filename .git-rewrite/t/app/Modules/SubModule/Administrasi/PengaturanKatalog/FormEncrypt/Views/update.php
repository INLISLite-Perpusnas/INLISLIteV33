<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style') ?>
<style>
.select2 {
    text-transform: none;
    font-weight: normal;
}
</style>
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-server icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Form Encrypt
                    <div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i
                                    class="fa fa-home"></i></a></li>
                        <li class="breadcrumb-item">Administrasi</li>
                        <li class="breadcrumb-item">Pengaturan Katalog</li>
                        <li class="breadcrumb-item active">Form Encrypt</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card col-md-6">
        <div class="card-header">
            <i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Form Encrypt
        </div>
        <div class="card-body">
            <div id="infoMessage"><?= $message ?? '' ?></div> <?= get_message('message') ?>
            <form id="frm_create" method="post" action="<?= base_url('master-form-encrypt/index') ?>"
                onsubmit="return validateForm()">
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="alias">Form Encrypt*</label>
                            <div>
                                <?php $parameter1 = get_setting_parameter('FormAlgorithm', is_profiling()); ?>
                                <?php $parameter2 = get_setting_parameter('FormKey', is_profiling()); ?>
                                <?php $parameter3 = get_setting_parameter('FormIV', is_profiling()); ?>
                                <div class="col-md-12">
                                    <label for="parameter1">Form Algorithm</label>
                                    <select name="parameter1" class="form-control" id="parameter1">
                                        <option value="AES-256-CBC"
                                            <?= ($parameter1 == 'AES-256-CBC') ? 'selected' : '' ?>>AES-256-CBC</option>
                                        <option value="AES-256-CBC"
                                            <?= ($parameter1 == 'AES-256-CBC') ? 'selected' : '' ?>>AES-128-CBC</option>
                                        <option value="AES-256-CBC"
                                            <?= ($parameter1 == 'AES-256-CBC') ? 'selected' : '' ?>>ECC</option>
                                        <option value="AES-256-CBC"
                                            <?= ($parameter1 == 'AES-256-CBC') ? 'selected' : '' ?>>ElGamal</option>
                                        <option value="AES-256-CBC"
                                            <?= ($parameter1 == 'AES-256-CBC') ? 'selected' : '' ?>>Blowfish</option>
                                        <option value="AES-256-CBC"
                                            <?= ($parameter1 == 'AES-256-CBC') ? 'selected' : '' ?>>Twofish</option>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label for="parameter1">Form Key</label>
                                    <input type="text" name="parameter2" class="form-control" id="parameter2"
                                        value="<?= $parameter2 ?>">
                                </div>
                                <div class="col-md-12">
                                    <label for="parameter1">Form IV</label>
                                    <input type="text" name="parameter3" class="form-control" id="parameter2"
                                        value="<?= $parameter2 ?>">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<?= $this->endSection('script') ?>