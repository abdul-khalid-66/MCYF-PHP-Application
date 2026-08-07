<?php /* views/pages/notifications.view.php */
$typeIcons = [
    'میٹنگ' => 'bi-people', 'ایونٹ' => 'bi-calendar-event', 'ہنگامی' => 'bi-exclamation-triangle',
    'کمیونٹی کام' => 'bi-hand-thumbs-up', 'عمومی' => 'bi-info-circle',
];
?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
  <?php foreach ($errors as $err): ?><div><i class="bi bi-exclamation-triangle me-1"></i><?= e($err) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<div class="d-flex justify-content-end gap-2 mb-3 flex-wrap">
  <?php if ($canManage): ?>
  <button class="btn btn-forum btn-sm" data-bs-toggle="modal" data-bs-target="#notificationModal">
    <i class="bi bi-plus-lg me-1"></i><?= t('notification_add') ?>
  </button>
  <?php endif; ?>
  <form method="POST" action="" class="d-inline">
    <?= csrfField() ?>
    <input type="hidden" name="action" value="mark_all">
    <button class="btn btn-outline-forum btn-sm">
      <i class="bi bi-check2-all me-1"></i><?= t('notification_mark_all_read') ?>
    </button>
  </form>
</div>

<div id="notificationList">
  <?php if (empty($notifications)): ?>
  <div class="text-center text-muted py-5">
    <i class="bi bi-bell-slash" style="font-size:2.5rem;"></i>
    <p class="mt-2"><?= t('msg_no_records') ?></p>
  </div>
  <?php endif; ?>

  <?php foreach ($notifications as $n):
      $icon = $typeIcons[$n['type']] ?? 'bi-bell';
  ?>
  <div class="ann-item <?= !$n['is_read'] ? 'unread' : '' ?>">
    <div class="d-flex justify-content-between align-items-start gap-2">
      <form method="POST" action="" class="d-flex gap-2 flex-grow-1" style="cursor:pointer;">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="toggle">
        <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
        <button type="submit" class="btn btn-link p-0 text-decoration-none text-start d-flex gap-2 flex-grow-1">
          <i class="bi <?= $icon ?> text-forum-gold fs-5"></i>
          <span>
            <strong class="d-block"><?= e($n['title']) ?></strong>
            <span class="d-block small text-muted mb-1"><?= e($n['message']) ?></span>
            <span class="badge bg-forum-soft text-forum-green border small"><?= e($n['type']) ?></span>
          </span>
        </button>
      </form>
      <div class="text-nowrap d-flex align-items-center gap-2">
        <span class="small text-muted"><?= formatDate($n['posted_at']) ?></span>
        <?php if ($canManage): ?>
        <form method="POST" action="" data-confirm="<?= t('msg_confirm_delete') ?>">
          <?= csrfField() ?>
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
          <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<?php if ($canManage): ?>
<div class="modal fade" id="notificationModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="save">
        <div class="modal-header">
          <h5 class="modal-title"><?= t('notification_add') ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label"><?= t('label_title') ?></label>
            <input required class="form-control" name="title">
          </div>
          <div class="mb-3">
            <label class="form-label"><?= t('label_description') ?></label>
            <textarea required class="form-control" rows="3" name="message"></textarea>
          </div>
          <div class="row g-3">
            <div class="col-6">
              <label class="form-label"><?= t('notification_type') ?></label>
              <input class="form-control" name="type" placeholder="میٹنگ / ایونٹ / ہنگامی / عمومی">
            </div>
            <div class="col-6">
              <label class="form-label"><?= t('label_date') ?></label>
              <input type="date" required class="form-control" name="posted_at" value="<?= date('Y-m-d') ?>">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-forum" data-bs-dismiss="modal"><?= t('btn_cancel') ?></button>
          <button type="submit" class="btn btn-forum"><?= t('btn_save') ?></button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>
