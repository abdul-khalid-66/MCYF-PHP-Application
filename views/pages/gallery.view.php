<?php /* views/pages/gallery.view.php */ ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
  <?php foreach ($errors as $err): ?><div><i class="bi bi-exclamation-triangle me-1"></i><?= e($err) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
  <select id="categoryFilter" class="form-select" style="max-width:240px;">
    <option value=""><?= t('gallery_all_categories') ?></option>
    <?php foreach ($categories as $cat): ?>
    <option value="<?= e(mb_strtolower($cat)) ?>"><?= e($cat) ?></option>
    <?php endforeach; ?>
  </select>

  <?php if ($canManage): ?>
  <div class="d-flex gap-2">
    <button class="btn btn-forum btn-sm" data-bs-toggle="modal" data-bs-target="#imageModal" onclick="openImageModal()">
      <i class="bi bi-image me-1"></i><?= t('gallery_add_image') ?>
    </button>
    <button class="btn btn-forum btn-sm" data-bs-toggle="modal" data-bs-target="#videoModal">
      <i class="bi bi-camera-video me-1"></i><?= t('gallery_add_video') ?>
    </button>
  </div>
  <?php endif; ?>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-3" role="tablist">
  <li class="nav-item">
    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabImages" type="button">
      <i class="bi bi-images me-1"></i><?= t('gallery_images') ?>
    </button>
  </li>
  <li class="nav-item">
    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabVideos" type="button">
      <i class="bi bi-camera-video me-1"></i><?= t('gallery_videos') ?>
    </button>
  </li>
</ul>

<div class="tab-content">

  <!-- Images tab -->
  <div class="tab-pane fade show active" id="tabImages">
    <div class="gallery-grid" id="imageGrid">
      <?php foreach ($images as $img): ?>
      <div class="gallery-item-wrap" data-category="<?= e(mb_strtolower($img['category'])) ?>">
        <a href="<?= e($img['url']) ?>" class="gallery-link gallery-item d-block" data-caption="<?= e($img['caption']) ?>">
          <img src="<?= e($img['url']) ?>" alt="<?= e($img['caption']) ?>" loading="lazy">
          <span class="gallery-caption"><?= e($img['caption']) ?></span>
        </a>
        <?php if ($canManage): ?>
        <div class="gallery-admin-actions">
          <button class="btn btn-sm btn-light js-edit-image" title="<?= t('btn_edit') ?>"
                  data-item='<?= htmlspecialchars(json_encode($img, JSON_UNESCAPED_UNICODE), ENT_QUOTES, "UTF-8") ?>'
                  data-bs-toggle="modal" data-bs-target="#imageModal">
            <i class="bi bi-pencil"></i>
          </button>
          <form method="POST" action="" data-confirm="<?= t('msg_confirm_delete') ?>">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete_image">
            <input type="hidden" name="id" value="<?= (int)$img['id'] ?>">
            <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
          </form>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php if (empty($images)): ?>
    <p class="text-muted text-center py-4"><?= t('gallery_no_images') ?></p>
    <?php endif; ?>
  </div>

  <!-- Videos tab -->
  <div class="tab-pane fade" id="tabVideos">
    <div class="gallery-grid" id="videoGrid">
      <?php foreach ($videos as $vid):
          $thumbUrl = $vid['type'] === 'youtube'
              ? 'https://img.youtube.com/vi/' . e($vid['youtube_id']) . '/hqdefault.jpg'
              : 'https://picsum.photos/seed/vid' . $vid['id'] . '/500/320';
      ?>
      <div class="gallery-item-wrap" data-category="<?= e(mb_strtolower($vid['category'])) ?>">
        <a href="#" class="video-link gallery-item d-block"
           data-type="<?= e($vid['type']) ?>"
           data-youtube="<?= e($vid['youtube_id']) ?>"
           data-video-path="<?= e($vid['video_path'] ? BASE_URL . '/' . $vid['video_path'] : '') ?>"
           data-caption="<?= e($vid['caption']) ?>">
          <img src="<?= $thumbUrl ?>" alt="<?= e($vid['caption']) ?>" loading="lazy">
          <span class="position-absolute top-50 start-50 translate-middle text-white" style="font-size:2.2rem;">
            <i class="bi bi-play-circle-fill"></i>
          </span>
          <span class="gallery-caption"><?= e($vid['caption']) ?></span>
        </a>
        <?php if ($canManage): ?>
        <div class="gallery-admin-actions">
          <form method="POST" action="" data-confirm="<?= t('msg_confirm_delete') ?>">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete_video">
            <input type="hidden" name="id" value="<?= (int)$vid['id'] ?>">
            <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
          </form>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php if (empty($videos)): ?>
    <p class="text-muted text-center py-4"><?= t('gallery_no_videos') ?></p>
    <?php endif; ?>
  </div>

</div>

<!-- Video lightbox modal -->
<div class="modal fade" id="videoLightbox" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content bg-dark">
      <div class="modal-header border-0">
        <h6 class="modal-title text-white" id="videoLightboxCaption"></h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0" id="videoLightboxBody" style="aspect-ratio:16/9;"></div>
    </div>
  </div>
