<?php
/** @var array<string, mixed> $service */
$hours = intdiv((int) $service['duration_minutes'], 60);
$mins = (int) $service['duration_minutes'] % 60;
$duration = trim(($hours ? $hours . ' hr ' : '') . ($mins ? $mins . ' min' : ''));
?>
<article class="service-detail <?= $service['is_package'] ? 'service-detail--package' : '' ?>" id="<?= e($service['slug']) ?>">
    <span class="feature__icon" style="width:64px;height:64px"><?= icon($service['icon']) ?></span>
    <div>
        <div class="cluster" style="margin-bottom: var(--space-2)">
            <h3 style="margin:0"><?= e($service['name']) ?></h3>
            <?php if ($service['is_package']): ?><span class="badge badge--accent">Package</span><?php endif; ?>
        </div>
        <p class="text-muted" style="margin:0"><?= e($service['description'] ?: $service['summary']) ?></p>
        <?php if ($service['feature_list']): ?>
            <ul class="check-list check-list--2">
                <?php foreach ($service['feature_list'] as $f): ?><li><?= icon('check') ?><?= e($f) ?></li><?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <div class="service-detail__aside">
        <small class="text-muted">From</small>
        <div class="price"><span class="price__now"><?= e(money($service['price_from'])) ?></span></div>
        <p class="text-sm text-muted" style="margin: var(--space-1) 0 var(--space-4)"><?= icon('clock', 'icon-sm') ?> <?= $service['is_package'] ? 'Consultation ' : '' ?><?= e($duration) ?></p>
        <a class="btn btn--dark btn--sm" href="<?= e(url('/services/book', ['service' => $service['id']])) ?>">Book this</a>
    </div>
</article>
