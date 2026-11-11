<?php
/**
 * Wraps account pages with the side navigation.
 * @var string $heading
 * @var string $body  pre-rendered page content
 */
$links = [
    ['/account', 'Overview', 'dashboard', true],
    ['/account/orders', 'Orders', 'receipt', false],
    ['/account/bookings', 'Bookings', 'calendar', false],
    ['/account/profile', 'Profile & address', 'user', false],
];
?>
<?= partial('partials/page-hero', ['crumbs' => [['My account', '/account']], 'heading' => $heading]) ?>

<section class="section section--tight">
    <div class="container account-layout">
        <nav class="account-nav" aria-label="Account">
            <?php foreach ($links as [$href, $label, $ico, $exact]): ?>
                <a href="<?= e(url($href)) ?>" <?= is_active($href, $exact) ? 'aria-current="page"' : '' ?>><?= icon($ico) ?><?= e($label) ?></a>
            <?php endforeach; ?>
            <?php if (auth_user()['role'] === 'admin'): ?>
                <a href="<?= e(url('/admin')) ?>"><?= icon('gauge') ?>Admin panel</a>
            <?php endif; ?>
            <form action="<?= e(url('/logout')) ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit"><?= icon('logout') ?>Sign out</button>
            </form>
        </nav>
        <div class="stack"><?= $body ?></div>
    </div>
</section>
