<?php
/**
 * Satu baris ruas di form MARC (aturan tampilan setara _entryBibliografiAdvance INLISLite 3).
 * Variabel: $row (fields), $value, $ind1, $ind2.
 *  - Enabled != 1          : isian readonly (mis. 001, 005, 006)
 *  - tombol "..."          : (bukan ruas tetap, bukan custom, enabled) atau (ruas tetap & custom)
 *  - IsCustomable == 1     : tombol membuka form ruas tetap per posisi (006/007/008)
 *  - ruas tetap            : tanpa indikator, maxlength = Length
 */
$tag        = $row['Tag'];
$index      = random_string('numeric', 13);
$fixed      = (int) ($row['Fixed'] ?? 0) === 1;
$enabled    = (int) ($row['Enabled'] ?? 1) === 1;
$custom     = (int) ($row['IsCustomable'] ?? 0) === 1;
$repeatable = (int) ($row['Repeatable'] ?? 0) === 1;
$showButton = (!$fixed && !$custom && $enabled) || ($fixed && $custom);
$maxlength  = ($fixed && (int) ($row['Length'] ?? -1) > 0) ? 'maxlength="' . (int) $row['Length'] . '"' : '';
$arr        = $repeatable ? '[]' : '';
?>
<tr class="rm-row">
	<td class="text-center">
		<?php if ((int) ($row['Mandatory'] ?? 0) !== 1) : ?>
			<button type="button" class="btn btn-danger marc-btn-remove" data-field_id="<?= $row['ID'] ?>"><i class="fa fa-trash"></i></button>
		<?php endif; ?>
	</td>
	<td>
		<input type="hidden" name="Index[<?= $tag ?>][]" value="Index_<?= $tag ?>_<?= $index ?>">
		<?= $tag ?>
	</td>
	<td>
		<?= esc($row['Name']) ?>
	</td>
	<?php foreach (['Indicator1' => $ind1, 'Indicator2' => $ind2] as $indName => $indValue) : ?>
		<td>
			<?php if (!$fixed) : ?>
				<div class="input-group">
					<input type="text" class="form-control" name="<?= $indName ?>[<?= $tag ?>]<?= $arr ?>" id="<?= $indName ?>_<?= $tag ?>_<?= $index ?>" placeholder="" value="<?= esc($indValue) ?>" />
					<div class="input-group-append">
						<button class="btn btn bg-primary text-white btn-<?= strtolower($indName) ?>" data-id="<?= $row['ID'] ?>" data-field_id="<?= $indName ?>_<?= $tag ?>_<?= $index ?>" data-tag="<?= $tag ?>" data-name="<?= esc($row['Name']) ?>" type="button"><i class="fa fa-list"></i></button>
					</div>
				</div>
			<?php endif; ?>
		</td>
	<?php endforeach; ?>
	<td>
		<div class="input-group">
			<input type="text" class="form-control" name="Value[<?= $tag ?>]<?= $arr ?>" id="Value_<?= $tag ?>_<?= $index ?>" placeholder="" value="<?= esc($value) ?>" <?= $enabled ? '' : 'readonly' ?> <?= $maxlength ?> />
			<?php if ($showButton) : ?>
				<div class="input-group-append">
					<button class="btn btn bg-primary text-white btn-value" data-id="<?= $row['ID'] ?>" data-custom="<?= $custom ? 1 : 0 ?>" data-field_id="Value_<?= $tag ?>_<?= $index ?>" data-tag="<?= $tag ?>" data-name="<?= esc($row['Name']) ?>" type="button"><i class="fa fa-list"></i></button>
				</div>
			<?php endif; ?>
		</div>
	</td>
</tr>
