<?php
/**
 * Fallback layout with no database calls — used for 500 errors so the
 * error page still renders when the database itself is the problem.
 * @var string $content
 */
?>
<!doctype html>
<html lang="en">
<head>
    <?php require BASE_PATH . '/views/partials/head.php'; ?>
    <link rel="stylesheet" href="<?= e(asset('assets/css/pages.css')) ?>">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <a class="brand" href="<?= e(url('/')) ?>"><?php require BASE_PATH . '/views/partials/logo.php'; ?></a>
        </div>
    </header>
    <main id="main"><?= $content ?></main>
</body>
</html>
