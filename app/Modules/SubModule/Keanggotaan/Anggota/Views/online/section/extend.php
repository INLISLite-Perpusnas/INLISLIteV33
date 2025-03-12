<?php
$request = service('request');
$slug = $request->getGet('slug') ?? 'profile';
$slug_title = ucwords(strtolower($slug));
$member_no = user()->username;
$anggota =  db_get_single('t_anggota', 'MemberNo=' . $member_no);
$member = get_member($member_no);

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
			<i class="header-icon lnr-calendar-full icon-gradient bg-success"> </i>
			Perpanjangan Anggota
		</div>
	</div>
	<div class="card-body">

	</div>
</div>