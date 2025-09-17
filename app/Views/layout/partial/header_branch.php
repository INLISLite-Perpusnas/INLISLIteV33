<?php

$header_title = "";
$header_sub_title = "";
$db = db_connect();
 $logo=$db->table('settingparameters')->where('Name', 'Logo')->get()->getRow()->Value?:"Perpustakaan Mitra";

$header_logo =base_url('uploads/branch/') . $logo?? base_url('perpusnas.png');
$branch_title = "INLISLite Backoffice";

$db=db_connect();
$nama_perpustakaan=$db->table('settingparameters')->where('Name', 'NamaPerpustakaan')->get()->getRow()->Value?:"Perpustakaan Mitra";
$npp_perpustakaan=$db->table('settingparameters')->where('Name', 'NPPPerpustakaan')->get()->getRow()->Value?:"NPP Perpustakaan Mitra";
$alamat_perpustakaan=$db->table('settingparameters')->where('Name', 'NamaLokasiPerpustakaan')->get()->getRow()->Value?:"Alamat Perpustakaan Mitra";
?>

<div class="app-header <?= get_parameter('header-cs-class'); ?>">
	<div class="app-header__logo <?= get_parameter('sidebar-cs-class'); ?>">
		<div class="logo-src">
			<div class="site-name">
				<a style="text-decoration: none; font-weight: normal" href="<?= base_url() ?>" class="<?= get_parameter('text-cs-class', 'text-white'); ?>">
					<?= $npp_perpustakaan?>
				</a>
			</div>
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
	<div class="app-header__content">
		<div class="app-header-left">
			<div class="widget-content p-0">
				<div class="widget-content-wrapper">
					<div class="widget-content-left">
						<img height="42" class="rounded" src="<?= $header_logo ?>" alt="INLISLite Logo">
					</div>
					<div class="widget-content-left  ml-3 header-user-info">
						<div class="widget-heading font-weight-bold font-size-24" style="opacity: 1">
							<span><?= $nama_perpustakaan ?></span>
						</div>
						<div class="widget-subheading" style="opacity: 0.9">
							<?= $alamat_perpustakaan?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="app-header-right">
			<div class="app-header-left">
				<div class="widget-content p-0">
					<div class="widget-content-wrapper">
						<div class="widget-heading font-weight-bold font-size-24">
							<span id="day"></span>
						</div>
					</div>
				</div>
			</div>

			<div class="header-btn-lg">
				<div class="widget-content p-0 font-weight-bold font-size-24" style="font-family: monospace, monospace;">
					<span id="clock"></span>
				</div>
			</div>
		</div>
	</div>
</div>