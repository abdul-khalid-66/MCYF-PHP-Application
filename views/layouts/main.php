<?php
/**
 * views/layouts/main.php
 *
 * The master layout. Pages call it like:
 *
 *   $pageTitle  = t('nav_dashboard');
 *   $activePage = 'dashboard';
 *   $content    = function() { ?>  ...html... <?php };
 *   require ROOT_PATH . '/views/layouts/main.php';
 *
 * Variables expected:
 *   $pageTitle  (string)   — used in <title>
 *   $activePage (string)   — highlights current nav item
 *   $content    (callable) — the page body
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
    h1, h2, h3, h4, h5, h6, .font-display {
      font-family: '<?= fontDisplay() ?>', serif;
    }
  </style>
</head>
<body>

<?php require ROOT_PATH . '/views/components/navbar.php'; ?>

<?php if (!empty($pageHero)): ?>
<header class="page-hero">
  <div class="container">
    <h1 class="mb-1"><?= $pageHero ?></h1>
    <?php if (!empty($pageHeroSub)): ?>
    <p class="lead mb-0"><?= $pageHeroSub ?></p>
    <?php endif; ?>
  </div>
</header>
<?php endif; ?>

<main class="container my-4">
  <?= flashHtml('success') ?>
  <?= flashHtml('error') ?>
  <?php ($content)(); ?>
</main>

<?php require ROOT_PATH . '/views/components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
<?php if (!empty($extraJs)): ?>
<?= $extraJs ?>
<?php endif; ?>
</body>
</html>
