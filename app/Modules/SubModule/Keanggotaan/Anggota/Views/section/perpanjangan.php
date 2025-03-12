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
		<?php foreach (get_perpanjangan($anggota->ID) as $row) : ?>
		<tr>
			<td width="35"></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
