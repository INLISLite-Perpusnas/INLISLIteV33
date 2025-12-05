<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style') ?>
<style>
	/* .tox.tox-tinymce.tox-fullscreen {
		z-index: 1050;
		top: 60px !important;
		left: 85px !important;
		width: calc(100% - 85px) !important;
	} */
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
						<li class="breadcrumb-item"><a href="<?= base_url(
																	'dashboard'
																) ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="breadcrumb-item"><a href="<?= base_url(
																	'cms/berita'
																) ?>"><?= lang('Berita') ?></a></li>
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

			<form id="frm_create" class="col-md-12 mx-auto" method="post" action="<?= base_url(
																						'cms/berita/create?slug=' . $slug
																					) ?>">
				<div class="form-row">
					<div class="col-md-12">
						<div class="position-relative form-group">
							<label for="title">Judul Berita*</label>
							<div>
								<input type="text" class="form-control" name="title" id="title" placeholder="Judul Berita " value="<?= set_value('title') ?>" />
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
						<div class="position-relative form-group">
							<label for="file_cover" class="">Cover</label>
							<div id="file_cover" class="dropzone"></div>
							<div id="file_cover_listed"></div>
							<div>
								<small class="info help-block text-muted">Format (JPG|PNG). Max 1 Files @10M</small>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="position-relative form-group">
							<label for="file_image" class="">Images</label>
							<div id="file_image" class="dropzone"></div>
							<div id="file_image_listed"></div>
							<div>
								<small class="info help-block text-muted">Format (JPG|PNG). Max 6 Files @10MB</small>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<button type="submit" class="btn btn-primary" name="submit">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>


<?= $this->endSection('page') ?>

<?= $this->section('script') ?>
<!-- <script>
	var file_cover = setDropzone('file_cover', 'page', '.png,.jpg,.jpeg', 1, 10);
	var file_image = setDropzone('file_image', 'page', '.png,.jpg,.jpeg', 6, 10);
</script> -->
<script>
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
</script>
<?= $this->endSection('script') ?>