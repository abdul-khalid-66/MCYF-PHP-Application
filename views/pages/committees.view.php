<?php /* views/pages/committees.view.php */ ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
  <?php foreach ($errors as $err): ?><div><i class="bi bi-exclamation-triangle me-1"></i><?= e($err) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<?php if ($canManage): ?>
<div class="d-flex justify-content-end mb-3">
  <button class="btn btn-forum" data-bs-toggle="modal" data-bs-target="#committeeModal" onclick="openCommitteeModal()">
    <i class="bi bi-plus-lg me-1"></i><?= t('committee_add') ?>
  </button>
</div>
<?php endif; ?>

<div class="row g-3" id="committeeList">
  <?php if (empty($committees)): ?>
  <div class="col-12">
    <div class="text-center text-muted py-5">
      <i class="bi bi-diagram-3" style="font-size:2.5rem;"></i>
      <p class="mt-2"><?= t('msg_no_records') ?></p>
    </div>
  </div>
  <?php endif; ?>

  <?php foreach ($committees as $c): ?>
  <div class="col-md-6 col-lg-4">
    <div class="card-forum h-100 p-3">
      <div class="d-flex justify-content-between align-items-start gap-2">
        <h5 class="mb-2"><i class="bi bi-diagram-3 text-forum-gold me-1"></i><?= e($c['name']) ?></h5>
        <?php if ($canManage): ?>
        <div class="text-nowrap">
          <button class="btn btn-sm btn-outline-forum me-1 js-edit-committee"
                  data-bs-toggle="modal" data-bs-target="#committeeModal"
                  data-item='<?= htmlspecialchars(json_encode($c, JSON_UNESCAPED_UNICODE), ENT_QUOTES, "UTF-8") ?>'>
            <i class="bi bi-pencil"></i>
          </button>
          <form method="POST" action="" class="d-inline" data-confirm="<?= t('msg_confirm_delete') ?>">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
          </form>
        </div>
        <?php endif; ?>
      </div>
      <p class="small text-muted"><?= e($c['description'] ?: '—') ?></p>
      <p class="mb-1"><strong><?= t('committee_chairman') ?>:</strong> <?= e($c['chairman_name'] ?: '—') ?></p>
      <p class="mb-1"><strong><?= t('committee_members') ?>:</strong></p>
      <?php if (empty($c['member_list'])): ?>
        <p class="small text-muted"><?= t('committee_no_members') ?></p>
      <?php else: ?>
      <ul class="small mb-0">
        <?php foreach ($c['member_list'] as $m): ?>
        <li><?= e($m['name']) ?></li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<?php if ($canManage): ?>
<div class="modal fade" id="committeeModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <form method="POST" action="">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" id="c_id">
        <div class="modal-header">
          <h5 class="modal-title" id="committeeModalLabel"><?= t('committee_add') ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="max-height:70vh;overflow-y:auto;">
          <div class="mb-3">
            <label class="form-label"><?= t('committee_name') ?></label>
            <input required class="form-control" name="name" id="c_name">
          </div>
          <div class="mb-3">
            <label class="form-label"><?= t('committee_description') ?></label>
            <textarea class="form-control" rows="2" name="description" id="c_description"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label"><?= t('committee_chairman') ?></label>
            <select class="form-select" name="chairman_id" id="c_chairman_id">
              <option value="">—</option>
              <?php foreach ($allMembers as $m): ?>
              <option value="<?= (int)$m['id'] ?>"><?= e($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-1">
            <label class="form-label"><?= t('committee_select_members') ?></label>
            <div class="border rounded-forum p-2" style="max-height:200px;overflow-y:auto;">
              <?php foreach ($allMembers as $m): ?>
              <div class="form-check">
                <input class="form-check-input committee-member-checkbox" type="checkbox"
                       name="member_ids[]" value="<?= (int)$m['id'] ?>" id="cm_<?= (int)$m['id'] ?>">
                <label class="form-check-label small" for="cm_<?= (int)$m['id'] ?>"><?= e($m['name']) ?></label>
              </div>
              <?php endforeach; ?>
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
    'addTitle'  => t_raw('committee_add'),
    'editTitle' => t_raw('btn_edit') . ' — ' . t_raw('committees_heading'),
], JSON_UNESCAPED_UNICODE);

$extraJs = <<<JS
<script>
const MCYF_COMMITTEE_LABELS = {$jsLabels};

document.addEventListener('click', function (e) {
  const btn = e.target.closest('.js-edit-committee');
  if (!btn) return;
  openCommitteeModal(JSON.parse(btn.dataset.item));
});


function openCommitteeModal(c) {
  const form = document.querySelector('#committeeModal form');
  form.reset();
  document.querySelectorAll('.committee-member-checkbox').forEach(cb => cb.checked = false);
  const label = document.getElementById('committeeModalLabel');

  if (c && c.id) {
    document.getElementById('c_id').value = c.id;
    document.getElementById('c_name').value = c.name || '';
    document.getElementById('c_description').value = c.description || '';
    document.getElementById('c_chairman_id').value = c.chairman_id || '';
    (c.member_list || []).forEach(m => {
      const cb = document.getElementById('cm_' + m.id);
      if (cb) cb.checked = true;
    });
    label.textContent = MCYF_COMMITTEE_LABELS.editTitle;
  } else {
    document.getElementById('c_id').value = '';
    label.textContent = MCYF_COMMITTEE_LABELS.addTitle;
  }
}
</script>
JS;
?>
<?php endif; ?>
