<?php
$shop = config('shop');
$addr = $shop['address'];
$mapUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($shop['map_query']);
?>
<div class="visit">
    <div class="visit__info">
        <p class="eyebrow">Visit the workshop</p>
        <h2>Come say hello</h2>
        <p class="text-muted">Walk-ins welcome for parts and quick jobs. For servicing, booking a slot means no waiting.</p>
        <ul class="info-list">
            <li><?= icon('map-pin') ?><div><strong>Address</strong><span><?= e($addr['line1']) ?>, <?= e($addr['postcode'] . ' ' . $addr['city']) ?>, <?= e($addr['state']) ?></span></div></li>
            <li><?= icon('phone') ?><div><strong>Phone / WhatsApp</strong><a href="tel:<?= e(preg_replace('/[^\d+]/', '', $shop['phone'])) ?>"><?= e($shop['phone']) ?></a></div></li>
            <li><?= icon('clock') ?><div style="flex:1"><strong>Opening hours</strong>
                <table class="hours"><?php foreach ($shop['hours'] as $h): ?><tr><td><?= e($h['days']) ?></td><td><?= e($h['time']) ?></td></tr><?php endforeach; ?></table>
            </div></li>
        </ul>
        <div class="cluster">
            <a class="btn btn--dark" href="<?= e($mapUrl) ?>" target="_blank" rel="noopener"><?= icon('external') ?>Get directions</a>
            <a class="btn btn--outline" href="<?= e(url('/contact')) ?>">Send a message</a>
        </div>
    </div>
    <div class="visit__map">
        <iframe
            title="Map showing <?= e(config('app.name')) ?>"
            src="https://www.google.com/maps?q=<?= e(rawurlencode($shop['map_query'])) ?>&output=embed"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</div>
