<?php
$request = service('request');
$slug = $request->getGet('slug') ?? 'backend-menu';
$parent_id = $request->getGet('parent_id');
$category = get_single('c_categories', 'id, name', 'slug = "' . $slug . '"');
$reference = get_single('c_menus', 'id, name', 'slug = "ref-permission"');
$permissions = get_table('c_references', 'id, name', 'menu_id = ' . $reference->id);
?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>
<?= $this->section('page'); ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-menu icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Tambah Menu
					<div class="page-title-subheading">Mohon melengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="breadcrumb-item">Setting</li>
						<li class="breadcrumb-item"><a href="<?= base_url('param') ?>">Paramater</a></li>
						<li class="active breadcrumb-item" aria-current="page">Tambah Menu</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="main-card mb-3 card">
				<div class="card-header">
					<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Tambah Menu
				</div>
				<div class="card-body">
					<div id="infoMessage"><?= $message ?? ''; ?></div>
					<?= get_message('message'); ?>
					<form id="frm_create_menu" class="col-md-12 mx-auto" method="post" action="<?= base_url('menu/create?slug=' . $slug); ?>">
						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="name">Tipe</label>
									<div>
										<div class="custom-radio custom-control custom-control-inline">
											<input type="radio" name="type" id="type_label" value="label" class="custom-control-input" checked>
											<label class="custom-control-label" for="type_label">Heading</label>
										</div>
										<div class="custom-radio custom-control custom-control-inline">
											<input type="radio" name="type" id="type_menu" value="menu" class="custom-control-input">
											<label class="custom-control-label" for="type_menu">Navigation</label>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="parent">Parent</label>
									<div>
										<select class="form-control" name="parent" id="parent" tabindex="-1" aria-hidden="true">
											<option value="0">&nbsp;Blank</option>
											<?= display_menu_option($category->id, 0) ?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="name">Label*</label>
									<div>
										<input type="text" class="form-control" id="name" name="name" placeholder="" value="<?= set_value('name'); ?>" />
									</div>
								</div>
							</div>
							<div class="col-md-6 menu">
								<div class="position-relative form-group">
									<label for="name">Route</label>
									<div>
										<input type="text" class="form-control" id="frm_create_controller" name="controller" placeholder="" value="<?= set_value('controller') ?>" />
										<small class="info help-block text-muted">Contoh: {katalog}/index</small>
									</div>
								</div>
							</div>
							<div class="col-md-6 menu">
								<div class="position-relative form-group">
									<label for="name">Icon</label>
									<div>
										<input type="text" class="form-control" id="frm_create_icon" name="icon" placeholder="" value="<?= set_value('icon') ?>" />
										<small class="info help-block text-muted">Contoh: <i class="pe-7s-menu"></i> pe-7s-menu</small>
									</div>
								</div>
							</div>
							<div class="col-md-12 menu">
								<div class="position-relative form-group">
									<label for="permission">Permission*</label>
									<div>
										<?php foreach ($permissions as $row) : ?>
											<div class="custom-checkbox custom-control custom-control-inline">
												<input type="checkbox" id="frm_create_permission_<?= $row->name ?>" name="permission[]" value="<?= $row->name ?>" class="custom-control-input" <?= ($row->name == 'access') ? 'checked' : '' ?>>
												<label class="custom-control-label" for="frm_create_permission_<?= $row->name ?>"><?= $row->name ?></label>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<input type="hidden" name="category_id" value="<?= $category->id ?>">
							<button type="submit" class="btn btn-primary" name="submit">Simpan</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<?= $this->endSection('page'); ?>
<?= $this->section('script'); ?>
<script>
	$(document).ready(function() {
		$('#name').focus();

		var parent_id = '<?= $parent_id ?>';
		console.log(parent_id);
		$('#parent').val(parent_id);

		$('.menu').hide();

		$('#type_menu').click(function() {
			$('.menu').show();
		});

		$('#type_label').click(function() {
			$('.menu').hide();
		});
	});
</script>
<?= $this->endSection('script'); ?>