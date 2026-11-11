<?php /** @var list<array<string, mixed>> $orders */ ?>
<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Order</th><th>Customer</th><th>Placed</th><th>Fulfilment</th><th>Status</th><th class="num">Total</th></tr></thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td><a href="<?= e(url('/admin/orders/' . $o['id'])) ?>"><?= e($o['order_number']) ?></a></td>
                    <td><?= e($o['customer_name']) ?><div class="text-xs text-muted"><?= e($o['phone']) ?></div></td>
                    <td class="nowrap"><?= e(format_date($o['created_at'], 'j M, g:i A')) ?></td>
                    <td><?= $o['fulfilment'] === 'pickup' ? 'Pickup' : 'Delivery' ?></td>
                    <td><?= status_badge($o['status']) ?></td>
                    <td class="num"><?= e(money($o['total'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
