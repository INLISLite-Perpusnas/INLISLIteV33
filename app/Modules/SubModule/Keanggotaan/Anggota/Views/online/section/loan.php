<div class="mb-3 card">
	<div class="card-header-tab card-header">
		<div class="card-header-title">
			<i class="header-icon lnr-enter icon-gradient bg-success"> </i>
			Daftar Peminjaman
		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table style="width: 100%;" id="tbl_loans" class="table table-hover table-striped table-bordered">
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
						<th>Buku Digital</th>
					</tr>
				</thead>
				<tbody>
					<?php $no = 1; ?>
					<?php foreach ($peminjaman as $row) : ?>
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
							<td width="120">
								<?php if (!empty($row->is_digital)) : ?>
									<span class="badge bg-success mb-1">Ya</span>
									<?php if ($row->LoanStatus == 'Loan' && !empty($row->catalog_id)) : ?>
										<br>
										<a href="<?= base_url('opac/baca-digital/' . $row->catalog_id) ?>" target="_blank" class="btn btn-primary btn-sm mt-1">
											<i class="fa fa-file-pdf"></i> Baca PDF
										</a>
									<?php endif; ?>
								<?php else : ?>
									<span class="badge bg-secondary">Tidak</span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>

			</table>
		</div>
	</div>
</div>