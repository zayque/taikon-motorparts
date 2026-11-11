<?php
/** @var array<string, mixed> $user */
ob_start();
?>
<div class="card">
    <div class="card__header"><h2>Profile &amp; delivery address</h2></div>
    <form class="card__body" action="<?= e(url('/account/profile')) ?>" method="post" novalidate>
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="<?= field_class('name') ?>">
                <label for="name">Full name</label>
                <input class="input" id="name" name="name" autocomplete="name" required value="<?= e(old('name', $user['name'])) ?>" <?= field_attrs('name') ?>>
                <?= field_error('name') ?>
            </div>
            <div class="<?= field_class('phone') ?>">
                <label for="phone">Phone</label>
                <input class="input" id="phone" type="tel" name="phone" autocomplete="tel" value="<?= e(old('phone', $user['phone'] ?? '')) ?>" <?= field_attrs('phone') ?>>
                <?= field_error('phone') ?>
            </div>
            <div class="<?= field_class('email') ?> span-2">
                <label for="email">Email</label>
                <input class="input" id="email" type="email" name="email" autocomplete="email" required value="<?= e(old('email', $user['email'])) ?>" <?= field_attrs('email') ?>>
                <?= field_error('email') ?>
            </div>
            <div class="<?= field_class('address_line') ?> span-2">
                <label for="address_line">Street address</label>
                <input class="input" id="address_line" name="address_line" autocomplete="street-address" value="<?= e(old('address_line', $user['address_line'] ?? '')) ?>" <?= field_attrs('address_line') ?>>
                <?= field_error('address_line') ?>
            </div>
            <div class="<?= field_class('postcode') ?>">
                <label for="postcode">Postcode</label>
                <input class="input" id="postcode" name="postcode" inputmode="numeric" maxlength="5" autocomplete="postal-code" value="<?= e(old('postcode', $user['postcode'] ?? '')) ?>" <?= field_attrs('postcode') ?>>
                <?= field_error('postcode') ?>
            </div>
            <div class="<?= field_class('city') ?>">
                <label for="city">City</label>
                <input class="input" id="city" name="city" autocomplete="address-level2" value="<?= e(old('city', $user['city'] ?? '')) ?>" <?= field_attrs('city') ?>>
                <?= field_error('city') ?>
            </div>
            <div class="<?= field_class('state') ?> span-2">
                <label for="state">State</label>
                <input class="input" id="state" name="state" autocomplete="address-level1" value="<?= e(old('state', $user['state'] ?? '')) ?>" <?= field_attrs('state') ?>>
                <?= field_error('state') ?>
            </div>
        </div>
        <button class="btn btn--primary" type="submit" style="margin-top: var(--space-5)">Save changes</button>
    </form>
</div>

<div class="card">
    <div class="card__header"><h2>Change password</h2></div>
    <form class="card__body" action="<?= e(url('/account/password')) ?>" method="post" novalidate>
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="<?= field_class('current_password') ?> span-2">
                <label for="current_password">Current password</label>
                <input class="input" id="current_password" type="password" name="current_password" autocomplete="current-password" required <?= field_attrs('current_password') ?>>
                <?= field_error('current_password') ?>
            </div>
            <div class="<?= field_class('password') ?>">
                <label for="password">New password</label>
                <input class="input" id="password" type="password" name="password" autocomplete="new-password" required minlength="8" <?= field_attrs('password') ?>>
                <?= field_error('password') ?>
            </div>
            <div class="field">
                <label for="password_confirmation">Confirm new password</label>
                <input class="input" id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
            </div>
        </div>
        <button class="btn btn--dark" type="submit" style="margin-top: var(--space-5)">Update password</button>
    </form>
</div>
<?php
echo partial('account/_layout', ['heading' => 'Profile & address', 'body' => ob_get_clean()]);
