<?php /* views/auth/signup.view.php */ ?>
<div class="auth-card" style="max-width:520px;">
  <div class="text-center mb-4">
    <i class="bi bi-person-plus text-forum-gold" style="font-size:2.2rem;"></i>
    <h3 class="mt-2 mb-0"><?= t('auth_signup_heading') ?></h3>
    <p class="text-muted small"><?= t('auth_signup_subtitle') ?></p>
  </div>

  <?php if ($success): ?>
  <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i><?= e($success) ?></div>
  <?php elseif ($error): ?>
  <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-1"></i><?= e($error) ?></div>
  <?php endif; ?>

  <?php if (!$success): ?>
  <form method="POST" action="">
    <?= csrfField() ?>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label"><?= t('auth_full_name') ?></label>
        <input required class="form-control" name="name" value="<?= e(post('name')) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label"><?= t('auth_mobile') ?></label>
        <input required class="form-control" name="mobile" value="<?= e(post('mobile')) ?>">
      </div>
      <div class="col-12">
        <label class="form-label"><?= t('auth_email_label') ?></label>
        <input required type="email" class="form-control" name="email" value="<?= e(post('email')) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label"><?= t('auth_password_label') ?></label>
        <input required type="password" class="form-control" name="password" minlength="6">
      </div>
      <div class="col-md-6">
        <label class="form-label"><?= t('auth_confirm_pw_label') ?></label>
        <input required type="password" class="form-control" name="confirm_password">
      </div>
    </div>
    <button type="submit" class="btn btn-forum w-100 mt-3"><?= t('auth_signup_btn') ?></button>
  </form>
  <?php endif; ?>

  <p class="text-center small text-muted mt-3 mb-0">
    <?= t('auth_have_account') ?> <a href="<?= BASE_URL ?>/auth/login"><?= t('auth_login_link') ?></a>
  </p>
</div>
