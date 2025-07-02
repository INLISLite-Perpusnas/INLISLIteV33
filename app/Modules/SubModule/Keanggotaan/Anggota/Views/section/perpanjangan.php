<table style="width: 100%;" id="tbl_sumbangan" class="table table-hover table-striped table-bordered">
	<thead>
		<tr>
			<th>No.</th>
			<th>Tanggal Berakhir</th>
			<th>Biaya</th>
			<th>Lunas</th>
		</tr>
	</thead>
	<tbody>
		<?php
		// Siapkan variabel untuk nomor urut
		$no = 1;
		// Loop data dari fungsi
		foreach (get_perpanjangan($anggota->ID) as $row) :
		?>
			<tr>
				<td width="35"><?= $no++ ?>.</td>
				<td><?= date('d-m-Y', strtotime($row->Tanggal)) ?></td>
				<td>Rp <?= number_format($row->Biaya, 0, ',', '.') ?></td>
				<td>
					<?php if ($row->IsLunas == 1): ?>
						<span class="badge bg-success">Lunas</span>
					<?php else: ?>
						<span class="badge bg-danger">Belum Lunas</span>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>