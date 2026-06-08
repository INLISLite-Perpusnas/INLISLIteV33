<?php 
	$db = db_connect();
	$builder = $db->table('memberloanauthorizelocation mal')
		->join('location_library ll','ll.ID=mal.LocationLoan_id')
		->where('mal.Member_id',$member->ID);
	$query = $builder->get();
	$member_locations = $query->getResult();

	$builder = $db->table('memberloanauthorizecategory mac')
		->join('collectioncategorys cc','cc.ID=mac.CategoryLoan_id')
		->where('mac.Member_id',$member->ID);
	$query = $builder->get();
	$member_categories = $query->getResult();
	helper('date_id');
?>
<?=$this->section('style');?>
<style>
#video {
  border: 1px solid black;
  box-shadow: 2px 2px 3px black;
  width:320px;
  height:240px;
}

#photo {
  border: 1px solid black;
  box-shadow: 2px 2px 3px black;
  width:320px;
  height:240px;
}

#canvas {
  display:none;
}

.camera {
  width: 340px;
  display:inline-block;
}

.output {
  width: 340px;
  display:inline-block;
}

#startbutton {
  display:block;
  position:relative;
  margin-left:auto;
  margin-right:auto;
  bottom:32px;
  background-color: rgba(0, 150, 0, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.7);
  box-shadow: 0px 0px 1px 2px rgba(0, 0, 0, 0.2);
  font-size: 14px;
  font-family: "Lucida Grande", "Arial", sans-serif;
  color: rgba(255, 255, 255, 1.0);
}

.contentarea {
  font-size: 16px;
  font-family: "Lucida Grande", "Arial", sans-serif;
  width: 760px;
}
</style>
<style>
	.card-horizontal {
		display: flex;
		flex: 1 1 auto;
	}
	tr.group,
	tr.group:hover {
		background-color: #F0F3F5 !important;
	}
	dl {
		display: grid;
		grid-template-columns: max-content auto;
	}

	dt {
		grid-column-start: 1;
		width: 100px;
		font-weight: normal;
	}

	dd {
		grid-column-start: 2;
	}

	#nav_profile li a.active{
		font-weight: bold !important;
		color: white !important;
	}
</style>
<?= $this->endSection('style'); ?>