</div>

<?php if ($canManage): ?>
<!-- Add/Edit Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="" enctype="multipart/form-data">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="save_image">
        <input type="hidden" name="id" id="img_id">
        <div class="modal-header">
          <h5 class="modal-title" id="imageModalLabel"><?= t('gallery_add_image') ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label"><?= t('label_photo') ?></label>
            <input type="file" accept="image/*" class="form-control" name="file" id="img_file">
            <div class="form-text"><?= t('gallery_or_url') ?>:</div>
            <input type="text" class="form-control mt-1" name="url" id="img_url" placeholder="https://...">
          </div>
          <div class="mb-3">
            <label class="form-label"><?= t('gallery_caption') ?></label>
            <input required class="form-control" name="caption" id="img_caption">
          </div>
          <div class="mb-2">
            <label class="form-label"><?= t('gallery_category') ?></label>
            <input required class="form-control" name="category" id="img_category" list="categoryList"
                   placeholder="<?= t('gallery_category_placeholder') ?>">
            <datalist id="categoryList">
              <?php foreach ($categories as $cat): ?><option value="<?= e($cat) ?>"><?php endforeach; ?>
            </datalist>
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

<!-- Add Video Modal -->
<div class="modal fade" id="videoModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="" enctype="multipart/form-data">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="save_video">
        <div class="modal-header">
          <h5 class="modal-title"><?= t('gallery_add_video') ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label"><?= t('gallery_youtube_label') ?></label>
            <input class="form-control" name="youtube" placeholder="https://youtube.com/watch?v=...">
          </div>
          <div class="text-center text-muted small mb-3">— <?= t('label_or') ?> —</div>
          <div class="mb-3">
            <label class="form-label"><?= t('gallery_video_file_label') ?></label>
            <input type="file" accept="video/*" class="form-control" name="file">
          </div>
          <div class="mb-3">
            <label class="form-label"><?= t('gallery_caption') ?></label>
            <input required class="form-control" name="caption">
          </div>
          <div class="mb-2">
            <label class="form-label"><?= t('gallery_category') ?></label>
            <input required class="form-control" name="category" list="categoryList"
                   placeholder="<?= t('gallery_category_placeholder') ?>">
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

<style>
.gallery-item-wrap { position: relative; }
.gallery-admin-actions {
  position: absolute; top: 6px; inset-inline-end: 6px;
  display: flex; gap: 4px; opacity: 0; transition: opacity .2s;
}
.gallery-item-wrap:hover .gallery-admin-actions { opacity: 1; }
</style>

<?php
$jsLabels = json_encode([
    'addImgTitle'  => t_raw('gallery_add_image'),
    'editImgTitle' => t_raw('btn_edit'),
], JSON_UNESCAPED_UNICODE);

$extraJs = <<<JS
<script>
const MCYF_GAL_LABELS = {$jsLabels};

document.addEventListener('click', function (e) {
  const btn = e.target.closest('.js-edit-image');
  if (!btn) return;
  openImageModal(JSON.parse(btn.dataset.item));
});


// Category filter (client-side, both tabs)
document.getElementById('categoryFilter')?.addEventListener('change', function () {
  const val = this.value;
  document.querySelectorAll('.gallery-item-wrap').forEach(item => {
    item.style.display = (!val || item.dataset.category === val) ? '' : 'none';
  });
});

function openImageModal(img) {
  const form = document.querySelector('#imageModal form');
  form.reset();
  const label = document.getElementById('imageModalLabel');
  if (img && img.id) {
    document.getElementById('img_id').value = img.id;
    document.getElementById('img_caption').value = img.caption || '';
    document.getElementById('img_category').value = img.category || '';
    document.getElementById('img_url').value = img.url && img.url.startsWith('http') ? img.url : '';
    label.textContent = MCYF_GAL_LABELS.editImgTitle;
  } else {
    document.getElementById('img_id').value = '';
    label.textContent = MCYF_GAL_LABELS.addImgTitle;
  }
}

// Video lightbox
document.addEventListener('click', function (e) {
  const link = e.target.closest('.video-link');
  if (!link) return;
  e.preventDefault();

  const type   = link.dataset.type;
  const yt     = link.dataset.youtube;
  const path   = link.dataset.videoPath;
  const caption= link.dataset.caption || '';

  const body = document.getElementById('videoLightboxBody');
  document.getElementById('videoLightboxCaption').textContent = caption;

  if (type === 'youtube' && yt) {
    body.innerHTML = '<iframe width="100%" height="100%" src="https://www.youtube.com/embed/' + yt + '?autoplay=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
  } else if (path) {
    body.innerHTML = '<video width="100%" height="100%" controls autoplay src="' + path + '"></video>';
  }

  const modal = new bootstrap.Modal(document.getElementById('videoLightbox'));
  modal.show();

  document.getElementById('videoLightbox').addEventListener('hidden.bs.modal', function () {
    body.innerHTML = '';
  }, { once: true });
});
</script>
JS;
