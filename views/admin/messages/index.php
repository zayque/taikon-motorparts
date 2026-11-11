<?php
/**
 * @var string $filter
 * @var \App\Support\Paginator $messages
 */
?>
<div class="admin-head">
    <div>
        <h1>Messages</h1>
        <p>Enquiries sent through the contact form.</p>
    </div>
</div>

<nav class="tabs" aria-label="Message filter">
    <a href="<?= e(url('/admin/messages')) ?>" <?= $filter === 'all' ? 'aria-current="page"' : '' ?>>All</a>
    <a href="<?= e(url('/admin/messages', ['filter' => 'unread'])) ?>" <?= $filter === 'unread' ? 'aria-current="page"' : '' ?>>Unread</a>
</nav>

<div class="card">
    <?php if ($messages->items): ?>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>From</th><th>Subject</th><th>Received</th></tr></thead>
                <tbody>
                    <?php foreach ($messages->items as $m): ?>
                        <tr class="<?= $m['is_read'] ? '' : 'row-unread' ?>">
                            <td><?= e($m['name']) ?><div class="text-xs text-muted"><?= e($m['email']) ?></div></td>
                            <td><a href="<?= e(url('/admin/messages/' . $m['id'])) ?>"><?= e($m['subject']) ?></a><div class="text-xs text-muted"><?= e(str_excerpt($m['body'], 80)) ?></div></td>
                            <td class="nowrap"><?= e(format_date($m['created_at'], 'j M, g:i A')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty"><span class="empty__icon"><?= icon('message') ?></span><p><?= $filter === 'unread' ? 'You’re all caught up.' : 'No messages yet.' ?></p></div>
    <?php endif; ?>
</div>
<?= partial('partials/pagination', ['paginator' => $messages]) ?>