<div class="row">
	<div class="col-md-12">
		<div class="card-shadow-dark profile-responsive card-border mb-3 card">
			<div class="dropdown-menu-header">
				<div class="dropdown-menu-header-inner bg-night-sky text-light">
					<div class="menu-header-image" style="background-image: url('<?= base_url('themes/uigniter') ?>/images/dropdown-header/abstract2.jpg')"></div>
					<div class="menu-header-content btn-pane-right">
						<div class="avatar-icon-wrapper mr-2 avatar-icon-xl">
							<div class="avatar-icon">
							<?php 
								$default = base_url('uploads/default/nophoto.jpg');
								$image = (!empty($member->PhotoUrl)) ? base_url('uploads/anggota/' . $member->PhotoUrl) : $default;
							?>
							</div>
						</div>
						<div>
							<h5 class="menu-header-title"><?= $member->MemberNo; ?></h5>
							<h6 class="menu-header-subtitle"><?=$member->Fullname;?></h6>
						</div>
						<div class="menu-header-btn-pane">
							<ul class="nav" id="nav_profile">
								<li class="nav-item"><a data-toggle="tab" href="#tab1" class="text-light nav-link show active ">Profil Anggota</a></li>
								<li class="nav-item"><a data-toggle="tab" href="#tab2" class="text-light nav-link show">Lokasi Perpustakaan</a></li>
								<li class="nav-item"><a data-toggle="tab" href="#tab3" class="text-light nav-link show">Kategori Koleksi</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="card">
						<div class="card-body">
							<div class="tab-content">
								<div class="tab-pane show active" id="tab1" role="tabpanel">
									<div class="card">
										<div class="card-horizontal">
											<div class="img-square-wrapper">
												<img width="280" src="<?=$image?>" onerror="this.onerror=null;this.src='<?=$default?>';" alt="User Profile">
											</div>
											<div class="card-body">	
												<div class="row">
													<div class="col-lg-6">
														<ul class="list-group list-group-flush mb-3">
															<li class="list-group-item">
																<div class="widget-content p-0">
																	<div class="widget-content-wrapper">
																		<div class="widget-content-left mr-3">
																			<i class="fa fa-id-card"></i>
																		</div>
																		<div class="widget-content-left">
																			<div class="widget-heading">Nomor Anggota</div>
																		</div>
																		<div class="widget-content-right text-primary font-weight-bold">
																			<?= $member->MemberNo; ?>
																		</div>
																	</div>
																</div>
															</li>
															<li class="list-group-item">
																<div class="widget-content p-0">
																	<div class="widget-content-wrapper">
																		<div class="widget-content-left mr-3">
																			<i class="fa fa-user"></i>
																		</div>
																		<div class="widget-content-left">
																			<div class="widget-heading">Nama Anggota</div>
																		</div>
																		<div class="widget-content-right text-primary font-weight-bold">
																			<?= $member->Fullname; ?>
																		</div>
																	</div>
																</div>
															</li>
															<li class="list-group-item">
																<div class="widget-content p-0">
																	<div class="widget-content-wrapper">
																		<div class="widget-content-left mr-3">
																			<i class="fa fa-child"></i>
																		</div>
																		<div class="widget-content-left">
																			<div class="widget-heading">Jenis Kelamin</div>
																		</div>
																		<div class="widget-content-right text-primary font-weight-bold">
																			<?= ($member->Sex_id > 0 ? 'Laki-laki':'Perempuan'); ?>
																		</div>
																	</div>
																</div>
															</li>
															<li class="list-group-item">
																<div class="widget-content p-0">
																	<div class="widget-content-wrapper">
																		<div class="widget-content-left mr-3">
																			<i class="fa fa-calendar-plus"></i>
																		</div>
																		<div class="widget-content-left">
																			<div class="widget-heading">Tanggal Pendaftaran</div>
																		</div>
																		<div class="widget-content-right text-primary font-weight-bold">
																			<?php $from_date = new \DateTime($member->RegisterDate); ?>
																			<?= indo_long_date(date_format($from_date,"Y-m-d")," "); ?>
																		</div>
																	</div>
																</div>
															</li>
															<li class="list-group-item">
																<div class="widget-content p-0">
																	<div class="widget-content-wrapper">
																		<div class="widget-content-left mr-3">
																			<i class="fa fa-calendar-minus"></i>
																		</div>
																		<div class="widget-content-left">
																			<div class="widget-heading">Tanggal Berlaku Akhir</div>
																		</div>
																		<div class="widget-content-right text-primary font-weight-bold">
																			<?php $end_date = new \DateTime($member->EndDate); ?>
																			<?= indo_long_date(date_format($end_date,"Y-m-d")," "); ?>
																		</div>
																	</div>
																</div>
															</li>
															
														</ul>
														
													</div>
													<div class="col-lg-6">
														<ul class="list-group list-group-flush">
															<li class="list-group-item">
																<div class="widget-content p-0">
																	<div class="widget-content-wrapper">
																		<div class="widget-content-left mr-3">
																			<i class="fa fa-check"></i>
																		</div>
																		<div class="widget-content-left">
																			<div class="widget-heading">Jenis Anggota</div>
																		</div>
																		<div class="widget-content-right text-primary font-weight-bold">
																			<?= $jenis_anggota->jenisanggota??''; ?>
																		</div>
																	</div>
																</div>
															</li>
															<li class="list-group-item">
																<div class="widget-content p-0">
																	<div class="widget-content-wrapper">
																		<div class="widget-content-left mr-3">
																			<i class="fa fa-calendar-check"></i>
																		</div>
																		<div class="widget-content-left">
																			<div class="widget-heading">Masa Berlaku Anggota</div>
																		</div>
																		<div class="widget-content-right text-primary font-weight-bold">
																			<?php $expiry =  $jenis_anggota->MasaBerlakuAnggota??0; ?>
																			<?= formatRupiah($expiry, '') ?> hari
																		</div>
																	</div>
																</div>
															</li>
															<li class="list-group-item">
																<div class="widget-content p-0">
																	<div class="widget-content-wrapper">
																		<div class="widget-content-left mr-3">
																			<i class="fa fa-calendar-check"></i>
																		</div>
																		<div class="widget-content-left">
																			<div class="widget-heading">Limit Lama Peminjaman</div>
																		</div>
																		<div class="widget-content-right text-primary font-weight-bold">
																			<?= $jenis_anggota->MaxLoanDays??0; ?> hari
																		</div>
																	</div>
																</div>
															</li>
															<li class="list-group-item">
																<div class="widget-content p-0">
																	<div class="widget-content-wrapper">
																		<div class="widget-content-left mr-3">
																			<i class="fa fa-calendar-check"></i>
																		</div>
																		<div class="widget-content-left">
																			<div class="widget-heading">Limit Lama Perpanjangan</div>
																		</div>
																		<div class="widget-content-right text-primary font-weight-bold">
																			<?= $jenis_anggota->DayPerpanjang??0; ?> hari
																		</div>
																	</div>
																</div>
															</li>
															<li class="list-group-item">
																<div class="widget-content p-0">
																	<div class="widget-content-wrapper">
																		<div class="widget-content-left mr-3">
																			<i class="fa fa-calculator"></i>
																		</div>
																		<div class="widget-content-left">
																			<div class="widget-heading">Limit Jumlah Perpanjangan</div>
																		</div>
																		<div class="widget-content-right text-primary font-weight-bold">
																			<?= $jenis_anggota->CountPerpanjang??0; ?> kali
																		</div>
																	</div>
																</div>
															</li>
															<li class="list-group-item">
																<div class="widget-content p-0">
																	<div class="widget-content-wrapper">
																		<div class="widget-content-left mr-3">
																			<i class="fa fa-calculator"></i>
																		</div>
																		<div class="widget-content-left">
																			<div class="widget-heading">Limit Jumlah Peminjaman</div>
																		</div>
																		<div class="widget-content-right text-primary font-weight-bold">
																			<?= $jenis_anggota->MaxPinjamKoleksi??0; ?> koleksi
																		</div>
																	</div>
																</div>
															</li>
															<li class="list-group-item">
																<div class="widget-content p-0">
																	<div class="widget-content-wrapper">
																		<div class="widget-content-left mr-3">
																			<i class="fa fa-wallet"></i>
																		</div>
																		<div class="widget-content-left">
																			<div class="widget-heading">Biaya Pendaftaran</div>
																		</div>
																		<div class="widget-content-right text-primary font-weight-bold">
																			<?= formatRupiah($jenis_anggota->BiayaPendaftaran??0); ?>
																		</div>
																	</div>
																</div>
															</li>
															<li class="list-group-item">
																<div class="widget-content p-0">
																	<div class="widget-content-wrapper">
																		<div class="widget-content-left mr-3">
																			<i class="fa fa-wallet"></i>
																		</div>
																		<div class="widget-content-left">
																			<div class="widget-heading">Biaya Perpanjangan</div>
																		</div>
																		<div class="widget-content-right text-primary font-weight-bold">
																			<?= formatRupiah($jenis_anggota->BiayaPerpanjangan??0); ?>
																		</div>
																	</div>
																</div>
															</li>
															
															
															
														</ul>
													</div>
												</div>	
											</div>
										</div>
										<!-- <div class="card-footer">
											<small class="text-muted">Terakhir Login: </small>
										</div> -->
									</div>
								</div>
								<div class="tab-pane show" id="tab2" role="tabpanel">
									<table style="width: 100%;" class="table table-hover table-bordered">
										<thead class="bg-night-sky text-light">
											<tr>
												<th>
													#
												</th>
												<th>
													Kode
												</th>
												<th>
													Nama Lokasi Perpustakaan
												</th>
												<th>
													Alamat
												</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($member_locations as $row):?>
												<tr>
													<td><?=$row->ID?></td>
													<td><?=$row->Code?></td>
													<td><?=$row->Name?></td>
													<td><?=$row->Address?></td>
											</tr>
											<?php endforeach;?>
                						</tbody>
									</table>
								</div>
								<div class="tab-pane show" id="tab3" role="tabpanel">
									<table style="width: 100%;" class="table table-hover table-bordered">
										<thead class="bg-night-sky text-light">
											<tr>
												<th>
													#
												</th>
												<th>
													Kode
												</th>
												<th>
													Nama Kategori Koleksi
												</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($member_categories as $row):?>
												<tr>
													<td><?=$row->ID?></td>
													<td><?=$row->Code?></td>
													<td><?=$row->Name?></td>
											</tr>
											<?php endforeach;?>
                						</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?=$this->section('script');?>
<?= $this->include('Member\Views\modal_upload'); ?>
<?= $this->include('Member\Views\modal_camera'); ?>
<?=$this->endSection('script');?>