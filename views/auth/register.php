<div class="auth">
    <div class="auth__aside">
        <p class="eyebrow" style="color: var(--accent)">Join the crew</p>
        <h2>Ride in.<br>Check out faster.</h2>
        <ul class="check-list" style="color:#d6d8dc">
            <li><?= icon('check') ?>Order history and live status</li>
            <li><?= icon('check') ?>Manage workshop bookings online</li>
            <li><?= icon('check') ?>Saved delivery address</li>
        </ul>
    </div>

    <div class="auth__main">
        <div class="auth__card">
            <h1>Create an account</h1>
            <p class="text-muted">It takes less than a minute.</p>

            <form action="<?= e(url('/register')) ?>" method="post" class="stack" novalidate>
                <?= csrf_field() ?>
                <div class="<?= field_class('name') ?>">
                    <label for="name">Full name</label>
                    <input class="input" id="name" name="name" autocomplete="name" required autofocus value="<?= e(old('name')) ?>" <?= field_attrs('name') ?>>
                    <?= field_error('name') ?>
                </div>
                <div class="<?= field_class('email') ?>">
                    <label for="email">Email</label>
                    <input class="input" id="email" type="email" name="email" autocomplete="email" required value="<?= e(old('email')) ?>" <?= field_attrs('email') ?>>
                    <?= field_error('email') ?>
                </div>
                <div class="<?= field_class('phone') ?>">
                    <label for="phone">Phone <span class="optional">(optional, for order updates)</span></label>
                    <input class="input" id="phone" type="tel" name="phone" autocomplete="tel" value="<?= e(old('phone')) ?>" <?= field_attrs('phone') ?>>
                    <?= field_error('phone') ?>
                </div>
                <div class="<?= field_class('password') ?>">
                    <label for="password">Password</label>
                    <input class="input" id="password" type="password" name="password" autocomplete="new-password" required minlength="8" <?= field_attrs('password') ?>>
                    <span class="hint">At least 8 characters.</span>
                    <?= field_error('password') ?>
                </div>
                <div class="field">
                    <label for="password_confirmation">Confirm password</label>
                    <input class="input" id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
                </div>
                <button class="btn btn--primary btn--lg btn--block" type="submit">Create account</button>
            </form>

            <p class="auth__switch">Already have an account? <a href="<?= e(url('/login')) ?>">Sign in</a></p>
        </div>
    </div>
</div>
