<?php
/**
 * @var float $revenueMonth
 * @var float $revenueToday
 * @var array<string, int> $orderCounts
 * @var int $pendingBookings
 * @var int $unreadMessages
 * @var int $customers
 * @var int $products
 * @var list<array{day: string, total: float}> $daily
 * @var list<array<string, mixed>> $recentOrders
 * @var list<array<string, mixed>> $upcoming
 * @var list<array<string, mixed>> $lowStock
 */
$max = max(1, max(array_column($daily, 'total')));
$periodTotal = array_sum(array_column($daily, 'total'));
$today = date('Y-m-d');
?>
<div class="admin-head">
    <div>
        <h1>Good <?= (int) date('G') < 12 ? 'morning' : ((int) date('G') < 18 ? 'afternoon' : 'evening') ?></h1>
        <p><?= e(date('l, j F Y')) ?> — here’s what needs your attention.</p>
    </div>
    <div class="cluster">
        <a class="btn btn--outline btn--sm" href="<?= e(url('/admin/products/create')) ?>"><?= icon('plus') ?>Add product</a>
    </div>
</div>

<div class="kpis">
    <div class="kpi kpi--accent">
        <?= icon('tag') ?>
        <small>Revenue this month</small>
        <strong><?= e(money($revenueMonth)) ?></strong>
        <span>Today: <?= e(money($revenueToday)) ?></span>
    </div>
    <a class="kpi" href="<?= e(url('/admin/orders', ['status' => 'pending'])) ?>">
        <?= icon('receipt') ?>
        <small>Orders to process</small>
        <strong><?= $orderCounts['pending'] + $orderCounts['processing'] ?></strong>
        <span><?= $orderCounts['pending'] ?> new · <?= $orderCounts['processing'] ?> processing</span>
    </a>
    <a class="kpi" href="<?= e(url('/admin/bookings', ['status' => 'pending'])) ?>">
        <?= icon('calendar') ?>
        <small>Bookings to confirm</small>
        <strong><?= $pendingBookings ?></strong>
        <span><?= count($upcoming) ?> upcoming shown below</span>
    </a>
    <a class="kpi" href="<?= e(url('/admin/messages', ['filter' => 'unread'])) ?>">
        <?= icon('message') ?>
        <small>Unread messages</small>
        <strong><?= $unreadMessages ?></strong>
        <span><?= $customers ?> customers · <?= $products ?> live products</span>
    </a>
</div>

<div class="admin-grid">
    <div class="stack">
        <div class="card">
            <div class="card__header">
                <h2>Revenue — last 14 days</h2>
                <strong><?= e(money($periodTotal)) ?></strong>
            </div>
            <div class="bars" role="img" aria-label="Daily revenue for the last 14 days, total <?= e(money($periodTotal)) ?>">
                <?php foreach ($daily as $d): ?>
                    <div class="bars__col <?= $d['day'] === $today ? 'is-today' : '' ?>" data-tip="<?= e(format_date($d['day'], 'j M')) ?>: <?= e(money($d['total'])) ?>">
                        <div class="bars__bar" style="height: <?= round($d['total'] / $max * 100, 1) ?>%"></div>
                        <span class="bars__label"><?= e(date('j', strtotime($d['day']))) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="height: var(--space-4)"></div>
        </div>

        <div class="card">
            <div class="card__header">
                <h2>Recent orders</h2>
                <a class="btn btn--text btn--sm" href="<?= e(url('/admin/orders')) ?>">All orders</a>
            </div>
            <?php if ($recentOrders): ?>
                <?= partial('admin/orders/_table', ['orders' => $recentOrders]) ?>
            <?php else: ?>
                <div class="empty"><span class="empty__icon"><?= icon('receipt') ?></span><p>No orders yet. They’ll appear here as soon as customers check out.</p></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="stack">
        <div class="card">
            <div class="card__header">
                <h2>Upcoming bookings</h2>
                <a class="btn btn--text btn--sm" href="<?= e(url('/admin/bookings')) ?>">All</a>
            </div>
            <?php if ($upcoming): ?>
                <ul class="list">
                    <?php foreach ($upcoming as $b): ?>
                        <li>
                            <div>
                                <a href="<?= e(url('/admin/bookings/' . $b['id'])) ?>"><?= e($b['customer_name']) ?></a>
                                <div class="text-xs text-muted"><?= e($b['service_name']) ?> · <?= e($b['motorcycle']) ?></div>
                            </div>
                            <div class="text-right">
                                <div class="nowrap text-sm"><?= e(format_date($b['booking_date'], 'D j M')) ?>, <?= e(date('g:i A', strtotime($b['booking_time']))) ?></div>
                                <?= status_badge($b['status']) ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="empty"><p>No upcoming bookings.</p></div>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card__header">
                <h2>Low stock</h2>
                <a class="btn btn--text btn--sm" href="<?= e(url('/admin/products', ['stock' => 'low'])) ?>">View</a>
            </div>
            <?php if ($lowStock): ?>
                <ul class="list">
                    <?php foreach ($lowStock as $p): ?>
                        <li>
                            <div>
                                <a href="<?= e(url('/admin/products/' . $p['id'] . '/edit')) ?>"><?= e($p['name']) ?></a>
                                <div class="text-xs text-muted"><?= e($p['sku']) ?></div>
                            </div>
                            <span class="badge <?= (int) $p['stock'] === 0 ? 'badge--danger' : 'badge--warning' ?>"><?= (int) $p['stock'] ?> left</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="empty"><p>All products are well stocked.</p></div>
            <?php endif; ?>
        </div>
    </div>
</div>
