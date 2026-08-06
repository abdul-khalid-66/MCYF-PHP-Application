<?php
require_once __DIR__ . '/../bootstrap.php';
requireAuth(); // must be logged in (role can be pending)

$pageTitle  = t_raw('error_pending_heading');
$activePage = '';
$content    = function () { ?>
<div class="text-center py-5">
  <i class="bi bi-hourglass-split text-forum-gold" style="font-size:4rem;"></i>
  <h2 class="mt-3"><?= t('error_pending_heading') ?></h2>
  <p class="text-muted"><?= t('error_pending_msg') ?></p>
  <a href="<?= BASE_URL ?>/auth/logout.php" class="btn btn-outline-forum mt-2">
    <i class="bi bi-box-arrow-left me-1"></i><?= t('nav_logout') ?>
  </a>
</div>
<?php };
require ROOT_PATH . '/views/layouts/main.php';
