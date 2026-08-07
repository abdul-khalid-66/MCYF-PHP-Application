<?php /* views/pages/announcements.view.php */ ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
  <?php foreach ($errors as $err): ?><div><i class="bi bi-exclamation-triangle me-1"></i><?= e($err) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<?php if ($canManage): ?>
<div class="d-flex justify-content-end mb-3">
  <button class="btn btn-forum" data-bs-toggle="modal" data-bs-target="#announcementModal" onclick="openAnnouncementModal()">
    <i class="bi bi-plus-lg me-1"></i><?= t('announcement_add') ?>
  </button>
</div>
<?php endif; ?>

<div id="announcementList">
  <?php if (empty($announcements)): ?>
  <div class="text-center text-muted py-5">
    <i class="bi bi-megaphone" style="font-size:2.5rem;"></i>
    <p class="mt-2"><?= t('msg_no_records') ?></p>
  </div>
  <?php endif; ?>

  <?php foreach ($announcements as $a): ?>
  <div class="card-forum p-3 mb-3">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
      <div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <h5 class="mb-0"><?= e($a['title']) ?></h5>
          <span class="badge priority-<?= e($a['priority']) ?>"><?= t('priority_' . $a['priority']) ?></span>
        </div>
        <p class="mb-1 text-muted"><?= nl2br(e($a['description'])) ?></p>
        <span class="small text-muted"><i class="bi bi-calendar3 me-1"></i><?= formatDate($a['posted_at']) ?></span>
      </div>
      <?php if ($canManage): ?>
      <div class="text-nowrap">
        <button class="btn btn-sm btn-outline-forum me-1"
                data-bs-toggle="modal" data-bs-target="#announcementModal"
                onclick='openAnnouncementModal(<?= json_encode($a, JSON_UNESCAPED_UNICODE) ?>)'>
          <i class="bi bi-pencil"></i>
        </button>
        <form method="POST" action="" class="d-inline" data-confirm="<?= t('msg_confirm_delete') ?>">
          <?= csrfField() ?>
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
          <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
        </form>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<?php if ($canManage): ?>
<div class="modal fade" id="announcementModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" id="a_id">
        <div class="modal-header">
          <h5 class="modal-title" id="announcementModalLabel"><?= t('announcement_add') ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label"><?= t('label_title') ?></label>
            <input required class="form-control" name="title" id="a_title">
          </div>
          <div class="mb-3">
            <label class="form-label"><?= t('label_description') ?></label>
            <textarea required class="form-control" rows="3" name="description" id="a_description"></textarea>
          </div>
          <div class="row g-3">
            <div class="col-6">
              <label class="form-label"><?= t('label_date') ?></label>
              <input type="date" required class="form-control" name="posted_at" id="a_posted_at">
            </div>
            <div class="col-6">
              <label class="form-label"><?= t('announcement_priority') ?></label>
              <select class="form-select" name="priority" id="a_priority">
                <option value="general"><?= t('priority_general') ?></option>
                <option value="important"><?= t('priority_important') ?></option>
                <option value="urgent"><?= t('priority_urgent') ?></option>
              </select>
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

<?php
$jsLabels = json_encode([
    'addTitle'  => t_raw('announcement_add'),
    'editTitle' => t_raw('btn_edit') . ' — ' . t_raw('announcements_heading'),
], JSON_UNESCAPED_UNICODE);

$extraJs = <<<JS
<script>
const MCYF_ANN_LABELS = {$jsLabels};
function openAnnouncementModal(a) {
  const form = document.querySelector('#announcementModal form');
  form.reset();
  const label = document.getElementById('announcementModalLabel');
  if (a && a.id) {
    document.getElementById('a_id').value = a.id;
    document.getElementById('a_title').value = a.title || '';
    document.getElementById('a_description').value = a.description || '';
    document.getElementById('a_posted_at').value = a.posted_at || '';
    document.getElementById('a_priority').value = a.priority || 'general';
    label.textContent = MCYF_ANN_LABELS.editTitle;
  } else {
    document.getElementById('a_id').value = '';
    label.textContent = MCYF_ANN_LABELS.addTitle;
  }
}
</script>
JS;
?>
<?php endif; ?>
