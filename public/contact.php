<?php
require_once __DIR__ . '/../bootstrap.php';
// Public page — matches original app behavior (accessible logged in or as guest)

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('form_action') === 'contact_submit') {
    verifyCsrf();
    $name    = post('name');
    $contact = post('contact_info');
    $subject = post('subject');
    $message = post('message');

    if ($name === '' || $contact === '' || $message === '') {
        $errors[] = 'نام، رابطہ اور پیغام لازمی ہیں۔';
    } else {
        ContactMessage::create($name, $contact, $subject, $message);
        sessionFlash('success', t_raw('contact_sent'));
        redirect(BASE_URL . '/contact');
    }
}

$pdo      = DB::connection();
$contacts = $pdo->query("SELECT * FROM contact_info ORDER BY sort_order")->fetchAll();
$phones   = array_filter($contacts, fn($r) => $r['type'] === 'phone');
$emails   = array_filter($contacts, fn($r) => $r['type'] === 'email');
$addresses= array_filter($contacts, fn($r) => $r['type'] === 'address');
$maps     = array_filter($contacts, fn($r) => $r['type'] === 'map');

$pageTitle  = t_raw('contact_heading');
$pageHero   = t('contact_heading');
$activePage = 'contact';
$content    = function () use ($phones, $emails, $addresses, $maps, $errors) {
    require ROOT_PATH . '/views/pages/contact.view.php';
};
require ROOT_PATH . '/views/layouts/main.php';
