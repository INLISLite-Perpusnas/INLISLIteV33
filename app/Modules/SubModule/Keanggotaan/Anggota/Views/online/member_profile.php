<?php
$member_type = get_member_type($member->JenisAnggota_id);
$photo = !empty($member->PhotoUrl) ? base_url('uploads/anggota/' . rawurlencode($member->PhotoUrl)) : '';
$rows = [
	['icon' => 'fa-envelope', 'label' => 'Email', 'value' => $member->Email ?: '-'],
	['icon' => 'fa-id-card', 'label' => 'Jenis Anggota', 'value' => $member_type->jenisanggota ?? '-'],
	['icon' => 'fa-calendar-check', 'label' => 'Maks Lama Peminjaman', 'value' => (int) ($member_type->MaxLoanDays ?? 0) . ' Hari'],
	['icon' => 'fa-book', 'label' => 'Maks Jumlah Peminjaman', 'value' => (int) ($member_type->MaxPinjamKoleksi ?? 0) . ' Koleksi'],
	['icon' => 'fa-calendar-plus', 'label' => 'Maks Perpanjangan', 'value' => (int) ($member_type->DayPerpanjang ?? 0) . ' Hari'],
	['icon' => 'fa-credit-card', 'label' => 'Denda Terlambat', 'value' => 'Rp ' . number_format((float) ($member_type->DendaPerTenor ?? 0), 0, ',', '.') . ' / ' . ($member_type->DendaTenorSatuan ?? 'Hari')],
];
?>
<style>
	.member-card {
		margin-bottom: 16px;
		overflow: hidden;
		background: var(--ina-surface, #fff);
		border: 1px solid var(--ina-border, #e5e7eb);
		border-radius: var(--ina-radius, 12px);
		box-shadow: var(--ina-shadow, 0 1px 3px rgba(16, 24, 40, .08));
	}

	.member-card-head {
		padding: 24px 16px 20px;
		text-align: center;
		border-bottom: 1px solid var(--ina-border, #e5e7eb);
	}

	.member-card-avatar {
		display: flex;
		align-items: center;
		justify-content: center;
		width: 88px;
		height: 88px;
		margin: 0 auto 12px;
		overflow: hidden;
		font-size: 32px;
		color: var(--ina-primary, #1d5fd1);
		background: var(--ina-primary-subtle, #eef4ff);
		border-radius: 50%;
	}

	.member-card-avatar img {
		width: 100%;
		height: 100%;
		object-fit: cover;
	}

	.member-card-name {
		margin: 0;
		font-size: 18px;
		font-weight: 600;
		line-height: 26px;
		color: var(--ina-text, #1f2937);
		overflow-wrap: anywhere;
	}

	.member-card-no {
		margin: 2px 0 0;
		font-size: 14px;
		color: var(--ina-text-muted, #6b7280);
		overflow-wrap: anywhere;
	}

	.member-card-list {
		padding: 0;
		margin: 0;
		list-style: none;
	}

	.member-card-list li {
		display: flex;
		align-items: flex-start;
		gap: 12px;
		padding: 12px 16px;
		border-bottom: 1px solid var(--ina-border, #e5e7eb);
	}

	.member-card-list li:last-child {
		border-bottom: 0;
	}

	.member-card-list i {
		flex-shrink: 0;
		width: 20px;
		margin-top: 3px;
		text-align: center;
		color: var(--ina-icon, #4b5563);
	}

	.member-card-text {
		flex: 1;
		min-width: 0;
	}

	.member-card-label {
		display: block;
		font-size: 13px;
		line-height: 18px;
		color: var(--ina-text-muted, #6b7280);
	}

	.member-card-value {
		display: block;
		font-size: 15px;
		font-weight: 600;
		line-height: 22px;
		color: var(--ina-text, #1f2937);
		overflow-wrap: anywhere;
	}

</style>
<div class="member-card">
	<div class="member-card-head">
		<div class="member-card-avatar">
			<?php if ($photo !== '') : ?>
				<img src="<?= esc($photo, 'attr') ?>" alt="Foto <?= esc(trim((string) $member->Fullname), 'attr') ?>" onerror="this.outerHTML='<i class=&quot;fa fa-user&quot; aria-hidden=&quot;true&quot;></i>'">
			<?php else : ?>
				<i class="fa fa-user" aria-hidden="true"></i>
			<?php endif; ?>
		</div>
		<h2 class="member-card-name"><?= esc(trim((string) $member->Fullname) ?: '-') ?></h2>
		<p class="member-card-no"><?= esc($member->MemberNo ?? '-') ?></p>
	</div>
	<ul class="member-card-list">
		<li>
			<i class="fa fa-calendar-alt" aria-hidden="true"></i>
			<div class="member-card-text">
				<span class="member-card-label">Masa Berlaku</span>
				<span class="member-card-value"><?= !empty($member->EndDate) ? esc(date('d-m-Y', strtotime($member->EndDate))) : '-' ?></span>
			</div>
		</li>
		<?php foreach ($rows as $row) : ?>
			<li>
				<i class="fa <?= $row['icon'] ?>" aria-hidden="true"></i>
				<div class="member-card-text">
					<span class="member-card-label"><?= esc($row['label']) ?></span>
					<span class="member-card-value"><?= esc($row['value']) ?></span>
				</div>
			</li>
		<?php endforeach; ?>
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