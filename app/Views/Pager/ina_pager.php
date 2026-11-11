<?php
/**
 * Pagination gaya INA Digital (varian Tabel):
 * "Halaman X dari Y" + tombol awal, sebelumnya, nomor halaman, berikutnya, akhir.
 * Pasangkan dengan assets/css/ina-pagination.css.
 *
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */
$pager->setSurroundCount(1);

$current   = $pager->getCurrentPageNumber();
$pageCount = max(1, $pager->getPageCount());
$links     = $pager->links();
$firstLink = $links ? (int) $links[0]['title'] : 1;
$lastLink  = $links ? (int) end($links)['title'] : 1;
?>
<div class="ina-pagination">
    <span class="ina-pagination__info">Halaman <?= $current ?> dari <?= $pageCount ?></span>

    <?php if ($pageCount > 1): ?>
        <nav aria-label="Navigasi halaman">
            <ul class="ina-pagination__list">
                <li>
                    <?php if ($pager->hasPreviousPage()): ?>
                        <a class="ina-pagination__btn" href="<?= $pager->getFirst() ?>" aria-label="Halaman pertama" title="Halaman pertama">
                            <i class="fa fa-angle-double-left" aria-hidden="true"></i>
                        </a>
                    <?php else: ?>
                        <span class="ina-pagination__btn is-disabled" aria-hidden="true"><i class="fa fa-angle-double-left"></i></span>
                    <?php endif; ?>
                </li>
                <li>
                    <?php if ($pager->hasPreviousPage()): ?>
                        <a class="ina-pagination__btn" href="<?= $pager->getPreviousPage() ?>" aria-label="Halaman sebelumnya" title="Halaman sebelumnya">
                            <i class="fa fa-angle-left" aria-hidden="true"></i>
                        </a>
                    <?php else: ?>
                        <span class="ina-pagination__btn is-disabled" aria-hidden="true"><i class="fa fa-angle-left"></i></span>
                    <?php endif; ?>
                </li>

                <?php if ($firstLink > 1): ?>
                    <li><a class="ina-pagination__btn" href="<?= $pager->getFirst() ?>" aria-label="Halaman 1">1</a></li>
                    <?php if ($firstLink > 2): ?>
                        <li><span class="ina-pagination__ellipsis" aria-hidden="true">…</span></li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php foreach ($links as $link): ?>
                    <li>
                        <?php if ($link['active']): ?>
                            <span class="ina-pagination__btn is-active" aria-current="page"><?= $link['title'] ?></span>
                        <?php else: ?>
                            <a class="ina-pagination__btn" href="<?= $link['uri'] ?>" aria-label="Halaman <?= $link['title'] ?>"><?= $link['title'] ?></a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>

                <?php if ($lastLink < $pageCount): ?>
                    <?php if ($lastLink < $pageCount - 1): ?>
                        <li><span class="ina-pagination__ellipsis" aria-hidden="true">…</span></li>
                    <?php endif; ?>
                    <li><a class="ina-pagination__btn" href="<?= $pager->getLast() ?>" aria-label="Halaman <?= $pageCount ?>"><?= $pageCount ?></a></li>
                <?php endif; ?>

                <li>
                    <?php if ($pager->hasNextPage()): ?>
                        <a class="ina-pagination__btn" href="<?= $pager->getNextPage() ?>" aria-label="Halaman berikutnya" title="Halaman berikutnya">
                            <i class="fa fa-angle-right" aria-hidden="true"></i>
                        </a>
                    <?php else: ?>
                        <span class="ina-pagination__btn is-disabled" aria-hidden="true"><i class="fa fa-angle-right"></i></span>
                    <?php endif; ?>
                </li>
                <li>
                    <?php if ($pager->hasNextPage()): ?>
                        <a class="ina-pagination__btn" href="<?= $pager->getLast() ?>" aria-label="Halaman terakhir" title="Halaman terakhir">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                        </a>
                    <?php else: ?>
                        <span class="ina-pagination__btn is-disabled" aria-hidden="true"><i class="fa fa-angle-double-right"></i></span>
                    <?php endif; ?>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
