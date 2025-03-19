<?php

$header_title = "";
$header_sub_title = "";
$header_logo = base_url('perpusnas.png');
$branch_title = "INLISLite Backoffice";

if (!empty(branch_id())) {
	$branchModel = new \NamaPerpustakaan\Models\BranchModel();
	$branch = $branchModel->find(branch_id());
	$header_title = $branch->Name ?? "Perpustakaan Nasional";
	$header_sub_title = ($branch->Address ?? "") . " | Email: " . ($branch->Email ?? "") . " | Telp." . ($branch->Phone ?? "");
	if (!empty($branch->Logo)) {
		$header_logo = base_url('uploads/branch/' . $branch->Logo);
	}
	$branch_title = "NPP: " . $branch->Code;
} else {
	$header_title = "Perpustakaan Nasional";
	$header_sub_title = "Jl. Medan Merdeka No. 1, Jakarta 10110";

	$npp_provinsi_id = user()->npp_provinsi_id;
	if (!empty($npp_provinsi_id)) {
		$regionModel = new \Region\Models\RegionModel();
		$region = $regionModel->where('code', user()->npp_provinsi_id)->where('level', 1)->first();
		if (!empty($region)) {
			$header_title = "PROVINSI : " . $region->name;
			$header_sub_title = "";
		}
	}

	$npp_kabkota_id = user()->npp_kabkota_id;
	if (!empty($npp_kabkota_id)) {
		$regionModel = new \Region\Models\RegionModel();
		$region = $regionModel->where('code', user()->npp_kabkota_id)->where('level', 2)->first();
		if (!empty($region)) {
			$header_sub_title = "KABUPATEN / KOTA : " . $region->name;
		}
	}
}
?>

<div class="app-header <?= get_parameter('header-cs-class'); ?>">
	<div class="app-header__logo <?= get_parameter('sidebar-cs-class'); ?>">
		<div class="logo-src">
			<div class="site-name">
				<a style="text-decoration: none; font-weight: normal" href="<?= base_url() ?>" class="<?= get_parameter('text-cs-class', 'text-white'); ?>">
					<?= $branch_title ?>
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
							<span><?= $header_title ?></span>
						</div>
						<div class="widget-subheading" style="opacity: 0.9">
							<?= $header_sub_title ?>
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