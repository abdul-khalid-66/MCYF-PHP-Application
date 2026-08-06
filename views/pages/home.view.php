<?php /* views/pages/home.view.php */ ?>

<!-- Stats row -->
<section class="container my-5">
  <div class="row g-3">
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <i class="bi bi-people stat-icon"></i>
        <div class="stat-number"><?= $activeMembers ?></div>
        <div class="small"><?= t('stat_active_members') ?></div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <i class="bi bi-calendar-event stat-icon"></i>
        <div class="stat-number"><?= $eventsCount ?></div>
        <div class="small"><?= t('stat_events') ?></div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <i class="bi bi-diagram-3 stat-icon"></i>
        <div class="stat-number"><?= $committeesCount ?></div>
        <div class="small"><?= t('stat_committees') ?></div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <i class="bi bi-heart-pulse stat-icon"></i>
        <div class="stat-number"><?= $emergencyCount ?></div>
        <div class="small"><?= t('stat_emergency') ?></div>
      </div>
    </div>
  </div>
</section>

<!-- About section -->
<section class="container my-5" id="about">
  <h2 class="section-title text-center d-block"><?= t('about_heading') ?></h2>
  <div class="row g-4 align-items-center">
    <div class="col-lg-6">
      <p class="fs-5"><?= e($about['us'] ?? '') ?></p>
      <div class="row g-3 mt-2">
        <div class="col-6">
          <div class="card-forum p-3">
            <h6 class="text-forum-gold mb-1"><i class="bi bi-eye me-1"></i><?= t('about_vision_heading') ?></h6>
            <p class="small mb-0"><?= e($about['vision'] ?? '') ?></p>
          </div>
        </div>
        <div class="col-6">
          <div class="card-forum p-3">
            <h6 class="text-forum-gold mb-1"><i class="bi bi-bullseye me-1"></i><?= t('about_mission_heading') ?></h6>
            <p class="small mb-0"><?= e($about['mission'] ?? '') ?></p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card-forum p-4 geo-frame">
        <div class="geo-corner tl"></div>
        <div class="geo-corner br"></div>
        <h6 class="section-title"><?= t('about_services') ?></h6>
        <?php
        $objectives = array_filter(
            explode("\n", $about['objectives'] ?? ''),
            fn($l) => trim($l) !== ''
        );
        ?>
        <ul class="mb-0">
          <?php foreach ($objectives as $obj): ?>
          <li><?= e(trim($obj)) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- Upcoming Events -->
<section class="container my-5" id="events">
  <h2 class="section-title text-center d-block"><?= t('events_heading') ?></h2>
  <div class="row g-3">
    <?php foreach ($events as $ev):
        $thumbStmt->execute([$ev['id']]);
        $thumb = $thumbStmt->fetchColumn() ?: 'https://picsum.photos/seed/evtdefault/500/320';
    ?>
    <div class="col-md-4">
      <div class="card-forum h-100 overflow-hidden">
        <img src="<?= e($thumb) ?>" class="w-100" style="height:160px;object-fit:cover;" alt="<?= e($ev['name']) ?>">
        <div class="p-3">
          <h6 class="mb-1"><?= e($ev['name']) ?></h6>
          <p class="small text-muted mb-1">
            <i class="bi bi-calendar3 me-1"></i><?= e($ev['event_date']) ?>
          </p>
          <p class="small text-muted mb-0">
            <i class="bi bi-geo-alt me-1"></i><?= e($ev['venue']) ?>
          </p>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($events)): ?>
    <p class="text-muted text-center"><?= t('msg_no_records') ?></p>
    <?php endif; ?>
  </div>
</section>

<!-- Join CTA -->
<section class="container my-5">
  <div class="card-forum p-5 text-center" style="background:linear-gradient(135deg,var(--forum-green-soft),#fff);">
    <h3 class="mb-2"><?= t('cta_heading') ?></h3>
    <p class="text-muted mb-3"><?= t('cta_body') ?></p>
    <div>
      <a href="<?= BASE_URL ?>/auth/signup.php" class="btn btn-forum me-2"><?= t('cta_join') ?></a>
      <a href="<?= BASE_URL ?>/contact.php" class="btn btn-outline-forum"><?= t('cta_contact') ?></a>
    </div>
  </div>
</section>
