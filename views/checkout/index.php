<?php
/**
 * @var array{lines: list<array<string, mixed>>, subtotal: float, count: int} $cart
 * @var array<string, mixed> $user
 */
$methods = config('shipping.methods');
$payments = config('shipping.payment_methods');
$fulfilment = old('fulfilment', 'delivery');
$payment = old('payment_method', 'cod');
$flat = (float) config('shipping.flat_rate');
$freeOver = (float) config('shipping.free_over');
$deliveryFee = \App\Services\Cart::shippingFor($cart['subtotal'], 'delivery');
$states = ['Johor', 'Kedah', 'Kelantan', 'Kuala Lumpur', 'Labuan', 'Melaka', 'Negeri Sembilan', 'Pahang', 'Penang', 'Perak', 'Perlis', 'Putrajaya', 'Sabah', 'Sarawak', 'Selangor', 'Terengganu'];
?>

<?= partial('partials/page-hero', [
    'crumbs'  => [['Cart', '/cart'], ['Checkout', null]],
    'heading' => 'Checkout',
    'lead'    => 'Secure checkout — we’ll confirm your order by email.',
]) ?>

<section class="section section--tight">
    <form class="container cart-layout" action="<?= e(url('/checkout')) ?>" method="post" novalidate data-checkout
          data-subtotal="<?= e($cart['subtotal']) ?>" data-flat="<?= e($flat) ?>" data-free-over="<?= e($freeOver) ?>">
        <?= csrf_field() ?>

        <div>
            <div class="card card__body checkout-section">
                <h2><span class="num">1</span>Contact details</h2>
                <div class="form-grid">
                    <div class="<?= field_class('name') ?> span-2">
                        <label for="name">Full name</label>
                        <input class="input" id="name" name="name" autocomplete="name" required value="<?= e(old('name', $user['name'])) ?>" <?= field_attrs('name') ?>>
                        <?= field_error('name') ?>
                    </div>
                    <div class="<?= field_class('email') ?>">
                        <label for="email">Email</label>
                        <input class="input" id="email" type="email" name="email" autocomplete="email" required value="<?= e(old('email', $user['email'])) ?>" <?= field_attrs('email') ?>>
                        <?= field_error('email') ?>
                    </div>
                    <div class="<?= field_class('phone') ?>">
                        <label for="phone">Phone</label>
                        <input class="input" id="phone" type="tel" name="phone" autocomplete="tel" required value="<?= e(old('phone', $user['phone'] ?? '')) ?>" placeholder="012-345 6789" <?= field_attrs('phone') ?>>
                        <?= field_error('phone') ?>
                    </div>
                </div>
            </div>

            <div class="card card__body checkout-section">
                <h2><span class="num">2</span>Delivery</h2>
                <div class="options options--2" role="radiogroup" aria-label="Delivery method">
                    <?php foreach ($methods as $value => $label): ?>
                        <label class="option">
                            <input type="radio" name="fulfilment" value="<?= e($value) ?>" <?= $fulfilment === $value ? 'checked' : '' ?>>
                            <div>
                                <strong><?= e($label) ?></strong>
                                <?php if ($value === 'delivery'): ?>
                                    <span><?= $deliveryFee > 0 ? e(money($flat)) . ' · free over ' . e(money($freeOver)) : 'Free for this order' ?></span>
                                <?php else: ?>
                                    <span>Free · Kuang workshop, usually ready same day</span>
                                <?php endif; ?>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="form-grid" style="margin-top: var(--space-5)" data-show-when="delivery" <?= $fulfilment !== 'delivery' ? 'hidden' : '' ?>>
                    <div class="<?= field_class('address_line') ?> span-2">
                        <label for="address_line">Street address</label>
                        <input class="input" id="address_line" name="address_line" autocomplete="street-address" value="<?= e(old('address_line', $user['address_line'] ?? '')) ?>" <?= field_attrs('address_line') ?>>
                        <?= field_error('address_line') ?>
                    </div>
                    <div class="<?= field_class('postcode') ?>">
                        <label for="postcode">Postcode</label>
                        <input class="input" id="postcode" name="postcode" autocomplete="postal-code" inputmode="numeric" maxlength="5" value="<?= e(old('postcode', $user['postcode'] ?? '')) ?>" <?= field_attrs('postcode') ?>>
                        <?= field_error('postcode') ?>
                    </div>
                    <div class="<?= field_class('city') ?>">
                        <label for="city">City</label>
                        <input class="input" id="city" name="city" autocomplete="address-level2" value="<?= e(old('city', $user['city'] ?? '')) ?>" <?= field_attrs('city') ?>>
                        <?= field_error('city') ?>
                    </div>
                    <div class="<?= field_class('state') ?> span-2">
                        <label for="state">State</label>
                        <?php $selectedState = old('state', $user['state'] ?? 'Selangor'); ?>
                        <select class="select" id="state" name="state" autocomplete="address-level1" <?= field_attrs('state') ?>>
                            <?php foreach ($states as $s): ?>
                                <option <?= $selectedState === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= field_error('state') ?>
                    </div>
                </div>
                <p class="text-sm text-muted" data-show-when="pickup" <?= $fulfilment !== 'pickup' ? 'hidden' : '' ?> style="margin: var(--space-4) 0 0">
                    <?= icon('map-pin', 'icon-sm') ?> We’ll message you when your order is ready to collect at <?= e(config('shop.address.line1')) ?>, <?= e(config('shop.address.city')) ?>.
                </p>
            </div>

            <div class="card card__body checkout-section">
                <h2><span class="num">3</span>Payment</h2>
                <div class="options" role="radiogroup" aria-label="Payment method">
                    <?php foreach ($payments as $value => $label): ?>
                        <label class="option" <?= $value === 'pay_at_store' ? 'data-pickup-only' : '' ?>>
                            <input type="radio" name="payment_method" value="<?= e($value) ?>" <?= $payment === $value ? 'checked' : '' ?>>
                            <div>
                                <strong><?= e($label) ?></strong>
                                <span><?= match ($value) {
                                    'cod'           => 'Pay the rider in cash when your parcel arrives.',
                                    'bank_transfer' => 'We’ll show our bank details after you place the order.',
                                    default         => 'Pay by cash, card or DuitNow QR when you collect.',
                                } ?></span>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
                <?= field_error('payment_method') ?>

                <div class="<?= field_class('notes') ?>" style="margin-top: var(--space-5)">
                    <label for="notes">Order notes <span class="optional">(optional)</span></label>
                    <textarea class="textarea" id="notes" name="notes" rows="3" maxlength="500" placeholder="Bike model, preferred delivery time, etc."><?= e(old('notes')) ?></textarea>
                    <?= field_error('notes') ?>
                </div>
            </div>
        </div>

        <aside class="cart-layout__aside">
            <div class="card card__body">
                <h2 style="font-size: var(--text-xl)">Your order</h2>
                <?php foreach ($cart['lines'] as $line): $p = $line['product']; ?>
                    <div class="mini-line">
                        <span class="mini-line__media">
                            <?php if ($p['image']): ?><img src="<?= e(upload_url($p['image'])) ?>" alt=""><?php else: ?><span class="placeholder-art"><?= icon($p['category_icon']) ?></span><?php endif; ?>
                            <span class="mini-line__qty"><?= (int) $line['quantity'] ?></span>
                        </span>
                        <span class="mini-line__name"><?= e($p['name']) ?></span>
                        <span class="mini-line__price"><?= e(money($line['line_total'])) ?></span>
                    </div>
                <?php endforeach; ?>

                <dl class="summary" style="margin-top: var(--space-4)">
                    <div><dt>Subtotal</dt><dd><?= e(money($cart['subtotal'])) ?></dd></div>
                    <div><dt>Delivery</dt><dd data-shipping><?= $fulfilment === 'pickup' || $deliveryFee == 0 ? 'Free' : e(money($deliveryFee)) ?></dd></div>
                    <div class="summary__total"><dt>Total</dt><dd data-total><?= e(money($cart['subtotal'] + ($fulfilment === 'pickup' ? 0 : $deliveryFee))) ?></dd></div>
                </dl>

                <button class="btn btn--primary btn--lg btn--block" type="submit" data-submit-once><?= icon('lock') ?>Place order</button>
                <p class="text-xs text-muted text-center" style="margin: var(--space-3) 0 0">By placing your order you agree to be contacted about it by phone or WhatsApp.</p>
            </div>
        </aside>
    </form>
</section>
