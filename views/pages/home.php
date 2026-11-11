<?php
/**
 * @var list<array<string, mixed>> $categories
 * @var list<array<string, mixed>> $featured
 * @var list<array<string, mixed>> $services
 */
$shop = config('shop');
$years = (int) date('Y') - (int) $shop['since'];
$builds = [
    ['Benelli TNT600', 'Service + valve clearance', 'gauge'],
    ['SYM Sport Bonus SR', 'Adjustable rear spring', 'shield'],
    ['Yamaha 135LC', 'Full new cover set', 'sparkles'],
    ['Kawasaki Z1000', 'Yoshimura slip-on exhaust', 'cog'],
    ['Honda C70', 'Custom street cub build', 'bike'],
    ['Yamaha Y15ZR', 'LED projector conversion', 'wrench'],
];
?>

<!-- Hero -->
<section class="hero">
    <div class="container">
        <div>
            <p class="eyebrow">Kuang · Rawang · Since <?= (int) $shop['since'] ?></p>
            <h1>Parts that fit.<br><em>Service</em> that lasts.</h1>
            <p class="lead">Genuine and performance parts, riding gear and a proper workshop — all under one roof. From a quick oil change to a full custom build, we keep Selangor riding.</p>

            <form class="hero-search" action="<?= e(url('/shop')) ?>" role="search">
                <?= icon('search') ?>
                <input type="search" name="q" placeholder="Try “brake pads Y15ZR” or “NGK”" aria-label="Search parts">
                <button class="btn btn--primary" type="submit"><span>Find parts</span><?= icon('arrow-right') ?></button>
            </form>

            <div class="cluster">
                <a class="btn btn--ghost-light" href="<?= e(url('/services/book')) ?>"><?= icon('calendar') ?>Book a service</a>
                <a class="btn btn--text" style="--btn-fg:#fff" href="<?= e(url('/shop')) ?>">Browse the shop <?= icon('arrow-right') ?></a>
            </div>

            <dl class="hero-stats">
                <div><dt>Years on the road</dt><dd><?= $years ?>+</dd></div>
                <div><dt>Parts in stock</dt><dd><?= array_sum(array_column($categories, 'product_count')) ?>+</dd></div>
                <div><dt>Workshop services</dt><dd><?= (int) $serviceCount ?></dd></div>
            </dl>
        </div>

        <div class="hero__art" aria-hidden="true">
            <svg viewBox="0 0 400 400" fill="none">
                <circle cx="200" cy="200" r="190" stroke="#2a2d33" stroke-width="2"/>
                <circle cx="200" cy="200" r="150" stroke="#ff5b14" stroke-width="2" stroke-dasharray="4 10"/>
                <g stroke="#ffffff" stroke-width="5" stroke-linecap="round" stroke-linejoin="round">
                    <!-- wheels -->
                    <circle cx="108" cy="262" r="54"/>
                    <circle cx="300" cy="262" r="54"/>
                    <circle cx="108" cy="262" r="34" stroke-width="2" stroke-dasharray="3 7"/>
                    <circle cx="300" cy="262" r="34" stroke-width="2" stroke-dasharray="3 7"/>
                    <circle cx="108" cy="262" r="7" fill="#fff"/>
                    <circle cx="300" cy="262" r="7" fill="#fff"/>
                    <!-- swingarm, frame, fork, bars -->
                    <path d="M108 262l82-28"/>
                    <path d="M188 206l84-42"/>
                    <path d="M300 262l-34-112"/>
                    <path d="M256 144l34-8"/>
                    <!-- tank, seat, tail -->
                    <path d="M186 178c18-16 58-20 76-10l-6 30c-22 4-50 8-70 8z" fill="#111214"/>
                    <path d="M186 190l-62 4"/>
                    <path d="M124 194l-30-10"/>
                    <!-- exhaust -->
                    <path d="M206 258l-56 10h-62" stroke-width="7"/>
                </g>
                <!-- engine -->
                <rect x="172" y="210" width="62" height="46" rx="8" fill="#ff5b14"/>
                <path d="M182 222h42M182 233h42M182 244h42" stroke="#111214" stroke-width="3" stroke-linecap="round"/>
                <!-- headlight -->
                <circle cx="284" cy="170" r="9" fill="#ff5b14"/>
                <path d="M60 330h280" stroke="#2a2d33" stroke-width="3" stroke-dasharray="20 14"/>
            </svg>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <p class="eyebrow">Shop by category</p>
                <h2>Everything your bike needs</h2>
            </div>
            <a class="btn btn--outline" href="<?= e(url('/shop')) ?>">View all products</a>
        </div>
        <div class="category-grid">
            <?php foreach ($categories as $cat): ?>
                <a class="category-tile" href="<?= e(url('/shop/category/' . $cat['slug'])) ?>">
                    <?= icon($cat['icon'], 'category-tile__icon') ?>
                    <span class="category-tile__count"><?= (int) $cat['product_count'] ?> items</span>
                    <h3><?= e($cat['name']) ?></h3>
                    <p><?= e($cat['description']) ?></p>
                    <span class="category-tile__go">Shop now <?= icon('arrow-right') ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured products -->
