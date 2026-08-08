<?php
/**
 * views/components/footer.php
 * Dynamic footer — contact info pulled from DB, text from lang file.
 */

try {
    $pdo      = DB::connection();
    $contacts = $pdo
        ->query("SELECT type, value FROM contact_info ORDER BY sort_order")
        ->fetchAll();
} catch (Throwable) {
    $contacts = [];
}

$phones  = array_filter($contacts, fn($r) => $r['type'] === 'phone');
$emails  = array_filter($contacts, fn($r) => $r['type'] === 'email');
$addresses = array_filter($contacts, fn($r) => $r['type'] === 'address');
?>
<footer class="footer-forum mt-5">
  <div class="container">
    <div class="row gy-4">

      <!-- Brand column -->
      <div class="col-md-4">
        <h6 class="mb-2"><?= e(appName()) ?></h6>
        <p class="small mb-0"><?= t('footer_tagline') ?></p>
      </div>

      <!-- Quick links -->
      <div class="col-md-4">
        <h6 class="mb-2"><?= t('footer_quick_links') ?></h6>
        <ul class="list-unstyled small d-flex flex-column gap-1">
          <li><a href="<?= BASE_URL ?>/index"><?= t('nav_home') ?></a></li>
          <li><a href="<?= BASE_URL ?>/index#about"><?= t('nav_about_us') ?></a></li>
          <li><a href="<?= BASE_URL ?>/contact"><?= t('nav_contact') ?></a></li>
          <?php if (!isLoggedIn()): ?>
          <li><a href="<?= BASE_URL ?>/auth/login"><?= t('nav_login') ?></a></li>
          <?php endif; ?>
        </ul>
      </div>

      <!-- Contact info -->
      <div class="col-md-4">
        <h6 class="mb-2"><?= t('footer_contact') ?></h6>
        <ul class="list-unstyled small d-flex flex-column gap-1">
          <?php foreach ($phones as $c): ?>
          <li><i class="bi bi-telephone me-1"></i><?= e($c['value']) ?></li>
          <?php endforeach; ?>
          <?php foreach ($emails as $c): ?>
          <li><i class="bi bi-envelope me-1"></i><?= e($c['value']) ?></li>
          <?php endforeach; ?>
          <?php foreach ($addresses as $c): ?>
          <li><i class="bi bi-geo-alt me-1"></i><?= e($c['value']) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>

    </div>

    <div class="geo-divider my-4"></div>
    <p class="small text-center mb-0 opacity-75"><?= t('footer_copyright') ?></p>
  </div>
</footer>
