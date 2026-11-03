<?php
/**
 * Card Metrics dashboard. $m: label, value, sub, icon, color, url (opsional). $compact: kartu data pendukung.
 */
$value = number_format((int) ($m['value'] ?? 0), 0, ',', '.');
$tag   = !empty($m['url']) ? 'a' : 'div';
$attr  = !empty($m['url']) ? ' href="' . esc(base_url($m['url']), 'attr') . '"' : ' role="group"';
?>
<<?= $tag ?> class="metric-card<?= !empty($compact) ? ' compact' : '' ?>"<?= $attr ?> aria-label="<?= esc($m['label'] . ': ' . $value, 'attr') ?>">
    <div class="metric-top">
        <div>
            <h3 class="metric-label"><?= esc($m['label']) ?></h3>
            <div class="metric-value"><?= $value ?></div>
        </div>
        <span class="metric-icon <?= esc($m['color'] ?? 'blue', 'attr') ?>" aria-hidden="true">
            <i class="<?= esc($m['icon'], 'attr') ?>"></i>
        </span>
    </div>
    <?php if (!empty($m['sub'])) : ?>
        <p class="metric-sub"><?= esc($m['sub']) ?></p>
    <?php endif; ?>
</<?= $tag ?>>
