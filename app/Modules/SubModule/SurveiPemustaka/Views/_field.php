<?php
/**
 * Satu baris form horizontal (label kiri, input kanan) + pesan error.
 * @var string $label
 * @var string $name
 * @var string $input  HTML input
 * @var array  $errors
 */
$error = $errors[$name] ?? null;
?>
<div class="form-group row<?= $error ? ' has-error' : '' ?>">
    <label class="col-sm-2 col-form-label text-sm-right" for="frm_<?= esc($name, 'attr') ?>"><?= esc($label) ?></label>
    <div class="col-sm-10">
        <?= $input ?>
        <?php if ($error): ?><div class="invalid-feedback d-block"><?= esc($error) ?></div><?php endif; ?>
    </div>
</div>
