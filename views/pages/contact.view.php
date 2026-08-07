<?php /* views/pages/contact.view.php */ ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
  <?php foreach ($errors as $err): ?><div><i class="bi bi-exclamation-triangle me-1"></i><?= e($err) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<div class="row g-4">

  <!-- Contact info -->
  <div class="col-lg-5">
    <div class="card-forum p-4 h-100">
      <h5 class="section-title"><?= t('contact_heading') ?></h5>

      <?php if (!empty($phones)): ?>
      <div class="mb-3">
        <strong><i class="bi bi-telephone text-forum-gold me-1"></i><?= t('contact_phones') ?></strong>
        <?php foreach ($phones as $p): ?>
        <p class="mb-1"><a href="tel:<?= e($p['value']) ?>"><?= e($p['value']) ?></a></p>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($emails)): ?>
      <div class="mb-3">
        <strong><i class="bi bi-envelope text-forum-gold me-1"></i><?= t('contact_email') ?></strong>
        <?php foreach ($emails as $em): ?>
        <p class="mb-1"><a href="mailto:<?= e($em['value']) ?>"><?= e($em['value']) ?></a></p>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($addresses)): ?>
      <div class="mb-3">
        <strong><i class="bi bi-geo-alt text-forum-gold me-1"></i><?= t('contact_address') ?></strong>
        <?php foreach ($addresses as $ad): ?>
        <p class="mb-1"><?= e($ad['value']) ?></p>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($maps)): ?>
      <div class="mt-3">
        <strong class="d-block mb-2"><i class="bi bi-map text-forum-gold me-1"></i><?= t('contact_map_heading') ?></strong>
        <?php foreach ($maps as $m): ?>
        <div class="ratio ratio-16x9 rounded-forum overflow-hidden">
          <iframe src="<?= e($m['value']) ?>" style="border:0;" loading="lazy"></iframe>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Contact form -->
  <div class="col-lg-7">
    <div class="card-forum p-4">
      <h5 class="section-title"><?= t('contact_send') ?></h5>
      <form method="POST" action="">
        <?= csrfField() ?>
        <input type="hidden" name="form_action" value="contact_submit">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label"><?= t('contact_name') ?></label>
            <input required class="form-control" name="name">
          </div>
          <div class="col-md-6">
            <label class="form-label"><?= t('contact_email_or_mobile') ?></label>
            <input required class="form-control" name="contact_info">
          </div>
          <div class="col-12">
            <label class="form-label"><?= t('contact_subject') ?></label>
            <input class="form-control" name="subject">
          </div>
          <div class="col-12">
            <label class="form-label"><?= t('contact_message') ?></label>
            <textarea required class="form-control" rows="5" name="message"></textarea>
          </div>
        </div>
        <button type="submit" class="btn btn-forum mt-3">
          <i class="bi bi-send me-1"></i><?= t('contact_send') ?>
        </button>
      </form>
    </div>
  </div>

</div>
