<?php
$message = \App\Core\Session::getFlash('_message');
if (!$message) {
    return;
}
$icons = ['success' => 'check-circle', 'error' => 'alert', 'warning' => 'alert', 'info' => 'info'];
?>
<div class="flash-region" data-flash>
    <div class="alert alert--<?= e($message['type']) ?>" role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>">
        <?= icon($icons[$message['type']] ?? 'info') ?>
        <p><?= e($message['text']) ?></p>
        <button type="button" class="alert__close" data-dismiss aria-label="Dismiss"><?= icon('x', 'icon-sm') ?></button>
    </div>
</div>
