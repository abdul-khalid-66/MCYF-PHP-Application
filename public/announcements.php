<?php
require_once __DIR__ . '/../bootstrap.php';
$userId    = requireAuth('announcements');
$canManage = hasPermission('announcements_manage');

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canManage) {
    verifyCsrf();
    $action = post('action');

    try {
        if ($action === 'save') {
            $id   = (int) post('id');
            $data = [
                'title'       => post('title'),
                'description' => post('description'),
                'priority'    => post('priority', 'general'),
                'posted_at'   => post('posted_at'),
            ];
            if ($data['title'] === '' || $data['description'] === '') {
                throw new RuntimeException('عنوان اور تفصیل لازمی ہیں۔');
            }
            if ($id > 0) {
                Announcement::update($id, $data);
            } else {
                Announcement::create($data, $userId);
            }
            $success = t_raw('msg_saved');

        } elseif ($action === 'delete') {
            Announcement::delete((int) post('id'));
            $success = t_raw('msg_deleted');
        }
    } catch (RuntimeException $ex) {
        $errors[] = $ex->getMessage();
    }

    if ($success && empty($errors)) {
        sessionFlash('success', $success);
        redirect(BASE_URL . '/announcements');
    }
}

$announcements = Announcement::all();

$pageTitle  = t_raw('announcements_heading');
$pageHero   = t('announcements_heading');
$activePage = 'announcements';
$content    = function () use ($announcements, $canManage, $errors) {
    require ROOT_PATH . '/views/pages/announcements.view.php';
};
require ROOT_PATH . '/views/layouts/main.php';
