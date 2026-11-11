<?php
/**
 * @var list<array<string, mixed>> $services
 * @var int $selected
 * @var array<string, mixed>|null $user
 * @var string $date
 * @var array<string, int> $slots  time => remaining capacity
 * @var string $minDate
 * @var string $maxDate
 */
$selectedTime = old('booking_time', '');
?>

<?= partial('partials/page-hero', [
    'crumbs'  => [['Services', '/services'], ['Book', null]],
    'eyebrow' => 'Online booking',
    'heading' => 'Book a workshop slot',
    'lead'    => 'Choose a service and time. We’ll confirm by phone or WhatsApp.',
]) ?>

<section class="section section--tight">
    <form class="container cart-layout" action="<?= e(url('/services/book')) ?>" method="post" novalidate data-booking>
        <?= csrf_field() ?>
        <div>
            <div class="card card__body checkout-section">
                <h2><span class="num">1</span>Choose a service</h2>
                <div class="options">
                    <?php foreach ($services as $s): ?>
                        <label class="option">
                            <input type="radio" name="service_id" value="<?= (int) $s['id'] ?>" <?= $selected === (int) $s['id'] ? 'checked' : '' ?> required>
                            <div style="flex:1">
                                <div class="spread">
                                    <strong><?= e($s['name']) ?></strong>
                                    <strong class="nowrap">from <?= e(money($s['price_from'])) ?></strong>
                                </div>
                                <span><?= e($s['summary']) ?></span>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
                <?= field_error('service_id') ?>
            </div>

            <div class="card card__body checkout-section">
                <h2><span class="num">2</span>Pick a date &amp; time</h2>
                <div class="<?= field_class('booking_date') ?>" style="max-width: 280px">
                    <label for="booking_date">Date</label>
                    <input class="input" type="date" id="booking_date" name="booking_date" value="<?= e($date) ?>" min="<?= e($minDate) ?>" max="<?= e($maxDate) ?>" required data-slots-url="<?= e(url('/services/slots')) ?>" <?= field_attrs('booking_date') ?>>
                    <span class="hint">Sundays: mornings only.</span>
                    <?= field_error('booking_date') ?>
                </div>

                <fieldset class="<?= field_class('booking_time') ?>" style="border:0; padding:0; margin: var(--space-5) 0 0">
                    <legend class="label" style="margin-bottom: var(--space-2)">Time</legend>
                    <div class="slot-grid" data-slots aria-live="polite">
                        <?php foreach ($slots as $time => $left): ?>
                            <label class="slot">
                                <input type="radio" name="booking_time" value="<?= e($time) ?>" <?= $left <= 0 ? 'disabled' : '' ?> <?= $selectedTime === $time ? 'checked' : '' ?>>
                                <strong><?= e(date('g:i A', strtotime($time))) ?></strong>
                                <span><?= $left > 0 ? $left . ' left' : 'Full' ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <?= field_error('booking_time') ?>
                </fieldset>
            </div>

            <div class="card card__body checkout-section">
                <h2><span class="num">3</span>Your details</h2>
                <div class="form-grid">
                    <div class="<?= field_class('customer_name') ?>">
                        <label for="customer_name">Name</label>
                        <input class="input" id="customer_name" name="customer_name" autocomplete="name" required value="<?= e(old('customer_name', $user['name'] ?? '')) ?>" <?= field_attrs('customer_name') ?>>
                        <?= field_error('customer_name') ?>
                    </div>
                    <div class="<?= field_class('phone') ?>">
                        <label for="phone">Phone / WhatsApp</label>
                        <input class="input" id="phone" type="tel" name="phone" autocomplete="tel" required value="<?= e(old('phone', $user['phone'] ?? '')) ?>" <?= field_attrs('phone') ?>>
                        <?= field_error('phone') ?>
                    </div>
                    <div class="<?= field_class('email') ?> span-2">
                        <label for="email">Email</label>
                        <input class="input" id="email" type="email" name="email" autocomplete="email" required value="<?= e(old('email', $user['email'] ?? '')) ?>" <?= field_attrs('email') ?>>
                        <?= field_error('email') ?>
                    </div>
                    <div class="<?= field_class('motorcycle') ?>">
                        <label for="motorcycle">Motorcycle make &amp; model</label>
                        <input class="input" id="motorcycle" name="motorcycle" required placeholder="e.g. Yamaha Y15ZR 2021" value="<?= e(old('motorcycle')) ?>" <?= field_attrs('motorcycle') ?>>
                        <?= field_error('motorcycle') ?>
                    </div>
                    <div class="<?= field_class('plate_number') ?>">
                        <label for="plate_number">Plate number <span class="optional">(optional)</span></label>
                        <input class="input" id="plate_number" name="plate_number" maxlength="15" style="text-transform: uppercase" value="<?= e(old('plate_number')) ?>" <?= field_attrs('plate_number') ?>>
                        <?= field_error('plate_number') ?>
                    </div>
                    <div class="<?= field_class('notes') ?> span-2">
                        <label for="notes">What should we know? <span class="optional">(optional)</span></label>
                        <textarea class="textarea" id="notes" name="notes" rows="3" maxlength="1000" placeholder="Symptoms, mileage, parts you’re bringing…"><?= e(old('notes')) ?></textarea>
                        <?= field_error('notes') ?>
                    </div>
                </div>
            </div>
        </div>

        <aside class="cart-layout__aside">
            <div class="card card__body">
                <h2 style="font-size: var(--text-xl)">Good to know</h2>
                <ul class="perks">
                    <li><?= icon('clock') ?>Please arrive 5–10 minutes early.</li>
                    <li><?= icon('receipt') ?>Pay at handover — no deposit needed.</li>
                    <li><?= icon('shield') ?>We quote before any extra work.</li>
                    <li><?= icon('calendar') ?>Cancel online up to the day before.</li>
                </ul>
                <button class="btn btn--primary btn--lg btn--block" type="submit" style="margin-top: var(--space-5)" data-submit-once><?= icon('check') ?>Confirm booking</button>
                <?php if (!$user): ?>
                    <p class="text-xs text-muted text-center" style="margin: var(--space-3) 0 0"><a href="<?= e(url('/login')) ?>">Sign in</a> to manage bookings from your account.</p>
                <?php endif; ?>
            </div>
        </aside>
    </form>
</section>
