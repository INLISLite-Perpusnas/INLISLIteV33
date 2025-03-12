<?php
$request = service('request');
helper('menu');
?>
<div class="app-sidebar <?= get_parameter('sidebar-cs-class'); ?>">
	<div class="app-header__logo <?= get_parameter('sidebar-cs-class'); ?>">
		<div class="logo-src">
			<?php if (get_parameter('show-logo-sidebar') == 0) : ?>
				<div class="site-name">
					<a style="text-decoration: none; padding-right:9px; padding-bottom:3px;" href="<?= base_url() ?>" class="<?= get_parameter('text-cs-class', 'text-white'); ?>"><?= get_parameter('site-name', 'Backoffice') ?> </a>
				</div>
			<?php endif; ?>
		</div>
		<div class="header__pane ml-auto">
			<div>
				<button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
					<span class="hamburger-box">
						<span class="hamburger-inner"></span>
					</span>
				</button>
			</div>
		</div>
	</div>
	<div class="app-header__mobile-menu">
		<div>
			<button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
				<span class="hamburger-box">
					<span class="hamburger-inner"></span>
				</span>
			</button>
		</div>
	</div>
	<div class="app-header__menu">
		<span>
			<button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
				<span class="btn-icon-wrapper">
					<i class="fa fa-ellipsis-v fa-w-6"></i>
				</span>
			</button>
		</span>
	</div>
	<div class="scrollbar-sidebar">
		<div class="app-sidebar__inner">
			<?php if (get_parameter('branch') == '1') : ?>
				<div class="user-profile" style="text-transform: none !important;">
					<div class="text-center info pt-3">
						<ul class="list-group">
							<a href="javascript:void(0);" class="list-group-item-secondary list-group-item">
								<div class="text-center image pt-3 mb-2">
									<?php
									$default = base_url('themes/uigniter/images/avatars/2.jpg');
									$image = base_url('uploads/user/' . user()->avatar);
									if (empty(user()->avatar)) {
										$image = $default;
									}
									?>

									<img width="100" class="rounded-circle" src="<?= $image ?>" onerror="this.onerror=null;this.src='<?= $default ?>';" alt="">
								</div>

								<p class="font-weight-bold"><?= user()->first_name . ' ' . user()->last_name  ?></p>
							</a>
							<a href="<?= base_url('user/profile') ?>" class="list-group-item-info list-group-item">
								Profil Saya
							</a>
							<a href="<?= base_url('user/change_password') ?>" class="list-group-item-info list-group-item">
								Ubah Password
							</a>
							<a href="<?= base_url('logout') ?>" class="list-group-item-info list-group-item">
								Logout
							</a>
						</ul>
					</div>
				</div>
			<?php endif; ?>

			<ul class="vertical-nav-menu">
				<?php set_parameter('sidebar-mode', 'auto'); ?>
				<?php if (get_parameter('sidebar-mode') == 'auto') : ?>
					<?php
					$display_menu_backend = display_menu_backend(0, 1);
					echo $display_menu_backend;
					?>
				<?php else : ?>
					<?php include 'navigation.php'; ?>
					<?php foreach ($menuData as $menuItem) : ?>
						<?php if ($menuItem['heading'] ?? false == true) : ?>
							<li class="app-sidebar__heading"><?= $menuItem['title'] ?></li>
						<?php else : ?>
							<li class="<?= $menuItem['active'] ? 'mm-active' : '' ?>">
								<a href="<?= (isset($menuItem['url']) && ($menuItem['url'] != '#')) ? base_url($menuItem['url']) : '#' ?>" class="">
									<i class="metismenu-icon <?= $menuItem['icon'] ?? 'pe-7s-angle-right-circle' ?>" style=""></i>
									<?= $menuItem['title'] ?> <?= (isset($menuItem['subMenu'])) ? '<i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>' : '' ?>
								</a>
								<?php if (isset($menuItem['subMenu'])) : ?>
									<ul class="mm-collapse">
										<?php foreach ($menuItem['subMenu'] as $subMenuItem) : ?>
											<li class="<?= $subMenuItem['active'] ? 'mm-active' : '' ?>">
												<a href="<?= (isset($subMenuItem['url']) && ($subMenuItem['url'] != '#')) ? base_url($subMenuItem['url']) : '#' ?>" class="">
													<i class="metismenu-icon <?= $subMenuItem['icon'] ?? 'pe-7s-angle-right-circle' ?>" style=""></i>
													<?= $subMenuItem['title'] ?> <?= (isset($subMenuItem['subMenu'])) ? '<i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>' : '' ?>
												</a>
												<?php if (isset($subMenuItem['subMenu'])) : ?>
													<ul class="mm-collapse">
														<?php foreach ($subMenuItem['subMenu'] as $subSubMenuItem) : ?>
															<li class="<?= $subSubMenuItem['active'] ? 'mm-active' : '' ?>">
																<a href="<?= (isset($subSubMenuItem['url']) && ($subSubMenuItem['url'] != '#')) ? base_url($subSubMenuItem['url']) : '#' ?>" class="">
																	<i class="metismenu-icon <?= $subSubMenuItem['icon'] ?? 'pe-7s-angle-right-circle' ?>" style=""></i>
																	<?= $subSubMenuItem['title'] ?>
																</a>
															</li>
														<?php endforeach; ?>
													</ul>
												<?php endif; ?>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</li>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>