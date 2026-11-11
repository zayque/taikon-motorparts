<?php
$shop = config('shop');
$user = auth_user();
?>

<?= partial('partials/page-hero', [
    'crumbs'  => [['Contact', null]],
    'eyebrow' => 'We reply within one working day',
    'heading' => 'Contact us',
    'lead'    => 'Questions about a part, a quote for a job, or help with an order — we’re here.',
]) ?>

<section class="section">
    <div class="container cart-layout">
        <div class="card">
            <div class="card__body">
                <h2>Send us a message</h2>
                <p class="text-muted">Include your bike’s make, model and year if you’re asking about parts.</p>

                <form action="<?= e(url('/contact')) ?>" method="post" novalidate>
                    <?= csrf_field() ?>
                    <div class="visually-hidden" aria-hidden="true">
                        <label>Leave this empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>

                    <div class="form-grid">
                        <div class="<?= field_class('name') ?>">
                            <label for="name">Your name</label>
                            <input class="input" id="name" name="name" autocomplete="name" required value="<?= e(old('name', $user['name'] ?? '')) ?>" <?= field_attrs('name') ?>>
                            <?= field_error('name') ?>
                        </div>
                        <div class="<?= field_class('email') ?>">
                            <label for="email">Email</label>
                            <input class="input" id="email" type="email" name="email" autocomplete="email" required value="<?= e(old('email', $user['email'] ?? '')) ?>" <?= field_attrs('email') ?>>
                            <?= field_error('email') ?>
                        </div>
                        <div class="<?= field_class('phone') ?>">
                            <label for="phone">Phone <span class="optional">(optional)</span></label>
                            <input class="input" id="phone" type="tel" name="phone" autocomplete="tel" value="<?= e(old('phone', $user['phone'] ?? '')) ?>" <?= field_attrs('phone') ?>>
                            <?= field_error('phone') ?>
                        </div>
                        <div class="<?= field_class('subject') ?>">
                            <label for="subject">Subject</label>
                            <input class="input" id="subject" name="subject" required value="<?= e(old('subject')) ?>" placeholder="e.g. Brake pads for Y15ZR" <?= field_attrs('subject') ?>>
                            <?= field_error('subject') ?>
                        </div>
                        <div class="<?= field_class('body') ?> span-2">
                            <label for="body">Message</label>
                            <textarea class="textarea" id="body" name="body" rows="6" required <?= field_attrs('body') ?>><?= e(old('body')) ?></textarea>
                            <?= field_error('body') ?>
                        </div>
                        <div class="span-2">
                            <button class="btn btn--primary btn--lg" type="submit"><?= icon('mail') ?>Send message</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <aside class="cart-layout__aside stack">
            <div class="card card__body">
                <h3>Faster on WhatsApp</h3>
                <p class="text-muted text-sm">Send a photo of the part or your bike and we’ll reply with options and prices.</p>
                <a class="btn btn--dark btn--block" href="https://wa.me/<?= e($shop['whatsapp']) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?>Open WhatsApp</a>
            </div>
            <div class="card card__body">
                <ul class="info-list" style="margin:0">
                    <li><?= icon('phone') ?><div><strong>Call</strong><a href="tel:<?= e(preg_replace('/[^\d+]/', '', $shop['phone'])) ?>"><?= e($shop['phone']) ?></a></div></li>
                    <li><?= icon('mail') ?><div><strong>Email</strong><a href="mailto:<?= e($shop['email']) ?>"><?= e($shop['email']) ?></a></div></li>
                    <li><?= icon('map-pin') ?><div><strong>Workshop</strong><span><?= e($shop['address']['line1']) ?>, <?= e($shop['address']['postcode'] . ' ' . $shop['address']['city']) ?></span></div></li>
                </ul>
            </div>
        </aside>
    </div>
</section>

<section class="section section--white">
    <div class="container">
        <?= partial('partials/visit') ?>
    </div>
</section>
