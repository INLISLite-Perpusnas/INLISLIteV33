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
						</select>
						<div class="input-group-append">
							<button class="btn btn bg-primary text-white marc-btn-add" type="button" style="min-width:80px"><i class="fa fa-plus"></i> Tambah Tag </button>
						</div>
					</div>
				</td>
			</tr>
		</thead>
		<tbody id="marc-tbody">
			<?php 
			// Tampilkan data existing dari database
			$displayed_tags = []; // Track tags yang sudah ditampilkan
			foreach ($session_tags as $row) : 
				$tag = $row['Tag'];
				$existing_tag_data = isset($existing_data[$tag]) ? $existing_data[$tag] : [];
				
				// Jika ada data existing untuk tag ini
				if (!empty($existing_tag_data)) {
					foreach ($existing_tag_data as $idx => $existing_item) {
						$index = random_string('numeric', 13);
						$displayed_tags[$tag] = true; // Mark tag sebagai sudah ditampilkan
			?>
				<tr class="rm-row">
					<td class="text-center">
						<?php if ($row['Mandatory'] != 1) : ?>
							<button type="button" class="btn btn-danger marc-btn-remove" data-field_id="<?= $row['ID'] ?>"><i class="fa fa-trash"></i></button>
						<?php endif; ?>
					</td>
					<td>
						<input type="hidden" name="Index[<?= $tag ?>][]" value="Index_<?= $tag ?>_<?= $index ?>">
						<?= $tag ?>
					</td>
					<td>
						<?= $row['Name'] ?>
					</td>
					<td>
						<?php if ($row['Fixed'] != 1) : ?>
							<div class="input-group">
								<input type="text" class="form-control" name="Indicator1[<?= $tag ?>]<?= ($row['Repeatable'] == 1) ? '[]' : '' ?>" id="Indicator1_<?= $tag ?>_<?= $index ?>" placeholder="" value="<?= $existing_item['Indicator1'] ?>" />
								<div class="input-group-append">
									<button class="btn btn bg-primary text-white btn-indicator1" data-id="<?= $row['ID'] ?>" data-field_id="Indicator1_<?= $tag ?>_<?= $index ?>" data-tag="<?= $tag ?>" data-name="<?= $row['Name'] ?>" type="button"><i class="fa fa-list"></i></button>
								</div>
							</div>
						<?php endif; ?>
					</td>
					<td>
						<?php if ($row['Fixed'] != 1) : ?>
							<div class="input-group">
								<input type="text" class="form-control" name="Indicator2[<?= $tag ?>]<?= ($row['Repeatable'] == 1) ? '[]' : '' ?>" id="Indicator2_<?= $tag ?>_<?= $index ?>" placeholder="" value="<?= $existing_item['Indicator2'] ?>" />
								<div class="input-group-append">
									<button class="btn btn bg-primary text-white btn-indicator2" data-id="<?= $row['ID'] ?>" data-field_id="Indicator2_<?= $tag ?>_<?= $index ?>" data-tag="<?= $tag ?>" data-name="<?= $row['Name'] ?>" type="button"><i class="fa fa-list"></i></button>
								</div>
							</div>
						<?php endif; ?>
					</td>
					<td>
						<div class="input-group">
							<input type="text" class="form-control" name="Value[<?= $tag ?>]<?= ($row['Repeatable'] == 1) ? '[]' : '' ?>" id="Value_<?= $tag ?>_<?= $index ?>" placeholder="" value="<?= htmlspecialchars($existing_item['Value']) ?>" />
							<div class="input-group-append">
								<button class="btn btn bg-primary text-white btn-value" data-id="<?= $row['ID'] ?>" data-field_id="Value_<?= $tag ?>_<?= $index ?>" data-tag="<?= $tag ?>" data-name="<?= $row['Name'] ?>" type="button"><i class="fa fa-list"></i></button>
							</div>
						</div>
					</td>
				</tr>
			<?php 
					}
				} elseif (!isset($displayed_tags[$tag])) { 
					// Jika tidak ada data existing dan belum ditampilkan, tampilkan field kosong untuk mandatory tags
					$index = random_string('numeric', 13); 
			?>
				<tr class="rm-row">
					<td class="text-center">
						<?php if ($row['Mandatory'] != 1) : ?>
							<button type="button" class="btn btn-danger marc-btn-remove" data-field_id="<?= $row['ID'] ?>"><i class="fa fa-trash"></i></button>
						<?php endif; ?>
					</td>
					<td>
						<input type="hidden" name="Index[<?= $tag ?>][]" value="Index_<?= $tag ?>_<?= $index ?>">
						<?= $tag ?>
					</td>
					<td>
						<?= $row['Name'] ?>
					</td>
					<td>
						<?php if ($row['Fixed'] != 1) : ?>
							<div class="input-group">
								<input type="text" class="form-control" name="Indicator1[<?= $tag ?>]<?= ($row['Repeatable'] == 1) ? '[]' : '' ?>" id="Indicator1_<?= $tag ?>_<?= $index ?>" placeholder="" value="#" />
								<div class="input-group-append">
									<button class="btn btn bg-primary text-white btn-indicator1" data-id="<?= $row['ID'] ?>" data-field_id="Indicator1_<?= $tag ?>_<?= $index ?>" data-tag="<?= $tag ?>" data-name="<?= $row['Name'] ?>" type="button"><i class="fa fa-list"></i></button>
								</div>
							</div>
						<?php endif; ?>
					</td>
					<td>
						<?php if ($row['Fixed'] != 1) : ?>
							<div class="input-group">
								<input type="text" class="form-control" name="Indicator2[<?= $tag ?>]<?= ($row['Repeatable'] == 1) ? '[]' : '' ?>" id="Indicator2_<?= $tag ?>_<?= $index ?>" placeholder="" value="#" />
								<div class="input-group-append">
									<button class="btn btn bg-primary text-white btn-indicator2" data-id="<?= $row['ID'] ?>" data-field_id="Indicator2_<?= $tag ?>_<?= $index ?>" data-tag="<?= $tag ?>" data-name="<?= $row['Name'] ?>" type="button"><i class="fa fa-list"></i></button>
								</div>
							</div>
						<?php endif; ?>
					</td>
					<td>
						<div class="input-group">
							<input type="text" class="form-control" name="Value[<?= $tag ?>]<?= ($row['Repeatable'] == 1) ? '[]' : '' ?>" id="Value_<?= $tag ?>_<?= $index ?>" placeholder="" value="$a" />
							<div class="input-group-append">
								<button class="btn btn bg-primary text-white btn-value" data-id="<?= $row['ID'] ?>" data-field_id="Value_<?= $tag ?>_<?= $index ?>" data-tag="<?= $tag ?>" data-name="<?= $row['Name'] ?>" type="button"><i class="fa fa-list"></i></button>
							</div>
						</div>
					</td>
				</tr>
			<?php 
				}
			endforeach; 
			?>
		</tbody>
	</table>

	<div class="form-group">
		<button type="submit" class="btn btn-primary btn-lg" name="submit"><i class="fa fa-save mr-2"></i>Update</button>
		<a href="<?= base_url('katalog') ?>" class="btn btn-secondary btn-lg"><i class="fa fa-arrow-left mr-2"></i>Kembali</a>
	</div>
</div>