<?php
$request = service('request');
$worksheet_id = $request->getGet('worksheet_id') ?? $catalog->Worksheet_id;
$select2 = select_two();
?>
<div class="card-header" style="min-height:100px">
	<div class="form-group col-md-6">
		<div class="select-wrapper input-group mt-2 mb-2">
			<select class="form-control" name="Worksheet_id" id="worksheet_id">
				<?php foreach ($worksheets as $row) : ?>
					<option value="<?= $row->ID ?>" <?= set_select('Worksheet_id', $row->ID, ($worksheet_id == $row->ID)); ?>><?= $row->Name ?></option>
				<?php endforeach; ?>
			</select>
			<div class="input-group-append">
				<button class="btn bg-primary text-white worksheet-btn-load" type="button" style="min-width:80px"><i class="fa fa-check "></i> Pilih Jenis Bahan </button>
			</div>
		</div>
	</div>
</div>

<div class="card-header">
	<i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Data Bibliografis
	<div class="btn-actions-pane-right actions-icon-btn">
		<div class="col-md-6">
			<a href="<?= base_url('katalog/edit/' . $catalog->ID) ?>" class="btn btn-dark btn-lg"><i class="fa fa-th mr-2"></i>Tampilkan Form Sederhana</a>
		</div>
	</div>
</div>
<div class="card-body">
	<input type="hidden" name="ControlNumber" value="<?= $catalog->ControlNumber ?>">
	<input type="hidden" name="BIBID" value="<?= $catalog->BIBID ?>">
	<input type="hidden" name="Title" value="<?= $catalog->Title ?>">
	<table style="width: 100%;" id="marc-tbl" class="table table-borderless">
		<thead>
			<tr>
				<th width="55"></th>
				<th width="55">Tag</th>
				<th width="300">Nama</th>
				<th width="150">Indikator 1</th>
				<th width="150">Indikator 2</th>
				<th>Isi</th>
			</tr>
		</thead>
		<thead>
			<tr>
				<td colspan="5"></td>
				<td>
					<div class="input-group select-wrapper">
						<select class="form-control" name="field_id" id="field_id">
							<option value="">-Pilih-</option>
							<?php foreach ($filtered_tags as $ft) : ?>
								<option value="<?= $ft['ID'] ?>">[<?= $ft['Tag'] ?>] <?= esc($ft['Name']) ?></option>
							<?php endforeach; ?>
						</select>
						<div class="input-group-append">
							<button class="btn btn bg-primary text-white marc-btn-add" type="button" style="min-width:80px"><i class="fa fa-plus"></i> Tambah Tag </button>
						</div>
					</div>
				</td>
			</tr>
		</thead>
		<tbody id="marc-tbody">
			<?php $shown = []; ?>
			<?php foreach ($session_tags as $row) : ?>
				<?php if (!empty($existing_data[$row['Tag']])) : ?>
					<?php if (isset($shown[$row['Tag']])) continue; $shown[$row['Tag']] = true; ?>
					<?php foreach ($existing_data[$row['Tag']] as $item) : ?>
						<?= view('Katalog\Views\slug\marc_row', ['row' => $row, 'value' => $item['Value'], 'ind1' => $item['Indicator1'], 'ind2' => $item['Indicator2']]) ?>
					<?php endforeach; ?>
				<?php else : ?>
					<?= view('Katalog\Views\slug\marc_row', ['row' => $row, 'value' => marc_default_value($row, $worksheet_id), 'ind1' => '#', 'ind2' => '#']) ?>
				<?php endif; ?>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div class="form-group">
		<button type="submit" class="btn btn-primary btn-lg" name="submit"><i class="fa fa-save mr-2"></i>Update</button>
		<a href="<?= base_url('katalog') ?>" class="btn btn-secondary btn-lg"><i class="fa fa-arrow-left mr-2"></i>Kembali</a>
	</div>
</div>