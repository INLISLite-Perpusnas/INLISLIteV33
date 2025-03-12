<table style="width: 100%;" id="tbl_sumbangan" class="table table-hover table-striped table-bordered">
	<thead>
		<tr>
			<th>No.</th>
			<th>Jumlah Sumbangan </th>
			<th>Jumlah koleksi</th>
			<th>Keterangan</th>
			
		</tr>
	</thead>
	<tbody>
		<?php foreach (get_sumbangan($anggota->id) as $row) : ?>
		<tr>
			<td width="35"></td>
			<td><?=$row->jumlah?></td>
			<td>-</td>
			<td><?=$row->description?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>