<?php
/** @var list<array<string, mixed>> $services */
$regular = array_filter($services, static fn ($s) => !$s['is_package']);
$packages = array_filter($services, static fn ($s) => $s['is_package']);
?>

<?= partial('partials/page-hero', [
    'crumbs'  => [['Services', null]],
    'eyebrow' => 'The workshop',
    'heading' => 'Services & packages',
    'lead'    => 'Honest servicing, careful repairs and custom builds. Prices shown are starting rates — we always quote before extra work.',
]) ?>

<section class="section">
    <div class="container stack">
        <div class="section-head">
            <div>
                <p class="eyebrow">Servicing &amp; repairs</p>
                <h2>Keep it running right</h2>
            </div>
            <a class="btn btn--primary" href="<?= e(url('/services/book')) ?>"><?= icon('calendar') ?>Book a slot</a>
        </div>
        <?php foreach ($regular as $s): ?>
            <?= partial('services/_detail', ['service' => $s]) ?>
        <?php endforeach; ?>
    </div>
</section>

<?php if ($packages): ?>
<section class="section section--white">
    <div class="container stack">
        <div class="section-head">
            <div>
                <p class="eyebrow">Exclusive packages</p>
                <h2>Builds &amp; rebuilds</h2>
                <p class="lead">From design to remade engines and accessories — packaged so it fits exactly what you want and your budget.</p>
            </div>
        </div>
        <?php foreach ($packages as $s): ?>
            <?= partial('services/_detail', ['service' => $s]) ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="section section--dark">
    <div class="container">
        <div class="section-head">
            <div>
                <p class="eyebrow">How booking works</p>
                <h2>Four steps, no waiting</h2>
            </div>
        </div>
        <ol class="steps">
            <li><h3>Pick a service</h3><p>Choose what you need — not sure? Pick Basic Service and tell us the symptoms.</p></li>
            <li><h3>Choose a slot</h3><p>See live availability and pick a date and time that works for you.</p></li>
            <li><h3>We confirm</h3><p>We’ll call or WhatsApp to confirm and note anything special.</p></li>
            <li><h3>Ride in</h3><p>Arrive at your slot. Pay at handover — cash, card or DuitNow.</p></li>
        </ol>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="cta-band">
            <div>
                <h2>Ready when you are</h2>
                <p>Online booking takes about a minute. Same-week slots are usually available.</p>
            </div>
            <a class="btn btn--dark btn--lg" href="<?= e(url('/services/book')) ?>"><?= icon('calendar') ?>Book now</a>
        </div>
    </div>
</section>
