<?php /* views/auth/login.view.php */ ?>
<div style="width:100%; max-width:900px;">
  <div class="row g-4 justify-content-center">

    <!-- Login card -->
    <div class="col-md-6">
      <div class="auth-card h-100">
        <div class="text-center mb-4">
          <?php if (appLogoPath()): ?>
            <img src="<?= e(appLogoPath()) ?>" alt="logo" style="height:60px;object-fit:contain;">
          <?php else: ?>
            <i class="bi <?= e(appIcon()) ?> text-forum-gold" style="font-size:2.4rem;"></i>
          <?php endif; ?>
          <h3 class="mt-2 mb-0"><?= e(appName()) ?></h3>
          <p class="text-muted small"><?= t('auth_login_heading') ?></p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <span><?= e($error) ?></span>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
          <?= csrfField() ?>

          <div class="mb-3">
            <label class="form-label"><?= t('auth_email_label') ?></label>
            <input required type="email" name="email" class="form-control"
                   value="<?= e(post('email')) ?>"
                   placeholder="example@masoodforum.org">
          </div>

          <div class="mb-2">
            <label class="form-label"><?= t('auth_password_label') ?></label>
            <input required type="password" name="password" class="form-control" placeholder="••••••••">
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="remember" name="remember">
              <label class="form-check-label small" for="remember"><?= t('auth_remember_me') ?></label>
            </div>
            <a href="<?= BASE_URL ?>/auth/forgot-password.php" class="small"><?= t('auth_forgot_password') ?></a>
          </div>

          <button type="submit" class="btn btn-forum w-100"><?= t('auth_login_btn') ?></button>
        </form>

        <p class="text-center small text-muted mt-3 mb-1">
          <?= t('auth_no_account') ?> <a href="<?= BASE_URL ?>/auth/signup.php"><?= t('auth_register_link') ?></a>
        </p>
        <p class="text-center small mb-0">
          <a href="<?= BASE_URL ?>/index.php"><i class="bi bi-arrow-right me-1"></i><?= t('nav_back_to_site') ?></a>
        </p>
      </div>
    </div>

    <!-- Test accounts card -->
    <div class="col-md-6">
      <div class="auth-card h-100">
        <h5 class="mb-1">
          <i class="bi bi-people-fill text-forum-gold me-1"></i><?= t('auth_test_accounts') ?>
        </h5>
        <p class="small text-muted mb-3"><?= t('auth_test_subtitle') ?></p>

        <?php
        try {
            $pdo   = DB::connection();
            $tests = $pdo->query(
                "SELECT name, email, role FROM members
                 WHERE role IN ('super_admin','admin','committee_head','member','pending')
                 ORDER BY FIELD(role,'super_admin','admin','committee_head','member','pending')"
            )->fetchAll();
        } catch (Throwable) {
            $tests = [];
        }
        ?>

        <div class="d-flex flex-column gap-2">
          <?php foreach ($tests as $u): ?>
          <button type="button"
                  class="btn btn-outline-forum text-start d-flex justify-content-between align-items-center py-2"
                  onclick="fillLogin('<?= e($u['email']) ?>')">
            <span>
              <span class="badge bg-forum-soft text-forum-green border me-2">
                <?= t('role_' . $u['role']) ?>
              </span>
              <?= e($u['name']) ?>
              <small class="text-muted d-block mt-1"><?= e($u['email']) ?></small>
            </span>
            <i class="bi bi-box-arrow-in-left fs-5"></i>
          </button>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

  </div>
</div>

<?php $extraJs = <<<'JS'
<script>
function fillLogin(email) {
  document.querySelector('[name="email"]').value = email;
  document.querySelector('[name="password"]').focus();
}
</script>
JS;
