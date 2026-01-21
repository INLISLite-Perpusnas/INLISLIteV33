<?php
$request = service('request');
$slug = $request->getGet('slug');
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
        width: 100%; /* Banner biasanya lebar */
        max-width: 300px;
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
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>

<div class="app-main__inner">
  
    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Tambah Banner
        </div>
        <div class="card-body">
            <div id="infoMessage"><?= $message ?? ''; ?></div>
            <?= get_message('message'); ?>

            <form id="frm_create" class="col-md-12 mx-auto" method="post" enctype="multipart/form-data" action="<?= base_url('cms/banner/create?slug=' . $slug); ?>">
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="title">Judul Banner*</label>
                            <div>
                                <input type="text" class="form-control" name="title" id="title" placeholder="Judul Banner " value="<?= set_value('title'); ?>" required />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <label>Kategori*</label>
                            <select class="form-control" name="category" id="category" tabindex="-1" aria-hidden="true">
                                <?php foreach (get_ref('ref-banner', 'slug') as $row) : ?>
                                    <option value="<?= $row->name ?>" <?= (slugify($row->name) == $slug) ? 'selected' : '' ?>><?= $row->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Keterangan </label>
                    <div>
                        <textarea id="frm_create_description" name="description" placeholder="Keterangan " rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description') ?></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="position-relative form-group p-3 border rounded">
                            <label for="file_cover" class="font-weight-bold">Upload Banner (Wajib)</label>
                            
                            <input type="file" class="form-control-file" name="file_cover" id="file_cover" accept=".jpg,.jpeg,.png" onchange="previewFile(this, 'preview_cover_container')" required>
                            
                            <small class="form-text text-muted mt-2">
                                - Format: JPG, JPEG, PNG<br>
                                - Maksimal Ukuran: 2 MB
                            </small>

                            <div id="preview_cover_container" class="preview-container"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary" name="submit">Submit</button>
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
                        <img src="${e.target.result}" title="${input.files[0].name}">
                    </div>`;
                $(previewContainer).html(html);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
<?= $this->endSection('script'); ?>