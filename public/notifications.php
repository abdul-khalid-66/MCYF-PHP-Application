<?php
require_once __DIR__ . '/../bootstrap.php';
$userId    = requireAuth('notifications');
$canManage = hasPermission('admin'); // any admin/super_admin can post notifications

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = post('action');

    try {
        if ($action === 'toggle') {
            NotificationItem::toggleRead((int) post('id'));
            redirect(BASE_URL . '/notifications');

        } elseif ($action === 'mark_all') {
            NotificationItem::markAllRead();
            redirect(BASE_URL . '/notifications');

        } elseif ($action === 'save' && $canManage) {
            $data = [
                'title'     => post('title'),
                'message'   => post('message'),
                'type'      => post('type'),
                'posted_at' => post('posted_at'),
            ];
            if ($data['title'] === '' || $data['message'] === '') {
                throw new RuntimeException('عنوان اور پیغام لازمی ہیں۔');
            }
            NotificationItem::create($data, $userId);
            $success = t_raw('msg_saved');

        } elseif ($action === 'delete' && $canManage) {
            NotificationItem::delete((int) post('id'));
            $success = t_raw('msg_deleted');
        }
    } catch (RuntimeException $ex) {
        $errors[] = $ex->getMessage();
    }

    if ($success && empty($errors)) {
        sessionFlash('success', $success);
        redirect(BASE_URL . '/notifications');
    }
}

$notifications = NotificationItem::all();

$pageTitle  = t_raw('notifications_heading');
$pageHero   = t('notifications_heading');
$activePage = 'notifications';
$content    = function () use ($notifications, $canManage, $errors) {
    require ROOT_PATH . '/views/pages/notifications.view.php';
};
require ROOT_PATH . '/views/layouts/main.php';
