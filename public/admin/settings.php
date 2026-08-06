<?php
require_once __DIR__ . '/../../bootstrap.php';
$userId = requireAuth('admin');

$pdo = DB::connection();

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $fields = [
        'app_lang'        => post('app_lang'),
        'app_name_ur'     => post('app_name_ur'),
        'app_name_en'     => post('app_name_en'),
        'app_subtitle'    => post('app_subtitle'),
        'app_icon'        => post('app_icon'),
        'theme_primary'   => post('theme_primary'),
        'theme_secondary' => post('theme_secondary'),
        'theme_accent'    => post('theme_accent'),
        'theme_extra'     => post('theme_extra'),
    ];

    // Handle logo upload
    if (!empty($_FILES['app_logo']['name'])) {
        $file     = $_FILES['app_logo'];
        $maxBytes = MAX_UPLOAD_MB * 1024 * 1024;

        if ($file['size'] > $maxBytes) {
            $error = 'فائل کا سائز حد سے زیادہ ہے۔';
        } elseif (!in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
            $error = 'صرف PNG، JPG، WEBP اور GIF فائلیں قابل قبول ہیں۔';
        } else {
            $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
            $dest = ROOT_PATH . '/public/assets/uploads/logos/logo.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $fields['app_logo'] = 'assets/uploads/logos/logo.' . $ext;
            }
        }
    }

    if (!$error) {
        $stmt = $pdo->prepare(
            "INSERT INTO settings (`key`, `value`) VALUES (:k, :v)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)"
        );
        foreach ($fields as $k => $v) {
            $stmt->execute([':k' => $k, ':v' => $v]);
        }
        $success = t_raw('settings_saved');
        // Reload so themeVars() reflects new colors on this page
        redirect(BASE_URL . '/admin/settings.php?saved=1');
    }
}

// Load current settings
$rows     = $pdo->query("SELECT `key`, `value` FROM settings")->fetchAll();
$settings = array_column($rows, 'value', 'key');

if (isset($_GET['saved'])) {
    $success = t_raw('settings_saved');
}

// Available languages (scan lang/ folder)
$langDirs = array_filter(
    glob(ROOT_PATH . '/lang/*', GLOB_ONLYDIR),
    fn($d) => file_exists($d . '/lang.php')
);
$availableLangs = array_map('basename', $langDirs);

