<?php
/** @var list<array<string, mixed>> $bookings */
ob_start();
?>
<div class="spread">
    <p class="text-muted" style="margin:0">Pending bookings can be cancelled online up to the day before.</p>
    <a class="btn btn--primary btn--sm" href="<?= e(url('/services/book')) ?>"><?= icon('plus') ?>New booking</a>
</div>
<div class="card">
    <?php if ($bookings): ?>
        <?= partial('account/_bookings-table', ['bookings' => $bookings, 'actions' => true]) ?>
    <?php else: ?>
        <div class="empty">
            <span class="empty__icon"><?= icon('calendar') ?></span>
            <h3>No bookings yet</h3>
            <p>Book a service online and pick a time that suits you.</p>
            <a class="btn btn--primary" href="<?= e(url('/services/book')) ?>">Book a service</a>
        </div>
    <?php endif; ?>
</div>
<?php
echo partial('account/_layout', ['heading' => 'My bookings', 'body' => ob_get_clean()]);
