<?php $pager->setSurroundCount(2) ?>

<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center flex-wrap gap-1 mb-0">

        <?php if ($pager->hasPrevious()): ?>
            <li class="page-item">
                <a class="page-link rounded-pill px-3" href="<?= $pager->getFirst() ?>" aria-label="First">
                    <i class="fas fa-angle-double-left"></i>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link rounded-pill px-3" href="<?= $pager->getPrevious() ?>" aria-label="Previous">
                    <i class="fas fa-angle-left"></i>
                </a>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link rounded-pill px-3 fw-semibold" href="<?= $link['uri'] ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNext()): ?>
            <li class="page-item">
                <a class="page-link rounded-pill px-3" href="<?= $pager->getNext() ?>" aria-label="Next">
                    <i class="fas fa-angle-right"></i>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link rounded-pill px-3" href="<?= $pager->getLast() ?>" aria-label="Last">
                    <i class="fas fa-angle-double-right"></i>
                </a>
            </li>
        <?php endif ?>

    </ul>
</nav>