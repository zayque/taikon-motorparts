<?php
/**
 * @var int $status
 * @var string $message
 */
$headline = match ($status) {
    404 => 'Wrong turn',
    403 => 'No entry',
    419 => 'Session expired',
    default => 'Engine trouble',
};
?>
<section class="container error-page">
    <div class="error-page__code"><?= (int) $status ?></div>
    <h1><?= e($headline) ?></h1>
    <p><?= e($message ?: 'Something went wrong.') ?></p>
    <div class="cluster" style="justify-content:center">
        <a class="btn btn--primary" href="<?= e(url('/')) ?>"><?= icon('home') ?>Back to home</a>
        <?php if ($status === 404): ?><a class="btn btn--outline" href="<?= e(url('/shop')) ?>">Browse the shop</a><?php endif; ?>
    </div>
</section>
