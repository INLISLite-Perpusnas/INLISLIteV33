
<div class="mb-3 card">
	<div class="card-header-tab card-header">
		<div class="card-header-title">
			<i class="header-icon lnr-cart icon-gradient bg-success"> </i>
			Daftar Pemesanan
		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table style="width: 100%;" id="tbl_reserves" class="table table-hover table-striped table-bordered">
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
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($peminjaman as $row) : ?>
						<tr>
							<td class="text-center" width="5">

							</td>
							<td width="100">
								<?= _spec($row->barcode_no); ?> <br>
							</td>
							<td width="400"><?= _spec($row->title); ?></td>
							<td><?= _spec($row->publisher); ?></td>
							<td width="100"><?= _spec($row->loan_date); ?></td>
							<td width="100"><?= _spec($row->due_date); ?></td>
							<td width="35">

							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>