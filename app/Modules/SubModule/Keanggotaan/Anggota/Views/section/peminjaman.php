<table style="width: 100%;" id="tbl_sumbangan" class="table table-hover table-striped table-bordered">
	<thead>
		<tr>
			<th>No.</th>
			<th>No. Peminjaman</th>
			<th>No. Item</th>
			<th>Judul</th>
			<th>Status</th>
			<th>Tgl. Pinjam</th>
			<th>Tgl. Harus Kembali</th>
			<th>Tgl. Kembali</th>
			<th>Hari Terlambat</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach (get_peminjaman($anggota->ID) as $row) : ?>
		<tr>
			<td width="35"></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>