<?php
/**
 * @var list<array<string, mixed>> $bookings
 * @var bool|null $actions show the cancel button
 */
$actions ??= false;
$tomorrow = date('Y-m-d', strtotime('+1 day'));
?>
<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Ref</th><th>Service</th><th>Date &amp; time</th><th>Bike</th><th>Status</th><?php if ($actions): ?><th></th><?php endif; ?></tr></thead>
        <tbody>
            <?php foreach ($bookings as $b): ?>
                <tr>
                    <td class="nowrap"><?= e($b['booking_number']) ?></td>
                    <td><?= e($b['service_name']) ?></td>
                    <td class="nowrap"><?= e(format_date($b['booking_date'], 'D, j M Y')) ?><br><span class="text-muted"><?= e(date('g:i A', strtotime($b['booking_time']))) ?></span></td>
                    <td><?= e($b['motorcycle']) ?><?php if ($b['plate_number']): ?><br><span class="text-muted"><?= e($b['plate_number']) ?></span><?php endif; ?></td>
                    <td><?= status_badge($b['status']) ?></td>
                    <?php if ($actions): ?>
                        <td class="num">
                            <?php if ($b['status'] === 'pending' && $b['booking_date'] >= $tomorrow): ?>
                                <form action="<?= e(url('/account/bookings/' . $b['id'] . '/cancel')) ?>" method="post" data-confirm="Cancel this booking?">
                                    <?= csrf_field() ?>
                                    <button class="btn btn--text btn--sm" type="submit">Cancel</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
