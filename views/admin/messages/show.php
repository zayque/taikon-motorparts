<?php /** @var array<string, mixed> $message */ ?>
<div class="admin-head">
    <div>
        <a class="btn btn--text btn--sm" href="<?= e(url('/admin/messages')) ?>"><?= icon('arrow-left') ?>Messages</a>
        <h1><?= e($message['subject']) ?></h1>
        <p>From <?= e($message['name']) ?> · <?= e(format_date($message['created_at'], 'j M Y, g:i A')) ?></p>
    </div>
</div>

<div class="admin-form">
    <div class="card card__body">
        <p class="message-body" style="margin:0"><?= e($message['body']) ?></p>
    </div>
    <div class="stack">
        <div class="card card__body">
            <dl class="meta-list">
                <dt>Name</dt><dd><?= e($message['name']) ?></dd>
                <dt>Email</dt><dd><a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a></dd>
                <?php if ($message['phone']): ?><dt>Phone</dt><dd><a href="tel:<?= e($message['phone']) ?>"><?= e($message['phone']) ?></a></dd><?php endif; ?>
            </dl>
        </div>
        <a class="btn btn--primary btn--block" href="mailto:<?= e($message['email']) ?>?subject=<?= e(rawurlencode('Re: ' . $message['subject'])) ?>"><?= icon('mail') ?>Reply by email</a>
        <form action="<?= e(url('/admin/messages/' . $message['id'] . '/delete')) ?>" method="post" data-confirm="Delete this message?">
            <?= csrf_field() ?>
            <button class="btn btn--text btn--block" type="submit"><?= icon('trash') ?>Delete</button>
        </form>
    </div>
</div>
