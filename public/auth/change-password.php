<?php
require_once __DIR__ . '/../../bootstrap.php';
$userId = requireAuth('profile');

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $current = post('current_password');
    $new     = post('new_password');
    $confirm = post('confirm_password');

    $pdo  = DB::connection();
    $stmt = $pdo->prepare("SELECT password FROM members WHERE id = ?");
    $stmt->execute([$userId]);
    $row  = $stmt->fetch();

    if (!$row || !password_verify($current, $row['password'])) {
        $error = 'موجودہ پاس ورڈ غلط ہے۔';
    } elseif ($new !== $confirm) {
        $error = 'نیا پاس ورڈ اور تصدیق ایک جیسی نہیں ہیں۔';
    } elseif (strlen($new) < 6) {
        $error = 'پاس ورڈ کم از کم 6 حروف کا ہونا چاہیے۔';
    } else {
        $upd = $pdo->prepare("UPDATE members SET password = ? WHERE id = ?");
        $upd->execute([password_hash($new, PASSWORD_BCRYPT), $userId]);
        $success = t_raw('auth_success_change_pw');
    }
}

$pageTitle  = t_raw('auth_change_pw_heading');
$pageHero   = t('auth_change_pw_heading');
$activePage = 'profile';
$content    = function () use ($success, $error) { ?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card-forum p-4">
      <?php if ($success): ?>
      <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i><?= e($success) ?></div>
      <?php elseif ($error): ?>
      <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-1"></i><?= e($error) ?></div>
      <?php endif; ?>
      <form method="POST" action="">
        <?= csrfField() ?>
        <div class="mb-3">
          <label class="form-label"><?= t('auth_current_pw') ?></label>
          <input required type="password" name="current_password" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label"><?= t('auth_new_pw') ?></label>
          <input required type="password" name="new_password" class="form-control" minlength="6">
        </div>
        <div class="mb-3">
          <label class="form-label"><?= t('auth_confirm_pw') ?></label>
          <input required type="password" name="confirm_password" class="form-control">
        </div>
        <button class="btn btn-forum"><?= t('auth_change_pw_btn') ?></button>
        <a href="<?= BASE_URL ?>/profile" class="btn btn-outline-secondary ms-2"><?= t('btn_cancel') ?></a>
      </form>
    </div>
  </div>
</div>
<?php };
require ROOT_PATH . '/views/layouts/main.php';
