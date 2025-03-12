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
							<div class='custom-title'>Daftar Karantina</div>
						</div>
					</div>
					<div class='card-body'>
						<?php echo form_open($proses, ['id' => "karantina"]); ?>
						<div class='card-header border-0'>
							<div class="input-group">
								<div class="col-sm-4">
									<select class="form-control" id="option_s1" name="param">
										<option value="0">
											Pulihkan
										</option>
										<option value="1">
											Hapus Permanent
										</option>
									</select>
								</div>
								<div class="input-group-append">
									<button class="btn btn-sm btn-outline-secondary" type="submit">Proses</button>
								</div>
							</div>
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
										foreach ($karantinaList as $karantina) {
										?>
										<tr>
											<!-- <td><//?php echo $catalogs['ControlNumber'] ?></td> -->
											<td><?= $i++; ?></td>
											<td><input type="checkbox" name="checkItem[]" id="checkItem" value="<?= $karantina->ID ?>"></td>
											<td><?php echo $karantina->noBarcode ?></td>
											<td><?php echo $karantina->tglPengadaan ?></td>
											<td><?php echo $karantina->noInduk ?></td>
											<td width="50%"><?php echo $karantina->biblio ?></td>
											<td style="text-align:center">
												<!-- <button type="button" id="submitx" class="btn btn-success"><i class="fa fa-check"></i> Send Message</button> -->
												<a class='btn btn-info btn-sm' onclick="prosesKarantina(<?= $karantina->ID ?>)" href="javascript:void(0)"><i class='fa fa-retweet'></i> Pulihkan</a>
												<!-- <input id="coffee-submit" type="submit" name="checkItem[]" value="<//?= $karantina->ID ?>"> -->
												<!-- <a class='btn btn-info btn-sm' href='<?= base_url() ?>/backend/collections/proseskarantina' data-toggle='tooltip' title='' data-original-title='Ubah' data-placement='left'><i class='fa fa-retweet'></i> Pulihkan</a> -->
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