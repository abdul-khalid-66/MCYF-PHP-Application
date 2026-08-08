<?php
require_once __DIR__ . '/../bootstrap.php';
$userId = requireAuth('profile');

$user = Member::find($userId);
if (!$user) {
    authLogout();
    redirect(BASE_URL . '/auth/login');
}

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $name  = post('name');
    $email = post('email');

    if ($name === '' || $email === '') {
        $errors[] = 'نام اور ای میل لازمی ہیں۔';
    } else {
        // Email uniqueness (excluding self)
        $dupe = Member::findByEmail($email);
        if ($dupe && (int)$dupe['id'] !== $userId) {
            $errors[] = 'یہ ای میل پہلے سے ایک اور اکاؤنٹ کے پاس ہے۔';
        }
    }

    if (empty($errors)) {
        try {
            $data = [
                'name'        => $name,
                'father_name' => post('father_name'),
                'email'       => $email,
                'mobile'      => post('mobile'),
                'address'     => post('address'),
                'education'   => post('education'),
                'occupation'  => post('occupation'),
            ];

            $photoPath = handleImageUpload('photo', 'avatars');
            if ($photoPath) {
                $data['photo'] = $photoPath;
            }

            Member::update($userId, $data);
            sessionFlash('success', t_raw('profile_updated'));
            redirect(BASE_URL . '/profile');
        } catch (RuntimeException $ex) {
            $errors[] = $ex->getMessage();
        }
    }
}

$pageTitle  = t_raw('profile_edit_heading');
$pageHero   = t('profile_edit_heading');
$pageHeroSub= t('profile_edit_subtitle');
$activePage = 'profile';
$content    = function () use ($user, $errors) { ?>

<div class="row g-4 justify-content-center">
  <div class="col-lg-4">
    <div class="card-forum p-4 text-center">
      <img src="<?= e(memberPhotoUrl($user['photo'])) ?>" class="member-avatar mx-auto" style="width:120px;height:120px;" id="photoPreview" alt="">
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card-forum p-4">
      <h5 class="section-title"><?= t('profile_edit_heading') ?></h5>

      <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $err): ?><div><i class="bi bi-exclamation-triangle me-1"></i><?= e($err) ?></div><?php endforeach; ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="" enctype="multipart/form-data">
        <?= csrfField() ?>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label"><?= t('auth_full_name') ?></label>
            <input required class="form-control" name="name" value="<?= e($user['name']) ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label"><?= t('member_father') ?></label>
            <input class="form-control" name="father_name" value="<?= e($user['father_name']) ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label"><?= t('member_mobile') ?></label>
            <input class="form-control" name="mobile" value="<?= e($user['mobile']) ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label"><?= t('member_email') ?></label>
            <input required type="email" class="form-control" name="email" value="<?= e($user['email']) ?>">
          </div>
          <div class="col-12">
            <label class="form-label"><?= t('member_address') ?></label>
            <input class="form-control" name="address" value="<?= e($user['address']) ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label"><?= t('member_education') ?></label>
            <input class="form-control" name="education" value="<?= e($user['education']) ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label"><?= t('member_occupation') ?></label>
            <input class="form-control" name="occupation" value="<?= e($user['occupation']) ?>">
          </div>
          <div class="col-12">
            <label class="form-label"><?= t('profile_upload_photo') ?></label>
            <input type="file" class="form-control" name="photo" accept="image/*" id="photoInput">
          </div>
        </div>
        <div class="mt-3">
          <button type="submit" class="btn btn-forum"><i class="bi bi-check2 me-1"></i><?= t('profile_save') ?></button>
          <a href="<?= BASE_URL ?>/profile" class="btn btn-outline-forum"><i class="bi bi-x-lg me-1"></i><?= t('btn_cancel') ?></a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php };

addExtraJs(<<<'JS'
<script>
document.getElementById('photoInput')?.addEventListener('change', function (e) {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = ev => { document.getElementById('photoPreview').src = ev.target.result; };
  reader.readAsDataURL(file);
});
</script>
JS);

require ROOT_PATH . '/views/layouts/main.php';
