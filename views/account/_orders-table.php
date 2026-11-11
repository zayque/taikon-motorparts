<?php /** @var list<array<string, mixed>> $orders */ ?>
<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Order</th><th>Date</th><th>Items</th><th>Status</th><th class="num">Total</th></tr></thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td><a href="<?= e(url('/account/orders/' . $o['order_number'])) ?>"><?= e($o['order_number']) ?></a></td>
                    <td class="nowrap"><?= e(format_date($o['created_at'])) ?></td>
                    <td><?= (int) $o['item_count'] ?></td>
                    <td><?= status_badge($o['status']) ?></td>
                    <td class="num"><?= e(money($o['total'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
