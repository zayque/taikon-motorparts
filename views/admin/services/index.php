<?php /** @var list<array<string, mixed>> $services */ ?>
<div class="admin-head">
    <div>
        <h1>Services</h1>
        <p>Workshop services and packages customers can book online.</p>
    </div>
    <a class="btn btn--primary" href="<?= e(url('/admin/services/create')) ?>"><?= icon('plus') ?>Add service</a>
</div>

<div class="card">
    <?php if ($services): ?>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Service</th><th>Type</th><th class="num">From</th><th class="num">Duration</th><th class="num">Bookings</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($services as $s): ?>
                        <tr>
                            <td>
                                <div class="product-cell">
                                    <span class="feature__icon" style="width:36px;height:36px"><?= icon($s['icon'], 'icon-sm') ?></span>
                                    <div><a href="<?= e(url('/admin/services/' . $s['id'] . '/edit')) ?>"><?= e($s['name']) ?></a><small><?= e(str_excerpt($s['summary'], 60)) ?></small></div>
                                </div>
                            </td>
                            <td><?= $s['is_package'] ? '<span class="badge badge--accent">Package</span>' : 'Service' ?></td>
                            <td class="num"><?= e(money($s['price_from'])) ?></td>
                            <td class="num"><?= (int) $s['duration_minutes'] ?> min</td>
                            <td class="num"><?= (int) $s['booking_count'] ?></td>
                            <td><?= $s['is_active'] ? '<span class="badge badge--success">Bookable</span>' : '<span class="badge badge--neutral">Hidden</span>' ?></td>
                            <td><div class="inline-actions"><a class="icon-btn" href="<?= e(url('/admin/services/' . $s['id'] . '/edit')) ?>" title="Edit"><?= icon('edit') ?><span class="visually-hidden">Edit <?= e($s['name']) ?></span></a></div></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty"><span class="empty__icon"><?= icon('wrench') ?></span><p>No services yet.</p></div>
    <?php endif; ?>
</div>
