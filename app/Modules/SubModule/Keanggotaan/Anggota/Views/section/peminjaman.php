<table style="width: 100%;" id="tbl_sumbangan" class="table table-hover table-striped table-bordered">
	<thead>
		<tr>
			<th class="text-center">
				No.
			</th>
			<th>No. Barcode</th>
			<th>Judul</th>
			<th>Penerbitan</th>
			<th>Tanggal Peminjaman</th>
			<th>Jatuh Tempo</th>
		</tr>
	</thead>
	<tbody>
		<?php $no = 1; ?>
		<?php foreach (get_peminjaman($anggota->ID) as $row) : ?>
			<tr>
				<td class="text-center" width="5">
					<?= $no++; ?>
				</td>
				<td width="100">
					<?= _spec($row->barcode_no); ?><br>
				</td>
				<td width="400"><?= _spec($row->title); ?></td>
				<td><?= _spec($row->publisher); ?></td>
				<td width="100"><?= _spec($row->LoanDate); ?></td>
				<td width="100"><?= _spec($row->DueDate); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>