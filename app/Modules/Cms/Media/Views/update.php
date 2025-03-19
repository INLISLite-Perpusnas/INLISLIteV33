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
				<div>Media <?= $media->category_sub ?>
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Beranda</a></li>
						<li class="breadcrumb-item">Media </li>
						<li class="active breadcrumb-item" aria-current="page">Ubah <?= $media->category_sub ?> </li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-header">
			<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Ubah Media
		</div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? ''; ?></div>
			<?= get_message('message'); ?>

			<form id="frm" class="col-md-12 mx-auto" method="post" action="">
				<div class="form-row">
					<div class="col-md-12">
						<div class="position-relative form-group">
							<label for="name">Judul Media*</label>
							<div>
								<input type="text" class="form-control" id="title" name="title" placeholder="Judul Media" value="<?= set_value('name', $media->title); ?>" />
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label>Kategori*</label>
							<select class="form-control" name="category" id="category" tabindex="-1" aria-hidden="true">
								<option value="Media">Media</option>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label>Sub Kategori*</label>
							<select class="form-control" name="category_sub" id="category_sub" tabindex="-1" aria-hidden="true">
								<?php foreach (get_ref('ref-media', 'slug') as $row) : ?>
									<option value="<?= $row->name ?>" <?= ($row->name == $media->category_sub) ? 'selected' : '' ?>><?= $row->name ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="description">Keterangan</label>
					<div>
						<textarea id="description" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description', $media->description) ?></textarea>
					</div>
				</div>

				<div class="position-relative form-group">
					<label for="url">File URL</label>
					<div>
						<div class="input-group">
							<input type="text" value="<?= permalink('uploads/media/' . $media->file_image) ?>" class="form-control">
							<div class="input-group-append">
								<button type="button" class="btn btn-secondary btn-clipboard" data-value="<?= permalink('uploads/media/' . $media->file_image) ?>"><i class="fa fa-copy"></i> Copy File URL</button>
							</div>
						</div>
					</div>
				</div>

				<?php if ($media->category_sub == 'Pdf') : ?>
					<div class="position-relative form-group">
						<label for="url">Flip URL</label>
						<div>
							<div class="input-group">
								<input type="text" value="<?= permalink('flip/uploads/media/' . $media->file_image) ?>" class="form-control">
								<div class="input-group-append">
									<button type="button" class="btn btn-secondary btn-clipboard" data-value="<?= permalink('flip/uploads/media/' . $media->file_image) ?>"><i class="fa fa-copy"></i> Copy Flip URL</button>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>


				<hr>
				<?php
				$default = base_url('uploads/default/no_cover.jpg');
				$file_image = (!empty($media->file_image)) ? base_url('uploads/media/' . $media->file_image) : $default;
				$file_pdf = base_url('flip/uploads/media/' . $media->file_image);
				?>

				<div class="form-row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="content">Preview Media</label>
							<div>
								<?php if ($media->category_sub == 'Video') : ?>
									<video controls="controls" controlsList="nodownload" style="width: 100%; height:auto">
										<source src="<?= $file_image ?>" />
									</video>
								<?php elseif ($media->category_sub == 'Audio') : ?>
									<audio controls controlsList="nodownload" style="width: 100%; height:50px">
										<source src="<?= $file_image ?>" type="audio/mpeg" />
									</audio>
								<?php elseif ($media->category_sub == 'Image') : ?>
									<img src="<?= $file_image ?>" alt="Image" class="img" style="width: 100%; height:auto">
								<?php else : ?>
									<a class="popup-link btn btn-primary" href="<?= $file_pdf ?>">Lihat File</a>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-12">
						<div class="position-relative form-group">
							<label for="file_image" class="">Upload Media</label>
							<div id="file_image" class="dropzone"></div>
							<div id="file_image_listed"></div>
							<div>
								<small class="info help-block text-muted">Max 1 Files @10MB</small>
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
	var file_image = setDropzone('file_image', 'cms/media', '.png,.jpg,.jpeg,.mp3,.mp4,.mkv,.mov,.avi,.pdf', 1, 10);
</script>
<script>
	$('.btn-clipboard').click(function() {
		var value = $(this).data('value');
		copyToClipboard(value);
	});

	function copyToClipboard(text) {
		var sampleTextarea = document.createElement("textarea");
		document.body.appendChild(sampleTextarea);
		sampleTextarea.value = text;
		sampleTextarea.select();
		document.execCommand("copy");
		document.body.removeChild(sampleTextarea);
	}
</script>
<script>
	$(".lazy").Lazy();

	$('.image-link').magnificPopup({
		type: 'image'
	});

	$('.popup-link').magnificPopup({
		type: 'iframe',
		iframe: {
			markup: '<style>.mfp-iframe-holder .mfp-content {max-width: 95%;height:95%}</style>' +
				'<div class="mfp-iframe-scaler" >' +
				'<div class="mfp-close"></div>' +
				'<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
				'</div></div>'
		},
	});
</script>
<?= $this->endSection('script'); ?>