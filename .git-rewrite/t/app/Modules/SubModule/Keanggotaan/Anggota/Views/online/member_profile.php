<?php
$member_type = get_member_type($member->JenisAnggota_id);
?>
<div class="card-shadow-dark profile-responsive card-border mb-2 card">
	<div class="dropdown-menu-header">
		<div class="dropdown-menu-header-inner bg-corporate-primary2">
			<div class="menu-header-image" style="background-image: url('<?= base_url('themes/uigniter') ?>/images/dropdown-header/abstract8.jpg')"></div>
			<div class="menu-header-content btn-pane-right">
				<div class="avatar-icon-wrapper mr-2 avatar-icon-xl">
					<div class="avatar-icon">
						<?php
						$default = base_url('uploads/default/no_cover.jpg');
						$image = $default;

						// $image = base_url('uploads/user/'.$user->avatar);
						// if(empty($user->avatar)){
						//     $image = $default;
						// }
						?>

						<img src="<?= $image ?>" onerror="this.onerror=null;this.src='<?= $default ?>';" alt="User Profile">
					</div>
				</div>
				<div>
					<h5 class="menu-header-title"><?= $member->name ?? 'Nama Anggota' ?></h5>
					<h6 class="menu-header-subtitle"><?= $member->MemberNo ?? 'Nomor Anggota' ?></p>
				</div>
				<div class="menu-header-btn-pane">

				</div>
			</div>
		</div>
	</div>
	<ul class="list-group list-group-flush">
		<li class="list-group-item">
			<div class="widget-content p-0">
				<div class="widget-content-wrapper">
					<div class="widget-content-left mr-3">
						<i class="fa fa-envelope"></i>
					</div>
					<div class="widget-content-left">
						<div class="widget-heading"><?= $member->Email ?? '-' ?></div>
					</div>
					<div class="widget-content-right">

					</div>
				</div>
			</div>
		</li>
		<li class="list-group-item">
			<div class="widget-content p-0">
				<div class="widget-content-wrapper">
					<div class="widget-content-left mr-3">
						<i class="fa fa-calendar"></i>
					</div>
					<div class="widget-content-left">
						<div class="widget-heading"><?= substr($member->EndDate, 0, 10) ?? '-' ?></div>
					</div>
					<div class="widget-content-right">
						<a href="<?= base_url('anggota/extend/' . $member_no) ?>" class="btn btn-sm btn-warning">Perpanjang Anggota</a>
					</div>
				</div>
			</div>
		</li>
		<li class="list-group-item">
			<div class="widget-content p-0">
				<div class="widget-content-wrapper">
					<div class="widget-content-left mr-3">
						<i class="fa fa-id-card"></i>
					</div>
					<div class="widget-content-left">
						<div class="widget-heading"><?= $member_type->name ?? '-' ?></div>
					</div>
					<div class="widget-content-right">
						<small class="text-muted">Jenis Anggota</small>
					</div>
				</div>
			</div>
		</li>
		<li class="list-group-item">
			<div class="widget-content p-0">
				<div class="widget-content-wrapper">
					<div class="widget-content-left mr-3">
						<i class="fa fa-calendar-check"></i>
					</div>
					<div class="widget-content-left">
						<div class="widget-heading"><?= $member_type->max_loan_days ?? '-' ?> Hari</div>
					</div>
					<div class="widget-content-right">
						<small class="text-muted">Maks Lama Peminjaman</small>
					</div>
				</div>
			</div>
		</li>
		<li class="list-group-item">
			<div class="widget-content p-0">
				<div class="widget-content-wrapper">
					<div class="widget-content-left mr-3">
						<i class="fa fa-calendar-check"></i>
					</div>
					<div class="widget-content-left">
						<div class="widget-heading"><?= $member_type->max_loan_attempt ?? '-' ?> Kali</div>
					</div>
					<div class="widget-content-right">
						<small class="text-muted">Maks Jumlah Peminjaman</small>
					</div>
				</div>
			</div>
		</li>
		<li class="list-group-item">
			<div class="widget-content p-0">
				<div class="widget-content-wrapper">
					<div class="widget-content-left mr-3">
						<i class="fa fa-calendar-check"></i>
					</div>
					<div class="widget-content-left">
						<div class="widget-heading"><?= $member_type->extend_days ?? '-' ?> Hari</div>
					</div>
					<div class="widget-content-right">
						<small class="text-muted">Maks Perpanjangan</small>
					</div>
				</div>
			</div>
		</li>
		<li class="list-group-item">
			<div class="widget-content p-0">
				<div class="widget-content-wrapper">
					<div class="widget-content-left mr-3">
						<i class="fa fa-credit-card"></i>
					</div>
					<div class="widget-content-left">
						<div class="widget-heading"><?= $member_type->charge_amount ?? '-' ?> </div>
					</div>
					<div class="widget-content-right">
						<small class="text-muted">Denda Terlambat / <?= $member_type->charge_unit ?? '-' ?></small>
					</div>
				</div>
			</div>
		</li>
	</ul>
</div>

<?php if (!empty($member->Location_loan_ids)): ?>
	<?php foreach (get_ref_table('m_lokasiperpustakaan', ['id', 'name', 'alamat'], 'id in (' . $member->Location_loan_ids . ')', 'data') as $row): ?>
		<div class="mb-2 card">
			<div class="card-body">
				<div class="widget-content p-0">
					<div class="widget-content-wrapper">
						<div class="widget-content-left mr-3">
							<i class="fa fa-map-marker"></i>
						</div>
						<div class="widget-content-left">
							<div class="widget-heading"><?= $row->name ?></div>
						</div>
						<div class="widget-content-right">
						</div>
					</div>
					<p class="mt-1">
						<?= $row->alamat ?>
					</p>
					<!-- <p class="mt-3">
						<a href="https://maps.google.com/?q=" target="_blank" title="Lihat Google Maps" class="btn btn-sm btn-secondary" style="min-width:35px"><i class="fa fa-map"> </i> Google Maps</a> 
					</p> -->
				</div>
			</div>
		</div>
	<?php endforeach; ?>
<?php endif; ?>