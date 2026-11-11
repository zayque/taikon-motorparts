<?php
/**
 * Dark inner-page header.
 * @var string $heading
 * @var string|null $eyebrow
 * @var string|null $lead
 * @var list<array{0: string, 1: string|null}> $crumbs  [label, href|null]
 */
?>
<section class="page-hero">
    <div class="container">
        <?php if (!empty($crumbs)): ?>
            <ol class="breadcrumbs">
                <li><a href="<?= e(url('/')) ?>">Home</a></li>
                <?php foreach ($crumbs as [$label, $href]): ?>
                    <li><?php if ($href): ?><a href="<?= e(url($href)) ?>"><?= e($label) ?></a><?php else: ?><span aria-current="page"><?= e($label) ?></span><?php endif; ?></li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
        <?php if (!empty($eyebrow)): ?><p class="eyebrow"><?= e($eyebrow) ?></p><?php endif; ?>
        <h1><?= e($heading) ?></h1>
        <?php if (!empty($lead)): ?><p class="lead"><?= e($lead) ?></p><?php endif; ?>
    </div>
</section>
