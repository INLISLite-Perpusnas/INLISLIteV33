<?php
$request = service('request'); ?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
	/* .tox.tox-tinymce.tox-fullscreen {
		z-index: 1050;
		top: 60px !important;
		left: 85px !important;
		width: calc(100% - 85px) !important;
	} */
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
				<div>Visitor <?= $visitor->category_sub ?>
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Beranda</a></li>
						<li class="breadcrumb-item">Visitor </li>
						<li class="active breadcrumb-item" aria-current="page">Ubah <?= $visitor->category_sub ?> </li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-header">
			<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Ubah Visitor
		</div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? ''; ?></div>
			<?= get_message('message'); ?>

			<form id="frm" class="col-md-12 mx-auto" method="post" action="">
				<div class="form-row">
					<div class="col-md-12">
						<div class="position-relative form-group">
							<label for="name">Judul Visitor*</label>
							<div>
								<input type="text" class="form-control" id="title" name="title" placeholder="Judul Visitor" value="<?= set_value('name', $visitor->title); ?>" />
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="position-relative form-group">
							<label for="slug">Slug*</label>
							<div>
								<input type="text" class="form-control" id="slug" name="slug" placeholder="Slug" value="<?= set_value('slug', $visitor->slug); ?>" />
								<small class="info help-block text-muted">Permalink: <a href="<?= permalink('visitor/' . $visitor->slug) ?>" target="_blank"><?= permalink('visitor/' . $visitor->slug) ?></a></small>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label>Kategori*</label>
							<select class="form-control" name="category" id="category" tabindex="-1" aria-hidden="true">
								<option value="Visitor">Visitor</option>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label>Sub Kategori*</label>
							<select class="form-control" name="category_sub" id="category_sub" tabindex="-1" aria-hidden="true">
								<?php foreach (get_ref('ref-visitor', 'slug') as $row) : ?>
									<option value="<?= $row->name ?>" <?= ($row->name == $visitor->category_sub) ? 'selected' : '' ?>><?= $row->name ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label for="sort">Urutan</label>
							<div>
								<input type="number" class="form-control" name="sort" id="sort" placeholder="Urutan " value="<?= set_value('sort', $visitor->sort); ?>" />
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="description">Keterangan</label>
					<div>
						<textarea id="description" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description', $visitor->description) ?></textarea>
					</div>
				</div>

				<div class="form-group">
					<label for="content">Uraian</label>
					<div>
						<textarea id="content" name="content" placeholder="" rows="1" class="form-control autosize-input"><?= set_value('content', $visitor->content); ?></textarea>
					</div>
				</div>

				<div class="form-group">
					<button type="submit" class="btn btn-primary" name="submit">Simpan</button>
				</div>
			</form>
		</div>
	</div>
</div>


<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
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
		// 	toolbar: 'fullscreen code | undo redo | bold italic underline strikethrough | fontsizeselect formatselect fontselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | insertfile image media pageembed template link anchor codesample | forecolor backcolor casechange permanentpen formatpainter removeformat | preview save print | pagebreak | charmap emoticons | a11ycheck ltr rtl  | table tabledelete ',
		// 	font_formats: "Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats",
		// 	setup: function(editor) {
		// 		editor.on('init', function(e) {
		// 			//   editor.execCommand("fontName", false, "Book Antiqua");
		// 			// editor.setContent(content);
		// 		});
		// 	},
		// 	fontsize_formats: "12px 14px 16px 18px 20px 24px 28px 32px",
		// 	content_style: "body { font-size: 14px;}",
		// });
	});
</script>
<?= $this->endSection('script'); ?>