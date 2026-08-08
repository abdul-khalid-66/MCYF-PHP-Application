<?php
require_once __DIR__ . '/../../bootstrap.php';

$pageTitle  = t_raw('error_access_denied');
$activePage = '';
$content    = function () { ?>
<div class="text-center py-5">
  <i class="bi bi-shield-lock text-danger" style="font-size:4rem;"></i>
  <h2 class="mt-3"><?= t('error_access_denied') ?></h2>
  <p class="text-muted"><?= t('error_access_msg') ?></p>
  <a href="<?= BASE_URL ?>/index" class="btn btn-forum mt-2">
    <i class="bi bi-house me-1"></i><?= t('error_back_home') ?>
  </a>
</div>
<?php };
require ROOT_PATH . '/views/layouts/main.php';
