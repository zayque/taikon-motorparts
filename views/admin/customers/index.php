<?php
/**
 * @var string $q
 * @var \App\Support\Paginator $customers
 */
?>
<div class="admin-head">
    <div>
        <h1>Customers</h1>
        <p><?= $customers->total ?> registered customer<?= $customers->total === 1 ? '' : 's' ?>.</p>
    </div>
</div>

<div class="card">
    <form class="toolbar" method="get" action="<?= e(url('/admin/customers')) ?>">
        <input class="input" type="search" name="q" value="<?= e($q) ?>" placeholder="Name, email or phone" aria-label="Search customers">
        <button class="btn btn--dark btn--sm" type="submit"><?= icon('search') ?>Search</button>
    </form>
    <?php if ($customers->items): ?>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Customer</th><th>Phone</th><th>Location</th><th>Joined</th><th class="num">Orders</th><th class="num">Lifetime value</th></tr></thead>
                <tbody>
                    <?php foreach ($customers->items as $c): ?>
                        <tr>
                            <td><strong><?= e($c['name']) ?></strong><div class="text-xs text-muted"><a href="mailto:<?= e($c['email']) ?>"><?= e($c['email']) ?></a></div></td>
                            <td><?= e($c['phone'] ?: '—') ?></td>
                            <td><?= e(trim(($c['city'] ?? '') . ($c['state'] ? ', ' . $c['state'] : ''), ', ') ?: '—') ?></td>
                            <td class="nowrap"><?= e(format_date($c['created_at'])) ?></td>
                            <td class="num"><a href="<?= e(url('/admin/orders', ['q' => $c['email']])) ?>"><?= (int) $c['order_count'] ?></a></td>
                            <td class="num"><?= e(money($c['lifetime_value'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty"><span class="empty__icon"><?= icon('users') ?></span><p>No customers found.</p></div>
    <?php endif; ?>
</div>
<?= partial('partials/pagination', ['paginator' => $customers]) ?>
