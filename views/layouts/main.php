<?php /** @var string $content */ ?>
<!doctype html>
<html lang="en">
<head>
    <?php require BASE_PATH . '/views/partials/head.php'; ?>
    <link rel="stylesheet" href="<?= e(asset('assets/css/pages.css')) ?>">
</head>
<body>
    <?php require BASE_PATH . '/views/partials/header.php'; ?>
    <?php require BASE_PATH . '/views/partials/flash.php'; ?>

    <main id="main">
        <?= $content ?>
    </main>

    <?php require BASE_PATH . '/views/partials/footer.php'; ?>

    <script>window.APP = { base: <?= json_encode(url('/')) ?>, csrf: <?= json_encode(\App\Core\Csrf::token()) ?> };</script>
    <script src="<?= e(asset('assets/js/app.js')) ?>" defer></script>
</body>
</html>
