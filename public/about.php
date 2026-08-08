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

// Charter is stored as one "Title|||Body" pair per line, with an optional
// trailing line starting with "NOTE|||" for the closing note.
$charterPoints = [];
$charterNote   = '';
foreach (explode("\n", $about['charter'] ?? '') as $line) {
    $line = trim($line);
    if ($line === '') continue;
    if (!str_contains($line, '|||')) continue;
    [$title, $body] = explode('|||', $line, 2);
    if ($title === 'NOTE') {
        $charterNote = $body;
    } else {
        $charterPoints[] = ['title' => $title, 'body' => $body];
    }
}

$pageTitle   = t_raw('about_heading');
$pageHero    = t('about_heading');
$pageHeroSub = t('about_page_subtitle');
$activePage  = 'about';
$content     = function () use ($about, $objectives, $charterPoints, $charterNote) {
    require ROOT_PATH . '/views/pages/about.view.php';
};
require ROOT_PATH . '/views/layouts/main.php';
