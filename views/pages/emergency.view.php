<?php /* views/pages/emergency.view.php */ ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
  <?php foreach ($errors as $err): ?><div><i class="bi bi-exclamation-triangle me-1"></i><?= e($err) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<?php if ($canManage): ?>
<div class="d-flex justify-content-end mb-3">
  <button class="btn btn-forum" data-bs-toggle="modal" data-bs-target="#emergencyModal" onclick="openEmergencyModal()">
    <i class="bi bi-plus-lg me-1"></i><?= t('emergency_add') ?>
  </button>
</div>
<?php endif; ?>

<div class="row g-3">
  <?php if (empty($services)): ?>
  <div class="col-12">
    <div class="text-center text-muted py-5">
      <i class="bi bi-heart-pulse" style="font-size:2.5rem;"></i>
      <p class="mt-2"><?= t('msg_no_records') ?></p>
    </div>
  </div>
  <?php endif; ?>

  <?php foreach ($services as $s): ?>
  <div class="col-sm-6 col-lg-4">
    <div class="card-forum h-100 p-4 text-center">
      <i class="bi <?= e($s['icon']) ?> text-forum-gold" style="font-size:2.2rem;"></i>
      <h6 class="mt-2 mb-1"><?= e($s['title']) ?></h6>
      <p class="small text-muted mb-2"><?= e($s['description']) ?></p>
      <?php if ($canManage): ?>
      <div class="mt-auto">
        <button class="btn btn-sm btn-outline-forum me-1"
                data-bs-toggle="modal" data-bs-target="#emergencyModal"
                onclick='openEmergencyModal(<?= json_encode($s, JSON_UNESCAPED_UNICODE) ?>)'>
          <i class="bi bi-pencil"></i>
        </button>
        <form method="POST" action="" class="d-inline" data-confirm="<?= t('msg_confirm_delete') ?>">
          <?= csrfField() ?>
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
          <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
        </form>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<?php if ($canManage): ?>
<div class="modal fade" id="emergencyModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" id="es_id">
        <div class="modal-header">
          <h5 class="modal-title" id="emergencyModalLabel"><?= t('emergency_add') ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label"><?= t('label_title') ?></label>
            <input required class="form-control" name="title" id="es_title">
          </div>
          <div class="mb-3">
            <label class="form-label"><?= t('label_description') ?></label>
            <textarea class="form-control" rows="3" name="description" id="es_description"></textarea>
          </div>
          <div class="mb-2">
            <label class="form-label">Icon</label>
            <div class="row g-2" id="es_icon_picker">
              <?php foreach ($iconChoices as $iconClass => $label): ?>
              <div class="col-3 text-center">
                <label class="d-block border rounded-forum p-2 cursor-pointer icon-choice-label">
                  <input type="radio" name="icon" value="<?= e($iconClass) ?>" class="d-none icon-choice-radio">
                  <i class="bi <?= e($iconClass) ?> fs-4 d-block"></i>
                  <span class="small"><?= e($label) ?></span>
                </label>
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

<style>
.icon-choice-label { transition: all .15s; }
.icon-choice-radio:checked + i,
.icon-choice-radio:checked ~ span { color: var(--forum-gold); }
.icon-choice-radio:checked ~ * { color: var(--forum-gold); }
label:has(.icon-choice-radio:checked) { border-color: var(--forum-gold) !important; background: var(--forum-gold-soft); }
</style>

<?php
$jsLabels = json_encode([
    'addTitle'  => t_raw('emergency_add'),
    'editTitle' => t_raw('btn_edit'),
], JSON_UNESCAPED_UNICODE);

$extraJs = <<<JS
<script>
const MCYF_ES_LABELS = {$jsLabels};

function openEmergencyModal(s) {
  const form = document.querySelector('#emergencyModal form');
  form.reset();
  const label = document.getElementById('emergencyModalLabel');

  if (s && s.id) {
    document.getElementById('es_id').value = s.id;
    document.getElementById('es_title').value = s.title || '';
    document.getElementById('es_description').value = s.description || '';
    const radio = form.querySelector('input[value="' + s.icon + '"]');
    if (radio) radio.checked = true;
    label.textContent = MCYF_ES_LABELS.editTitle;
  } else {
    document.getElementById('es_id').value = '';
    label.textContent = MCYF_ES_LABELS.addTitle;
  }
}
</script>
JS;
?>
<?php endif; ?>
