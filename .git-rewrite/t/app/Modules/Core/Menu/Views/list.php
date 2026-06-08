<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<link rel="stylesheet" href="<?= base_url('assets/vendors') ?>/nestable/nestable.css">
<style>
	.dd {
		max-width: none !important;
	}

	.dd-handle-label {
		opacity: 0;
	}

	.clickable {
		cursor: pointer;
	}

	.menu-toggle-activate {
		cursor: pointer;
	}

	.menu-toggle-activate_inactive>.dd3-content {
		background: #F7D2DC !important;
	}

	.dd-item>button {
		display: block;
		position: relative;
		cursor: pointer;
		float: left;
		width: 30px;
		height: 35px;
		margin: 2px 0;
		padding: 0;
		text-indent: 100%;
		white-space: nowrap;
		overflow: hidden;
		border: 0;
		background: transparent;
		font-size: 16px;
		line-height: 1;
		text-align: center;
		font-weight: bold;
	}

	.dd-item>button:before {
		content: '+';
		display: block;
		position: absolute;
		width: 100%;
		text-align: center;
		text-indent: 0;
	}

	.dd-item>button[data-action="collapse"]:before {
		content: '-';
	}

	.app-page-title {
		padding: 15px 30px;
	}
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
	<div class="app-page-title">
		<div class="page-title-wrapper">
			<div class="page-title-heading">
				<div class="page-title-icon">
					<i class="pe-7s-config icon-gradient bg-strong-bliss"></i>
				</div>
				<div>Menu
					<div class="page-title-subheading">Daftar Semua Menu</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
						<li class="breadcrumb-item">Setting</li>
						<li class="active breadcrumb-item" aria-current="page">Menu</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<div class="main-card mb-3 card">
				<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i> <?= $category->name ?>
					<div class="btn-actions-pane-right actions-icon-btn">
					</div>
				</div>
				<div class="card-body">
					<div class="alert alert-info alert-dismissible fade show">
						# Double klik untuk aktifkan atau nonaktifkan menu
					</div>

					<div class="dd" id="nestable" style="width:100% !important">
						<?php
						if (empty($menus)) {
							echo "<div class=\"box-no-data\">Data Menu tidak ada</div>";
						} else {
							echo $menus;
						}
						?>
					</div>
					<div class="nestable-output"></div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="main-card mb-3 card">
				<div class="card-header">
					<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form <?= $form ?> Menu/Sub
				</div>
				<div class="card-body">
					<div id="infoMessage"><?= $message ?? ''; ?></div>
					<?= get_message('message'); ?>
					<form id="frm_create_menu" class="col-md-12 mx-auto" method="post" action="<?= $action; ?>">
						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group">
									<label for="name">Jenis</label>
									<div>
										<div class="custom-radio custom-control custom-control-inline">
											<input type="radio" name="type" id="type_menu" value="menu" class="custom-control-input" <?= ($menu->type ?? 'menu') == 'menu' ? 'checked' : '' ?>>
											<label class="custom-control-label" for="type_menu">Menu</label>
										</div>
										<div class="custom-radio custom-control custom-control-inline">
											<input type="radio" name="type" id="type_label" value="label" class="custom-control-input" <?= ($menu->type ?? 'menu') == 'label' ? 'checked' : '' ?>>
											<label class="custom-control-label" for="type_label">Label</label>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="parent">Parent Menu</label>
									<div>
										<select class="form-control" name="parent" id="parent" tabindex="-1" aria-hidden="true">
											<option value="0">&nbsp;Blank</option>
											<?= $parent_menus ?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="name">Nama Menu/Sub*</label>
									<div>
										<input type="text" class="form-control" id="name" name="name" placeholder="" value="<?= set_value('name', $menu->name ?? ''); ?>" required />
									</div>
								</div>
							</div>
							<div class="col-md-6 menu">
								<div class="position-relative form-group">
									<label for="name">Route</label>
									<div>
										<input type="text" class="form-control" id="frm_create_controller" name="controller" placeholder="" value="<?= set_value('controller', $menu->controller ?? '') ?>" />
										<small class="info help-block text-muted">Contoh: {route}/index</small>
									</div>
								</div>
							</div>
							<div class="col-md-6 menu">
								<div class="position-relative form-group">
									<label for="name">Icon</label>
									<div>
										<input type="text" class="form-control" id="frm_create_icon" name="icon" placeholder="" value="<?= set_value('icon', $menu->icon ?? '') ?>" />
										<small class="info help-block text-muted">Contoh: <i class="fa fa-list"></i> fa fa-list</small>
									</div>
								</div>
							</div>
							<div class="col-md-12 menu">
								<div class="position-relative form-group">
									<label for="permission">Permission*</label>
									<div>
										<div class="form-row">
											<?php foreach ($permissions as $key => $permission) : ?>
												<div class="form-group col" style="display: inline-block">
													<div>
														<label for="<?= $permission ?>"><?= ucfirst($permission); ?> </label><br>
														<input type="checkbox" name="permission[<?= $permission ?>]" id="<?= $permission ?>" data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="normal" <?= ($permission == 'access') ? 'checked' : (in_array($permission, $menu_permissions) ? 'checked' : '') ?>>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								</div>
							</div>
							<?php if (!empty($menu_id)) : ?>
								<div class="col-md-12">
									<div class="form-group">
										<label for="description">Slug</label>
										<div>
											<input type="text" class="form-control" id="frm_edit_slug" name="form_slug" placeholder="" value="<?= set_value('form_slug', $menu->slug ?? '') ?>" required />
											<small class="info help-block text-muted">Contoh: {route}-{unique}</small>
										</div>
									</div>
								</div>
							<?php endif; ?>
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
<?= $this->include('Menu\Views\category_add_modal'); ?>
<?= $this->include('Menu\Views\category_update_modal'); ?>
<script src="<?= base_url('assets/vendors'); ?>/nestable/jquery.nestable.js"></script>
<script>
	$(".apply-fullscreen-status").on('change', function() {
		var switchStatus = $(this).is(':checked');

		if (switchStatus) {
			window.location.href = '<?= base_url('menu?slug=' . $slug . '&show-category=0') ?>';
		} else {
			window.location.href = '<?= base_url('menu?slug=' . $slug . '&show-category=1') ?>';
		}
	});

	$(document).ready(function() {
		$('.clickable').on('click', function() {
			var href = $(this).attr('data-href');
			window.location.href = href;
			return false;
		});

		$('#nestable, #categories').on('click', '.remove-data', function() {
			var url = $(this).attr('data-href');
			Swal.fire({
				title: '<?= lang('App.swal.are_you_sure') ?>',
				text: "<?= lang('App.swal.can_not_be_restored') ?>",
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#dd6b55',
				confirmButtonText: '<?= lang('App.btn.yes') ?>',
				cancelButtonText: '<?= lang('App.btn.no') ?>'
			}).then((result) => {
				if (result.value) {
					window.location.href = url;
				}
			});
			return false;
		});

		$('#categories').on('click', '.show-data', function() {
			var url = $(this).attr('data-href');
			$.ajax({
				url: url,
				type: 'get',
				dataType: 'json',
				success: function(response) {
					$('#frm_edit').attr("data-id", response.id);
					$('#frm_edit_name').val(response.name);
					$('#frm_edit_sort').val(response.sort);
					$('#frm_edit_description').val(response.description);
					$('#frm_edit_slug').val(response.slug);

					$('#modal_edit').modal('show');
				}
			});
		});

		function updateOrderMenu(ignoreMessage) {
			$('.loading').removeClass('loading-hide');
			var shownotif = true;
			var menu = $('.dd').nestable('serialize');

			if (typeof shownotif == 'undefined') {
				var shownotif = true;
			}

			if (typeof ignoreMessage == 'undefined') {
				var ignoreMessage = false;
			}

			// console.log(menu);
			$.ajax({
					url: BASE_URL + '/api/menu/save_ordering',
					type: 'POST',
					dataType: 'JSON',
					data: {
						'menu': menu,
					},
				})
				.done(function(data) {
					console.log(data);
					if (data.status === 201) {
						if (shownotif) {
							if (!ignoreMessage) {
								toastr['success'](data.messages.success);
							}
						}
					} else {
						if (shownotif) {
							if (!ignoreMessage) {
								toastr['error']('Menu gagal disimpan');
							}
						}
					}
				})
				.fail(function(data) {
					if (!ignoreMessage) {
						toastr['error']('Menu gagal disimpan');
					}
				});
		}

		function setMenuActive(id, status) {
			var data = [];

			data.push({
				name: 'id',
				value: id
			});

			data.push({
				name: 'status',
				value: status
			});

			$.ajax({
					url: BASE_URL + '/api/menu/set_status',
					type: 'POST',
					dataType: 'JSON',
					data: data,
				})
				.done(function(data) {
					console.log(data);
					if (data.status === 201) {
						toastr['success'](data.messages.success);
						updateOrderMenu(true)
					} else {
						toastr['error']('Menu gagal disimpan');
					}
				})
				.fail(function(data) {
					toastr['error']('Menu gagal disimpan');
				});
		}

		var BASE_URL = '<?= base_url() ?>';
		var timeout;

		$('#nestable').nestable({
			group: 1,
			maxDepth: 10,
		}).nestable('expandAll');

		$('.dd').on('change', function() {
			clearTimeout(timeout);
			timeout = setTimeout(updateOrderMenu, 1000);
		});

		$('.menu-toggle-activate').dblclick(function(event) {
			event.stopPropagation();
			var status = $(this).data('status');
			var id = $(this).data('id');

			switch (status) {
				case undefined:
				case 0:
					$(this).removeClass('menu-toggle-activate_inactive');
					$(this).data('status', 1)
					setMenuActive(id, 1);
					break;
				case 1:
					$(this).addClass('menu-toggle-activate_inactive');
					$(this).data('status', 0)
					setMenuActive(id, 0);
					break;
			}
		});
	});
</script>
<script>
	$(document).ready(function() {
		var type = "<?= $type ?>";
		var parent_id = '<?= $parent_id ?>';
		$('#name').focus();
		$('#parent').val(parent_id);
		$('.menu').show();
		if (type == 'label') {
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