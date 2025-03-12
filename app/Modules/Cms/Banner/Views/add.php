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
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>


<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-note icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Banner <?= ucwords(unslugify($slug)) ?>
					<div class="page-title-subheading">Mohon lengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="breadcrumb-item"><a href="<?= base_url('page') ?>"><?= lang('Page.module') ?></a></li>
						<li class="active breadcrumb-item" aria-current="page">Tambah Banner</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-header">
			<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Tambah Banner
		</div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? ''; ?></div>
			<?= get_message('message'); ?>

			<form id="frm_create" class="col-md-12 mx-auto" method="post" action="<?= base_url('cms/banner/create?slug=' . $slug); ?>">
				<div class="form-row">
					<div class="col-md-12">
						<div class="position-relative form-group">
							<label for="title">Judul Banner*</label>
							<div>
								<input type="text" class="form-control" name="title" id="title" placeholder="Judul Banner " value="<?= set_value('title'); ?>" />
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
					<div class="col-md-4">
						<div class="position-relative form-group">
							<label for="sort">Urutan</label>
							<div>
								<input type="number" class="form-control" name="sort" id="sort" placeholder="Urutan " value="<?= set_value('sort'); ?>" />
							</div>
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
						<div class="position-relative form-group">
							<label for="file_image" class="">Upload Banner</label>
							<div id="file_image" class="dropzone"></div>
							<div id="file_image_listed"></div>
							<div>
								<small class="info help-block text-muted">Format (JPG|PNG). Max 1 Files @2MB</small>
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


<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
	var file_image = setDropzone('file_image', 'cms/banner', '.png,.jpg,.jpeg', 1, 2);
</script>
<?= $this->endSection('script'); ?>