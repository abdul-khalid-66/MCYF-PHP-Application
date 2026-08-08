<?php
/**
 * views/layouts/auth.php
 * Minimal layout for auth pages (login, signup, forgot-password).
 * No navbar/footer — just the centered auth card(s).
 */
?>
<!doctype html>
<html lang="<?= langCode() ?>" dir="<?= langDir() ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? '') ?> | <?= e(appName()) ?></title>

  <?php if (fontUrl()): ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="<?= e(fontUrl()) ?>" rel="stylesheet">
  <?php endif; ?>

  <link href="<?= e(bootstrapCss()) ?>" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">

  <?= themeVars() ?>

  <style>
    body {
      font-family: '<?= fontBody() ?>', '<?= fontDisplay() ?>', serif;
    }
  </style>
</head>
<body>
<div class="auth-wrap py-5">
  <?php ($content)(); ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
<?= getExtraJs() ?>
</body>
</html>
