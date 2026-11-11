<?php
/**
 * @var array{lines: list<array<string, mixed>>, subtotal: float, count: int} $cart
 * @var float $shipping
 */
$freeOver = (float) config('shipping.free_over');
$remaining = max(0, $freeOver - $cart['subtotal']);
$progress = $freeOver > 0 ? min(100, (int) round($cart['subtotal'] / $freeOver * 100)) : 100;
?>

<?= partial('partials/page-hero', [
    'crumbs'  => [['Cart', null]],
    'heading' => 'Your cart',
    'lead'    => $cart['count'] ? $cart['count'] . ' item' . ($cart['count'] === 1 ? '' : 's') . ' ready for checkout.' : null,
]) ?>

<section class="section section--tight">
    <div class="container">
        <?php if (!$cart['lines']): ?>
            <div class="card empty">
                <span class="empty__icon"><?= icon('cart') ?></span>
                <h2>Your cart is empty</h2>
                <p>Find the parts and gear you need — we’ll have them ready to ship or collect.</p>
                <a class="btn btn--primary" href="<?= e(url('/shop')) ?>">Start shopping</a>
            </div>
        <?php else: ?>
            <div class="cart-layout">
                <form class="card" action="<?= e(url('/cart/update')) ?>" method="post" id="cart-form">
                    <?= csrf_field() ?>
                    <?php foreach ($cart['lines'] as $line): $p = $line['product']; ?>
                        <div class="cart-line">
                            <a class="cart-line__media" href="<?= e(url('/shop/' . $p['slug'])) ?>" tabindex="-1" aria-hidden="true">
                                <?php if ($p['image']): ?>
                                    <img src="<?= e(upload_url($p['image'])) ?>" alt="" loading="lazy">
                                <?php else: ?>
                                    <span class="placeholder-art"><?= icon($p['category_icon']) ?></span>
                                <?php endif; ?>
                            </a>
                            <div>
                                <a class="cart-line__name" href="<?= e(url('/shop/' . $p['slug'])) ?>"><?= e($p['name']) ?></a>
                                <div class="cart-line__meta"><?= e(money($p['price'])) ?> each · SKU <?= e($p['sku']) ?></div>
                            </div>
                            <div class="cart-line__controls">
                                <div class="qty qty--sm" data-qty data-autosubmit-qty>
                                    <button type="button" data-step="-1" aria-label="Decrease quantity of <?= e($p['name']) ?>"><?= icon('minus') ?></button>
                                    <input type="number" name="quantities[<?= (int) $p['id'] ?>]" value="<?= (int) $line['quantity'] ?>" min="0" max="<?= min((int) $p['stock'], \App\Services\Cart::MAX_QTY_PER_LINE) ?>" aria-label="Quantity of <?= e($p['name']) ?>" inputmode="numeric">
                                    <button type="button" data-step="1" aria-label="Increase quantity of <?= e($p['name']) ?>"><?= icon('plus') ?></button>
                                </div>
                                <span class="cart-line__total"><?= e(money($line['line_total'])) ?></span>
                            </div>
                            <button class="icon-btn cart-line__remove" type="submit" form="remove-<?= (int) $p['id'] ?>" aria-label="Remove <?= e($p['name']) ?>"><?= icon('trash') ?></button>
                        </div>
                    <?php endforeach; ?>
                    <div class="card__footer spread">
                        <a class="btn btn--text" href="<?= e(url('/shop')) ?>"><?= icon('arrow-left') ?>Continue shopping</a>
                        <button class="btn btn--outline btn--sm" type="submit" data-update-btn>Update cart</button>
                    </div>
                </form>

                <?php foreach ($cart['lines'] as $line): ?>
                    <form id="remove-<?= (int) $line['product']['id'] ?>" action="<?= e(url('/cart/remove')) ?>" method="post" hidden>
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= (int) $line['product']['id'] ?>">
                    </form>
                <?php endforeach; ?>

                <aside class="cart-layout__aside">
                    <div class="card card__body">
                        <h2 style="font-size: var(--text-xl)">Order summary</h2>

                        <div class="free-ship">
                            <?php if ($remaining > 0): ?>
                                Add <strong><?= e(money($remaining)) ?></strong> more for free delivery.
                            <?php else: ?>
                                <strong class="text-accent">You’ve unlocked free delivery!</strong>
                            <?php endif; ?>
                            <div class="free-ship__bar" role="progressbar" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100" aria-label="Progress to free delivery"><span style="width: <?= $progress ?>%"></span></div>
                        </div>

                        <dl class="summary">
                            <div><dt>Subtotal</dt><dd><?= e(money($cart['subtotal'])) ?></dd></div>
                            <div><dt>Delivery</dt><dd><?= $shipping > 0 ? e(money($shipping)) : 'Free' ?></dd></div>
                            <div class="summary__total"><dt>Estimated total</dt><dd><?= e(money($cart['subtotal'] + $shipping)) ?></dd></div>
                        </dl>
                        <p class="text-xs text-muted">Self-pickup at the workshop is always free — choose it at checkout.</p>

                        <a class="btn btn--primary btn--lg btn--block" href="<?= e(url('/checkout')) ?>"><?= icon('lock') ?>Checkout</a>
                        <?php if (!auth_user()): ?>
                            <p class="text-xs text-muted text-center" style="margin: var(--space-3) 0 0">You’ll sign in or create an account at the next step.</p>
                        <?php endif; ?>
                    </div>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</section>
