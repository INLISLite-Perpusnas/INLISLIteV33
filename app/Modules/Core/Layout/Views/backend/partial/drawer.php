<?php helper('adminigniter');?>
<?php 
	$ref_code = cart_ref_code();
	$ref_id = 0;
	$request = get_request($ref_code);
	$table = $table ?? '';
	$ref_bmns = array('bmn_04','bmn_06', 'bmn_07', 'bmn_09');
	if(!empty($request)){
		$ref_id = $request->id;
		$ref_slug = url_title($request->req_category, '-', TRUE);
		if(strtoupper($request->req_category) == 'TANAH DAN/ATAU BANGUNAN'){
			$ref_bmns = array('bmn_01','bmn_02');
		}
	}
?>

<div class="app-drawer-wrapper">
    <div class="drawer-nav-btn">
        <button type="button" class="hamburger hamburger--elastic is-active">
            <span class="hamburger-box"><span class="hamburger-inner"></span></span></button>
    </div>
    <div class="drawer-content-wrapper">
        <div class="scrollbar-container">

			<h3 class="drawer-heading font-weight-bold"><i class="fa fa-cart-plus"></i> Keranjang Usulan</h3>
            <div class="drawer-section p-0">
                <div class="todo-box">
                    <ul class="todo-list-wrapper list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="todo-indicator bg-primary"></div>
                            <div class="widget-content p-0">
                                <div class="widget-content-wrapper">
                                    <div class="widget-content-left ml-2">
                                        <div class="widget-heading">No. Usulan</div>
                                    </div>
                                    <div class="widget-content-right font-weight-bold">
										<?=$request->ref_code ?? ''?>
                                    </div>
                                </div>
                            </div>
                        </li>
						<li class="list-group-item">
                            <div class="todo-indicator bg-primary"></div>
                            <div class="widget-content p-0">
                                <div class="widget-content-wrapper">
                                    <div class="widget-content-left ml-2">
                                        <div class="widget-heading">Jumlah Aset</div>
                                    </div>
                                    <div class="widget-content-right font-weight-bold">
										<?=formatAmount(cart_total_items()??0)?> NUP
                                    </div>
                                </div>
                            </div>
                        </li>
						<li class="list-group-item">
                            <div class="todo-indicator bg-primary"></div>
                            <div class="widget-content p-0">
                                <div class="widget-content-wrapper">
                                    <div class="widget-content-left ml-2">
                                        <div class="widget-heading">Total Aset</div>
                                    </div>
                                    <div class="widget-content-right font-weight-bold">
                                        <?=formatRp(cart_total()??0)?>
                                    </div>
                                </div>
                            </div>
                        </li>
						<li class="list-group-item">
                            <div class="todo-indicator bg-primary"></div>
                            <div class="widget-content p-0">
                                <div class="widget-content-wrapper">
                                    <div class="widget-content-left ml-2">
                                        <div class="widget-heading">Jenis Usulan</div>
                                    </div>
                                    <div class="widget-content-right font-weight-bold">
										<?=$request->req_type ?? ''?>
                                    </div>
                                </div>
                            </div>
                        </li>
						<li class="list-group-item">
                            <div class="todo-indicator bg-primary"></div>
                            <div class="widget-content p-0">
                                <div class="widget-content-wrapper">
                                    <div class="widget-content-left ml-2">
                                        <div class="widget-heading">Kategori</div>
                                    </div>
                                    <div class="widget-content-right font-weight-bold">
										<?=$request->req_category ?? ''?>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

			<div class="drawer-heading">
				<div class="main-card mb-3 card">
					<div class="card-header">
						<?php if(cart_total_items() > 0):?>
							<a href="javascript:void()" data-url="<?=base_url('request/cart_destroy/')?>" class="btn btn-danger btn-destroy" style="text-transform: capitalize;" data-toggle="tooltip" data-placement="right" title="Semua Aset" >
								<i class="fa fa-trash"></i> Kosongkan Keranjang
							</a> &nbsp; 
						<?php endif;?>

						<div class="btn-actions-pane-right actions-icon-btn">
							<?php foreach($ref_bmns as $row):?>
								<a href="<?=base_url('bmn?table='.$row)?>" class="btn btn-success">
									<i class="fa fa-plus-square"></i> <?=get_bmn_label($row)->label?>
								</a> 
							<?php endforeach;?>
						</div>
					</div>
					<div class="card-body">
						<form name="cart_form_bmns" id="cart_form_bmns" action="<?= base_url('request/cart_remove/') ?>">
							<table style="width: 100%;" id="cart_tbl_bmns" class="table table-bordered table-hover table-condensed table-striped">
								<thead>
									<tr class="bg-primary text-white">
										<th class="text-center">
											<input type="checkbox" class="cart_check_all" name="check_all" title="Pilih Semua">
										</th>
										<th>Nama Barang</th>
										<th class="text-center">No. Aset</th>
										<th class="text-center">Merk/Tipe</th>
										<th class="text-center">Tanggal Perolehan</th>
										<th class="text-center">Jumlah Barang</th>
										<th class="text-center">Nilai Aset</th>
										<th class="text-center">Kondisi</th>
										<th class="text-center">Aksi</th>
									</tr>
								</thead>
								<tbody>
									<?php $i =1; foreach (cart_contents() as $row) : ?>
										<tr>
											<td class="text-center" width="5">
												<input type="checkbox" class="cart_check" name="id[]" value="<?= $row->rowid; ?>">
											</td>
											<td width="200">
												<span class="text-primary"><?= _spec($row->options->KD_BRG); ?></span><br>
												<span class="text-muted"><?= _spec(get_barang_data($row->options->KD_BRG)->name??''); ?></span>
											</td>
											<td class="text-center" width="90"><?= _spec($row->options->NO_ASET); ?></td>
											<td class="text-muted" width="100"><?= _spec($row->options->MERK_TYPE); ?></td>
											<td class="text-center" width="120"><?= _spec($row->options->TGL_PERLH); ?></td>
											<td class="text-center" width="80"><?= _spec($row->qty); ?></td>
											<td class="text-right font-weight-bold text-primary" width="200"><?= _spec(formatRp($row->options->RPH_ASET)); ?></td>
											<td class="text-center" width="80">
												<span class="badge badge-sm badge-pill badge-<?=get_condition_label($row->options->KONDISI)->class?>"><?=get_condition_label($row->options->KONDISI)->label?></span>
											</td>
											<td class="text-center" width="30">
												<a href="<?= base_url('request/cart_remove/'.$row->rowid) ?>" data-toggle="tooltip" data-placement="left" title="Hapus dari Keranjang" class="btn btn-danger"><i class="fa fa-trash"> </i></a>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</form>
					</div>
					<?php if(cart_total_items() > 0):?>
						<div class="card-footer">
							<button type="button" id="remove_from_cart" class="btn btn-danger" data-toggle="tooltip" data-placement="right" title="Semua Aset yang Terpilih"><i class="fa fa-trash"> </i> Hapus dari Keranjang</button>

							<div class="btn-actions-pane-right actions-icon-btn">
								<a href="javascript:void(0);" data-url="<?=base_url('request/cart_checkout/'.$request->id)?>" class="btn btn-warning btn-checkout pull-right" data-toggle="tooltip" data-placement="left" title="Akhiri Penambahan Aset"><i class="fa fa-sign-out"></i> Checkout</a>
							</div>
						</div>
					<?php endif;?>
				</div>
			</div>
        </div>
    </div>
</div>
<div class="app-drawer-overlay d-none animated fadeIn"></div>