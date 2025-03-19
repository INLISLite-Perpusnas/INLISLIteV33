<div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Eksemplar Katalog
	<div class="btn-actions-pane-right actions-icon-btn">
		<?php if (is_allowed('eksemplar/create')) : ?>
			<a href="<?= base_url('eksemplar/create?slug=&catalog_id=' . $catalog->ID) ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i>
				Tambah Eksemplar
			</a>
		<?php endif; ?>
	</div>
</div>
<div class="card-body">
	<form name="form_items" id="form_items">
		<table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
			<thead>
				<tr>
					<th class="text-center" width="100">No. Barcode</th>
					<th class="text-center" width="100">Tanggal Pengadaan</th>
					<th class="text-center" width="100">No. Induk</th>
					<th class="text-center" width="">Data Bibliografis</th>
					<th class="text-center" width="">OPAC</th>
					<th class="text-center" width="">ISDRM</th>
					<th class="text-center" width="80">Aksi</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</form>
</div>