<?php
require_once __DIR__ . '/../bootstrap.php';
$userId    = requireAuth('committees');
$canManage = hasPermission('committees_manage');

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
                'description' => post('description'),
                'chairman_id' => (int) post('chairman_id') ?: null,
            ];
            $memberIds = array_map('intval', $_POST['member_ids'] ?? []);

            if ($data['name'] === '') {
                throw new RuntimeException('کمیٹی کا نام لازمی ہے۔');
            }

            if ($id > 0) {
                Committee::update($id, $data, $memberIds);
            } else {
                Committee::create($data, $memberIds);
            }
            $success = t_raw('msg_saved');

        } elseif ($action === 'delete') {
            Committee::delete((int) post('id'));
            $success = t_raw('msg_deleted');
        }
    } catch (RuntimeException $ex) {
        $errors[] = $ex->getMessage();
    }

    if ($success && empty($errors)) {
        sessionFlash('success', $success);
        redirect(BASE_URL . '/committees.php');
    }
}

$committees = Committee::all();
foreach ($committees as &$c) {
    $c['member_list'] = Committee::members((int) $c['id']);
}
unset($c);

$allMembers = $canManage ? Member::all() : [];

$pageTitle  = t_raw('committees_heading');
$pageHero   = t('committees_heading');
$activePage = 'committees';
$content    = function () use ($committees, $canManage, $allMembers, $errors) {
    require ROOT_PATH . '/views/pages/committees.view.php';
};
require ROOT_PATH . '/views/layouts/main.php';
