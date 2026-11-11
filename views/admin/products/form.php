<?php
/**
 * @var array<string, mixed>|null $product
 * @var list<array<string, mixed>> $categories
 */
$isEdit = $product !== null;
$v = static fn (string $key, mixed $default = '') => old($key, $product[$key] ?? $default);
$hasOld = old('name', null) !== null;
$checked = static fn (string $key, bool $default) => ($hasOld ? old($key, '') === '1' : (bool) ($product[$key] ?? $default)) ? 'checked' : '';
$action = $isEdit ? url('/admin/products/' . $product['id']) : url('/admin/products');
?>
<div class="admin-head">
    <div>
        <a class="btn btn--text btn--sm" href="<?= e(url('/admin/products')) ?>"><?= icon('arrow-left') ?>Products</a>
        <h1><?= $isEdit ? e($product['name']) : 'Add product' ?></h1>
    </div>
    <?php if ($isEdit && $product['is_active']): ?>
        <a class="btn btn--outline btn--sm" href="<?= e(url('/shop/' . $product['slug'])) ?>" target="_blank" rel="noopener"><?= icon('external') ?>View in shop</a>
    <?php endif; ?>
</div>

<form class="admin-form" action="<?= e($action) ?>" method="post" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>
    <div>
        <div class="card">
            <div class="card__header"><h2>Details</h2></div>
            <div class="card__body form-grid">
                <div class="<?= field_class('name') ?> span-2">
                    <label for="name">Name</label>
                    <input class="input" id="name" name="name" required value="<?= e($v('name')) ?>" <?= field_attrs('name') ?>>
                    <?= field_error('name') ?>
                </div>
                <div class="<?= field_class('sku') ?>">
                    <label for="sku">SKU</label>
                    <input class="input" id="sku" name="sku" required style="text-transform:uppercase" value="<?= e($v('sku')) ?>" placeholder="SP-BRK-001" <?= field_attrs('sku') ?>>
                    <span class="hint">Letters, numbers and dashes.</span>
                    <?= field_error('sku') ?>
                </div>
                <div class="<?= field_class('slug') ?>">
                    <label for="slug">URL slug <span class="optional">(auto)</span></label>
                    <input class="input" id="slug" name="slug" value="<?= e($v('slug')) ?>" data-slug-from="name" <?= field_attrs('slug') ?>>
                    <?= field_error('slug') ?>
                </div>
                <div class="<?= field_class('brand') ?>">
                    <label for="brand">Brand <span class="optional">(optional)</span></label>
                    <input class="input" id="brand" name="brand" value="<?= e($v('brand')) ?>" <?= field_attrs('brand') ?>>
                    <?= field_error('brand') ?>
                </div>
                <div class="<?= field_class('category_id') ?>">
                    <label for="category_id">Category</label>
                    <select class="select" id="category_id" name="category_id" required <?= field_attrs('category_id') ?>>
                        <option value="">Choose…</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= (string) $v('category_id') === (string) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= field_error('category_id') ?>
                </div>
                <div class="<?= field_class('fits') ?> span-2">
                    <label for="fits">Compatible models <span class="optional">(optional)</span></label>
                    <input class="input" id="fits" name="fits" value="<?= e($v('fits')) ?>" placeholder="e.g. Yamaha Y15ZR, LC135" <?= field_attrs('fits') ?>>
                    <?= field_error('fits') ?>
                </div>
                <div class="field span-2">
                    <label for="description">Description</label>
                    <textarea class="textarea" id="description" name="description" rows="6"><?= e($v('description')) ?></textarea>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card__header"><h2>Pricing &amp; inventory</h2></div>
            <div class="card__body form-grid">
                <div class="<?= field_class('price') ?>">
                    <label for="price">Price (RM)</label>
                    <input class="input" id="price" name="price" type="number" step="0.01" min="0" required value="<?= e($v('price')) ?>" <?= field_attrs('price') ?>>
                    <?= field_error('price') ?>
                </div>
                <div class="<?= field_class('compare_price') ?>">
                    <label for="compare_price">Compare-at price <span class="optional">(shows as sale)</span></label>
                    <input class="input" id="compare_price" name="compare_price" type="number" step="0.01" min="0" value="<?= e($v('compare_price')) ?>" <?= field_attrs('compare_price') ?>>
                    <?= field_error('compare_price') ?>
                </div>
                <div class="<?= field_class('stock') ?>">
                    <label for="stock">Stock quantity</label>
                    <input class="input" id="stock" name="stock" type="number" step="1" min="0" required value="<?= e($v('stock', 0)) ?>" <?= field_attrs('stock') ?>>
                    <?= field_error('stock') ?>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="card">
            <div class="card__header"><h2>Visibility</h2></div>
            <div class="card__body">
                <label class="switch">
                    <div><strong>Live in shop</strong><span>Hidden products can’t be bought.</span></div>
                    <input type="checkbox" name="is_active" value="1" <?= $checked('is_active', true) ?>>
                </label>
                <label class="switch">
                    <div><strong>Featured</strong><span>Shown on the home page.</span></div>
                    <input type="checkbox" name="is_featured" value="1" <?= $checked('is_featured', false) ?>>
                </label>
            </div>
        </div>

        <div class="card">
            <div class="card__header"><h2>Photo</h2></div>
            <div class="card__body stack">
                <label class="image-drop" data-image-drop>
                    <?php if ($isEdit && $product['image']): ?><img src="<?= e(upload_url($product['image'])) ?>" alt=""><?php endif; ?>
                    <span><?= icon('image') ?>Click to upload<br><span class="text-xs">JPG, PNG or WebP · max 2 MB</span></span>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp" aria-label="Product photo">
                </label>
                <?= field_error('image') ?>
                <?php if ($isEdit && $product['image']): ?>
                    <label class="check"><input type="checkbox" name="remove_image" value="1">Remove current photo</label>
                <?php endif; ?>
            </div>
        </div>

        <div class="card card__body stack">
            <button class="btn btn--primary btn--block" type="submit"><?= icon('check') ?><?= $isEdit ? 'Save changes' : 'Create product' ?></button>
            <a class="btn btn--text btn--block" href="<?= e(url('/admin/products')) ?>">Cancel</a>
        </div>
    </div>
</form>

<?php if ($isEdit): ?>
    <form action="<?= e(url('/admin/products/' . $product['id'] . '/delete')) ?>" method="post" data-confirm="Delete “<?= e($product['name']) ?>”? Products with past orders are hidden instead." style="margin-top: var(--space-6)">
        <?= csrf_field() ?>
        <button class="btn btn--danger btn--sm" type="submit"><?= icon('trash') ?>Delete product</button>
    </form>
<?php endif; ?>
