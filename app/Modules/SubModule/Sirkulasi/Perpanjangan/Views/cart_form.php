<div class="mb-3 card">
	<div class="card-header">
		<i class="header-icon lnr-cart icon-gradient bg-success"> </i>
		TROLI PERPANJANGAN
		<div class="btn-actions-pane-right actions-icon-btn">
			<?php if (is_allowed('peminjaman/create')) : ?>
				<a data-toggle="modal" data-target="#modal_loan" href="javascript:void(0);" class="btn btn-success" title="Daftar Peminjaman"><i class="fa fa-th-list"></i> Daftar Peminjaman</a>
			<?php endif; ?>
		</div>
	</div>
	<div class="card-body">
		<form method="post" action="<?= base_url('sirkulasi-perpanjangan/do_extend') ?>">
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
												<div class="widget-subheading"><?= $row->options->member->MemberNo ?></div>
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
									<a href="javascript:void(0);" data-href="<?= base_url('sirkulasi-perpanjangan/cart_remove/' . $row->id) ?>" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="fa fa-times"> </i></a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<div class="d-block">
				<button type="submit" class="btn btn-primary" style="min-width: 120px" data-toggle="tooltip" data-placement="top" title="Simpan ke Daftar Peminjaman"><i class="fa fa-save"></i> Simpan ke Daftar Perpanjangan </button>
				<a id="empty_cart" href="javascript:void(0);" style="min-width: 120px" data-toggle="tooltip" data-placement="top" title="Hapus Item Troli Perpanjangan" class="btn btn-outline-danger"><i class="fa fa-trash"> </i> Kosongkan Troli</a>
			</div>
		</form>
	</div>
</div>

<?= $this->section('script'); ?>
<?= $this->include('Perpanjangan\Views\modal_loan'); ?>
<script>
	$('#empty_cart').click(function() {
		var url = "<?= base_url('sirkulasi-perpanjangan/cart_destroy') ?>";
		console.log(url);

		Swal.fire({
			title: 'Anda yakin?',
			html: "Semua koleksi akan dihapus <br>dari Troli Perpanjangan",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#dd6b55',
			confirmButtonText: '<?= lang('App.btn.yes') ?>',
			cancelButtonText: '<?= lang('App.btn.no') ?>'
		}).then((result) => {
			if (result.value) {
				window.location.href = url;
			}
		});
		return false;
	});
</script>
<?= $this->endSection('script'); ?>