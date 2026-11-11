<?php
/**
 * @var array<string, string> $filters
 * @var \App\Support\Paginator $bookings
 */
$when = ['upcoming' => 'Upcoming', 'past' => 'Past', 'all' => 'All dates'];
?>
<div class="admin-head">
    <div>
        <h1>Bookings</h1>
        <p>Confirm new workshop bookings and mark them complete after handover.</p>
    </div>
</div>

<nav class="tabs" aria-label="Booking period">
    <?php foreach ($when as $key => $label): ?>
        <a href="<?= e(url('/admin/bookings', ['when' => $key, 'status' => $filters['status'], 'q' => $filters['q']])) ?>" <?= $filters['when'] === $key ? 'aria-current="page"' : '' ?>><?= e($label) ?></a>
    <?php endforeach; ?>
</nav>

<div class="card">
    <form class="toolbar" method="get" action="<?= e(url('/admin/bookings')) ?>" data-autosubmit>
        <input type="hidden" name="when" value="<?= e($filters['when']) ?>">
        <input class="input" type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="Ref, name, phone or plate" aria-label="Search bookings">
        <select class="select" name="status" aria-label="Status">
            <option value="">Any status</option>
            <?php foreach (\App\Models\Booking::STATUSES as $s): ?>
                <option value="<?= e($s) ?>" <?= $filters['status'] === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn--dark btn--sm" type="submit"><?= icon('search') ?>Filter</button>
    </form>

    <?php if ($bookings->items): ?>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>When</th><th>Customer</th><th>Service</th><th>Bike</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($bookings->items as $b): ?>
                        <tr>
                            <td class="nowrap"><strong><?= e(format_date($b['booking_date'], 'D, j M')) ?></strong><br><span class="text-muted"><?= e(date('g:i A', strtotime($b['booking_time']))) ?></span></td>
                            <td><a href="<?= e(url('/admin/bookings/' . $b['id'])) ?>"><?= e($b['customer_name']) ?></a><div class="text-xs text-muted"><?= e($b['phone']) ?></div></td>
                            <td><?= e($b['service_name']) ?></td>
                            <td><?= e($b['motorcycle']) ?><?php if ($b['plate_number']): ?><div class="text-xs text-muted"><?= e($b['plate_number']) ?></div><?php endif; ?></td>
                            <td><?= status_badge($b['status']) ?></td>
                            <td>
                                <?php if ($b['status'] === 'pending'): ?>
                                    <form action="<?= e(url('/admin/bookings/' . $b['id'] . '/status')) ?>" method="post">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="status" value="confirmed">
                                        <button class="btn btn--outline btn--sm" type="submit"><?= icon('check') ?>Confirm</button>
                                    </form>
                                <?php elseif ($b['status'] === 'confirmed'): ?>
                                    <form action="<?= e(url('/admin/bookings/' . $b['id'] . '/status')) ?>" method="post">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="status" value="completed">
                                        <button class="btn btn--outline btn--sm" type="submit">Complete</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty"><span class="empty__icon"><?= icon('calendar') ?></span><p>No bookings match.</p></div>
    <?php endif; ?>
</div>
<?= partial('partials/pagination', ['paginator' => $bookings]) ?>
