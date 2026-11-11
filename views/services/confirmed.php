<?php
/** @var array<string, mixed> $booking */
$shop = config('shop');
?>
<section class="section">
    <div class="container confirm">
        <div class="confirm__head">
            <span class="confirm__icon"><?= icon('check') ?></span>
            <h1>You’re booked in!</h1>
            <p class="lead" style="margin-inline:auto">We’ll confirm your slot by phone or WhatsApp on <strong><?= e($booking['phone']) ?></strong>.</p>
            <p class="confirm__ref"><?= e($booking['booking_number']) ?></p>
        </div>

        <div class="card card__body">
            <dl class="meta-list">
                <dt>Service</dt><dd><?= e($booking['service_name']) ?></dd>
                <dt>Date</dt><dd><?= e(format_date($booking['booking_date'], 'l, j F Y')) ?></dd>
                <dt>Time</dt><dd><?= e(date('g:i A', strtotime($booking['booking_time']))) ?></dd>
                <dt>Motorcycle</dt><dd><?= e($booking['motorcycle']) ?><?= $booking['plate_number'] ? ' · ' . e($booking['plate_number']) : '' ?></dd>
                <dt>Status</dt><dd><?= status_badge($booking['status']) ?></dd>
                <dt>Where</dt><dd><?= e($shop['address']['line1']) ?>, <?= e($shop['address']['postcode'] . ' ' . $shop['address']['city']) ?></dd>
            </dl>
        </div>

        <div class="cluster" style="justify-content:center; margin-top: var(--space-6)">
            <?php if (auth_user()): ?>
                <a class="btn btn--dark" href="<?= e(url('/account/bookings')) ?>">My bookings</a>
            <?php endif; ?>
            <a class="btn btn--outline" href="<?= e(url('/shop')) ?>">Shop parts</a>
        </div>
    </div>
</section>