<?php if ($featured): ?>
<section class="section section--white">
    <div class="container">
        <div class="section-head">
            <div>
                <p class="eyebrow">Rider favourites</p>
                <h2>Featured parts &amp; gear</h2>
            </div>
            <a class="btn btn--text" href="<?= e(url('/shop')) ?>">See everything <?= icon('arrow-right') ?></a>
        </div>
        <div class="product-grid">
            <?php foreach ($featured as $product): ?>
                <?= partial('partials/product-card', ['product' => $product]) ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Workshop -->
<section class="section section--dark">
    <div class="container">
        <div class="section-head">
            <div>
                <p class="eyebrow">The workshop</p>
                <h2>Servicing, upgrades &amp; custom builds</h2>
                <p class="lead">Book online, ride in at your slot, and ride out sorted. Every job gets a test ride before handover.</p>
            </div>
            <a class="btn btn--primary" href="<?= e(url('/services/book')) ?>"><?= icon('calendar') ?>Book a slot</a>
        </div>
        <div class="grid grid-4">
            <?php foreach ($services as $service): ?>
                <article class="service-card">
                    <div class="service-card__top">
                        <span class="feature__icon"><?= icon($service['icon']) ?></span>
                        <?php if ($service['is_package']): ?><span class="badge badge--accent">Package</span><?php endif; ?>
                    </div>
                    <h3><?= e($service['name']) ?></h3>
                    <p><?= e($service['summary']) ?></p>
                    <div class="service-card__price">
                        <div><small>From</small><strong><?= e(money($service['price_from'])) ?></strong></div>
                        <a class="btn btn--ghost-light btn--sm" href="<?= e(url('/services/book', ['service' => $service['id']])) ?>">Book</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Why us -->
<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <p class="eyebrow">Why riders choose us</p>
                <h2>Straight talk, fair prices</h2>
            </div>
        </div>
        <div class="grid grid-4">
            <div class="feature">
                <span class="feature__icon"><?= icon('shield') ?></span>
                <div><h3>JPJ-compliant parts</h3><p>Everything we sell and fit meets road-use standards — no surprises at your next inspection.</p></div>
            </div>
            <div class="feature">
                <span class="feature__icon"><?= icon('wrench') ?></span>
                <div><h3>Experienced mechanics</h3><p>Two decades of kapcai, sport and big-bike work. We diagnose properly before we replace.</p></div>
            </div>
            <div class="feature">
                <span class="feature__icon"><?= icon('truck') ?></span>
                <div><h3>Delivery or pickup</h3><p>Free delivery over <?= e(money(config('shipping.free_over'))) ?>, or collect from the workshop the same day.</p></div>
            </div>
            <div class="feature">
                <span class="feature__icon"><?= icon('tag') ?></span>
                <div><h3>Fair, upfront pricing</h3><p>Quotes before work starts. Parts bought here are fitted at a discounted labour rate.</p></div>
            </div>
        </div>
    </div>
</section>

<!-- Recent builds -->
<section class="section section--white">
    <div class="container">
        <div class="section-head">
            <div>
                <p class="eyebrow">From the workshop floor</p>
                <h2>Recent jobs</h2>
            </div>
            <a class="btn btn--text" href="<?= e(url('/services')) ?>">All services <?= icon('arrow-right') ?></a>
        </div>
        <div class="grid grid-3">
            <?php foreach ($builds as [$bike, $job, $ico]): ?>
                <article class="build-card">
                    <?= icon($ico, 'build-card__icon') ?>
                    <small><?= e($job) ?></small>
                    <h3><?= e($bike) ?></h3>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Visit -->
<section class="section">
    <div class="container">
        <?= partial('partials/visit') ?>
    </div>
</section>
