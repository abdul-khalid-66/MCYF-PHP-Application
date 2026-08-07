<?php /* views/pages/about.view.php */ ?>

<div class="row g-4">

  <!-- Sidebar quick nav -->
  <div class="col-lg-3">
    <div class="card-forum p-3 sticky-top" style="top:90px;">
      <div class="list-group list-group-flush">
        <a href="#us" class="list-group-item list-group-item-action border-0"><?= t('nav_about_us') ?></a>
        <a href="#vision" class="list-group-item list-group-item-action border-0"><?= t('nav_vision') ?></a>
        <a href="#mission" class="list-group-item list-group-item-action border-0"><?= t('nav_mission') ?></a>
        <a href="#objectives" class="list-group-item list-group-item-action border-0"><?= t('nav_objectives') ?></a>
        <a href="#charter" class="list-group-item list-group-item-action border-0"><?= t('nav_charter') ?></a>
        <a href="#constitution" class="list-group-item list-group-item-action border-0"><?= t('nav_constitution') ?></a>
      </div>
    </div>
  </div>

  <!-- Content -->
  <div class="col-lg-9">

    <section id="us" class="card-forum p-4 mb-4">
      <h4 class="section-title"><?= t('nav_about_us') ?></h4>
      <p class="mb-0"><?= e($about['us'] ?? '') ?></p>
    </section>

    <section id="vision" class="card-forum p-4 mb-4">
      <h4 class="section-title"><i class="bi bi-eye text-forum-gold me-1"></i><?= t('about_vision_heading') ?></h4>
      <p class="mb-0"><?= e($about['vision'] ?? '') ?></p>
    </section>

    <section id="mission" class="card-forum p-4 mb-4">
      <h4 class="section-title"><i class="bi bi-bullseye text-forum-gold me-1"></i><?= t('about_mission_heading') ?></h4>
      <p class="mb-0"><?= e($about['mission'] ?? '') ?></p>
    </section>

    <section id="objectives" class="card-forum p-4 mb-4">
      <h4 class="section-title"><i class="bi bi-list-check text-forum-gold me-1"></i><?= t('about_objectives') ?></h4>
      <ul class="mb-0">
        <?php foreach ($objectives as $obj): ?>
        <li><?= e(trim($obj)) ?></li>
        <?php endforeach; ?>
      </ul>
    </section>

    <section id="charter" class="card-forum p-4 mb-4">
      <h4 class="section-title"><i class="bi bi-file-earmark-text text-forum-gold me-1"></i><?= t('about_charter') ?></h4>
      <p class="mb-0"><?= e($about['charter'] ?? '') ?></p>
    </section>

    <section id="constitution" class="card-forum p-4">
      <h4 class="section-title"><i class="bi bi-journal-bookmark text-forum-gold me-1"></i><?= t('about_constitution') ?></h4>
      <p class="mb-0"><?= e($about['constitution'] ?? '') ?></p>
    </section>

  </div>
</div>
