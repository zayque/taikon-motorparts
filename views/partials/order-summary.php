<?php
/**
 * Shared order breakdown (confirmation page, account order page).
 * @var array<string, mixed> $order
 * @var list<array<string, mixed>> $items
 */
$payments = config('shipping.payment_methods');
?>
<div class="card">
    <div class="card__header">
        <h2>Order <?= e($order['order_number']) ?></h2>
        <?= status_badge($order['status']) ?>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Item</th><th class="num">Price</th><th class="num">Qty</th><th class="num">Total</th></tr></thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <?php if ($item['slug'] && $item['is_active']): ?>
                                <a href="<?= e(url('/shop/' . $item['slug'])) ?>"><?= e($item['product_name']) ?></a>
                            <?php else: ?>
                                <?= e($item['product_name']) ?>
                            <?php endif; ?>
                            <div class="text-xs text-muted">SKU <?= e($item['sku']) ?></div>
                        </td>
                        <td class="num"><?= e(money($item['unit_price'])) ?></td>
                        <td class="num"><?= (int) $item['quantity'] ?></td>
                        <td class="num"><?= e(money($item['line_total'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card__body grid grid-2" style="border-top: 1px solid var(--line)">
        <dl class="meta-list">
            <dt>Placed</dt><dd><?= e(format_date($order['created_at'], 'j M Y, g:i A')) ?></dd>
            <dt>Payment</dt><dd><?= e($payments[$order['payment_method']] ?? $order['payment_method']) ?></dd>
            <dt><?= $order['fulfilment'] === 'pickup' ? 'Collection' : 'Deliver to' ?></dt>
            <dd>
                <?php if ($order['fulfilment'] === 'pickup'): ?>
                    Self-pickup at workshop
                <?php else: ?>
                    <?= e($order['address_line']) ?><br><?= e($order['postcode'] . ' ' . $order['city']) ?>, <?= e($order['state']) ?>
                <?php endif; ?>
            </dd>
            <?php if ($order['notes']): ?><dt>Notes</dt><dd><?= nl2br(e($order['notes'])) ?></dd><?php endif; ?>
        </dl>
        <dl class="summary">
            <div><dt>Subtotal</dt><dd><?= e(money($order['subtotal'])) ?></dd></div>
            <div><dt>Delivery</dt><dd><?= (float) $order['shipping_fee'] > 0 ? e(money($order['shipping_fee'])) : 'Free' ?></dd></div>
            <div class="summary__total"><dt>Total</dt><dd><?= e(money($order['total'])) ?></dd></div>
        </dl>
    </div>
</div>
