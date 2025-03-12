<?php
$alert = session()->getFlashdata();
$type = key($alert);
// die;
if (!empty($alert[$type])) { ?>
	<?php foreach ($alert[$type] as $succes) : ?>
		<?= '<p id="test" alt="' . $type . '" hidden>' . $succes . '</p>' ?>
	<?php endforeach ?>
<?php
}
?>
<div class='content-wrapper'>
	<div class='container-fluid'>

		<!--page title-->
		<div class='page-title mb-4 d-flex align-items-center'>
			<div class='mr-auto'>
				<h4 class='weight500 d-inline-block pr-3 mr-3 border-right'><?= $title ?></h4>
				<nav aria-label='breadcrumb' class='d-inline-block '>
					<ol class='breadcrumb p-0'>
						<?php foreach ($breadcrumb as $bc) : ?>
							<li class='breadcrumb-item'><a href='#'><?= $bc ?></a></li>
						<?php endforeach; ?>
					</ol>
				</nav>
			</div>
		</div>
		<!--/page title-->

		<div class='row'>
			<div class='col-xl-12'>
				<div class='card card-shadow mb-4'>
					<div class='card-header border-0'>
						<div class='custom-title-wrap bar-primary'>
							<div class='custom-title'>Collections</div>
						</div>
					</div>
					<div class='card-body'>
						<?php echo form_open($proses, ['id' => "collection_list"]); ?>
						<div class='card-header border-0'>
							<table id='table_action' class='table table-borderless' cellspacing='0'>
								<tbody>
									<tr>
										<td style="width:40%">
											<div class="input-group">
												<div class="col-sm-8">
													<select class="form-control" id="CekAction" name="param">
														<option value="0">
															Karantina
														</option>
														<option value="1">
															Tampil di OPAC
														</option>
														<option value="2">
															Cetak Label
														</option>
														<option value="3">
															Masukkan ke keranjang item
														</option>
													</select>
												</div>
												<div id="if">
													<div class="input-group-append">
														<button class="btn btn-sm btn-outline-secondary" type="submit">Proses</button>
													</div>
												</div>
											</div>
										</td>
										<td id="format_cetak_label">
										</td>

									</tr>
								</tbody>
							</table>

						</div>
						<div class='table-responsive'>
							<table id='data_table' class='table table-bordered table-striped' cellspacing='0'>
								<thead>
									<tr id='tr'>
										<th>No</th>
										<th><input type="checkbox" name="checkAll" id="checkAll"></th>
										<th>NomorBarcode</th>
										<th>TanggalPengadaan</th>
										<th>NoInduk</th>
										<th>Data Bibliografis</th>
										<th>Action</th>
									</tr>
								</thead>

								<tbody><?php
										$i = 1;
										foreach ($collectionsListBib as $collections) {
										?>
										<tr>
											<td><?= $i++; ?></td>
											<td><input type="checkbox" name="checkItem[]" id="checkItem" value="<?= $collections->ID ?>"></td>
											<td><a href="<?= base_url('/backend/collections/' . $collections->ID) ?>"><?php echo $collections->noBarcode ?></a></td>
											<!-- <td><//?php echo $collections->noBarcode ?></td> -->
											<td><?php echo $collections->tglPengadaan ?></td>
											<td><?php echo $collections->noInduk ?></td>
											<td width="50%"><?php echo $collections->biblio ?></td>
											<td style="text-align:center">
												<a class='btn btn-info btn-sm' onclick="prosesKarantina(<?= $collections->ID ?>)" href="javascript:void(0)">Karantina</a>
											</td>
										</tr>
									<?php
										}
									?>
								</tbody>
							</table>
							<div class="row">
								<div class="col-md-6">
								</div>
								<div class="col-md-6 text-right">

								</div>
							</div>
						</div>
						<?= form_close() ?>
					</div>
				</div>
			</div>