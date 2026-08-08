<?php
require_once __DIR__ . '/../../bootstrap.php';
$userId = requireAuth('messages_manage');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = post('action');
    $id     = (int) post('id');

    if ($action === 'mark_read') {
        ContactMessage::markRead($id);
    } elseif ($action === 'delete') {
        ContactMessage::delete($id);
        sessionFlash('success', t_raw('msg_deleted'));
    }
    redirect(BASE_URL . '/admin/messages');
}

$messages = ContactMessage::all();

$pageTitle  = t_raw('admin_messages');
$pageHero   = t('admin_messages');
$activePage = 'admin';
$content    = function () use ($messages) {
    require ROOT_PATH . '/views/pages/messages.view.php';
};
require ROOT_PATH . '/views/layouts/main.php';
