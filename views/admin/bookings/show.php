<?php
/** @var array<string, mixed> $booking */
$waPhone = preg_replace('/\D/', '', $booking['phone']);
if (str_starts_with($waPhone, '0')) {
    $waPhone = '6' . $waPhone;
}
$waText = sprintf(
    'Hi %s, this is Taikon Motorparts confirming your %s booking on %s at %s (ref %s).',
    $booking['customer_name'],
    $booking['service_name'],
    format_date($booking['booking_date'], 'D j M'),
    date('g:i A', strtotime($booking['booking_time'])),
    $booking['booking_number']
);
?>
<div class="admin-head">
    <div>
        <a class="btn btn--text btn--sm" href="<?= e(url('/admin/bookings')) ?>"><?= icon('arrow-left') ?>Bookings</a>
        <h1 class="cluster">Booking <?= e($booking['booking_number']) ?> <?= status_badge($booking['status']) ?></h1>
        <p>Received <?= e(format_date($booking['created_at'], 'j M Y, g:i A')) ?></p>
    </div>
</div>

<div class="admin-form">
    <div class="stack">
        <div class="card card__body">
            <dl class="meta-list">
                <dt>Service</dt><dd><?= e($booking['service_name']) ?></dd>
                <dt>Date</dt><dd><?= e(format_date($booking['booking_date'], 'l, j F Y')) ?></dd>
                <dt>Time</dt><dd><?= e(date('g:i A', strtotime($booking['booking_time']))) ?></dd>
                <dt>Motorcycle</dt><dd><?= e($booking['motorcycle']) ?></dd>
                <dt>Plate</dt><dd><?= e($booking['plate_number'] ?: '—') ?></dd>
                <dt>Customer</dt><dd><?= e($booking['customer_name']) ?></dd>
                <dt>Phone</dt><dd><a href="tel:<?= e($booking['phone']) ?>"><?= e($booking['phone']) ?></a></dd>
                <dt>Email</dt><dd><a href="mailto:<?= e($booking['email']) ?>"><?= e($booking['email']) ?></a></dd>
            </dl>
        </div>
        <?php if ($booking['notes']): ?>
            <div class="card card__body">
                <h2 style="font-size: var(--text-lg)">Customer notes</h2>
                <p class="message-body" style="margin:0"><?= e($booking['notes']) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <div class="stack">
        <form class="card" action="<?= e(url('/admin/bookings/' . $booking['id'] . '/status')) ?>" method="post">
            <?= csrf_field() ?>
            <div class="card__header"><h2>Status</h2></div>
            <div class="card__body status-form">
                <label class="visually-hidden" for="status">Booking status</label>
                <select class="select" id="status" name="status">
                    <?php foreach (\App\Models\Booking::STATUSES as $s): ?>
                        <option value="<?= e($s) ?>" <?= $booking['status'] === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn--primary btn--sm" type="submit">Update</button>
            </div>
        </form>
        <a class="btn btn--dark btn--block" href="https://wa.me/<?= e($waPhone) ?>?text=<?= e(rawurlencode($waText)) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?>Send confirmation on WhatsApp</a>
    </div>
</div>
