<?php
/** @var \App\Support\Paginator $paginator */
if (!$paginator->hasPages()) {
    return;
}
?>
<nav class="pagination" aria-label="Pagination">
    <?php if ($paginator->page > 1): ?>
        <a href="<?= e($paginator->url($paginator->page - 1)) ?>" rel="prev" aria-label="Previous page"><?= icon('chevron-left', 'icon-sm') ?></a>
    <?php else: ?>
        <span class="is-disabled" aria-hidden="true"><?= icon('chevron-left', 'icon-sm') ?></span>
    <?php endif; ?>

    <?php foreach ($paginator->window() as $p): ?>
        <?php if ($p === null): ?>
            <span class="gap">…</span>
        <?php elseif ($p === $paginator->page): ?>
            <span aria-current="page"><?= $p ?></span>
        <?php else: ?>
            <a href="<?= e($paginator->url($p)) ?>"><?= $p ?></a>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if ($paginator->page < $paginator->lastPage): ?>
        <a href="<?= e($paginator->url($paginator->page + 1)) ?>" rel="next" aria-label="Next page"><?= icon('chevron-right', 'icon-sm') ?></a>
    <?php else: ?>
        <span class="is-disabled" aria-hidden="true"><?= icon('chevron-right', 'icon-sm') ?></span>
    <?php endif; ?>
</nav>
