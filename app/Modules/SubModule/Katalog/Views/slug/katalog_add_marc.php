<?php
$request = service('request');
$worksheet_id = $request->getGet('worksheet_id') ?? 1;
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
			<a href="<?= base_url('katalog/create') ?>" class="btn btn-dark btn-lg"><i class="fa fa-th mr-2"></i>Tampilkan Form Sederhana</a>
		</div>
	</div>
</div>
<div class="card-body">
	<input type="hidden" name="ControlNumber" value="">
	<input type="hidden" name="BIBID" value="">
	<input type="hidden" name="Title" value="">
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
						</select>
						<div class="input-group-append">
							<button class="btn btn bg-primary text-white marc-btn-add" type="button" style="min-width:80px"><i class="fa fa-plus"></i> Tambah Tag </button>
						</div>
					</div>
				</td>
			</tr>
		</thead>
		<tbody id="marc-tbody">
			<?php foreach ($session_tags as $row) : $index = random_string('numeric', 13); ?>
				<tr class="rm-row">
					<td class="text-center">
						<?php if ($row['Mandatory'] <> 1) : ?>
							<button type="button" class="btn btn-danger marc-btn-remove" data-field_id="<?= $row['ID'] ?>"><i class="fa fa-trash"></i></button>
						<?php endif; ?>
					</td>
					<td>
						<input type="hidden" name="Index[<?= $row['Tag'] ?>][]" value="Index_<?= $row['Tag'] ?>_<?= $index ?>">
						<?= $row['Tag'] ?>
					</td>
					<td>
						<?= $row['Name'] ?>
					</td>
					<td>
						<?php if ($row['Fixed'] <> 1) : ?>
							<div class="input-group">
								<input type="text" class="form-control" name="Indicator1[<?= $row['Tag'] ?>]<?= ($row['Repeatable'] == 1) ? '[]' : '' ?>" id="Indicator1_<?= $row['Tag'] ?>_<?= $index ?>" placeholder="" value="#" />
								<div class="input-group-append">
									<button class="btn btn bg-primary text-white btn-indicator1" data-id="<?= $row['ID'] ?>" data-field_id="Indicator1_<?= $row['Tag'] ?>_<?= $index ?>" data-tag="<?= $row['Tag'] ?>" data-name="<?= $row['Name'] ?>" type="button"><i class="fa fa-list"></i></button>
								</div>
							</div>
						<?php endif; ?>
					</td>
					<td>
						<?php if ($row['Fixed'] <> 1) : ?>
							<div class="input-group">
								<input type="text" class="form-control" name="Indicator2[<?= $row['Tag'] ?>]<?= ($row['Repeatable'] == 1) ? '[]' : '' ?>" id="Indicator2_<?= $row['Tag'] ?>_<?= $index ?>" placeholder="" value="#" />
								<div class="input-group-append">
									<button class="btn btn bg-primary text-white btn-indicator2" data-id="<?= $row['ID'] ?>" data-field_id="Indicator2_<?= $row['Tag'] ?>_<?= $index ?>" data-tag="<?= $row['Tag'] ?>" data-name="<?= $row['Name'] ?>" type="button"><i class="fa fa-list"></i></button>
								</div>
							</div>
						<?php endif; ?>
					</td>
					<td>
						<div class="input-group">
							<input type="text" class="form-control" name="Value[<?= $row['Tag'] ?>]<?= ($row['Repeatable'] == 1) ? '[]' : '' ?>" id="Value_<?= $row['Tag'] ?>_<?= $index ?>" placeholder="" value="$a" />
							<div class="input-group-append">
								<button class="btn btn bg-primary text-white btn-value" data-id="<?= $row['ID'] ?>" data-field_id="Value_<?= $row['Tag'] ?>_<?= $index ?>" data-tag="<?= $row['Tag'] ?>" data-name="<?= $row['Name'] ?>" type="button"><i class="fa fa-list"></i></button>
							</div>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>



	<div class="form-group">
		<button type="submit" class="btn btn-primary btn-lg" name="submit"><i class="fa fa-save mr-2"></i>Simpan</button>
	</div>

</div>