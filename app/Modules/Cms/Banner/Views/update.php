<?php
$request = service('request');
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

    /* Style untuk Preview Image */
    .preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }
    .img-preview-box {
        position: relative;
        width: 100%;
        max-width: 400px; /* Banner biasanya lebar */
        border: 1px solid #ddd;
        padding: 5px;
        border-radius: 5px;
        overflow: hidden;
        background: #f9f9f9;
    }
    .img-preview-box img {
        width: 100%;
        height: auto;
        object-fit: cover;
    }
    
    .current-image-box {
        padding: 10px;
        background: #f1f4f6;
        border: 1px dashed #ccc;
        border-radius: 5px;
        margin-bottom: 10px;
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>

<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-photo icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Banner <?= $banner->category ?>
                    <div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Beranda</a></li>
                        <li class="breadcrumb-item">Banner </li>
                        <li class="active breadcrumb-item" aria-current="page">Ubah <?= $banner->category ?> </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Ubah Banner
        </div>
        <div class="card-body">
            <div id="infoMessage"><?= $message ?? ''; ?></div>
            <?= get_message('message'); ?>

            <form id="frm" class="col-md-12 mx-auto" method="post" enctype="multipart/form-data" action="">
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="name">Judul Banner*</label>
                            <div>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Judul Banner" value="<?= set_value('title', $banner->title); ?>" required />
                                <small class="info help-block text-muted">Permalink: <a href="<?= permalink('banner/' . slugify($banner->category) . '/' . $banner->slug) ?>" target="_blank"><?= permalink('banner/' . slugify($banner->category) . '/' . $banner->slug) ?></a></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <label>Kategori*</label>
                            <select class="form-control" name="category" id="category" tabindex="-1" aria-hidden="true">
                                <?php foreach (get_ref('ref-banner', 'slug') as $row) : ?>
                                    <option value="<?= $row->name ?>" <?= ($row->name == $banner->category) ? 'selected' : '' ?>><?= $row->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <label for="sort">Urutan</label>
                            <div>
                                <input type="number" class="form-control" name="sort" id="sort" placeholder="Urutan " value="<?= set_value('sort', $banner->sort); ?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Keterangan</label>
                    <div>
                        <textarea id="description" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description', $banner->description) ?></textarea>
                    </div>
                </div>

                <hr>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="position-relative form-group p-3 border rounded">
                            <label for="file_cover" class="font-weight-bold">File Banner</label>
                            
                            <?php if (!empty($banner->file_cover)): ?>
                                <div class="current-image-box">
                                    <p class="mb-1 text-muted"><small>Banner Saat Ini:</small></p>
                                    <img src="<?= base_url('uploads/banner/' . $banner->file_cover) ?>" style="max-width: 100%; height: auto; border-radius: 4px;" alt="Current Banner">
                                </div>
                            <?php endif; ?>

                            <div class="mt-2">
                                <label><small>Ganti Banner (Biarkan kosong jika tidak ingin mengubah):</small></label>
                                <input type="file" class="form-control-file" name="file_cover" id="file_cover" accept=".jpg,.jpeg,.png" onchange="previewFile(this, 'preview_cover_container')">
                                <small class="text-muted">Format (JPG|PNG). Max 2MB.</small>
                            </div>

                            <div id="preview_cover_container" class="preview-container"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary" name="submit">Simpan Perubahan</button>
                    <a href="<?= base_url('cms/banner') ?>" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
    // Fungsi Preview Gambar
    function previewFile(input, previewId) {
        var previewContainer = document.getElementById(previewId);
        previewContainer.innerHTML = ''; 

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var html = `
                    <div class="img-preview-box">
                        <div class="text-center text-success mb-1" style="font-size:10px;"><b>Akan Diupload</b></div>
                        <img src="${e.target.result}" title="${input.files[0].name}">
                    </div>`;
                $(previewContainer).html(html);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
<?= $this->endSection('script'); ?>