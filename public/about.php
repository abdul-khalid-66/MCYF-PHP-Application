<?php
require_once __DIR__ . '/../bootstrap.php';
$userId = requireAuth('about');

$pdo       = DB::connection();
$aboutRows = $pdo->query("SELECT `key`, `value` FROM about_content")->fetchAll();
$about     = array_column($aboutRows, 'value', 'key');

$objectives = array_filter(
    explode("\n", $about['objectives'] ?? ''),
    fn($l) => trim($l) !== ''
);

$pageTitle   = t_raw('about_heading');
$pageHero    = t('about_heading');
$pageHeroSub = t('about_page_subtitle');
$activePage  = 'about';
$content     = function () use ($about, $objectives) {
    require ROOT_PATH . '/views/pages/about.view.php';
};
require ROOT_PATH . '/views/layouts/main.php';
