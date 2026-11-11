<?php
/**
 * @var array<string, string> $filters
 * @var array<string, int> $counts
 * @var \App\Support\Paginator $orders
 */
$all = array_sum($counts);
?>
<div class="admin-head">
    <div>
        <h1>Orders</h1>
        <p>Process new orders, mark them shipped and complete them once delivered or collected.</p>
    </div>
</div>

<nav class="tabs" aria-label="Order status">
    <a href="<?= e(url('/admin/orders', ['q' => $filters['q']])) ?>" <?= $filters['status'] === '' ? 'aria-current="page"' : '' ?>>All<span class="count"><?= $all ?></span></a>
    <?php foreach ($counts as $status => $n): ?>
        <a href="<?= e(url('/admin/orders', ['status' => $status, 'q' => $filters['q']])) ?>" <?= $filters['status'] === $status ? 'aria-current="page"' : '' ?>><?= e(ucfirst($status)) ?><span class="count"><?= $n ?></span></a>
    <?php endforeach; ?>
</nav>

<div class="card">
    <form class="toolbar" method="get" action="<?= e(url('/admin/orders')) ?>">
        <?php if ($filters['status']): ?><input type="hidden" name="status" value="<?= e($filters['status']) ?>"><?php endif; ?>
        <input class="input" type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="Order no., name, email or phone" aria-label="Search orders">
        <button class="btn btn--dark btn--sm" type="submit"><?= icon('search') ?>Search</button>
    </form>
    <?php if ($orders->items): ?>
        <?= partial('admin/orders/_table', ['orders' => $orders->items]) ?>
    <?php else: ?>
        <div class="empty"><span class="empty__icon"><?= icon('receipt') ?></span><p>No orders match.</p></div>
    <?php endif; ?>
</div>
<?= partial('partials/pagination', ['paginator' => $orders]) ?>
