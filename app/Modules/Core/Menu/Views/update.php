<?php
$request = service('request');
$slug    = $request->getGet('slug') ?? 'backend-menu';

if (!($permissions = read_cache('list_menu_permissions'))) {
	$baseModel = new \Base\Models\BaseModel();
	$baseModel->setTable('c_references');
	$permissions = $baseModel
		->select('c_references.*')
		->join('c_menus', 'c_menus.id = c_references.menu_id', 'inner')
		->where('c_menus.name', 'Permission')
		->orderBy('c_references.sort', 'asc')
		->findAll();

	write_cache('list_menu_categories', $permissions);
}

if (!($menus = read_cache('list_menu_menus'))) {
	$baseModel = new \Base\Models\BaseModel();
	$baseModel->setTable('c_menus');
	$menus = $baseModel
		->select('c_menus.*')
		->where('active', '1')
		->orderBy('sort', 'asc')
		->findAll();
	write_cache('list_menu_categories', $menus);
}

if (!($category = read_cache($cacheName = 'list_menu_category_' . md5($slug)))) {
	$baseModel = new \Base\Models\BaseModel();
	$baseModel->setTable('c_categories');
	$category  = $baseModel
		->select('c_categories.*')
		->where('slug', $slug)
		->first();
	write_cache($cacheName, $categories);
}
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
				<div>Ubah Menu
					<div class="page-title-subheading">Mohon melengkapi data pada form berikut.</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="breadcrumb-item">Setting</li>
						<li class="breadcrumb-item"><a href="<?= base_url('param') ?>">Paramater</a></li>
						<li class="active breadcrumb-item" aria-current="page">Ubah Menu</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-header">
			<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Ubah Menu
		</div>
		<div class="card-body">
			<div id="infoMessage"><?= $message ?? ''; ?></div>
			<?= get_message('message'); ?>
			<form id="frm_edit_menu" class="col-md-12 mx-auto" method="post" action="<?= base_url('menu/edit/' . $menu->id . '?slug=' . $slug); ?>">
				<div class="form-row">
					<div class="col-md-12">
						<div class="position-relative form-group">
							<label for="name">Tipe</label>
							<div>
								<div class="custom-radio custom-control custom-control-inline">
									<input type="radio" name="type" id="type_label" value="label" class="custom-control-input" <?= ($menu->type == 'label') ? 'checked' : '' ?>>
									<label class="custom-control-label" for="type_label">Referensi</label>
								</div>
								<div class="custom-radio custom-control custom-control-inline">
									<input type="radio" name="type" id="type_menu" value="menu" class="custom-control-input" <?= ($menu->type == 'menu') ? 'checked' : '' ?>>
									<label class="custom-control-label" for="type_menu">Navigasi</label>
								</div>

							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="position-relative form-group">
							<label for="parent">Parent</label>
							<div>
								<select class="form-control" name="parent" id="parent" tabindex="-1" aria-hidden="true">
									<option value="0"></option>
									<?= display_menu_option($category->id, 0) ?>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="position-relative form-group">
							<label for="name">Label*</label>
							<div>
								<input type="text" class="form-control" id="name" name="name" placeholder="Label" value="<?= set_value('name', $menu->name); ?>" />
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label for="description">Keterangan</label>
							<div>
								<textarea id="description" name="description" placeholder="Keterangan" rows="2" class="form-control autosize-input" style="min-height: 38px;"><?= set_value('description', $menu->description); ?></textarea>
							</div>
						</div>
					</div>
					<div class="col-md-6 menu">
						<div class="position-relative form-group">
							<label for="name">URL/Controller</label>
							<div>
								<input type="text" class="form-control" id="frm_edit_controller" name="controller" placeholder="URL/Controller" value="<?= set_value('controller', $menu->controller) ?>" />
								<small class="info help-block text-muted">Contoh: {admin_url}/frontend/home/welcome</small>
							</div>
						</div>
					</div>

					<div class="col-md-6 menu">
						<div class="position-relative form-group">
							<label for="name">Icon</label>
							<div>
								<input type="text" class="form-control" islug" name="icon" placeholder="Icon" value="<?= set_value('icon', $menu->icon) ?>" />
								<small class="info help-block text-muted">Contoh: <i class="fa fa-home"></i> fa fa-home</small>
							</div>
						</div>
					</div>
					<div class="col-md-12 menu">
						<div class="position-relative form-group">
							<label for="permission">Permission*</label>
							<div>
								<?php foreach ($permissions as $row) : ?>
									<div class="custom-checkbox custom-control custom-control-inline">
										<input type="checkbox" id="frm_edit_permission_<?= $row->name ?>" name="permission[]" value="<?= $row->name ?>" class="custom-control-input">
										<label class="custom-control-label" for="frm_edit_permission_<?= $row->name ?>"><?= $row->name ?></label>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label for="description">Slug</label>
							<div>
								<input type="text" class="form-control" id="frm_edit_slug" name="form_slug" placeholder="Slug" value="<?= set_value('form_slug', $menu->slug) ?>" />
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
		var permission = '<?= $menu->permission ?>';
		var permissions = permission.split("|");
		$.each(permissions, function(index, value) {
			$('#frm_edit_permission_' + value).prop("checked", true);
		});

		var parent_id = '<?= $menu->parent ?>';
		console.log(parent_id);
		$('#parent').val(parent_id);

		var menu_type = '<?= $menu->type ?>';

		if (menu_type == 'menu') {
			$('.menu').show();
		} else {
			$('.menu').hide();
		}

		$('#type_menu').click(function() {
			$('.menu').show();
		});

		$('#type_label').click(function() {
			$('.menu').hide();
		});
	});
</script>
<?= $this->endSection('script'); ?>