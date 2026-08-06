<?php
/**
 * views/components/navbar.php
 * Renders the correct nav variant based on auth state and permissions.
 * Expects: $activePage (string)
 */

$user = null;
if (isLoggedIn()) {
    $pdo  = DB::connection();
    $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([authUserId()]);
    $user = $stmt->fetch();
}

$logoPath = appLogoPath();
$appIcon  = appIcon();
$name     = appName();
$subtitle = appSubtitle();

// Helper: active class
$active = fn(string $page) => ($activePage ?? '') === $page ? 'active' : '';

// Unread notifications count
$unread = 0;
if ($user) {
    try {
        $unread = (int) DB::connection()
            ->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0")
            ->fetchColumn();
    } catch (Throwable) {}
}
?>
<nav class="navbar navbar-expand-lg navbar-forum sticky-top py-2">
  <div class="container">

    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>/<?= $user ? 'dashboard.php' : 'index.php' ?>">
      <?php if ($logoPath): ?>
        <img src="<?= e($logoPath) ?>" alt="<?= e($name) ?>" style="height:38px;width:auto;object-fit:contain;">
      <?php else: ?>
        <i class="bi <?= e($appIcon) ?> text-forum-gold fs-4"></i>
      <?php endif; ?>
      <span class="d-flex flex-column lh-sm">
        <span class="brand-title"><?= e($name) ?></span>
        <?php if ($subtitle): ?>
        <span class="brand-sub"><?= e($subtitle) ?></span>
        <?php endif; ?>
      </span>
    </a>

    <!-- Toggler -->
    <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#mainNav"
            aria-controls="mainNav" aria-expanded="false">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">

      <?php if ($user): ?>
      <!-- ── Authenticated nav ── -->
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-1 align-items-lg-center">

        <?php if (hasPermission('dashboard')): ?>
        <li class="nav-item">
          <a class="nav-link <?= $active('dashboard') ?>" href="<?= BASE_URL ?>/dashboard.php"><?= t('nav_dashboard') ?></a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('members')): ?>
        <li class="nav-item">
          <a class="nav-link <?= $active('members') ?>" href="<?= BASE_URL ?>/members.php"><?= t('nav_members') ?></a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('announcements')): ?>
        <li class="nav-item">
          <a class="nav-link <?= $active('announcements') ?>" href="<?= BASE_URL ?>/announcements.php"><?= t('nav_announcements') ?></a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('gallery')): ?>
        <li class="nav-item">
          <a class="nav-link <?= $active('gallery') ?>" href="<?= BASE_URL ?>/gallery.php"><?= t('nav_gallery') ?></a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('events')): ?>
        <li class="nav-item">
          <a class="nav-link <?= $active('events') ?>" href="<?= BASE_URL ?>/events.php"><?= t('nav_events') ?></a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('committees')): ?>
        <li class="nav-item">
          <a class="nav-link <?= $active('committees') ?>" href="<?= BASE_URL ?>/committees.php"><?= t('nav_committees') ?></a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('about')): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= $active('about') ?>" href="#" data-bs-toggle="dropdown">
            <?= t('nav_about') ?>
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/about.php#us"><?= t('nav_about_us') ?></a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/about.php#vision"><?= t('nav_vision') ?></a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/about.php#mission"><?= t('nav_mission') ?></a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/about.php#objectives"><?= t('nav_objectives') ?></a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/about.php#charter"><?= t('nav_charter') ?></a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/about.php#constitution"><?= t('nav_constitution') ?></a></li>
          </ul>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('emergency')): ?>
        <li class="nav-item">
          <a class="nav-link <?= $active('emergency') ?>" href="<?= BASE_URL ?>/emergency.php"><?= t('nav_emergency') ?></a>
        </li>
        <?php endif; ?>

        <?php if (hasPermission('contact')): ?>
        <li class="nav-item">
          <a class="nav-link <?= $active('contact') ?>" href="<?= BASE_URL ?>/contact.php"><?= t('nav_contact') ?></a>
        </li>
        <?php endif; ?>

      </ul>

      <!-- Right side: notifications + user dropdown -->
      <ul class="navbar-nav align-items-lg-center gap-1 ms-lg-3 mt-2 mt-lg-0">

        <?php if (hasPermission('notifications')): ?>
        <li class="nav-item">
          <a class="nav-link position-relative <?= $active('notifications') ?>" href="<?= BASE_URL ?>/notifications.php">
            <i class="bi bi-bell"></i>
            <?php if ($unread > 0): ?>
            <span class="badge rounded-pill badge-notif position-absolute top-0 start-0 translate-middle">
              <?= $unread ?>
            </span>
            <?php endif; ?>
          </a>
        </li>
        <?php endif; ?>

        <!-- User avatar dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 <?= $active('profile') ?>"
             href="#" data-bs-toggle="dropdown">
            <?php if (!empty($user['photo'])): ?>
              <img src="<?= e($user['photo']) ?>" class="member-avatar-sm" alt="<?= e($user['name']) ?>">
            <?php else: ?>
              <i class="bi bi-person-circle fs-5"></i>
            <?php endif; ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li class="px-3 py-2 small text-muted"><?= e($user['name']) ?></li>
            <li><hr class="dropdown-divider my-1"></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/profile.php">
              <i class="bi bi-person me-2"></i><?= t('nav_profile') ?>
            </a></li>

            <?php if (hasPermission('admin')): ?>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/index.php">
              <i class="bi bi-speedometer2 me-2"></i><?= t('nav_admin_panel') ?>
            </a></li>
            <?php endif; ?>

            <?php if (hasPermission('user_management')): ?>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/users.php">
              <i class="bi bi-people-fill me-2"></i><?= t('nav_users_roles') ?>
            </a></li>
            <?php endif; ?>

            <li><a class="dropdown-item" href="<?= BASE_URL ?>/auth/change-password.php">
              <i class="bi bi-key me-2"></i><?= t('nav_change_password') ?>
            </a></li>
            <li><hr class="dropdown-divider my-1"></li>
            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/auth/logout.php">
              <i class="bi bi-box-arrow-left me-2"></i><?= t('nav_logout') ?>
            </a></li>
          </ul>
        </li>
      </ul>

      <?php else: ?>
      <!-- ── Public nav ── -->
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-1 align-items-lg-center">
        <li class="nav-item"><a class="nav-link <?= $active('home') ?>" href="<?= BASE_URL ?>/index.php"><?= t('nav_home') ?></a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/index.php#about"><?= t('nav_about_us') ?></a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/index.php#events"><?= t('nav_events') ?></a></li>
        <li class="nav-item"><a class="nav-link <?= $active('contact') ?>" href="<?= BASE_URL ?>/contact.php"><?= t('nav_contact') ?></a></li>
      </ul>
      <ul class="navbar-nav align-items-lg-center gap-2 ms-lg-3 mt-2 mt-lg-0">
        <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="<?= BASE_URL ?>/auth/login.php"><?= t('nav_login') ?></a></li>
        <li class="nav-item"><a class="btn btn-gold btn-sm" href="<?= BASE_URL ?>/auth/signup.php"><?= t('nav_register') ?></a></li>
      </ul>
      <?php endif; ?>

    </div><!-- /.collapse -->
  </div><!-- /.container -->
</nav>
