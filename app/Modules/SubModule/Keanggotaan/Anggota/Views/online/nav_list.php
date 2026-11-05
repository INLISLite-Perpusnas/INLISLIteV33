<?php
$navItems = [
	'profile'   => ['label' => 'Profil Saya', 'icon' => 'fa-user'],
	'loan'      => ['label' => 'Daftar Peminjaman', 'icon' => 'fa-book'],
	'violation' => ['label' => 'Daftar Pelanggaran', 'icon' => 'fa-exclamation-triangle'],
	'reserve'   => ['label' => 'Daftar Pemesanan', 'icon' => 'fa-shopping-cart'],
	'request'   => ['label' => 'Daftar Usulan', 'icon' => 'fa-bookmark'],
];
?>
<style>
	/* Button Group INA Digital, varian Single Selection (design.inadigital.go.id/components/button-group/guide) */
	.ina-button-group-wrap {
		margin-bottom: 24px;
		overflow-x: auto;
		-webkit-overflow-scrolling: touch;
	}

	.ina-button-group {
		display: inline-flex;
		min-width: max-content;
		background: var(--ina-surface, #fff);
		border: 1px solid #d1d5db;
		border-radius: 8px;
	}

	.ina-button-group a {
		display: inline-flex;
		align-items: center;
		gap: 8px;
		min-height: 44px;
		padding: 10px 16px;
		font-size: 15px;
		font-weight: 500;
		line-height: 1.3;
		white-space: nowrap;
		color: var(--ina-text-subtle, #374151);
		text-decoration: none;
		transition: background-color .15s ease, color .15s ease;
	}

	.ina-button-group a + a {
		border-left: 1px solid #d1d5db;
	}

	.ina-button-group a:first-child {
		border-radius: 7px 0 0 7px;
	}

	.ina-button-group a:last-child {
		border-radius: 0 7px 7px 0;
	}

	.ina-button-group a:hover {
		color: var(--ina-text, #1f2937);
		background: var(--ina-hover, #f3f4f6);
	}

	.ina-button-group a:focus-visible {
		position: relative;
		z-index: 1;
		outline: 2px solid var(--ina-primary, #1d5fd1);
		outline-offset: 2px;
	}

	.ina-button-group a.active {
		font-weight: 600;
		color: var(--ina-primary, #1d5fd1);
		background: var(--ina-primary-subtle, #eef4ff);
	}
</style>
<div class="ina-button-group-wrap">
	<nav class="ina-button-group" aria-label="Menu keanggotaan">
		<?php foreach ($navItems as $navSlug => $navItem) : ?>
			<a class="<?= $slug === $navSlug ? 'active' : '' ?>" href="<?= base_url('anggota/online?slug=' . $navSlug) ?>" <?= $slug === $navSlug ? 'aria-current="page"' : '' ?>>
				<i class="fa <?= $navItem['icon'] ?>" aria-hidden="true"></i>
				<?= esc($navItem['label']) ?>
			</a>
		<?php endforeach; ?>
	</nav>
</div>
