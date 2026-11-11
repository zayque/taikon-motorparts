<div class="auth">
    <div class="auth__aside">
        <p class="eyebrow" style="color: var(--accent)">Welcome back</p>
        <h2>Your garage,<br>one click away.</h2>
        <p>Track orders, manage workshop bookings and check out faster with your saved address.</p>
    </div>

    <div class="auth__main">
        <div class="auth__card">
            <h1>Sign in</h1>
            <p class="text-muted">Use the email you registered with.</p>

            <form action="<?= e(url('/login')) ?>" method="post" class="stack" novalidate>
                <?= csrf_field() ?>
                <div class="<?= field_class('email') ?>">
                    <label for="email">Email</label>
                    <input class="input" id="email" type="email" name="email" autocomplete="email" required autofocus value="<?= e(old('email')) ?>" <?= field_attrs('email') ?>>
                    <?= field_error('email') ?>
                </div>
                <div class="<?= field_class('password') ?>">
                    <label for="password">Password</label>
                    <input class="input" id="password" type="password" name="password" autocomplete="current-password" required <?= field_attrs('password') ?>>
                    <?= field_error('password') ?>
                </div>
                <button class="btn btn--primary btn--lg btn--block" type="submit">Sign in</button>
            </form>

            <p class="text-sm text-muted" style="margin-top: var(--space-4)">Forgot your password? Call or WhatsApp us on <?= e(config('shop.phone')) ?> and we’ll reset it for you.</p>

            <p class="auth__switch">New to Taikon? <a href="<?= e(url('/register')) ?>">Create an account</a></p>
        </div>
    </div>
</div>
