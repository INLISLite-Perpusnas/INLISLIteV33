<?php
helper(['parameter']);
$request = service('request');

$fullscreen =  '';
if ($request->getVar('fullscreen') == 1) {
	$fullscreen = 'closed-sidebar';
}
?>

<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link rel="icon" href="<?= base_url(get_parameter('favicon')) ?>">
	<meta http-equiv="Content-Language" content="en">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?= $title ?? get_parameter('site-name'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
	<meta name="msapplication-tap-highlight" content="no">
	<link rel="stylesheet" href="<?= base_url('themes/uigniter'); ?>/css/base.css">
	<?php if (get_parameter('show-logo-sidebar') == '1') : ?>
		<style>
			.app-header.header-text-dark .app-header__logo .logo-src {
				height: 23px;
				width: 97px;
				background: url("<?= base_url() . get_parameter('logo-small'); ?>");
			}

			.app-header.header-text-light .app-header__logo .logo-src {
				height: 23px;
				width: 97px;
				background: url("<?= base_url() . get_parameter('logo-small'); ?>");
			}
		</style>
	<?php else : ?>
		<style>
			.app-header.header-text-dark .app-header__logo .logo-src {
				background: none;
			}

			.app-header.header-text-light .app-header__logo .logo-src {
				background: none;
			}
		</style>
	<?php endif; ?>
	<style>
		.site-name {
			font-size: 18px;
			margin: .75rem 0;
			font-weight: 700;
			color: #fff;
			white-space: nowrap;
			position: relative;
		}
	</style>
	<?= $this->include('App\Views\layout\partial\style'); ?>
	<?= $this->include('App\Views\layout\partial\style_custom'); ?>
	<?= $this->renderSection('style'); ?>
</head>

<body>
	<div class="app-container app-theme-white body-tabs-shadow <?= $fullscreen ?> <?= get_parameter('container-header-class') ?> <?= get_parameter('container-sidebar-class') ?> <?= get_parameter('container-footer-class') ?>">

		<div class="app-header" style="height:80px; background: #336899">
			<div class="app-header__content">
				<div class="main-nav">
					<nav class="navbar navbar-expand-md">
						<a class="navbar-brand" href="<?= base_url() ?>">
							<img src="/uploads/default/favicon.png" style="height:60px; position:relative" alt="Logo">
						</a>
						<div class="collapse navbar-collapse mean-menu text-light" id="navbarSupportedContent">
							<?= $this->renderSection('branch'); ?>
						</div>
					</nav>
				</div>
				<?= $this->renderSection('header'); ?>
			</div>
		</div>

		<div class="app-main mt-3">
			<?= $this->renderSection('page'); ?>
		</div>
	</div>

	<?= $this->include('App\Views\layout\partial\script'); ?>
	<?= $this->include('App\Views\layout\partial\script_custom'); ?>
	<?= $this->renderSection('script'); ?>
</body>

</html>