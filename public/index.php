<?php
require_once __DIR__ . '/../bootstrap.php';

$pdo = DB::connection();

// Stats
$activeMembers = (int)$pdo->query("SELECT COUNT(*) FROM members WHERE status = 'active'")->fetchColumn();
$eventsCount   = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()")->fetchColumn();
$committeesCount = (int)$pdo->query("SELECT COUNT(*) FROM committees")->fetchColumn();
$emergencyCount  = (int)$pdo->query("SELECT COUNT(*) FROM emergency_services")->fetchColumn();

// Upcoming events (3)
$events = $pdo->query(
    "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3"
)->fetchAll();

// Event thumbnails
$thumbStmt = $pdo->prepare(
    "SELECT image FROM event_gallery WHERE event_id = ? LIMIT 1"
);

// About content
$aboutRows = $pdo->query("SELECT `key`, `value` FROM about_content")->fetchAll();
$about     = array_column($aboutRows, 'value', 'key');

$pageTitle  = e(appName());
$activePage = 'home';
$content    = function () use (
    $activeMembers, $eventsCount, $committeesCount, $emergencyCount,
    $events, $thumbStmt, $about
) {
    require ROOT_PATH . '/views/pages/home.view.php';
};

// Home uses the full layout but with its own hero OUTSIDE main.php
// so we need to override. We include the layout manually:
?>
<!doctype html>
<html lang="<?= langCode() ?>" dir="<?= langDir() ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(appName()) ?></title>
  <?php if (fontUrl()): ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="<?= e(fontUrl()) ?>" rel="stylesheet">
  <?php endif; ?>
  <link href="<?= e(bootstrapCss()) ?>" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
  <?= themeVars() ?>
  <style>
    body { font-family: '<?= fontBody() ?>', '<?= fontDisplay() ?>', serif; }
    h1,h2,h3,h4,h5,h6 { font-family: '<?= fontDisplay() ?>', serif; }
  </style>
</head>
<body>
<?php require ROOT_PATH . '/views/components/navbar.php'; ?>

<!-- Hero -->
<header class="page-hero text-center py-5" id="hero">
  <div class="container">
    <?php if (appLogoPath()): ?>
      <img src="<?= e(appLogoPath()) ?>" alt="logo" style="height:70px;object-fit:contain;">
    <?php else: ?>
      <i class="bi <?= e(appIcon()) ?> text-forum-gold" style="font-size:2.8rem;"></i>
    <?php endif; ?>
    <h1 class="mt-2 mb-2" style="font-size:2.4rem;"><?= e(appName()) ?></h1>
    <p class="lead mb-4"><?= e(appSubtitle()) ?></p>
    <div class="d-flex justify-content-center gap-2 flex-wrap">
      <?php if (isLoggedIn()): ?>
        <a href="<?= BASE_URL ?>/dashboard.php" class="btn btn-gold btn-lg">
          <i class="bi bi-speedometer2 me-1"></i><?= t('hero_cta_dashboard') ?>
        </a>
      <?php else: ?>
        <a href="<?= BASE_URL ?>/auth/signup.php" class="btn btn-gold btn-lg">
          <i class="bi bi-person-plus me-1"></i><?= t('hero_cta_join') ?>
        </a>
        <a href="<?= BASE_URL ?>/auth/login.php" class="btn btn-outline-light btn-lg">
          <?= t('hero_cta_login') ?>
        </a>
      <?php endif; ?>
    </div>
  </div>
</header>

<main>
  <?php ($content)(); ?>
</main>

<?php require ROOT_PATH . '/views/components/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>
