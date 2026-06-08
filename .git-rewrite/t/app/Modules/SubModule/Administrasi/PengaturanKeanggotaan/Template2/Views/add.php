<?php
$request = service('request');
$slug = $request->getGet('slug') ?? '';
?>

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
				<div>Template <?= ucwords(unslugify($slug)) ?>
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
																	'page'
																) ?>"><?= lang('Page.module') ?></a></li>
						<li class="active breadcrumb-item" aria-current="page">Tambah Template</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-header">
			<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Tambah Template
		</div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? '' ?></div>
			<?= get_message('message') ?>

			<form id="frm_create" class="col-md-12 mx-auto" method="post" action="<?= base_url(
																						'master-template-kartu/create?slug=' . $slug
																					) ?>">
				<div class="form-row">
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label>Kategori*</label>
							<select class="form-control" name="category" id="category" tabindex="-1" aria-hidden="true">
								<?php foreach (get_ref('ref-template', 'slug') as $row) : ?>
									<option value="<?= $row->slug ?>" <?= ($row->slug == $slug) ? 'selected' : '' ?>><?= $row->name ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label>Layout*</label>
							<select class="form-control" name="category_sub" id="category_sub" tabindex="-1" aria-hidden="true">
								<option value="Landscape">Landscape</option>
								<option value="Potrait">Potrait</option>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label for="title">Nama Template*</label>
							<div>
								<input type="text" class="form-control" name="title" id="title" placeholder="Judul Template " value="<?= set_value('title') ?>" />
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="description">Keterangan </label>
					<div>
						<textarea id="description" name="description" placeholder="Keterangan " rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description') ?></textarea>
					</div>
				</div>

				<div class="form-group">
					<label for="content">Content </label>
					<div>
						<textarea id="content" name="content" placeholder="Keterangan " rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('content') ?></textarea>
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
<script>
	$(document).ready(function() {
		tinyMCE.init({
			selector: 'textarea#content',
			height: 430,
			menubar: false,
			pagebreak_separator: '<div style="page-break-after:always;clear:both"></div>',
			plugins: 'link code image table pagebreak media lists fullscreen',
			toolbar: 'fullscreen code removeformat | bold italic underline strikethrough | fontsizeselect fontselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | insertfile image media pageembed link anchor codesample | forecolor backcolor casechange permanentpen formatpainter |  undo redo pagebreak | charmap emoticons | a11ycheck ltr rtl  | table tabledelete ',
			font_formats: "System Font=Dosis, san serif; Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva;",
			setup: function(editor) {
				// editor.on('init', function(e) {
				// 	editor.execCommand("fontName", true, "System Font");
				// 	editor.setContent(content);
				// });
			},
			fontsize_formats: "12pt 13pt 14pt 15pt 16pt 17pt 18pt 19pt 20pt 24pt 28pt 32pt 34pt 36pt 72pt",
			content_style: "body { font-size: 12pt;}",
		});
	});
</script>
<?= $this->endSection('script') ?>