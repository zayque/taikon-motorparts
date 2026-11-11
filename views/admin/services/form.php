<?php
/**
 * @var array<string, mixed>|null $service
 * @var list<string> $icons
 */
$isEdit = $service !== null;
$v = static fn (string $key, mixed $default = '') => old($key, $service[$key] ?? $default);
$hasOld = old('name', null) !== null;
$checked = static fn (string $key, bool $default) => ($hasOld ? old($key, '') === '1' : (bool) ($service[$key] ?? $default)) ? 'checked' : '';
?>
<div class="admin-head">
    <div>
        <a class="btn btn--text btn--sm" href="<?= e(url('/admin/services')) ?>"><?= icon('arrow-left') ?>Services</a>
        <h1><?= $isEdit ? e($service['name']) : 'Add service' ?></h1>
    </div>
</div>

<form class="admin-form" action="<?= e(url($isEdit ? '/admin/services/' . $service['id'] : '/admin/services')) ?>" method="post" novalidate>
    <?= csrf_field() ?>
    <div class="card">
        <div class="card__header"><h2>Details</h2></div>
        <div class="card__body form-grid">
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
            <div class="<?= field_class('summary') ?> span-2">
                <label for="summary">One-line summary</label>
                <input class="input" id="summary" name="summary" maxlength="255" required value="<?= e($v('summary')) ?>" <?= field_attrs('summary') ?>>
                <?= field_error('summary') ?>
            </div>
            <div class="field span-2">
                <label for="description">Description</label>
                <textarea class="textarea" id="description" name="description" rows="4"><?= e($v('description')) ?></textarea>
            </div>
            <div class="field span-2">
                <label for="features">What’s included</label>
                <textarea class="textarea" id="features" name="features" rows="5" placeholder="One item per line"><?= e($v('features')) ?></textarea>
                <span class="hint">One item per line — shown as a checklist.</span>
            </div>
            <div class="<?= field_class('price_from') ?>">
                <label for="price_from">Starting price (RM)</label>
                <input class="input" id="price_from" name="price_from" type="number" step="0.01" min="0" required value="<?= e($v('price_from')) ?>" <?= field_attrs('price_from') ?>>
                <?= field_error('price_from') ?>
            </div>
            <div class="<?= field_class('duration_minutes') ?>">
                <label for="duration_minutes">Duration (minutes)</label>
                <input class="input" id="duration_minutes" name="duration_minutes" type="number" step="15" min="15" required value="<?= e($v('duration_minutes', 60)) ?>" <?= field_attrs('duration_minutes') ?>>
                <?= field_error('duration_minutes') ?>
            </div>
        </div>
    </div>

    <div class="stack">
        <div class="card">
            <div class="card__header"><h2>Display</h2></div>
            <div class="card__body">
                <label class="switch"><div><strong>Bookable</strong><span>Shown on the services page and booking form.</span></div><input type="checkbox" name="is_active" value="1" <?= $checked('is_active', true) ?>></label>
                <label class="switch"><div><strong>Package</strong><span>Listed under “Builds &amp; rebuilds”.</span></div><input type="checkbox" name="is_package" value="1" <?= $checked('is_package', false) ?>></label>
                <div class="form-grid" style="margin-top: var(--space-4)">
                    <div class="field">
                        <label for="icon">Icon</label>
                        <select class="select" id="icon" name="icon">
                            <?php foreach ($icons as $ico): ?><option value="<?= e($ico) ?>" <?= $v('icon', 'wrench') === $ico ? 'selected' : '' ?>><?= e(ucfirst($ico)) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="<?= field_class('sort_order') ?>">
                        <label for="sort_order">Order</label>
                        <input class="input" id="sort_order" name="sort_order" type="number" value="<?= e($v('sort_order', 0)) ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="card card__body stack">
            <button class="btn btn--primary btn--block" type="submit"><?= icon('check') ?><?= $isEdit ? 'Save changes' : 'Create service' ?></button>
            <a class="btn btn--text btn--block" href="<?= e(url('/admin/services')) ?>">Cancel</a>
        </div>
    </div>
</form>

<?php if ($isEdit): ?>
    <form action="<?= e(url('/admin/services/' . $service['id'] . '/delete')) ?>" method="post" data-confirm="Delete “<?= e($service['name']) ?>”? Existing bookings keep their record." style="margin-top: var(--space-6)">
        <?= csrf_field() ?>
        <button class="btn btn--danger btn--sm" type="submit"><?= icon('trash') ?>Delete service</button>
    </form>
<?php endif; ?>
