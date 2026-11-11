<?php
/** @var list<array<string, mixed>> $orders */
ob_start();
?>
<div class="card">
    <?php if ($orders): ?>
        <?= partial('account/_orders-table', ['orders' => $orders]) ?>
    <?php else: ?>
        <div class="empty">
            <span class="empty__icon"><?= icon('receipt') ?></span>
            <h3>No orders yet</h3>
            <p>When you place an order it will show up here with its status.</p>
            <a class="btn btn--primary" href="<?= e(url('/shop')) ?>">Start shopping</a>
        </div>
    <?php endif; ?>
</div>
<?php
echo partial('account/_layout', ['heading' => 'My orders', 'body' => ob_get_clean()]);
