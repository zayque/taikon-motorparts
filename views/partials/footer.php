<?php
$shop = config('shop');
$addr = $shop['address'];
$phoneHref = preg_replace('/[^\d+]/', '', $shop['phone']);
?>
<footer class="site-footer">
    <div class="footer-cta">
        <div class="container">
            <div>
                <h2>Not sure which part fits?</h2>
                <p>Send us your bike model on WhatsApp — we'll find the right part.</p>
            </div>
            <a class="btn btn--primary" href="https://wa.me/<?= e($shop['whatsapp']) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?>Chat on WhatsApp</a>
        </div>
    </div>

    <div class="container footer-main">
        <div>
            <a class="brand" href="<?= e(url('/')) ?>" aria-label="<?= e(config('app.name')) ?> — home">
                <?php require __DIR__ . '/logo.php'; ?>
            </a>
            <p style="margin-top: var(--space-4); max-width: 34ch;"><?= e($shop['tagline']) ?> Family-run in Kuang since <?= (int) $shop['since'] ?>.</p>
        </div>

        <div>
            <p class="footer-title">Shop</p>
            <ul class="footer-list">
                <?php foreach (\App\Models\Category::all() as $cat): ?>
                    <li><a href="<?= e(url('/shop/category/' . $cat['slug'])) ?>"><?= e($cat['name']) ?></a></li>
                <?php endforeach; ?>
                <li><a href="<?= e(url('/shop')) ?>">All products</a></li>
            </ul>
        </div>

        <div>
            <p class="footer-title">Workshop</p>
            <ul class="footer-list">
                <li><a href="<?= e(url('/services')) ?>">Services &amp; packages</a></li>
                <li><a href="<?= e(url('/services/book')) ?>">Book an appointment</a></li>
                <li><a href="<?= e(url('/about')) ?>">About us</a></li>
                <li><a href="<?= e(url('/account')) ?>">My account</a></li>
            </ul>
        </div>

        <div>
            <p class="footer-title">Visit us</p>
            <ul class="footer-list footer-contact">
                <li><?= icon('map-pin') ?><span><?= e($addr['line1']) ?>,<br><?= e($addr['postcode'] . ' ' . $addr['city']) ?>, <?= e($addr['state']) ?></span></li>
                <li><?= icon('phone') ?><a href="tel:<?= e($phoneHref) ?>"><?= e($shop['phone']) ?></a></li>
                <li><?= icon('mail') ?><a href="mailto:<?= e($shop['email']) ?>"><?= e($shop['email']) ?></a></li>
                <?php foreach ($shop['hours'] as $h): ?>
                    <li><?= icon('clock') ?><span><?= e($h['days']) ?><br><?= e($h['time']) ?></span></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <span>&copy; <?= date('Y') ?> <?= e(config('app.name')) ?>. All rights reserved.</span>
            <span>Prices in Malaysian Ringgit (RM). Parts meet JPJ road-use standards.</span>
        </div>
    </div>
</footer>
