<?php
require_once __DIR__ . '/../bootstrap.php';
$userId    = requireAuth('emergency');
$canManage = hasPermission('emergency_manage');

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
                'icon'        => post('icon', 'bi-heart-pulse'),
                'description' => post('description'),
            ];
            if ($data['title'] === '') {
                throw new RuntimeException('عنوان لازمی ہے۔');
            }
            if ($id > 0) {
                EmergencyService::update($id, $data);
            } else {
                EmergencyService::create($data);
            }
            $success = t_raw('msg_saved');

        } elseif ($action === 'delete') {
            EmergencyService::delete((int) post('id'));
            $success = t_raw('msg_deleted');
        }
    } catch (RuntimeException $ex) {
        $errors[] = $ex->getMessage();
    }

    if ($success && empty($errors)) {
        sessionFlash('success', $success);
        redirect(BASE_URL . '/emergency');
    }
}

$services = EmergencyService::all();

// Fixed icon choices for the admin form (label keys translated per active language)
$iconChoices = [
    'bi-hospital'      => t_raw('emergency_icon_hospital'),
    'bi-flower1'       => t_raw('emergency_icon_burial'),
    'bi-house-heart'   => t_raw('emergency_icon_disaster'),
    'bi-droplet-half'  => t_raw('emergency_icon_blood'),
    'bi-cash-coin'     => t_raw('emergency_icon_financial'),
    'bi-truck'         => t_raw('emergency_icon_transport'),
    'bi-shield-check'  => t_raw('emergency_icon_safety'),
    'bi-heart-pulse'   => t_raw('emergency_icon_medical'),
];

$pageTitle  = t_raw('emergency_heading');
$pageHero   = t('emergency_heading');
$activePage = 'emergency';
$content    = function () use ($services, $canManage, $iconChoices, $errors) {
    require ROOT_PATH . '/views/pages/emergency.view.php';
};
require ROOT_PATH . '/views/layouts/main.php';
