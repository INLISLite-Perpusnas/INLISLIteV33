<?php
/**
 * app/Views/Pager/pusling_pager.php
 * Daftarkan di app/Config/Pager.php:
 *   public array $templates = [
 *       'default_full' => 'CodeIgniter\Pager\Views\default_full',
 *       ...
 *       'pusling'      => 'App\Views\Pager\pusling_pager',
 *   ];
 *
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */
$pager->setSurroundCount(3);
$links = $pager->links();
$count = $pager->getPageCount();

$firstShown = $links ? $links[array_key_first($links)]['title'] : 1;
$lastShown = $links ? $links[array_key_last($links)]['title'] : 1;
?>
<nav aria-label="Pagination">
    <ul class="pagination">
        <li class="page-item <?= $pager->hasPreviousPage() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasPreviousPage() ? $pager->getPreviousPage() : '#' ?>"
                aria-label="Sebelumnya">
                <i class="fa fa-chevron-left"></i>
            </a>
        </li>

        <?php if ($firstShown > 1): ?>
            <li class="page-item"><a class="page-link" href="<?= $pager->getFirst() ?>">1</a></li>
            <?php if ($firstShown > 2): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
            <?php endif; ?>
        <?php endif; ?>

        <?php foreach ($links as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
            </li>
        <?php endforeach; ?>

        <?php if ($lastShown < $count): ?>
            <?php if ($lastShown < $count - 1): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
            <?php endif; ?>
            <li class="page-item"><a class="page-link" href="<?= $pager->getLast() ?>"><?= $count ?></a></li>
        <?php endif; ?>

        <li class="page-item <?= $pager->hasNextPage() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasNextPage() ? $pager->getNextPage() : '#' ?>"
                aria-label="Berikutnya">
                <i class="fa fa-chevron-right"></i>
            </a>
        </li>
    </ul>
</nav>