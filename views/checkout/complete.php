<?php
/**
 * @var array<string, mixed> $order
 * @var list<array<string, mixed>> $items
 */
$bank = config('shipping.bank');
?>
<section class="section">
    <div class="container confirm">
        <div class="confirm__head">
            <span class="confirm__icon"><?= icon('check') ?></span>
            <h1>Thank you, <?= e(explode(' ', $order['customer_name'])[0]) ?>!</h1>
            <p class="lead" style="margin-inline:auto">Your order has been received. We’ll contact you at <strong><?= e($order['phone']) ?></strong> to confirm.</p>
            <p class="confirm__ref"><?= e($order['order_number']) ?></p>
        </div>

        <?php if ($order['payment_method'] === 'bank_transfer'): ?>
            <div class="alert alert--info" style="margin-bottom: var(--space-5)">
                <?= icon('info') ?>
                <div>
                    <p><strong>Complete your payment by bank transfer</strong></p>
                    <p><?= e($bank['name']) ?> · <?= e($bank['account']) ?> · <?= e($bank['holder']) ?><br>
                    Amount: <strong><?= e(money($order['total'])) ?></strong> · Reference: <strong><?= e($order['order_number']) ?></strong></p>
                    <p>Send the receipt to us on WhatsApp so we can process your order right away.</p>
                </div>
            </div>
        <?php endif; ?>

        <?= partial('partials/order-summary', ['order' => $order, 'items' => $items]) ?>

        <div class="cluster" style="justify-content:center; margin-top: var(--space-6)">
            <a class="btn btn--dark" href="<?= e(url('/account/orders')) ?>">View my orders</a>
            <a class="btn btn--outline" href="<?= e(url('/shop')) ?>">Continue shopping</a>
        </div>
    </div>
</section>
