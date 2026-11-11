<?php
use App\Services\Cart;

$user = auth_user();
$shop = config('shop');
$cartCount = Cart::count();
$nav = [
    ['/shop', 'Shop'],
    ['/services', 'Services'],
    ['/about', 'About'],
    ['/contact', 'Contact'],
];
?>
<a class="skip-link" href="#main">Skip to content</a>

<div class="topbar">
    <div class="container">
        <div class="topbar__items">
            <a class="topbar__item" href="tel:<?= e(preg_replace('/[^\d+]/', '', $shop['phone'])) ?>"><?= icon('phone') ?><?= e($shop['phone']) ?></a>
            <span class="topbar__item topbar__item--hide-sm"><?= icon('clock') ?><?= e($shop['hours'][0]['days']) ?>, <?= e($shop['hours'][0]['time']) ?></span>
        </div>
        <div class="topbar__items">
            <span class="topbar__item topbar__item--hide-sm"><?= icon('map-pin') ?>Kuang, <?= e($shop['address']['state']) ?></span>
            <a class="topbar__item" href="<?= e(url('/services/book')) ?>"><?= icon('calendar') ?>Book a service</a>
        </div>
    </div>
</div>

<header class="site-header">
    <div class="container">
        <button class="icon-btn nav-toggle" type="button" aria-controls="main-nav" aria-expanded="false" data-nav-toggle>
            <?= icon('menu') ?><span class="visually-hidden">Menu</span>
        </button>

        <a class="brand" href="<?= e(url('/')) ?>" aria-label="<?= e(config('app.name')) ?> — home">
            <?php require __DIR__ . '/logo.php'; ?>
        </a>

        <nav class="main-nav" id="main-nav" aria-label="Main">
            <div class="mobile-search">
                <form class="header-search" action="<?= e(url('/shop')) ?>" role="search">
                    <?= icon('search') ?>
                    <input type="search" name="q" placeholder="Search parts, brands, models…" aria-label="Search products">
                </form>
            </div>
            <ul class="main-nav__list">
                <?php foreach ($nav as [$href, $label]): ?>
                    <li><a class="main-nav__link" href="<?= e(url($href)) ?>" <?= is_active($href) ? 'aria-current="page"' : '' ?>><?= e($label) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <form class="header-search" action="<?= e(url('/shop')) ?>" role="search">
            <?= icon('search') ?>
            <input type="search" name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Search parts, brands, models…" aria-label="Search products">
        </form>

        <div class="header-actions">
            <?php if ($user): ?>
                <div class="dropdown" data-dropdown>
                    <button class="icon-btn" type="button" aria-haspopup="true" aria-expanded="false" data-dropdown-toggle>
                        <?= icon('user') ?><span class="account-label"><?= e(explode(' ', $user['name'])[0]) ?></span>
                    </button>
                    <div class="dropdown__menu">
                        <div class="dropdown__header">
                            <strong><?= e($user['name']) ?></strong>
                            <span class="text-xs text-muted"><?= e($user['email']) ?></span>
                        </div>
                        <?php if ($user['role'] === 'admin'): ?>
                            <a class="dropdown__item" href="<?= e(url('/admin')) ?>"><?= icon('dashboard') ?>Admin panel</a>
                        <?php endif; ?>
                        <a class="dropdown__item" href="<?= e(url('/account')) ?>"><?= icon('user') ?>My account</a>
                        <a class="dropdown__item" href="<?= e(url('/account/orders')) ?>"><?= icon('receipt') ?>Orders</a>
                        <a class="dropdown__item" href="<?= e(url('/account/bookings')) ?>"><?= icon('calendar') ?>Bookings</a>
                        <form action="<?= e(url('/logout')) ?>" method="post">
                            <?= csrf_field() ?>
                            <button class="dropdown__item" type="submit"><?= icon('logout') ?>Sign out</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <a class="icon-btn" href="<?= e(url('/login')) ?>"><?= icon('user') ?><span class="account-label">Sign in</span></a>
            <?php endif; ?>

            <a class="icon-btn" href="<?= e(url('/cart')) ?>" aria-label="Cart, <?= $cartCount ?> items">
                <?= icon('cart') ?>
                <span class="cart-count" data-cart-count="<?= $cartCount ?>" data-count="<?= $cartCount ?>"><?= $cartCount ?></span>
            </a>
        </div>
    </div>
</header>
