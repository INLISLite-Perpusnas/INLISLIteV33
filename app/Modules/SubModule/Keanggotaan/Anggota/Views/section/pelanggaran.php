<table style="width: 100%;" id="tbl_sumbangan" class="table table-hover table-striped table-bordered">
	<thead>
		<tr>
			<th>No.</th>
			<th>No. Barcode</th>
			<th>Judul</th>
			<th>Jenis pelanggaran</th>
			<th>Sanksi</th>
			<th>Jumlah Denda</th>
			<th>Jumlah Suspend</th>
			<th>Tanggal Denda</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach (get_pelanggaran($anggota->ID) as $row): ?>
			<tr>
				<td width="35"></td>
				<td>
					<?= $row->barcode_no ?>
				</td>
				<td>
					<?= $row->title ?>
				</td>
				<td>
					<?= $row->jenis_pelanggaran ?>
				</td>
				<td>
					<?= $row->jenis_denda ?>
				</td>
				<td>
					<?= 'Rp ' . number_format($row->JumlahDenda, 0, ',', '.') ?>
				</td>
				<td>
					<?= $row->JumlahSuspend ?>
				</td>
				<td>
					<?= date('d F Y', strtotime($row->CreateDate)) ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>