<?php
$request = service('request');
$eksemplarLoanItemModel = new \Sirkulasi\Models\EksemplarLoanItemModel();
$query = $eksemplarLoanItemModel
	->select('t_eksemplar_loan_item.*')
	->select('t_eksemplar.barcode_no, t_eksemplar.register_no, t_eksemplar.rfid, t_eksemplar.price ')
	->select('t_anggota.name as member_name, t_anggota.MemberNo as member_no')
	->select('t_catalog.title, t_catalog.publisher')

	->join('t_eksemplar', 't_eksemplar.id = t_eksemplar_loan_item.eksemplar_id', 'inner')
	->join('t_anggota', 't_anggota.id = t_eksemplar_loan_item.anggota_id', 'inner')
	->join('t_catalog', 't_catalog.id = t_eksemplar.catalog_id', 'inner')
	->where('t_eksemplar_loan_item.loan_status', 'Loan')
	->where('t_anggota.MemberNo', $member_no);

$sirkulasis = $query->findAll();

?>

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
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($sirkulasis as $row) : ?>
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