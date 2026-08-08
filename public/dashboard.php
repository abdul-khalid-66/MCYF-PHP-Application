<?php
require_once __DIR__ . '/../bootstrap.php';
$userId = requireAuth('dashboard');

$user = Member::find($userId);
$pdo  = DB::connection();

// Stats
$statMembers       = Member::activeCount();
$statEvents        = (int) $pdo->query("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()")->fetchColumn();
$statAnnouncements = (int) $pdo->query("SELECT COUNT(*) FROM announcements")->fetchColumn();
$statNotifUnread   = (int) $pdo->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0")->fetchColumn();

// Widgets data
$latestAnnouncements = $pdo->query(
    "SELECT * FROM announcements ORDER BY posted_at DESC, id DESC LIMIT 3"
)->fetchAll();

$upcomingEvents = $pdo->query(
    "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3"
)->fetchAll();

$recentGallery = $pdo->query(
    "SELECT * FROM gallery_images ORDER BY created_at DESC LIMIT 8"
)->fetchAll();

$recentNotifications = $pdo->query(
    "SELECT * FROM notifications ORDER BY posted_at DESC, id DESC LIMIT 4"
)->fetchAll();

$pageTitle  = t_raw('nav_dashboard');
$activePage = 'dashboard';
$content    = function () use (
    $user, $statMembers, $statEvents, $statAnnouncements, $statNotifUnread,
    $latestAnnouncements, $upcomingEvents, $recentGallery, $recentNotifications
) { ?>

<div class="page-hero rounded-forum mb-4">
  <div class="container">
    <h1 class="mb-1"><?= t('dashboard_welcome') ?>، <?= e($user['name']) ?></h1>
    <p class="lead mb-0"><?= t('dashboard_position') ?>: <?= e($user['position'] ?: '—') ?></p>
  </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <i class="bi bi-people stat-icon"></i>
      <div class="stat-number"><?= $statMembers ?></div>
      <div class="small"><?= t('stat_active_members') ?></div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <i class="bi bi-calendar-event stat-icon"></i>
      <div class="stat-number"><?= $statEvents ?></div>
      <div class="small"><?= t('stat_events') ?></div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <i class="bi bi-megaphone stat-icon"></i>
      <div class="stat-number"><?= $statAnnouncements ?></div>
      <div class="small"><?= t('stat_announcements') ?></div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <i class="bi bi-bell stat-icon"></i>
      <div class="stat-number"><?= $statNotifUnread ?></div>
      <div class="small"><?= t('stat_new_notifications') ?></div>
    </div>
  </div>
</div>

<div class="row g-4">

  <!-- Latest announcements -->
  <div class="col-lg-6">
    <div class="dashboard-panel">
      <h5 class="dashboard-panel-title"><?= t('dashboard_latest_ann') ?></h5>
      <?php if (empty($latestAnnouncements)): ?>
        <p class="text-muted small"><?= t('msg_no_records') ?></p>
      <?php endif; ?>
      <?php foreach ($latestAnnouncements as $a):
          $priorityClass = 'priority-' . $a['priority'];
      ?>
      <div class="ann-item <?= $a['priority'] === 'urgent' ? 'unread' : '' ?>">
        <div class="d-flex justify-content-between align-items-start gap-2">
          <strong class="small"><?= e($a['title']) ?></strong>
          <span class="badge <?= $priorityClass ?>"><?= t('priority_' . $a['priority']) ?></span>
        </div>
        <p class="mb-1 small text-muted"><?= e(safe_strimwidth($a['description'], 0, 120, '…')) ?></p>
        <span class="small text-muted"><i class="bi bi-calendar3 me-1"></i><?= formatDate($a['posted_at']) ?></span>
      </div>
      <?php endforeach; ?>
      <a href="<?= BASE_URL ?>/announcements" class="btn btn-outline-forum btn-sm mt-2">
        <?= t('dashboard_view_all') ?>
      </a>
    </div>
  </div>

  <!-- Recent gallery -->
  <div class="col-lg-6">
    <div class="dashboard-panel">
      <h5 class="dashboard-panel-title"><?= t('dashboard_recent_gallery') ?></h5>
      <?php if (empty($recentGallery)): ?>
        <p class="text-muted small"><?= t('msg_no_records') ?></p>
      <?php else: ?>
      <div class="row g-2">
        <?php foreach ($recentGallery as $g): ?>
        <div class="col-6 col-md-3">
          <div class="gallery-item" style="aspect-ratio:1/1;">
            <img src="<?= e($g['url']) ?>" alt="<?= e($g['caption']) ?>" loading="lazy">
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <a href="<?= BASE_URL ?>/gallery" class="btn btn-outline-forum btn-sm mt-3">
        <?= t('dashboard_view_all') ?>
      </a>
    </div>
  </div>

  <!-- Upcoming events -->
  <div class="col-lg-6">
    <div class="dashboard-panel">
      <h5 class="dashboard-panel-title"><?= t('dashboard_upcoming_events') ?></h5>
      <?php if (empty($upcomingEvents)): ?>
        <p class="text-muted small"><?= t('msg_no_records') ?></p>
      <?php endif; ?>
      <ul class="list-group list-group-flush mb-0">
        <?php foreach ($upcomingEvents as $ev): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
          <div>
            <div class="fw-bold small"><?= e($ev['name']) ?></div>
            <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i><?= e($ev['venue']) ?></div>
          </div>
          <span class="badge bg-forum-soft text-forum-green border"><?= formatDate($ev['event_date']) ?></span>
        </li>
        <?php endforeach; ?>
      </ul>
      <a href="<?= BASE_URL ?>/events" class="btn btn-outline-forum btn-sm mt-2">
        <?= t('dashboard_view_all') ?>
      </a>
    </div>
  </div>

  <!-- Notifications -->
  <div class="col-lg-6">
    <div class="dashboard-panel">
      <h5 class="dashboard-panel-title"><?= t('dashboard_notifications') ?></h5>
      <?php if (empty($recentNotifications)): ?>
        <p class="text-muted small"><?= t('msg_no_records') ?></p>
      <?php endif; ?>
      <?php foreach ($recentNotifications as $n): ?>
      <div class="ann-item <?= !$n['is_read'] ? 'unread' : '' ?>">
        <div class="d-flex justify-content-between gap-2">
          <strong class="small"><?= e($n['title']) ?></strong>
          <span class="small text-muted"><?= formatDate($n['posted_at']) ?></span>
        </div>
        <p class="mb-0 small text-muted"><?= e(safe_strimwidth($n['message'], 0, 110, '…')) ?></p>
      </div>
      <?php endforeach; ?>
      <a href="<?= BASE_URL ?>/notifications" class="btn btn-outline-forum btn-sm mt-2">
        <?= t('dashboard_view_all') ?>
      </a>
    </div>
  </div>

</div>
<?php };
require ROOT_PATH . '/views/layouts/main.php';
