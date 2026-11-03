<?php
/**
 * Judul halaman + breadcrumb modul survei.
 * @var string $title
 * @var string $subtitle
 * @var array  $crumbs [label => url|null]
 */
?>
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="pe-7s-graph2 icon-gradient bg-strong-bliss"></i>
            </div>
            <div><?= esc($title) ?>
                <div class="page-title-subheading"><?= esc($subtitle ?? '') ?></div>
            </div>
        </div>
        <div class="page-title-actions">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                    <?php foreach ($crumbs as $label => $url): ?>
                        <?php if ($url): ?>
                            <li class="breadcrumb-item"><a href="<?= $url ?>"><?= esc($label) ?></a></li>
                        <?php else: ?>
                            <li class="breadcrumb-item" aria-current="page"><?= esc($label) ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>
        </div>
    </div>
</div>
