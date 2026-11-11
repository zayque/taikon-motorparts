<?php
/**
 * @var list<array<string, mixed>> $categories
 * @var array<string, mixed>|null $editing
 * @var list<string> $icons
 */
$v = static fn (string $key, mixed $default = '') => old($key, $editing[$key] ?? $default);
?>
<div class="admin-head">
    <div>
        <h1>Categories</h1>
        <p>Group products in the shop. Order controls how they appear on the home page and shop sidebar.</p>
    </div>
</div>

<div class="admin-form">
    <div class="card">
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Category</th><th>Slug</th><th class="num">Products</th><th class="num">Order</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($categories as $c): ?>
                        <tr>
                            <td>
                                <div class="product-cell" style="min-width:0">
                                    <span class="feature__icon" style="width:36px;height:36px"><?= icon($c['icon'], 'icon-sm') ?></span>
                                    <div><strong><?= e($c['name']) ?></strong><small><?= e(str_excerpt($c['description'], 60)) ?></small></div>
                                </div>
                            </td>
                            <td class="text-muted"><?= e($c['slug']) ?></td>
                            <td class="num"><?= (int) $c['product_count'] ?></td>
                            <td class="num"><?= (int) $c['sort_order'] ?></td>
                            <td>
                                <div class="inline-actions">
                                    <a class="icon-btn" href="<?= e(url('/admin/categories', ['edit' => $c['id']])) ?>" title="Edit"><?= icon('edit') ?><span class="visually-hidden">Edit <?= e($c['name']) ?></span></a>
                                    <form action="<?= e(url('/admin/categories/' . $c['id'] . '/delete')) ?>" method="post" data-confirm="Delete category “<?= e($c['name']) ?>”?">
                                        <?= csrf_field() ?>
                                        <button class="icon-btn" type="submit" title="Delete"><?= icon('trash') ?><span class="visually-hidden">Delete <?= e($c['name']) ?></span></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <form class="card" action="<?= e(url($editing ? '/admin/categories/' . $editing['id'] : '/admin/categories')) ?>" method="post" novalidate>
        <?= csrf_field() ?>
        <div class="card__header">
            <h2><?= $editing ? 'Edit category' : 'New category' ?></h2>
            <?php if ($editing): ?><a class="btn btn--text btn--sm" href="<?= e(url('/admin/categories')) ?>">Cancel</a><?php endif; ?>
        </div>
        <div class="card__body stack">
            <div class="<?= field_class('name') ?>">
                <label for="name">Name</label>
                <input class="input" id="name" name="name" required value="<?= e($v('name')) ?>" <?= field_attrs('name') ?>>
                <?= field_error('name') ?>
            </div>
            <div class="<?= field_class('slug') ?>">
                <label for="slug">Slug <span class="optional">(auto)</span></label>
                <input class="input" id="slug" name="slug" value="<?= e($v('slug')) ?>" data-slug-from="name" <?= field_attrs('slug') ?>>
                <?= field_error('slug') ?>
            </div>
            <div class="<?= field_class('description') ?>">
                <label for="description">Short description</label>
                <textarea class="textarea" id="description" name="description" rows="3" maxlength="255" style="min-height:90px"><?= e($v('description')) ?></textarea>
                <?= field_error('description') ?>
            </div>
            <div class="form-grid">
                <div class="<?= field_class('icon') ?>">
                    <label for="icon">Icon</label>
                    <select class="select" id="icon" name="icon">
                        <?php foreach ($icons as $ico): ?>
                            <option value="<?= e($ico) ?>" <?= $v('icon', 'box') === $ico ? 'selected' : '' ?>><?= e(ucfirst($ico)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="<?= field_class('sort_order') ?>">
                    <label for="sort_order">Order</label>
                    <input class="input" id="sort_order" name="sort_order" type="number" value="<?= e($v('sort_order', 0)) ?>">
                    <?= field_error('sort_order') ?>
                </div>
            </div>
            <button class="btn btn--primary btn--block" type="submit"><?= $editing ? 'Save category' : 'Add category' ?></button>
        </div>
    </form>
</div>
