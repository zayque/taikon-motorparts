<?php
/**
 * @var string $content
 * @var string|null $title
 */
use App\Models\Booking;
use App\Models\Message;
use App\Models\Order;

$user = auth_user();
$badges = $user && $user['role'] === 'admin' ? [
    'orders'   => Order::countsByStatus()['pending'],
    'bookings' => Booking::countPending(),
    'messages' => Message::countUnread(),
] : [];

$nav = [
    'Overview' => [
        ['/admin', 'Dashboard', 'dashboard', null, true],
    ],
    'Sales' => [
        ['/admin/orders', 'Orders', 'receipt', 'orders', false],
        ['/admin/bookings', 'Bookings', 'calendar', 'bookings', false],
        ['/admin/customers', 'Customers', 'users', null, false],
        ['/admin/messages', 'Messages', 'message', 'messages', false],
    ],
    'Catalogue' => [
        ['/admin/products', 'Products', 'box', null, false],
        ['/admin/categories', 'Categories', 'tag', null, false],
        ['/admin/services', 'Services', 'wrench', null, false],
    ],
];
?>
<!doctype html>
<html lang="en">
<head>
    <?php require BASE_PATH . '/views/partials/head.php'; ?>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="<?= e(asset('assets/css/admin.css')) ?>">
</head>
<body class="admin">
    <a class="skip-link" href="#main">Skip to content</a>

    <aside class="admin-sidebar" id="admin-sidebar">
        <a class="brand" href="<?= e(url('/admin')) ?>"><?php require BASE_PATH . '/views/partials/logo.php'; ?></a>
        <nav aria-label="Admin">
            <?php foreach ($nav as $group => $items): ?>
                <p class="admin-sidebar__group"><?= e($group) ?></p>
                <?php foreach ($items as [$href, $label, $ico, $badgeKey, $exact]): ?>
                    <a class="admin-sidebar__link" href="<?= e(url($href)) ?>" <?= is_active($href, $exact) ? 'aria-current="page"' : '' ?>>
                        <?= icon($ico) ?><span><?= e($label) ?></span>
                        <?php if ($badgeKey && !empty($badges[$badgeKey])): ?><span class="admin-sidebar__badge"><?= (int) $badges[$badgeKey] ?></span><?php endif; ?>
                    </a>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </nav>
        <div class="admin-sidebar__foot">
            <a class="admin-sidebar__link" href="<?= e(url('/')) ?>" target="_blank" rel="noopener"><?= icon('external') ?><span>View storefront</span></a>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <button class="icon-btn admin-menu-btn" type="button" aria-controls="admin-sidebar" aria-expanded="false" data-admin-menu><?= icon('menu') ?><span class="visually-hidden">Menu</span></button>
            <p class="admin-topbar__title"><?= e($title ?? 'Admin') ?></p>
            <?php if ($user): ?>
                <div class="cluster">
                    <span class="text-sm text-muted admin-topbar__user"><?= e($user['name']) ?></span>
                    <form action="<?= e(url('/logout')) ?>" method="post">
                        <?= csrf_field() ?>
                        <button class="btn btn--outline btn--sm" type="submit"><?= icon('logout') ?>Sign out</button>
                    </form>
                </div>
            <?php endif; ?>
        </header>

        <?php require BASE_PATH . '/views/partials/flash.php'; ?>

        <main id="main" class="admin-content">
            <?= $content ?>
        </main>
    </div>

    <script>window.APP = { base: <?= json_encode(url('/')) ?> };</script>
    <script src="<?= e(asset('assets/js/app.js')) ?>" defer></script>
    <script src="<?= e(asset('assets/js/admin.js')) ?>" defer></script>
</body>
</html>
