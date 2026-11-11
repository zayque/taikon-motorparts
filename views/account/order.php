<?php
/**
 * @var array<string, mixed> $order
 * @var list<array<string, mixed>> $items
 */
ob_start();
?>
<a class="btn btn--text btn--sm" href="<?= e(url('/account/orders')) ?>"><?= icon('arrow-left') ?>All orders</a>
<?= partial('partials/order-summary', ['order' => $order, 'items' => $items]) ?>
<p class="text-sm text-muted">Questions about this order? <a href="<?= e(url('/contact')) ?>">Contact us</a> and quote <strong><?= e($order['order_number']) ?></strong>.</p>
<?php
echo partial('account/_layout', ['heading' => 'Order details', 'body' => ob_get_clean()]);
