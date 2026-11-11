<?php
/**
 * @var array<string, mixed> $product
 * @var list<array<string, mixed>> $related
 */
$stock = (int) $product['stock'];
$low = (int) config('catalog.low_stock_threshold');
$onSale = $product['compare_price'] !== null && (float) $product['compare_price'] > (float) $product['price'];
$maxQty = min($stock, \App\Services\Cart::MAX_QTY_PER_LINE);
?>

<section class="section section--tight">
    <div class="container">
        <ol class="breadcrumbs breadcrumbs--light">
            <li><a href="<?= e(url('/')) ?>">Home</a></li>
            <li><a href="<?= e(url('/shop')) ?>">Shop</a></li>
            <li><a href="<?= e(url('/shop/category/' . $product['category_slug'])) ?>"><?= e($product['category_name']) ?></a></li>
            <li><span aria-current="page"><?= e($product['name']) ?></span></li>
        </ol>

        <div class="product-detail">
            <div class="product-detail__media">
                <?php if ($product['image']): ?>
                    <img src="<?= e(upload_url($product['image'])) ?>" alt="<?= e($product['name']) ?>" width="800" height="800">
                <?php else: ?>
                    <span class="placeholder-art"><?= icon($product['category_icon']) ?></span>
                <?php endif; ?>
            </div>

            <div>
                <?php if ($product['brand']): ?><p class="product-detail__brand"><?= e($product['brand']) ?></p><?php endif; ?>
                <h1><?= e($product['name']) ?></h1>

                <div class="cluster" style="margin-bottom: var(--space-4)">
                    <div class="price price--lg">
                        <span class="price__now"><?= e(money($product['price'])) ?></span>
                        <?php if ($onSale): ?><span class="price__was"><?= e(money($product['compare_price'])) ?></span><?php endif; ?>
                    </div>
                    <?php if ($onSale): ?><span class="badge badge--accent">Save <?= e(money($product['compare_price'] - $product['price'])) ?></span><?php endif; ?>
                </div>

                <?php if ($stock <= 0): ?>
                    <span class="stock stock--out">Out of stock</span>
                <?php elseif ($stock <= $low): ?>
                    <span class="stock stock--low">Only <?= $stock ?> left in stock</span>
                <?php else: ?>
                    <span class="stock stock--in">In stock — ready to ship</span>
                <?php endif; ?>

                <?php if ($stock > 0): ?>
                    <form class="product-detail__buy" action="<?= e(url('/cart/add')) ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                        <div class="qty" data-qty>
                            <button type="button" data-step="-1" aria-label="Decrease quantity"><?= icon('minus') ?></button>
                            <input type="number" name="quantity" value="1" min="1" max="<?= $maxQty ?>" aria-label="Quantity" inputmode="numeric">
                            <button type="button" data-step="1" aria-label="Increase quantity"><?= icon('plus') ?></button>
                        </div>
                        <button class="btn btn--dark" type="submit"><?= icon('cart') ?>Add to cart</button>
                        <button class="btn btn--primary" type="submit" name="buy_now" value="1">Buy now</button>
                    </form>
                <?php else: ?>
                    <div class="product-detail__buy">
                        <a class="btn btn--outline btn--block" href="<?= e(url('/contact')) ?>"><?= icon('mail') ?>Ask when it’s back</a>
                    </div>
                <?php endif; ?>

                <dl class="meta-list" style="margin-bottom: var(--space-5)">
                    <dt>SKU</dt><dd><?= e($product['sku']) ?></dd>
                    <dt>Category</dt><dd><a href="<?= e(url('/shop/category/' . $product['category_slug'])) ?>"><?= e($product['category_name']) ?></a></dd>
                    <?php if ($product['fits']): ?><dt>Fits</dt><dd><?= e($product['fits']) ?></dd><?php endif; ?>
                </dl>

                <?php if ($product['description']): ?>
                    <h2 style="font-size: var(--text-lg)">Description</h2>
                    <p><?= nl2br(e($product['description'])) ?></p>
                <?php endif; ?>

                <ul class="perks" style="margin-top: var(--space-5)">
                    <li><?= icon('truck') ?>Free delivery on orders over <?= e(money(config('shipping.free_over'))) ?></li>
                    <li><?= icon('store') ?>Or pick up from our Kuang workshop</li>
                    <li><?= icon('wrench') ?>Discounted fitting when you book a service with us</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php if ($related): ?>
<section class="section section--white">
    <div class="container">
        <div class="section-head">
            <h2>You may also need</h2>
            <a class="btn btn--text" href="<?= e(url('/shop/category/' . $product['category_slug'])) ?>">More <?= e($product['category_name']) ?> <?= icon('arrow-right') ?></a>
        </div>
        <div class="product-grid">
            <?php foreach ($related as $item): ?>
                <?= partial('partials/product-card', ['product' => $item]) ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
