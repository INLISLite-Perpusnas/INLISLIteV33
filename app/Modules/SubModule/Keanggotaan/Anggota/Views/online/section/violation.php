<div class="mb-3 card">
	<div class="card-header-tab card-header">
		<div class="card-header-title">
			<i class="header-icon lnr-enter icon-gradient bg-success"> </i>
			Daftar Peminjaman
		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table style="width: 100%;" id="tbl_violations" class="table table-hover table-striped table-bordered">
				<thead class="bg-night-sky text-light">
					<tr>
						<th class="text-center">
							No.
						</th>
						<th>No. Barcode</th>
						<th>Judul</th>
						<th>Penerbitan</th>
						<th>Tanggal Peminjaman</th>
						<th>Jatuh Tempo</th>
						<th>Jumlah Denda</th>
					</tr>
				</thead>
				<tbody>
					<?php $no = 1; ?>
					<?php foreach ($pelanggaran as $row) : ?>
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
							<td><?= _spec($row->JumlahDenda) ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>

			</table>
		</div>
	</div>
</div>