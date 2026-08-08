<?php /* views/pages/messages.view.php */ ?>

<?php if (empty($messages)): ?>
<div class="text-center text-muted py-5">
  <i class="bi bi-envelope" style="font-size:2.5rem;"></i>
  <p class="mt-2"><?= t('no_messages') ?></p>
</div>
<?php endif; ?>

<?php foreach ($messages as $msg): ?>
<div class="ann-item <?= !$msg['is_read'] ? 'unread' : '' ?>">
  <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
    <div>
      <strong class="d-block"><?= e($msg['subject'] ?: '—') ?></strong>
      <span class="small text-muted d-block">
        <i class="bi bi-person me-1"></i><?= t('message_from') ?>: <?= e($msg['name']) ?>
        &nbsp;•&nbsp;
        <i class="bi bi-telephone me-1"></i><?= e($msg['contact_info']) ?>
      </span>
      <p class="mb-1 mt-2"><?= nl2br(e($msg['message'])) ?></p>
      <span class="small text-muted"><?= formatDate($msg['created_at'], 'd M Y, h:i A') ?></span>
    </div>
    <div class="text-nowrap d-flex gap-1">
      <?php if (!$msg['is_read']): ?>
      <form method="POST" action="">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="mark_read">
        <input type="hidden" name="id" value="<?= (int)$msg['id'] ?>">
        <button class="btn btn-sm btn-outline-forum" title="<?= t('notification_mark_read') ?>">
          <i class="bi bi-check2"></i>
        </button>
      </form>
      <?php endif; ?>
      <form method="POST" action="" data-confirm="<?= t('msg_confirm_delete') ?>">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= (int)$msg['id'] ?>">
        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
      </form>
    </div>
  </div>
</div>
<?php endforeach; ?>
