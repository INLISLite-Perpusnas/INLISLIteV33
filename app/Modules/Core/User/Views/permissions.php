<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
	.card-columns {
		column-count: 4;
		column-gap: 4px;
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
				<div>Permission - User <b><?= $user->first_name ?? '' ?> <?= $user->last_name ?? '' ?></b>
					<div class="page-title-subheading">Daftar Semua Permission</div>
				</div>
			</div>
			<div class="page-title-actions">
				<nav class="" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i>Home</a></li>
						<li class="breadcrumb-item">Otorisasi</li>
						<li class="breadcrumb-item">User</li>
						<li class="active breadcrumb-item" aria-current="page">Permission</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<form id="frm_create" method="post" action="<?= base_url('user/permissions/' . $user->id) ?>" onsubmit="return validateForm()">
		<div class="row mb-3">
			<div class="col-md-12">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="btn bg-info text-white pt-2">Permission :</span>
					</div>
					<input type="text" name="keyword" id="keyword" class="form-control" placeholder="Kata kunci...">
					<div class="input-group-append">
						<button type="button" id="btnCheckAll" class="btn btn-outline-primary pt-2"><i class="fa fa-check-square mr-1"></i> Check All</button>
					</div>
					<div class="input-group-append">
						<button type="button" id="btnUncheckAll" class="btn btn-outline-primary pt-2""><i class=" fa fa-square mr-1"></i> Uncheck All</button>
					</div>
					<div class="input-group-append">
						<button type="button" id="btnReset" class="btn btn-outline-primary pt-2"><i class="fa fa-history mr-1"></i> Reset</button>
					</div>
					<div class="input-group-append">
						<button type="submit" class="btn btn-primary pt-2"><i class="fa fa-save mr-1"></i> Save</button>
					</div>
				</div>
			</div>
		</div>
		<div class="card-columns">
			<?php
			$menu_permissions = [];
			foreach ($permissions as $row) { // bikinin menu kartegori + group by kategori
				$menu_permissions[$row->menu][] = $row;
			}
			?>
			<?php foreach (array_keys($menu_permissions) as $menu) : ?>
				<div class="card mb-1 parent-menu" data-menu="<?= strtolower($menu); ?>">
					<div class="card-header"><?= $menu ?></div>
					<div class="card-body">
						<?php foreach ($menu_permissions[$menu] as $row) : ?>
							<div class="widget-content p-0">
								<div class="widget-content-wrapper mb-1">
									<div class="widget-content-left mr-3">
										<div class="switch has-switch switch-container-class" data-class="permission">
											<div class="switch-animate switch-on">
												<input type="checkbox" name="permissions[<?= $row->id; ?>][]" id="<?= $row->id; ?>" <?= (in_array($row->id, $permissions_users)) ? 'checked' : '' ?> data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="mini">
											</div>
										</div>
									</div>
									<div class="widget-content-left">
										<div class="widget-heading">
											<?= _spec($row->name); ?>
										</div>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</form>
</div>
<?= $this->endSection('page'); ?>
<?= $this->section('script'); ?>
<script>
	$('#keyword').keyup(function() {
		var keyword = $(this).val().toLowerCase();
		filter(keyword);
	});

	function filter(keyword) {
		$('.parent-menu').each(function() {
			var menu = $(this).data('menu');
			if (menu.includes(keyword)) {
				console.log('menu: ' + menu);
				$(this).addClass('checkAll');
				$(this).show();
			} else {
				$(this).removeClass('checkAll');
				$(this).hide();
			}
		});
	}

	$('#btnReset').click(function() {
		console.log("Button clicked!");
		filter('');
	});

	$('#btnCheckAll').click(function() {
		console.log("Button clicked!");
		var checkboxes = $('.card-columns .checkAll input[type="checkbox"]');
		if (checkboxes.length > 0) {
			checkboxes.each(function() {
				var checkboxId = $(this).attr('id');
				if (!$(this).prop('checked')) {
					$(this).bootstrapToggle('toggle');
					$(this).prop('checked', true);
					$(this).val(checkboxId);
				}
			});
		}
	});

	$('#btnUncheckAll').click(function() {
		console.log("Button clicked!");
		var checkboxes = $('.card-columns .checkAll input[type="checkbox"]');
		if (checkboxes.length > 0) {
			checkboxes.each(function() {
				var checkboxId = $(this).attr('id');
				if ($(this).prop('checked')) {
					$(this).bootstrapToggle('toggle');
					$(this).prop('checked', false);
					$(this).removeAttr('value');
				}
			});
		}
	});
	filter('');
</script>
<?= $this->endSection('script'); ?>