<?php
require_once __DIR__ . '/../../bootstrap.php';
$userId = requireAuth('admin');

$pdo = DB::connection();

$stats = [
    'members'       => $pdo->query("SELECT COUNT(*) FROM members")->fetchColumn(),
    'announcements' => $pdo->query("SELECT COUNT(*) FROM announcements")->fetchColumn(),
    'events'        => $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn(),
    'committees'    => $pdo->query("SELECT COUNT(*) FROM committees")->fetchColumn(),
    'gallery'       => (int)$pdo->query("SELECT COUNT(*) FROM gallery_images")->fetchColumn()
                     + (int)$pdo->query("SELECT COUNT(*) FROM gallery_videos")->fetchColumn(),
    'notifications' => $pdo->query("SELECT COUNT(*) FROM notifications")->fetchColumn(),
    'pending'       => $pdo->query("SELECT COUNT(*) FROM members WHERE role='pending'")->fetchColumn(),
];

$pageTitle  = t_raw('admin_heading');
$pageHero   = t('admin_heading');
$pageHeroSub= t('admin_subtitle');
$activePage = 'admin';
$content    = function () use ($stats) { ?>

<!-- Quick stats -->
<div class="row g-3 mb-4">
  <?php
  $statItems = [
    ['members', 'bi-people', t_raw('nav_members')],
    ['announcements', 'bi-megaphone', t_raw('nav_announcements')],
    ['events', 'bi-calendar-event', t_raw('nav_events')],
    ['committees', 'bi-diagram-3', t_raw('nav_committees')],
    ['gallery', 'bi-images', t_raw('nav_gallery')],
    ['notifications', 'bi-bell', t_raw('nav_notifications')],
  ];
  foreach ($statItems as [$key, $icon, $label]):
  ?>
  <div class="col-6 col-md-4 col-lg-2">
    <div class="stat-card">
      <i class="bi <?= $icon ?> stat-icon"></i>
      <div class="stat-number"><?= $stats[$key] ?></div>
      <div class="small"><?= $label ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<?php if ($stats['pending'] > 0): ?>
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
  <i class="bi bi-person-exclamation fs-5"></i>
  <span>
    <strong><?= $stats['pending'] ?></strong> درخواست گزار منظوری کے منتظر ہیں —
    <a href="<?= BASE_URL ?>/members?status=pending" class="alert-link">ابھی دیکھیں</a>
  </span>
</div>
<?php endif; ?>

<!-- Admin sections grid -->
<h5 class="section-title mb-3"><?= t('nav_admin_panel') ?></h5>
<div class="row g-3">

  <?php
  // Path is relative to BASE_URL directly — some pages live at the top level
  // (public pages with an inline admin-management section, e.g. members.php),
  // others (settings, future users.php) are admin-only and live under /admin/.
  $cards = [
    ['members.php',        'bi-people',          t_raw('admin_members'),           'members_manage'],
    ['announcements.php',  'bi-megaphone',       t_raw('admin_announcements'),     'announcements_manage'],
    ['notifications.php',  'bi-bell',            t_raw('admin_notifications'),     'admin'],
    ['events.php',         'bi-calendar-event',  t_raw('admin_events'),            'events_manage'],
    ['committees.php',     'bi-diagram-3',       t_raw('admin_committees'),        'committees_manage'],
    ['gallery.php',        'bi-images',          t_raw('admin_gallery'),           'gallery_manage'],
    ['emergency.php',      'bi-heart-pulse',     t_raw('admin_emergency'),         'emergency_manage'],
    ['admin/users.php',    'bi-people-fill',     t_raw('admin_users_roles'),       'user_management'],
    ['admin/settings.php', 'bi-gear',            t_raw('admin_platform_settings'), 'admin'],
  ];
  foreach ($cards as [$path, $icon, $label, $perm]):
    if (!hasPermission($perm)) continue;
  ?>
  <div class="col-sm-6 col-lg-4">
    <a href="<?= BASE_URL ?>/<?= $path ?>" class="text-decoration-none">
      <div class="card-forum p-4 d-flex align-items-center gap-3">
        <i class="bi <?= $icon ?> text-forum-gold fs-3"></i>
        <h6 class="mb-0"><?= e($label) ?></h6>
      </div>
    </a>
  </div>
  <?php endforeach; ?>

</div>
<?php };
require ROOT_PATH . '/views/layouts/main.php';
