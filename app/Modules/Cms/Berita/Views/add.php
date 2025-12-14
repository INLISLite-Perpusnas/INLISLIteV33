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
<<<<<<< HEAD
    // 1. Fungsi Preview Single File (Cover)
    function previewFile(input, previewId) {
        var previewContainer = document.getElementById(previewId);
        previewContainer.innerHTML = ''; // Hapus preview sebelumnya

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

    // 2. Fungsi Preview Multiple Files (Gallery)
    function previewMultipleFiles(input, previewId) {
        var previewContainer = document.getElementById(previewId);
        previewContainer.innerHTML = ''; // Hapus preview sebelumnya

        if (input.files) {
            var filesAmount = input.files.length;

            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();
                var fileName = input.files[i].name;

                reader.onload = function(event) {
                    var html = `
                        <div class="img-preview-box">
                            <img src="${event.target.result}" title="Preview">
                        </div>`;
                    $(previewContainer).append(html);
                }

                reader.readAsDataURL(input.files[i]);
            }
        }
    }

    // TinyMCE Init
    $(document).ready(function() {
        tinyMCE.init({
            selector: 'textarea#content',
            height: 430,
            menubar: false,
            pagebreak_separator: '<div style="page-break-after:always;clear:both"></div>',
            plugins: 'link code image table pagebreak media lists fullscreen',
            toolbar: 'fullscreen code removeformat | bold italic underline strikethrough | fontsizeselect fontselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | insertfile image media pageembed link anchor codesample | forecolor backcolor casechange permanentpen formatpainter |  undo redo pagebreak | charmap emoticons | a11ycheck ltr rtl  | table tabledelete ',
            font_formats: "System Font=Dosis, san serif; Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva;",
            fontsize_formats: "12pt 13pt 14pt 15pt 16pt 17pt 18pt 19pt 20pt 24pt 28pt 32pt 34pt 36pt 72pt",
            content_style: "body { font-size: 12pt;}",
        });
    });
=======
	function setDropzone(id, module, acceptedFiles, maxFiles, maxFileSize) {
		let dropzone = new Dropzone("div#" + id, {
			url: "<?= base_url('cms/berita/do_upload') ?>",
			paramName: "file",
			maxFiles: maxFiles,
			maxFileSize: maxFileSize, // in MB
			addRemoveLinks: true,
			acceptedFiles: acceptedFiles,
			init: function() {
				// Event ketika file baru ditambahkan
				this.on("addedfile", function(file) {
					// Jika maxFiles = 1, hapus file lama saat ada file baru
					if (maxFiles === 1 && this.files.length > 1) {
						this.removeFile(this.files[0]);
					}
				});

				// Event ketika file berhasil diunggah
				this.on("success", function(file, response) {
					$('#' + id + '_listed').append('<input type="hidden" name="' + id + '[]" value="' + response.filename + '">');
				});

				// Event ketika file dihapus
				this.on("removedfile", function(file) {
					// Cari dan hapus input tersembunyi yang sesuai
					$('#' + id + '_listed').find('input[value="' + file.name + '"]').remove();

					// Pastikan Dropzone dapat menerima file baru
					if (this.files.length < maxFiles) {
						this.options.maxFiles = maxFiles;
					}
				});

				// Handle ketika jumlah file melebihi batas
				this.on("maxfilesexceeded", function(file) {
					if (maxFiles === 1) {
						this.removeAllFiles(); // Hapus file lama
						this.addFile(file); // Tambahkan file yang baru
					} else {
						alert("Jumlah file melebihi batas: " + maxFiles);
						this.removeFile(file);
					}
				});
			}
		});
		return dropzone;
	}

	$(document).ready(function() {
		var file_cover = setDropzone('file_cover', 'page', '.png,.jpg,.jpeg', 1, 10);
		var file_image = setDropzone('file_image', 'page', '.png,.jpg,.jpeg', 6, 10);
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
					// Untuk set font awal, biasanya dilakukan melalui CSS atau konfigurasi khusus.
					// Jika Anda ingin melakukan sesuatu setelah editor siap:
					console.log('Summernote is initialized');
				}
			},
			// Anda bisa tambahkan setting B4-specific lainnya jika diperlukan
		});
	});
	// $('#content').summernote({
	//         height: 430,
	//         minHeight: null,
	//         maxHeight: null,
	//         focus: true,
	//         toolbar: [
	//             ['style', ['style', 'undo', 'redo', 'codeview']],
	//             ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
	//             ['fontname', ['fontname']],
	//             ['fontsize', ['fontsize']],
	//             ['color', ['color']],
	//             ['para', ['ul', 'ol', 'paragraph', 'table']],
	//             ['insert', ['link', 'picture', 'video', 'hr']],
	//         ],
	//         fontNames: ['System Font',
	//             'Dosis', 'Andale Mono', 'Arial', 'Arial Black', 'Book Antiqua',
	//             'Comic Sans MS', 'Courier New', 'Georgia', 'Helvetica', 'Impact',
	//             'Symbol', 'Tahoma', 'Times New Roman', 'Trebuchet MS', 'Verdana'
	//         ],
	//         fontSizes: [
	//             '12', '13', '14', '15', '16', '17', '18', '19', '20', '24',
	//             '28', '32', '34', '36', '72'
	//         ],
	//         styleTags: ['p', 'blockquote', 'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
	//         callbacks: {
	//             onInit: function() {
	//                 // Untuk set font awal, biasanya dilakukan melalui CSS atau konfigurasi khusus.
	//                 // Jika Anda ingin melakukan sesuatu setelah editor siap:
	//                 console.log('Summernote is initialized');
	//             }
	//         },
	//         // Anda bisa tambahkan setting B4-specific lainnya jika diperlukan
	//     });
>>>>>>> 768fa1327effd041bd29d938a21825fda142d99e
</script>
<?= $this->endSection('script') ?>