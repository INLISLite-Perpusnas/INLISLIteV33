<?php
$pager->setSurroundCount(2);
$links = $pager->links();
$firstVisible = $links[0]['title'] ?? 1;
$lastVisible = $links[count($links) - 1]['title'] ?? 1;
$pageCount = $pager->getPageCount();
?>

<nav aria-label="Navigasi halaman">
    <ul class="pagination justify-content-center flex-wrap gap-1 mb-0">

        <?php if ($firstVisible > 1): ?>
            <li class="page-item">
                <a class="page-link rounded-pill px-3 fw-semibold" href="<?= esc($pager->getFirst(), 'attr') ?>">1</a>
            </li>
            <?php if ($firstVisible > 2): ?>
                <li class="page-item text-muted px-2 align-self-center" aria-hidden="true">&hellip;</li>
            <?php endif; ?>
        <?php endif; ?>

        <?php foreach ($links as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link rounded-pill px-3 fw-semibold" href="<?= esc($link['uri'], 'attr') ?>"<?= $link['active'] ? ' aria-current="page"' : '' ?>>
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach; ?>

        <?php if ($lastVisible < $pageCount): ?>
            <?php if ($lastVisible < $pageCount - 1): ?>
                <li class="page-item text-muted px-2 align-self-center" aria-hidden="true">&hellip;</li>
            <?php endif; ?>
            <li class="page-item">
                <a class="page-link rounded-pill px-3 fw-semibold" href="<?= esc($pager->getLast(), 'attr') ?>"><?= $pageCount ?></a>
            </li>
        <?php endif; ?>

    </ul>
</nav>
