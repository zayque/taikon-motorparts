<?php
/** @var array<string, mixed> $product */
$href = url('/shop/' . $product['slug']);
$stock = (int) $product['stock'];
$onSale = $product['compare_price'] !== null && (float) $product['compare_price'] > (float) $product['price'];
$lowStock = $stock > 0 && $stock <= (int) config('catalog.low_stock_threshold');
?>
<article class="product-card">
    <a class="product-card__media" href="<?= e($href) ?>" tabindex="-1" aria-hidden="true">
        <?php if ($product['image']): ?>
            <img src="<?= e(upload_url($product['image'])) ?>" alt="" loading="lazy" width="400" height="300">
        <?php else: ?>
            <span class="placeholder-art"><?= icon($product['category_icon'] ?? 'box') ?></span>
        <?php endif; ?>
    </a>
    <div class="product-card__badges">
        <?php if ($stock <= 0): ?>
            <span class="badge badge--dark">Sold out</span>
        <?php elseif ($onSale): ?>
            <span class="badge badge--accent">-<?= (int) round((1 - $product['price'] / $product['compare_price']) * 100) ?>%</span>
        <?php endif; ?>
        <?php if ($lowStock): ?>
            <span class="badge badge--warning">Only <?= $stock ?> left</span>
        <?php endif; ?>
    </div>
    <div class="product-card__body">
        <?php if ($product['brand']): ?><span class="product-card__brand"><?= e($product['brand']) ?></span><?php endif; ?>
        <h3 class="product-card__title"><a href="<?= e($href) ?>"><?= e($product['name']) ?></a></h3>
        <?php if ($product['fits']): ?><span class="product-card__fits">Fits: <?= e($product['fits']) ?></span><?php endif; ?>

        <div class="product-card__footer">
            <div class="price">
                <span class="price__now"><?= e(money($product['price'])) ?></span>
                <?php if ($onSale): ?><span class="price__was"><?= e(money($product['compare_price'])) ?></span><?php endif; ?>
            </div>
            <?php if ($stock > 0): ?>
                <form action="<?= e(url('/cart/add')) ?>" method="post" data-add-to-cart>
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                    <button class="btn btn--dark btn--sm" type="submit" aria-label="Add <?= e($product['name']) ?> to cart">
                        <?= icon('plus') ?>Add
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</article>
