<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style') ?>
<style>
<<<<<<< HEAD
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
        object-fit: cover; /* Agar gambar tidak gepeng */
    }
=======
	/* .tox.tox-tinymce.tox-fullscreen {
		z-index: 1050;
		top: 60px !important;
		left: 85px !important;
		width: calc(100% - 85px) !important;
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
                    <i class="pe-7s-network icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Berita <?= ucwords(unslugify($slug)) ?>
                    <div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('cms/berita') ?>"><?= lang('Berita') ?></a></li>
                        <li class="active breadcrumb-item" aria-current="page">Tambah Berita</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Tambah Berita
        </div>
        <div class="card-body">
            <div id="infoMessage"><?= $message ?? '' ?></div>
            <?= get_message('message') ?>

            <form id="frm_create" class="col-md-12 mx-auto" method="post" enctype="multipart/form-data" action="<?= base_url('cms/berita/create?slug=' . $slug) ?>">
                
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="title">Judul Berita*</label>
                            <div>
                                <input type="text" class="form-control" name="title" id="title" placeholder="Judul Berita " value="<?= set_value('title') ?>" required />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="content">Uraian</label>
                    <div>
                        <textarea id="content" name="content" placeholder="" rows="1" class="form-control autosize-input"><?= set_value('content') ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Keterangan </label>
                    <div>
                        <textarea id="frm_create_description" name="description" placeholder="Keterangan " rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description') ?></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="position-relative form-group p-3 border rounded">
                            <label for="file_cover" class="font-weight-bold">Cover (Wajib)</label>
                            
                            <input type="file" class="form-control-file" name="file_cover" id="file_cover" accept=".jpg,.jpeg,.png" onchange="previewFile(this, 'preview_cover_container')">
                            
                            <small class="form-text text-muted mt-2 mb-2">
                                - Format: JPG, JPEG, PNG (Max 2 MB)
                            </small>

                            <div id="preview_cover_container" class="preview-container"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="position-relative form-group p-3 border rounded">
                            <label for="file_image" class="font-weight-bold">Images Gallery (Opsional)</label>
                            
                            <input type="file" class="form-control-file" name="file_image[]" id="file_image" multiple accept=".jpg,.jpeg,.png" onchange="previewMultipleFiles(this, 'preview_image_container')">
                            
                            <small class="form-text text-muted mt-2 mb-2">
                                - Tahan CTRL untuk memilih banyak foto. (Max 2 MB per file)
                            </small>

                            <div id="preview_image_container" class="preview-container"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary btn-lg" name="submit"><i class="fa fa-save"></i> Simpan Berita</button>
                    <a href="<?= base_url('cms/berita') ?>" class="btn btn-secondary btn-lg"><i class="fa fa-arrow-left"></i> Kembali</a>
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
        // Pastikan jQuery tersedia sebelum menggunakannya di sini,
        // meskipun di fungsi ini kita menggunakan DOM murni untuk kinerja yang lebih baik.
        previewContainer.innerHTML = ''; // Hapus preview sebelumnya

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                var html = `
                    <div class="img-preview-box">
                        <img src="${e.target.result}" title="${input.files[0].name}">
                    </div>`;
                // Menggunakan jQuery jika Anda sudah memuatnya
                $(previewContainer).html(html);
                // Atau DOM murni: previewContainer.innerHTML = html;
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    // 2. Fungsi Preview Multiple Files (Gallery)
    // Menggunakan let untuk i dan fileName agar reader.onload dapat mengakses nilai yang benar (closure).
    function previewMultipleFiles(input, previewId) {
        var previewContainer = document.getElementById(previewId);
        // Menggunakan jQuery untuk clearing, asumsikan jQuery tersedia
        $(previewContainer).empty(); // Hapus preview sebelumnya

        if (input.files) {
            var filesAmount = input.files.length;

            for (let i = 0; i < filesAmount; i++) {
                let reader = new FileReader(); // Gunakan let untuk reader
                let file = input.files[i];      // Tangkap file saat ini

                reader.onload = function(event) {
                    var html = `
                        <div class="img-preview-box">
                            <img src="${event.target.result}" title="${file.name}">
                        </div>`;
                    $(previewContainer).append(html);
                }

                reader.readAsDataURL(file);
            }
        }
    }

    // Perbaikan Utama: Membungkus inisialisasi Summernote di dalam $(document).ready()
    $(document).ready(function() {
        // TinyMCE Init (Menggunakan Summernote)
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