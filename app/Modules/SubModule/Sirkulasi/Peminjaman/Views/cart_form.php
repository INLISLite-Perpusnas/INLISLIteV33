<?php
$collection_loan = get_ref_single('collectionloans', 'ID IS NOT NULL','data');

$lastNumber = $collection_loan ? (int) substr($collection_loan->ID, -5) : 0;
$increment = $lastNumber + 1;

$collection_loan_id = get_pad_number($increment, date('ymd'), 5);

?>
<div class="mb-3 card">
	<div class="card-header">
		<i class="header-icon lnr-cart icon-gradient bg-success"> </i>
		TROLI PEMINJAMAN
		<div class="btn-actions-pane-right actions-icon-btn">
			<?php if (is_allowed('sirkulasi-peminjaman/create')) : ?>
				<a data-toggle="modal" data-target="#modal_koleksi" href="javascript:void(0);" class="btn btn-success" title="Daftar Koleksi"><i class="fa fa-th-list"></i> Daftar Koleksi</a>
			<?php endif; ?>
		</div>
	</div>
	<div class="card-body">
		<form method="post" action="<?= base_url('sirkulasi-peminjaman/create?member_no=' . $member_no) ?>">
			<div class="row">
				<div class="col-md-4">
					<div class="select-wrapper input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text">No. Transaksi</span>
						</div>
						<input readonly class="form-control" type="text" name="collection_loan_id" value="<?= $collection_loan_id ?>">
					</div>
				</div>
				<div class="col-md-4">
					<div class="select-wrapper input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text">Tanggal Pinjam</span>
						</div>
						<input class="form-control" type="date" name="loan_date" id="today" value="<?= date('Y-m-d') ?>">
					</div>
				</div>
			</div>

			<div class="table-responsive">
				<table style="width: 100%;" id="tbl_carts" class="table table-hover table-striped table-bordered">
					<thead class="bg-night-sky text-light">
						<tr>
							<th class="text-center">No. Barcode</th>
							<th class="text-center">Penerbit / Judul</th>
							<th class="text-center">Aksi</th>
						</tr>
					</thead>
					<tbody id="cart-tbody">
						<?php foreach ($carts as $row) : ?>
							<tr>
								<td>
									<div class="widget-content p-0">
										<div class="widget-content-wrapper">
											<div class="widget-content-left mr-3">
												<i class="far fa-qrcode fa-2x text-info"></i>
											</div>
											<div class="widget-content-left">
												<div class="widget-heading"><?= $row->options->collection->NomorBarcode ?></div>
											</div>
										</div>
									</div>
								</td>
								<td>
									<div class="widget-content p-0">
										<div class="widget-content-wrapper">
											<div class="widget-content-left mr-3">
												<i class="far fa-book fa-2x text-info"></i>
											</div>
											<div class="widget-content-left">
												<div class="widget-heading text-primary"><?= $row->options->catalog->Publisher ?></div>
												<div class="widget-heading"><?= $row->options->catalog->Title ?></div>
											</div>
										</div>
									</div>
								</td>
								<td class="text-center">
									<a href="javascript:void(0);" data-href="<?= base_url('sirkulasi-peminjaman/cart_remove/' . $row->id) ?>" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="fa fa-times"> </i></a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<div class="d-block">
				<button type="submit" class="btn btn-primary" style="min-width: 120px" data-toggle="tooltip" data-placement="top" title="Simpan Item Troli Peminjaman"><i class="fa fa-save"></i> Simpan ke Daftar Peminjaman </button>
				<a id="empty_cart" href="javascript:void(0);" style="min-width: 120px" data-href="<?= base_url('sirkulasi-peminjaman/cart_destroy') ?>" data-toggle="tooltip" data-placement="top" title="Hapus Item Troli Peminjaman" class="btn btn-outline-danger"><i class="fa fa-trash"> </i> Kosongkan Troli</a>
			</div>
		</form>
	</div>
</div>

<?= $this->section('script'); ?>
<?= $this->include('Peminjaman\Views\modal_koleksi'); ?>
<?= $this->endSection('script'); ?>