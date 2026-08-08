<?php
require_once __DIR__ . '/../../bootstrap.php';
$userId = requireAuth('user_management');

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = post('action');

    try {
        if ($action === 'change_role') {
            $targetId  = (int) post('id');
            $newRole   = post('role');
            $validRoles = ['pending', 'member', 'committee_head', 'admin', 'super_admin'];

            if ($targetId === $userId) {
                throw new RuntimeException(t_raw('own_role_locked'));
            }
            if (!in_array($newRole, $validRoles, true)) {
                throw new RuntimeException('غلط کردار منتخب کیا گیا۔');
            }
            $status = $newRole === 'pending' ? 'pending' : 'active';
            Member::update($targetId, ['role' => $newRole, 'status' => $status]);
            $success = t_raw('msg_saved');

        } elseif ($action === 'save_permissions' && hasPermission('roles_manage')) {
            foreach (EDITABLE_ROLES as $role) {
                $perms = array_map('strval', $_POST['perms'][$role] ?? []);
                RolePermission::setForRole($role, $perms);
            }
            $success = t_raw('msg_saved');
        }
    } catch (RuntimeException $ex) {
        $errors[] = $ex->getMessage();
    }

    if ($success && empty($errors)) {
        sessionFlash('success', $success);
        redirect(BASE_URL . '/admin/users');
    }
}

if (!RolePermission::isSeeded()) {
    RolePermission::seedDefaults();
}

$members     = Member::all();
$permissions = RolePermission::allGrouped();

$pageTitle  = t_raw('users_roles_heading');
$pageHero   = t('users_roles_heading');
$pageHeroSub= t('users_roles_subtitle');
$activePage = 'admin';
$content    = function () use ($members, $permissions, $userId, $errors) {
    require ROOT_PATH . '/views/pages/users-roles.view.php';
};
require ROOT_PATH . '/views/layouts/main.php';
