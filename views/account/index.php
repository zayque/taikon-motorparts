<?php
/**
 * @var list<array<string, mixed>> $orders
 * @var list<array<string, mixed>> $bookings
 * @var int $orderCount
 * @var int $upcomingCount
 */
$user = auth_user();
ob_start();
?>
<div class="stat-tiles">
    <div class="stat-tile"><small>Orders</small><strong><?= (int) $orderCount ?></strong></div>
    <div class="stat-tile"><small>Upcoming bookings</small><strong><?= (int) $upcomingCount ?></strong></div>
    <div class="stat-tile"><small>Member since</small><strong><?= e(format_date($user['created_at'], 'M Y')) ?></strong></div>
</div>

<div class="card">
    <div class="card__header">
        <h2>Recent orders</h2>
        <a class="btn btn--text btn--sm" href="<?= e(url('/account/orders')) ?>">View all</a>
    </div>
    <?php if ($orders): ?>
        <?= partial('account/_orders-table', ['orders' => $orders]) ?>
    <?php else: ?>
        <div class="empty">
            <span class="empty__icon"><?= icon('receipt') ?></span>
            <p>No orders yet.</p>
            <a class="btn btn--primary btn--sm" href="<?= e(url('/shop')) ?>">Shop parts</a>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card__header">
        <h2>Workshop bookings</h2>
        <a class="btn btn--text btn--sm" href="<?= e(url('/account/bookings')) ?>">View all</a>
    </div>
    <?php if ($bookings): ?>
        <?= partial('account/_bookings-table', ['bookings' => $bookings]) ?>
    <?php else: ?>
        <div class="empty">
            <span class="empty__icon"><?= icon('calendar') ?></span>
            <p>No bookings yet.</p>
            <a class="btn btn--primary btn--sm" href="<?= e(url('/services/book')) ?>">Book a service</a>
        </div>
    <?php endif; ?>
</div>
<?php
echo partial('account/_layout', ['heading' => 'Hi, ' . explode(' ', $user['name'])[0], 'body' => ob_get_clean()]);
