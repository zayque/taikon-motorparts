<?php
/**
 * @var array<string, mixed>|null $category
 * @var list<array<string, mixed>> $categories
 * @var array<string, mixed> $filters
 * @var \App\Support\Paginator $products
 */
$basePath = $category ? '/shop/category/' . $category['slug'] : '/shop';
$sortLabels = ['newest' => 'Newest', 'price_asc' => 'Price: low to high', 'price_desc' => 'Price: high to low', 'name' => 'Name A–Z'];
$crumbs = $category ? [['Shop', '/shop'], [$category['name'], null]] : [['Shop', null]];
?>

<?= partial('partials/page-hero', [
    'crumbs'  => $crumbs,
    'eyebrow' => $category ? 'Category' : 'The shop',
    'heading' => $filters['q'] !== '' ? 'Results for “' . $filters['q'] . '”' : ($category['name'] ?? 'Shop all parts'),
    'lead'    => $category['description'] ?? 'Spare parts, accessories, riding gear and tools — in stock and ready to fit.',
]) ?>

<section class="section section--tight">
    <div class="container shop-layout">
        <aside class="filters" aria-label="Filters">
            <div class="filters__group">
                <p class="filters__title">Categories</p>
                <ul class="filter-links">
                    <li>
                        <a href="<?= e(url('/shop', ['q' => $filters['q']])) ?>" <?= !$category ? 'aria-current="page"' : '' ?>>
                            <span><?= icon('store') ?>All products</span>
                            <span class="text-muted"><?= array_sum(array_column($categories, 'product_count')) ?></span>
                        </a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                        <li>
                            <a href="<?= e(url('/shop/category/' . $cat['slug'], ['q' => $filters['q']])) ?>" <?= ($category['id'] ?? null) === $cat['id'] ? 'aria-current="page"' : '' ?>>
                                <span><?= icon($cat['icon']) ?><?= e($cat['name']) ?></span>
                                <span class="text-muted"><?= (int) $cat['product_count'] ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="filters__group">
                <p class="filters__title">Need help?</p>
                <p class="text-sm text-muted">Not sure what fits your bike? Ask us — we’ll check compatibility for you.</p>
                <a class="btn btn--outline btn--sm" href="<?= e(url('/contact')) ?>">Ask a mechanic</a>
            </div>
        </aside>

        <div>
            <div class="shop-toolbar">
                <p class="shop-toolbar__count" aria-live="polite">
                    <?php if ($products->total): ?>
                        Showing <?= $products->from() ?>–<?= $products->to() ?> of <?= $products->total ?> products
                    <?php endif; ?>
                </p>
                <form method="get" action="<?= e(url($basePath)) ?>" data-autosubmit>
                    <?php if ($filters['q'] !== ''): ?><input type="hidden" name="q" value="<?= e($filters['q']) ?>"><?php endif; ?>
                    <label class="check"><input type="checkbox" name="in_stock" value="1" <?= $filters['in_stock'] ? 'checked' : '' ?>>In stock only</label>
                    <label class="visually-hidden" for="sort">Sort by</label>
                    <select class="select" id="sort" name="sort">
                        <?php foreach ($sortLabels as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= $filters['sort'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <noscript><button class="btn btn--sm btn--dark" type="submit">Apply</button></noscript>
                </form>
            </div>

            <?php if ($products->items): ?>
                <div class="product-grid">
                    <?php foreach ($products->items as $product): ?>
                        <?= partial('partials/product-card', ['product' => $product]) ?>
                    <?php endforeach; ?>
                </div>
                <?= partial('partials/pagination', ['paginator' => $products]) ?>
            <?php else: ?>
                <div class="card empty">
                    <span class="empty__icon"><?= icon('search') ?></span>
                    <h2>No parts found</h2>
                    <p>Try a different keyword or category — or message us and we’ll source it for you.</p>
                    <div class="cluster">
                        <a class="btn btn--dark" href="<?= e(url('/shop')) ?>">Clear filters</a>
                        <a class="btn btn--outline" href="<?= e(url('/contact')) ?>">Request a part</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
