<?php
$request = service('request'); ?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
	.tox.tox-tinymce.tox-fullscreen {
		z-index: 1050;
		top: 60px !important;
		left: 85px !important;
		width: calc(100% - 85px) !important;
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
				<div>Pengaturananggota <?= $pengaturananggota->category ?>
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Beranda</a></li>
						<li class="breadcrumb-item">Pengaturananggota </li>
						<li class="active breadcrumb-item" aria-current="page">Ubah <?= $pengaturananggota->category ?> </li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-header">
			<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Ubah Pengaturananggota
		</div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? ''; ?></div>
			<?= get_message('message'); ?>

			<form id="frm" class="col-md-12 mx-auto" method="post" action="">
				<div class="form-row">
					<div class="col-md-12">
						<div class="position-relative form-group">
							<label for="name">Judul Pengaturananggota*</label>
							<div>
								<select class="form-control" name="title" id="title" tabindex="-1" aria-hidden="true">
									<?php foreach (get_ref('ref-judul-kartuanggota', 'slug') as $row) : ?>
										<option value="<?= $row->name ?>" <?= ($row->name == $pengaturananggota->category) ? 'selected' : '' ?>><?= $row->name ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label>Kategori*</label>
							<select class="form-control" name="category" id="category" tabindex="-1" aria-hidden="true">
								<?php foreach (get_ref('ref-templete_kartuanggota', 'slug') as $row) : ?>
									<option value="<?= $row->name ?>" <?= ($row->name == $pengaturananggota->category) ? 'selected' : '' ?>><?= $row->name ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label for="sort">Urutan</label>
							<div>
								<input type="number" class="form-control" name="sort" id="sort" placeholder="Urutan " value="<?= set_value('sort', $pengaturananggota->sort); ?>" />
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="description">Keterangan</label>
					<div>
						<textarea id="content" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description', $pengaturananggota->description) ?></textarea>
					</div>
				</div>

				<hr>
				<?php
				$default = base_url('uploads/default/no_cover.jpg');
				$file_image = (!empty($pengaturananggota->file_image)) ? base_url('uploads/pengaturananggota/' . $pengaturananggota->file_image) : $default;
				?>
				<div class="form-row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="content">Preview Pengaturananggota</label>
							<div>
								<img width="100%" src="<?= $file_image ?>" alt="Image" class="img">
							</div>
						</div>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-12">
						<div class="position-relative form-group">
							<label for="file_image" class="">Upload Pengaturananggota</label>
							<div id="file_image" class="dropzone"></div>
							<div id="file_image_listed"></div>
							<div>
								<small class="info help-block text-muted">Format (JPG|PNG). Max 1 Files @ 2MB</small>
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


<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
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
<script>
	var file_image = setDropzone('file_image', 'pengaturananggota', '.png,.jpg,.jpeg', 1, 2);
</script>
<?= $this->endSection('script'); ?>