$pageTitle  = t_raw('settings_heading');
$pageHero   = t('settings_heading');
$pageHeroSub= t('settings_subtitle');
$activePage = 'admin';
$content    = function () use ($settings, $availableLangs, $success, $error) { ?>

<?php if ($success): ?>
<div class="alert alert-success"><i class="bi bi-check-circle me-1"></i><?= e($success) ?></div>
<?php elseif ($error): ?>
<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-1"></i><?= e($error) ?></div>
<?php endif; ?>

<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card-forum p-4">
      <form method="POST" action="" enctype="multipart/form-data">
        <?= csrfField() ?>

        <!-- Logo -->
        <div class="mb-4">
          <label class="form-label fw-bold"><?= t('settings_logo') ?></label>
          <?php if (!empty($settings['app_logo']) && file_exists(ROOT_PATH . '/public/' . $settings['app_logo'])): ?>
          <div class="mb-2">
            <img src="<?= BASE_URL . '/' . e($settings['app_logo']) ?>"
                 alt="logo" style="height:60px;object-fit:contain;border-radius:8px;border:1px solid #ddd;padding:4px;">
          </div>
          <?php endif; ?>
          <input type="file" class="form-control" name="app_logo" accept="image/*">
          <div class="form-text"><?= t('settings_logo_hint') ?></div>
        </div>

        <hr>

        <!-- Language -->
        <div class="mb-3">
          <label class="form-label fw-bold">زبان (Language)</label>
          <select name="app_lang" class="form-select">
            <?php foreach ($availableLangs as $lang): ?>
            <option value="<?= e($lang) ?>" <?= ($settings['app_lang'] ?? 'ur') === $lang ? 'selected' : '' ?>>
              <?= e($lang) ?>
            </option>
            <?php endforeach; ?>
          </select>
          <div class="form-text">
            نئی زبان شامل کرنے کے لیے <code>lang/</code> فولڈر میں نئی فائل بنائیں — مثلاً <code>ps.php</code>
          </div>
        </div>

        <hr>

        <!-- Platform name -->
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label fw-bold"><?= t('settings_platform_name') ?></label>
            <input type="text" class="form-control" name="app_name_ur"
                   value="<?= e($settings['app_name_ur'] ?? APP_NAME_UR) ?>" dir="rtl">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold"><?= t('settings_platform_name_en') ?></label>
            <input type="text" class="form-control" name="app_name_en"
                   value="<?= e($settings['app_name_en'] ?? APP_NAME) ?>" dir="ltr">
          </div>
          <div class="col-md-8">
            <label class="form-label fw-bold"><?= t('settings_subtitle_field') ?></label>
            <input type="text" class="form-control" name="app_subtitle"
                   value="<?= e($settings['app_subtitle'] ?? APP_SUBTITLE) ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold"><?= t('settings_icon') ?></label>
            <div class="input-group">
              <span class="input-group-text"><i id="iconPreview" class="bi <?= e($settings['app_icon'] ?? APP_ICON) ?>"></i></span>
              <input type="text" class="form-control" name="app_icon"
                     id="iconInput"
                     value="<?= e($settings['app_icon'] ?? APP_ICON) ?>"
                     placeholder="bi-mosque">
            </div>
            <div class="form-text"><a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons لائبریری</a></div>
          </div>
        </div>

        <hr>

        <!-- Theme colours -->
        <h6 class="mb-3"><?= t('settings_primary_color') ?> / رنگ تھیم</h6>
        <div class="row g-3 mb-4">
          <div class="col-md-3">
            <label class="form-label small"><?= t('settings_primary_color') ?></label>
            <div class="input-group">
              <input type="color" class="form-control form-control-color" name="theme_primary"
                     value="<?= e($settings['theme_primary'] ?? THEME_PRIMARY) ?>">
              <input type="text" class="form-control form-control-sm" name="theme_primary_hex"
                     value="<?= e($settings['theme_primary'] ?? THEME_PRIMARY) ?>" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label small"><?= t('settings_secondary_color') ?></label>
            <div class="input-group">
              <input type="color" class="form-control form-control-color" name="theme_secondary"
                     value="<?= e($settings['theme_secondary'] ?? THEME_SECONDARY) ?>">
              <input type="text" class="form-control form-control-sm"
                     value="<?= e($settings['theme_secondary'] ?? THEME_SECONDARY) ?>" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label small"><?= t('settings_accent_color') ?> (گولڈن)</label>
            <div class="input-group">
              <input type="color" class="form-control form-control-color" name="theme_accent"
                     value="<?= e($settings['theme_accent'] ?? THEME_ACCENT) ?>">
              <input type="text" class="form-control form-control-sm"
                     value="<?= e($settings['theme_accent'] ?? THEME_ACCENT) ?>" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label small"><?= t('settings_extra_color') ?></label>
            <div class="input-group">
              <input type="color" class="form-control form-control-color" name="theme_extra"
                     value="<?= e(!empty($settings['theme_extra']) ? $settings['theme_extra'] : '#8B2635') ?>">
              <input type="text" class="form-control form-control-sm"
                     value="<?= e($settings['theme_extra'] ?? '') ?>" readonly>
            </div>
            <div class="form-text">خالی چھوڑیں = غیر فعال</div>
          </div>
        </div>

        <!-- Colour pickers sync hex text -->
        <script>
        document.querySelectorAll('input[type="color"]').forEach(picker => {
          const hex = picker.nextElementSibling;
          picker.addEventListener('input', () => { if (hex) hex.value = picker.value; });
        });
        // Icon preview
        document.getElementById('iconInput')?.addEventListener('input', function() {
          const prev = document.getElementById('iconPreview');
          if (prev) prev.className = 'bi ' + this.value;
        });
        </script>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-forum">
            <i class="bi bi-floppy me-1"></i><?= t('settings_save') ?>
          </button>
          <a href="<?= BASE_URL ?>/admin/index.php" class="btn btn-outline-secondary"><?= t('btn_back') ?></a>
        </div>

      </form>
    </div>
  </div>
</div>
<?php };
require ROOT_PATH . '/views/layouts/main.php';
