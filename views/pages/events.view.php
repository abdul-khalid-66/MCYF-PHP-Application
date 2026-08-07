<?php /* views/pages/events.view.php */
$defaultThumb = 'https://picsum.photos/seed/evtdefault/500/320';
function eventThumbUrl(?string $path, string $default): string {
    if (!$path) return $default;
    if (str_starts_with($path, 'http')) return $path;
    return (file_exists(ROOT_PATH . '/public/' . $path)) ? BASE_URL . '/' . ltrim($path, '/') : $default;
}
?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
  <?php foreach ($errors as $err): ?><div><i class="bi bi-exclamation-triangle me-1"></i><?= e($err) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<?php if ($canManage): ?>
<div class="d-flex justify-content-end mb-3">
  <button class="btn btn-forum" data-bs-toggle="modal" data-bs-target="#eventModal" onclick="openEventModal()">
    <i class="bi bi-plus-lg me-1"></i><?= t('event_add') ?>
  </button>
</div>
<?php endif; ?>

<div class="row g-3" id="eventList">
  <?php if (empty($events)): ?>
  <div class="col-12">
    <div class="text-center text-muted py-5">
      <i class="bi bi-calendar-x" style="font-size:2.5rem;"></i>
      <p class="mt-2"><?= t('msg_no_records') ?></p>
    </div>
  </div>
  <?php endif; ?>

  <?php foreach ($events as $ev): ?>
  <div class="col-md-6 col-lg-4">
    <div class="card-forum h-100 overflow-hidden">
      <img src="<?= e(eventThumbUrl($ev['thumbnail'], $defaultThumb)) ?>" class="w-100" style="height:170px;object-fit:cover;" alt="<?= e($ev['name']) ?>">
      <div class="p-3">
        <div class="d-flex justify-content-between align-items-start gap-2">
          <h6 class="mb-1"><?= e($ev['name']) ?></h6>
          <span class="badge bg-forum-soft text-forum-green border text-nowrap"><?= formatDate($ev['event_date']) ?></span>
        </div>
        <p class="small text-muted mb-1"><i class="bi bi-geo-alt me-1"></i><?= e($ev['venue'] ?: '—') ?></p>
        <p class="small text-muted mb-2"><i class="bi bi-person-badge me-1"></i><?= e($ev['organizer'] ?: '—') ?></p>
        <?php if ($ev['description']): ?>
        <p class="small mb-2"><?= e(mb_strimwidth($ev['description'], 0, 140, '…')) ?></p>
        <?php endif; ?>

        <?php if ($canManage): ?>
        <div class="text-nowrap">
          <button class="btn btn-sm btn-outline-forum me-1 js-edit-event"
                  data-bs-toggle="modal" data-bs-target="#eventModal"
                  data-item='<?= htmlspecialchars(json_encode($ev, JSON_UNESCAPED_UNICODE), ENT_QUOTES, "UTF-8") ?>'>
            <i class="bi bi-pencil"></i>
          </button>
          <form method="POST" action="" class="d-inline" data-confirm="<?= t('msg_confirm_delete') ?>">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$ev['id'] ?>">
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
          </form>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<?php if ($canManage): ?>
<div class="modal fade" id="eventModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="" enctype="multipart/form-data">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" id="e_id">
        <div class="modal-header">
          <h5 class="modal-title" id="eventModalLabel"><?= t('event_add') ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label"><?= t('events_heading') ?></label>
            <input required class="form-control" name="name" id="e_name">
          </div>
          <div class="row g-3 mb-3">
            <div class="col-6">
              <label class="form-label"><?= t('event_date') ?></label>
              <input type="date" required class="form-control" name="event_date" id="e_event_date">
            </div>
            <div class="col-6">
              <label class="form-label"><?= t('event_venue') ?></label>
              <input class="form-control" name="venue" id="e_venue">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label"><?= t('event_organizer') ?></label>
            <input class="form-control" name="organizer" id="e_organizer">
          </div>
          <div class="mb-3">
            <label class="form-label"><?= t('event_description') ?></label>
            <textarea class="form-control" rows="3" name="description" id="e_description"></textarea>
          </div>
          <div class="mb-2">
            <label class="form-label"><?= t('label_photo') ?></label>
            <input type="file" accept="image/*" class="form-control" name="photo" id="e_photo_file">
          </div>
          <div class="mb-2 text-center">
            <img id="e_photo_preview" class="d-none" style="max-height:120px;border-radius:10px;">
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
    'addTitle'  => t_raw('event_add'),
    'editTitle' => t_raw('event_edit'),
    'baseUrl'   => BASE_URL,
], JSON_UNESCAPED_UNICODE);

$extraJs = <<<JS
<script>
const MCYF_EVENT_LABELS = {$jsLabels};

document.addEventListener('click', function (e) {
  const btn = e.target.closest('.js-edit-event');
  if (!btn) return;
  openEventModal(JSON.parse(btn.dataset.item));
});


document.getElementById('e_photo_file')?.addEventListener('change', function (e) {
  const file = e.target.files[0];
  const preview = document.getElementById('e_photo_preview');
  if (!file) { preview.classList.add('d-none'); return; }
  const reader = new FileReader();
  reader.onload = ev => { preview.src = ev.target.result; preview.classList.remove('d-none'); };
  reader.readAsDataURL(file);
});

function openEventModal(ev) {
  const form = document.querySelector('#eventModal form');
  form.reset();
  document.getElementById('e_photo_preview').classList.add('d-none');
  const label = document.getElementById('eventModalLabel');

  if (ev && ev.id) {
    document.getElementById('e_id').value = ev.id;
    document.getElementById('e_name').value = ev.name || '';
    document.getElementById('e_event_date').value = ev.event_date || '';
    document.getElementById('e_venue').value = ev.venue || '';
    document.getElementById('e_organizer').value = ev.organizer || '';
    document.getElementById('e_description').value = ev.description || '';
    label.textContent = MCYF_EVENT_LABELS.editTitle;
    if (ev.thumbnail) {
      const preview = document.getElementById('e_photo_preview');
      preview.src = ev.thumbnail.startsWith('http') ? ev.thumbnail : (MCYF_EVENT_LABELS.baseUrl + '/' + ev.thumbnail);
      preview.classList.remove('d-none');
    }
  } else {
    document.getElementById('e_id').value = '';
    label.textContent = MCYF_EVENT_LABELS.addTitle;
  }
}
</script>
JS;
?>
<?php endif; ?>
