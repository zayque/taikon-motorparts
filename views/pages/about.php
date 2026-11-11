<?php $shop = config('shop'); ?>

<?= partial('partials/page-hero', [
    'crumbs'  => [['About', null]],
    'eyebrow' => 'Our story',
    'heading' => 'A neighbourhood workshop, since ' . $shop['since'],
    'lead'    => 'Family-run in Kampung Batu 20, Kuang — selling parts, fixing bikes and building customs for Selangor riders.',
]) ?>

<section class="section">
    <div class="container about-intro">
        <div>
            <p class="eyebrow">Who we are</p>
            <h2>Built by riders, for riders</h2>
            <p class="lead">Taikon Motorparts started in <?= (int) $shop['since'] ?> as a small spare-parts counter in Kuang. Today we're a full workshop and parts store, still at the same address.</p>
            <p>We stock accessories, spare parts, riding gear and tools at fair prices — and everything we sell meets the standards set by the Road Transport Department (JPJ).</p>
            <p>Our mechanics inspect every component carefully before recommending a fix, so you only pay for what your bike actually needs. Whether it's a weekly commuter, a weekend sport bike or a classic you want rebuilt, you'll get the same honest advice.</p>
            <div class="cluster" style="margin-top: var(--space-5)">
                <a class="btn btn--primary" href="<?= e(url('/services')) ?>">Our services</a>
                <a class="btn btn--outline" href="<?= e(url('/contact')) ?>">Get in touch</a>
            </div>
        </div>
        <div class="about-badge" aria-hidden="true">
            <div>
                <span>Since</span>
                <strong><?= (int) $shop['since'] ?></strong>
                <span>Kuang · Selangor</span>
            </div>
        </div>
    </div>
</section>

<section class="section section--dark">
    <div class="container">
        <div class="section-head">
            <div>
                <p class="eyebrow">What we do</p>
                <h2>One stop for parts and service</h2>
            </div>
        </div>
        <div class="grid grid-3">
            <div class="feature">
                <span class="feature__icon"><?= icon('cog') ?></span>
                <div><h3>Parts &amp; accessories</h3><p>OEM replacements, performance upgrades and styling parts for kapcai, scooters and big bikes.</p></div>
            </div>
            <div class="feature">
                <span class="feature__icon"><?= icon('wrench') ?></span>
                <div><h3>Repairs &amp; servicing</h3><p>Routine servicing, valve clearance, brakes, suspension and electrical diagnostics.</p></div>
            </div>
            <div class="feature">
                <span class="feature__icon"><?= icon('sparkles') ?></span>
                <div><h3>Custom builds</h3><p>Street cubs, cafe racers and scramblers — designed with you, built in-house.</p></div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <p class="eyebrow">How a workshop job works</p>
                <h2>Simple, transparent, on time</h2>
            </div>
        </div>
        <ol class="steps">
            <li><h3>Book online</h3><p>Pick a service and a time slot that suits you — no phone tag.</p></li>
            <li><h3>Inspection &amp; quote</h3><p>We check the bike and confirm the price before any extra work.</p></li>
            <li><h3>The work</h3><p>Done by experienced mechanics with the right tools and torque specs.</p></li>
            <li><h3>Test ride &amp; handover</h3><p>Every job is road-tested. We walk you through what was done.</p></li>
        </ol>
    </div>
</section>

<section class="section section--white">
    <div class="container">
        <?= partial('partials/visit') ?>
    </div>
</section>
