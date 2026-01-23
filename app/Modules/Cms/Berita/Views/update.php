<?php
$request = service('request');
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style') ?>
<style>
    /* .tox.tox-tinymce.tox-fullscreen {
        z-index: 1050;
        top: 60px !important;
        left: 85px !important;
        width: calc(100% - 85px) !important;
<<<<<<< HEAD
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
        width: 120px;
        height: 120px;
        border: 1px solid #ddd;
        padding: 5px;
        border-radius: 5px;
        overflow: hidden;
        background: #f9f9f9;
    }

    .img-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Style untuk Existing Image (Foto Lama) */
    .existing-file-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 15px;
        padding: 10px;
        background: #f1f4f6;
        border-radius: 5px;
        border: 1px dashed #ccc;
    }

    .existing-item {
        position: relative;
        width: 130px;
        text-align: center;
    }

    .existing-item img {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #ddd;
    }
    
    .remove-checkbox {
        margin-top: 5px;
        font-size: 12px;
        color: #d92550;
        font-weight: bold;
        display: block;
    }
=======
    } */
>>>>>>> 768fa1327effd041bd29d938a21825fda142d99e
</style>
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>

<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-photo icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Berita <?= $berita->category ?>
                    <div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Beranda</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('cms/berita') ?>"><?= lang('Berita') ?></a></li>
                        <li class="active breadcrumb-item" aria-current="page">Ubah <?= $berita->category ?> </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Ubah Berita
        </div>
        <div class="card-body">
            <div id="infoMessage"><?= $message ?? '' ?></div>
            <?= get_message('message') ?>

            <form id="frm" class="col-md-12 mx-auto" method="post" enctype="multipart/form-data" action="">
                
                <div class="form-row">
                    <div class="col-md-8">
                        <div class="position-relative form-group">
                            <label for="title">Judul Berita*</label>
                            <div>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Judul Berita" value="<?= set_value('title', $berita->title) ?>" />
                                <small class="info help-block text-muted">Permalink: <a href="<?= permalink('berita/' . $berita->slug) ?>" target="_blank"><?= permalink('berita/' . $berita->slug) ?></a></small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <label for="sort">Urutan</label>
                            <div>
                                <input type="number" min="1" step="1" class="form-control" name="sort" id="sort" placeholder="Urutan " oninput="this.value = this.value.replace(/[^0-9]/g, '');" value="<?= set_value('sort', $berita->sort) ?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="content">Uraian</label>
                    <div>
                        <textarea id="content" name="content" placeholder="" rows="1" class="form-control autosize-input"><?= set_value('content', $berita->content) ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Keterangan</label>
                    <div>
                        <textarea id="description" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description', $berita->description) ?></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="position-relative form-group p-3 border rounded">
                            <label for="file_cover" class="font-weight-bold">Cover (Single)</label>
                            
                            <?php if (!empty($berita->file_cover)): ?>
                                <div class="existing-file-container">
                                    <div class="existing-item">
                                        <p class="mb-1 text-muted text-left"><small>Cover Saat Ini:</small></p>
                                        <img src="<?= base_url('uploads/berita/' . $berita->file_cover) ?>" alt="Current Cover">
                                        <small class="text-muted d-block text-truncate"><?= $berita->file_cover ?></small>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mt-2">
                                <label><small>Ganti Cover (Biarkan kosong jika tidak ingin mengubah):</small></label>
                                <input type="file" class="form-control-file" name="file_cover" id="file_cover" accept=".jpg,.jpeg,.png" onchange="previewFile(this, 'preview_cover_container')">
                            </div>

                            <div id="preview_cover_container" class="preview-container"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="position-relative form-group p-3 border rounded">
                            <label for="file_image" class="font-weight-bold">Images Gallery (Multiple)</label>
                            
                            <?php if (!empty($old_file_image_data)): ?>
                                <p class="mb-1 text-muted"><small>Gallery Saat Ini (Centang "Hapus" untuk membuang gambar):</small></p>
                                <div class="existing-file-container">
                                    <?php foreach ($old_file_image_data as $img): ?>
                                        <div class="existing-item">
                                            <img src="<?= base_url('uploads/berita/' . $img['name']) ?>" alt="Gallery">
                                            
                                            <label class="remove-checkbox">
                                                <input type="checkbox" name="remove_gallery[]" value="<?= $img['name'] ?>"> Hapus
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="mt-2">
                                <label><small>Tambah Foto Baru:</small></label>
                                <input type="file" class="form-control-file" name="file_image[]" id="file_image" multiple accept=".jpg,.jpeg,.png" onchange="previewMultipleFiles(this, 'preview_image_container')">
                                <small class="text-muted">Tahan CTRL untuk memilih banyak foto.</small>
                            </div>

                            <div id="preview_image_container" class="preview-container"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary" name="submit">Simpan Perubahan</button>
                    <a href="<?= base_url('cms/berita') ?>" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<script>
    // 1. Fungsi Preview Single File (Cover)
    function previewFile(input, previewId) {
        var previewContainer = document.getElementById(previewId);
        // Menggunakan jQuery untuk clearing, asumsikan jQuery tersedia
        $(previewContainer).empty();

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

    // 2. Fungsi Preview Multiple Files (Gallery)
    // Perbaikan: Menggunakan 'let' untuk 'i', 'reader', dan 'file' untuk fix closure/scope
    function previewMultipleFiles(input, previewId) {
        var previewContainer = document.getElementById(previewId);
        $(previewContainer).empty(); // Hapus preview sebelumnya

        if (input.files) {
            var filesAmount = input.files.length;

            for (let i = 0; i < filesAmount; i++) {
                let reader = new FileReader();
                let file = input.files[i]; // Tangkap file saat ini untuk closure yang benar

                reader.onload = function(event) {
                    var html = `
                        <div class="img-preview-box">
                             <div class="text-center text-success mb-1" style="font-size:10px;"><b>Akan Diupload</b></div>
                            <img src="${event.target.result}" title="${file.name}">
                        </div>`;
                    $(previewContainer).append(html);
                }

                reader.readAsDataURL(file);
            }
        }
    }

    // Perbaikan Utama: Membungkus inisialisasi Summernote di dalam $(document).ready()
    // dan memindahkannya keluar dari fungsi previewMultipleFiles
    $(document).ready(function() {
        // TinyMCE Init (Summernote)
        $('#content').summernote({
            height: 430,
            minHeight: null,
            maxHeight: null,
            focus: true,
            toolbar: [
                ['style', ['style', 'undo', 'redo', 'codeview']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph', 'table']],
                ['insert', ['link', 'picture', 'video', 'hr']],
            ],
            fontNames: ['System Font',
                'Dosis', 'Andale Mono', 'Arial', 'Arial Black', 'Book Antiqua',
                'Comic Sans MS', 'Courier New', 'Georgia', 'Helvetica', 'Impact',
                'Symbol', 'Tahoma', 'Times New Roman', 'Trebuchet MS', 'Verdana'
            ],
            fontSizes: [
                '12', '13', '14', '15', '16', '17', '18', '19', '20', '24',
                '28', '32', '34', '36', '72'
            ],
            styleTags: ['p', 'blockquote', 'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
            callbacks: {
                onInit: function() {
                    console.log('Summernote is initialized on #content');
                }
            },
        });
    });
</script>
<?= $this->endSection('script') ?>