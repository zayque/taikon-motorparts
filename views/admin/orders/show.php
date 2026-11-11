<?php
/**
 * @var array<string, mixed> $order
 * @var list<array<string, mixed>> $items
 */
$payments = config('shipping.payment_methods');
$waPhone = preg_replace('/\D/', '', $order['phone']);
if (str_starts_with($waPhone, '0')) {
    $waPhone = '6' . $waPhone;
}
?>
<div class="admin-head">
    <div>
        <a class="btn btn--text btn--sm" href="<?= e(url('/admin/orders')) ?>"><?= icon('arrow-left') ?>Orders</a>
        <h1 class="cluster">Order <?= e($order['order_number']) ?> <?= status_badge($order['status']) ?></h1>
        <p>Placed <?= e(format_date($order['created_at'], 'l, j F Y \a\t g:i A')) ?></p>
    </div>
</div>

<div class="admin-form">
    <div class="stack">
        <div class="card">
            <div class="card__header"><h2>Items</h2></div>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Product</th><th>SKU</th><th class="num">Price</th><th class="num">Qty</th><th class="num">Total</th></tr></thead>
                    <tbody>
                        <?php foreach ($items as $i): ?>
                            <tr>
                                <td><?php if ($i['product_id']): ?><a href="<?= e(url('/admin/products/' . $i['product_id'] . '/edit')) ?>"><?= e($i['product_name']) ?></a><?php else: ?><?= e($i['product_name']) ?><?php endif; ?></td>
                                <td class="text-muted"><?= e($i['sku']) ?></td>
                                <td class="num"><?= e(money($i['unit_price'])) ?></td>
                                <td class="num"><?= (int) $i['quantity'] ?></td>
                                <td class="num"><?= e(money($i['line_total'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card__body" style="border-top:1px solid var(--line); max-width: 360px; margin-left:auto">
                <dl class="summary">
                    <div><dt>Subtotal</dt><dd><?= e(money($order['subtotal'])) ?></dd></div>
                    <div><dt>Delivery</dt><dd><?= e(money($order['shipping_fee'])) ?></dd></div>
                    <div class="summary__total"><dt>Total</dt><dd><?= e(money($order['total'])) ?></dd></div>
                </dl>
            </div>
        </div>

        <?php if ($order['notes']): ?>
            <div class="card card__body">
                <h2 style="font-size: var(--text-lg)">Customer notes</h2>
                <p class="message-body" style="margin:0"><?= e($order['notes']) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <div class="stack">
        <form class="card" action="<?= e(url('/admin/orders/' . $order['id'] . '/status')) ?>" method="post">
            <?= csrf_field() ?>
            <div class="card__header"><h2>Status</h2></div>
            <div class="card__body">
                <div class="status-form">
                    <label class="visually-hidden" for="status">Order status</label>
                    <select class="select" id="status" name="status">
                        <?php foreach (\App\Models\Order::STATUSES as $s): ?>
                            <option value="<?= e($s) ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn--primary btn--sm" type="submit">Update</button>
                </div>
                <p class="text-xs text-muted" style="margin: var(--space-3) 0 0">Cancelling returns items to stock automatically.</p>
            </div>
        </form>

        <div class="card">
            <div class="card__header"><h2>Customer</h2></div>
            <div class="card__body">
                <dl class="meta-list">
                    <dt>Name</dt><dd><?= e($order['customer_name']) ?></dd>
                    <dt>Email</dt><dd><a href="mailto:<?= e($order['email']) ?>"><?= e($order['email']) ?></a></dd>
                    <dt>Phone</dt><dd><a href="tel:<?= e($order['phone']) ?>"><?= e($order['phone']) ?></a></dd>
                    <dt>Account</dt><dd><?= $order['user_id'] ? 'Registered' : 'Guest' ?></dd>
                </dl>
                <a class="btn btn--dark btn--sm btn--block" style="margin-top: var(--space-4)" href="https://wa.me/<?= e($waPhone) ?>?text=<?= e(rawurlencode('Hi ' . $order['customer_name'] . ', regarding your Taikon Motorparts order ' . $order['order_number'] . ': ')) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?>WhatsApp customer</a>
            </div>
        </div>

        <div class="card">
            <div class="card__header"><h2>Fulfilment &amp; payment</h2></div>
            <div class="card__body">
                <dl class="meta-list">
                    <dt>Method</dt><dd><?= $order['fulfilment'] === 'pickup' ? 'Self-pickup' : 'Delivery' ?></dd>
                    <?php if ($order['fulfilment'] === 'delivery'): ?>
                        <dt>Address</dt><dd><?= e($order['address_line']) ?><br><?= e($order['postcode'] . ' ' . $order['city']) ?><br><?= e($order['state']) ?></dd>
                    <?php endif; ?>
                    <dt>Payment</dt><dd><?= e($payments[$order['payment_method']] ?? $order['payment_method']) ?></dd>
                </dl>
            </div>
        </div>
    </div>
</div>
