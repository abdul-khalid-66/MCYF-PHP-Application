<?php
require_once __DIR__ . '/../bootstrap.php';
$userId    = requireAuth('events');
$canManage = hasPermission('events_manage');

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canManage) {
    verifyCsrf();
    $action = post('action');

    try {
        if ($action === 'save') {
            $id   = (int) post('id');
            $data = [
                'name'        => post('name'),
                'event_date'  => post('event_date'),
                'venue'       => post('venue'),
                'organizer'   => post('organizer'),
                'description' => post('description'),
            ];
            if ($data['name'] === '' || $data['event_date'] === '') {
                throw new RuntimeException('تقریب کا نام اور تاریخ لازمی ہیں۔');
            }

            if ($id > 0) {
                EventItem::update($id, $data);
            } else {
                $id = EventItem::create($data, $userId);
            }

            $photoPath = handleImageUpload('photo', 'gallery');
            if ($photoPath) {
                EventItem::addPhoto($id, $photoPath);
            }
            $success = t_raw('msg_saved');

        } elseif ($action === 'delete') {
            EventItem::delete((int) post('id'));
            $success = t_raw('msg_deleted');
        }
    } catch (RuntimeException $ex) {
        $errors[] = $ex->getMessage();
    }

    if ($success && empty($errors)) {
        sessionFlash('success', $success);
        redirect(BASE_URL . '/events.php');
    }
}

$events = EventItem::all();
// Attach thumbnail to each event for the view
foreach ($events as &$ev) {
    $ev['thumbnail'] = EventItem::thumbnail((int) $ev['id']);
}
unset($ev);

$pageTitle  = t_raw('events_heading');
$pageHero   = t('events_heading');
$activePage = 'events';
$content    = function () use ($events, $canManage, $errors) {
    require ROOT_PATH . '/views/pages/events.view.php';
};
require ROOT_PATH . '/views/layouts/main.php';
