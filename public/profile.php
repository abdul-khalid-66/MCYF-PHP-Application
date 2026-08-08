<?php
require_once __DIR__ . '/../bootstrap.php';
$userId = requireAuth('profile');

$user = Member::find($userId);
if (!$user) {
    authLogout();
    redirect(BASE_URL . '/auth/login');
}

$pageTitle  = t_raw('profile_heading');
$pageHero   = t('profile_heading');
$pageHeroSub= t('profile_subtitle');
$activePage = 'profile';
$content    = function () use ($user) { ?>

<div class="row g-4 justify-content-center">
  <div class="col-lg-7">
    <div class="card-forum p-4">
      <div class="text-center mb-4">
        <img src="<?= e(memberPhotoUrl($user['photo'])) ?>" class="member-avatar mx-auto" style="width:120px;height:120px;" alt="<?= e($user['name']) ?>">
        <h4 class="mt-3 mb-0"><?= e($user['name']) ?></h4>
        <span class="badge bg-forum-soft text-forum-green border mt-2"><?= e($user['position'] ?: '—') ?></span>
        <span class="badge bg-forum-soft text-forum-green border mt-2"><?= t('role_' . $user['role']) ?></span>
      </div>
      <div class="row g-3">
        <div class="col-md-6"><strong><?= t('member_father') ?>:</strong> <?= e($user['father_name'] ?: '—') ?></div>
        <div class="col-md-6"><strong><?= t('member_mobile') ?>:</strong> <?= e($user['mobile'] ?: '—') ?></div>
        <div class="col-md-6"><strong><?= t('member_email') ?>:</strong> <?= e($user['email']) ?></div>
        <div class="col-md-6"><strong><?= t('member_join_date') ?>:</strong> <?= formatDate($user['joined_at']) ?></div>
        <div class="col-12"><strong><?= t('member_address') ?>:</strong> <?= e($user['address'] ?: '—') ?></div>
        <div class="col-md-4"><strong><?= t('member_district') ?>:</strong> <?= e($user['district'] ?: '—') ?></div>
        <div class="col-md-4"><strong><?= t('member_tehsil') ?>:</strong> <?= e($user['tehsil'] ?: '—') ?></div>
        <div class="col-md-4"><strong><?= t('member_village') ?>:</strong> <?= e($user['village'] ?: '—') ?></div>
        <div class="col-md-6"><strong><?= t('member_education') ?>:</strong> <?= e($user['education'] ?: '—') ?></div>
        <div class="col-md-6"><strong><?= t('member_occupation') ?>:</strong> <?= e($user['occupation'] ?: '—') ?></div>
        <div class="col-md-6"><strong><?= t('member_blood_group') ?>:</strong> <?= e($user['blood_group'] ?: '—') ?></div>
        <div class="col-md-6"><strong><?= t('member_status') ?>:</strong> <?= t('member_' . $user['status']) ?></div>
      </div>
      <div class="d-flex gap-2 mt-4">
        <a href="<?= BASE_URL ?>/profile-edit" class="btn btn-forum">
          <i class="bi bi-pencil-square me-1"></i><?= t('profile_edit') ?>
        </a>
        <a href="<?= BASE_URL ?>/auth/change-password" class="btn btn-outline-forum">
          <i class="bi bi-key me-1"></i><?= t('nav_change_password') ?>
        </a>
      </div>
    </div>
  </div>
</div>
<?php };
require ROOT_PATH . '/views/layouts/main.php';
