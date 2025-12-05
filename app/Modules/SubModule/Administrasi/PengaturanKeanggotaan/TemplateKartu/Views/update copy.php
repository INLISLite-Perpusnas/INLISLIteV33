<?php
$request = service('request'); ?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style') ?>
<style>
	.tox.tox-tinymce.tox-fullscreen {
		z-index: 1050;
		top: 60px !important;
		left: 85px !important;
		width: calc(100% - 85px) !important;
	}
</style>
<?= $this->endSection('style') ?>

<?= $this->section('page') ?>


<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-note2 icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Template Kartu <?= $template->category ?>
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Beranda</a></li>
						<li class="breadcrumb-item">Template Kartu </li>
						<li class="active breadcrumb-item" aria-current="page">Ubah <?= $template->category ?> </li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-header">
			<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Ubah Template Kartu
		</div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? '' ?></div>
			<?= get_message('message') ?>

			<form id="frm" class="col-md-12 mx-auto" method="post" action="">
				<div class="form-row">
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label>Kategori*</label>
							<select class="form-control" name="category" id="category" tabindex="-1" aria-hidden="true">
								<?php foreach (get_ref('ref-template', 'slug') as $row) : ?>
									<option value="<?= $row->slug ?>" <?= (slugify($template->category) == $row->slug) ? 'selected' : '' ?>><?= $row->name ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label>Sub Kategori*</label>
							<select class="form-control" name="category_sub" id="category_sub" tabindex="-1" aria-hidden="true">
								<option value="depan" <?= $template->category_sub == 'depan' ? 'selected' : '' ?>>Depan</option>
								<option value="belakang" <?= $template->category_sub == 'belakang' ? 'selected' : '' ?>>Belakang</option>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label>Layout*</label>
							<select class="form-control" name="layout" id="layout" tabindex="-1" aria-hidden="true">
								<option value="landscape" <?= $template->layout == 'landscape' ? 'selected' : '' ?>>Landscape</option>
								<option value="potrait" <?= $template->layout == 'potrait' ? 'selected' : '' ?>>Potrait</option>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label for="name">Nama Template Kartu*</label>
							<div>
								<input type="text" class="form-control" id="title" name="title" placeholder="Nama Template Kartu" value="<?= set_value('name', $template->title) ?>" />
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="description">Keterangan</label>
					<div>
						<textarea id="description" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description', $template->description) ?></textarea>
					</div>
				</div>

				<div class="form-group">
					<label for="content">Content</label>
					<div>
						<textarea id="content" name="content" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('content', $template->content) ?></textarea>
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
<script>
	$(document).ready(function() {
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
		});
		// tinyMCE.init({
		// 	selector: 'textarea#content',
		// 	height: 430,
		// 	menubar: false,
		// 	pagebreak_separator: '<div style="page-break-after:always;clear:both"></div>',
		// 	plugins: 'link code image table pagebreak media lists fullscreen',
		// 	toolbar: 'fullscreen code removeformat | bold italic underline strikethrough | fontsizeselect fontselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | insertfile image media pageembed link anchor codesample | forecolor backcolor casechange permanentpen formatpainter |  undo redo pagebreak | charmap emoticons | a11ycheck ltr rtl  | table tabledelete ',
		// 	font_formats: "System Font=Dosis, san serif; Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva;",
		// 	setup: function(editor) {
		// 		// editor.on('init', function(e) {
		// 		// 	editor.execCommand("fontName", true, "System Font");
		// 		// 	editor.setContent(content);
		// 		// });
		// 	},
		// 	fontsize_formats: "12pt 13pt 14pt 15pt 16pt 17pt 18pt 19pt 20pt 24pt 28pt 32pt 34pt 36pt 72pt",
		// 	content_style: "body { font-size: 12pt;}",
		// });
	});
</script>
<?= $this->endSection('script') ?>