<?php
/**
 * @var array<string, mixed> $filters
 * @var list<array<string, mixed>> $categories
 * @var \App\Support\Paginator $products
 */
$low = (int) config('catalog.low_stock_threshold');
?>
<div class="admin-head">
    <div>
        <h1>Products</h1>
        <p><?= $products->total ?> product<?= $products->total === 1 ? '' : 's' ?><?= $filters['q'] || $filters['category_id'] || $filters['stock'] ? ' match your filters' : ' in the catalogue' ?>.</p>
    </div>
    <a class="btn btn--primary" href="<?= e(url('/admin/products/create')) ?>"><?= icon('plus') ?>Add product</a>
</div>

<div class="card">
    <form class="toolbar" method="get" action="<?= e(url('/admin/products')) ?>" data-autosubmit>
        <input class="input" type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="Search name, SKU or brand" aria-label="Search products">
        <select class="select" name="category" aria-label="Category">
            <option value="">All categories</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= (int) $c['id'] ?>" <?= $filters['category_id'] === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select class="select" name="stock" aria-label="Stock">
            <option value="">Any stock</option>
            <option value="low" <?= $filters['stock'] === 'low' ? 'selected' : '' ?>>Low stock (≤ <?= $low ?>)</option>
        </select>
        <button class="btn btn--dark btn--sm" type="submit"><?= icon('search') ?>Filter</button>
        <?php if ($filters['q'] || $filters['category_id'] || $filters['stock']): ?>
            <a class="btn btn--text btn--sm" href="<?= e(url('/admin/products')) ?>">Clear</a>
        <?php endif; ?>
    </form>

    <?php if ($products->items): ?>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Product</th><th>Category</th><th class="num">Price</th><th class="num">Stock</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($products->items as $p): ?>
                        <tr>
                            <td>
                                <div class="product-cell">
                                    <span class="thumb">
                                        <?php if ($p['image']): ?><img src="<?= e(upload_url($p['image'])) ?>" alt=""><?php else: ?><span class="placeholder-art"><?= icon($p['category_icon']) ?></span><?php endif; ?>
                                    </span>
                                    <div>
                                        <a href="<?= e(url('/admin/products/' . $p['id'] . '/edit')) ?>"><?= e($p['name']) ?></a>
                                        <small><?= e($p['sku']) ?><?= $p['brand'] ? ' · ' . e($p['brand']) : '' ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?= e($p['category_name']) ?></td>
                            <td class="num"><?= e(money($p['price'])) ?></td>
                            <td class="num">
                                <?php $s = (int) $p['stock']; ?>
                                <span class="badge <?= $s === 0 ? 'badge--danger' : ($s <= $low ? 'badge--warning' : 'badge--neutral') ?>"><?= $s ?></span>
                            </td>
                            <td>
                                <?= $p['is_active'] ? '<span class="badge badge--success">Live</span>' : '<span class="badge badge--neutral">Hidden</span>' ?>
                                <?= $p['is_featured'] ? '<span class="badge badge--accent">Featured</span>' : '' ?>
                            </td>
                            <td>
                                <div class="inline-actions">
                                    <?php if ($p['is_active']): ?>
                                        <a class="icon-btn" href="<?= e(url('/shop/' . $p['slug'])) ?>" target="_blank" rel="noopener" title="View in shop"><?= icon('eye') ?><span class="visually-hidden">View <?= e($p['name']) ?></span></a>
                                    <?php endif; ?>
                                    <a class="icon-btn" href="<?= e(url('/admin/products/' . $p['id'] . '/edit')) ?>" title="Edit"><?= icon('edit') ?><span class="visually-hidden">Edit <?= e($p['name']) ?></span></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty">
            <span class="empty__icon"><?= icon('box') ?></span>
            <h3>No products found</h3>
            <p>Try a different search, or add your first product.</p>
            <a class="btn btn--primary" href="<?= e(url('/admin/products/create')) ?>">Add product</a>
        </div>
    <?php endif; ?>
</div>
<?= partial('partials/pagination', ['paginator' => $products]) ?>
