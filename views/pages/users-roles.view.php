<?php /* views/pages/users-roles.view.php */

// Map each permission key to an existing nav_/admin_ lang key for its label,
// and whether it's a "manage" (admin-level) permission needing a suffix.
$permLabelMap = [
    'dashboard'             => ['nav_dashboard', false],
    'members'               => ['nav_members', false],
    'announcements'         => ['nav_announcements', false],
    'gallery'                => ['nav_gallery', false],
    'events'                 => ['nav_events', false],
    'committees'             => ['nav_committees', false],
    'notifications'          => ['nav_notifications', false],
    'emergency'               => ['nav_emergency', false],
    'about'                   => ['nav_about', false],
    'contact'                 => ['nav_contact', false],
    'profile'                 => ['nav_profile', false],
    'admin'                   => ['nav_admin_panel', false],
    'user_management'         => ['nav_users_roles', false],
    'members_manage'          => ['nav_members', true],
    'announcements_manage'    => ['nav_announcements', true],
    'events_manage'           => ['nav_events', true],
    'committees_manage'       => ['nav_committees', true],
    'emergency_manage'        => ['nav_emergency', true],
    'gallery_manage'          => ['nav_gallery', true],
    'roles_manage'            => ['nav_users_roles', true],
    'messages_manage'         => ['admin_messages', false],
];

function permLabel(string $key, array $map): string
{
    [$navKey, $isManage] = $map[$key] ?? [$key, false];
    $label = t($navKey);
    return $isManage ? $label . ' (' . t('label_manage') . ')' : $label;
}
?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
  <?php foreach ($errors as $err): ?><div><i class="bi bi-exclamation-triangle me-1"></i><?= e($err) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Member roles table -->
<h5 class="section-title"><?= t('nav_members') ?></h5>
<div class="table-responsive mb-5">
  <table class="table table-forum align-middle mb-0">
    <thead>
      <tr>
        <th></th>
        <th><?= t('member_name') ?></th>
        <th><?= t('member_email') ?></th>
        <th><?= t('member_role') ?></th>
        <th><?= t('member_status') ?></th>
        <th><?= t('label_actions') ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($members as $m): ?>
      <tr>
        <td><img src="<?= e(memberPhotoUrl($m['photo'])) ?>" class="member-avatar-sm" alt=""></td>
        <td><?= e($m['name']) ?></td>
        <td><?= e($m['email']) ?></td>
        <td>
          <?php if ((int)$m['id'] === $userId): ?>
            <span class="badge bg-forum-soft text-forum-green border"><?= t('role_' . $m['role']) ?></span>
            <span class="small text-muted d-block"><?= t('own_role_locked') ?></span>
          <?php else: ?>
          <form method="POST" action="" class="d-flex gap-2">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="change_role">
            <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
            <select name="role" class="form-select form-select-sm" style="min-width:150px;">
              <?php foreach (['pending','member','committee_head','admin','super_admin'] as $r): ?>
              <option value="<?= $r ?>" <?= $m['role'] === $r ? 'selected' : '' ?>><?= t('role_' . $r) ?></option>
              <?php endforeach; ?>
            </select>
            <button class="btn btn-sm btn-outline-forum"><?= t('change_role') ?></button>
          </form>
          <?php endif; ?>
        </td>
        <td>
          <span class="badge <?= $m['status'] === 'active' ? 'badge-forum' : ($m['status'] === 'pending' ? 'badge-pending' : 'badge-inactive') ?>">
            <?= t('member_' . $m['status']) ?>
          </span>
        </td>
        <td>
          <a href="<?= BASE_URL ?>/members?q=<?= urlencode($m['email']) ?>" class="btn btn-sm btn-outline-forum">
            <i class="bi bi-pencil"></i>
          </a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Permissions matrix -->
<?php if (hasPermission('roles_manage')): ?>
<h5 class="section-title"><?= t('permissions_matrix') ?></h5>
<p class="text-muted small"><?= t('permissions_hint') ?></p>

<form method="POST" action="">
  <?= csrfField() ?>
  <input type="hidden" name="action" value="save_permissions">

  <div class="table-responsive">
    <table class="table table-forum align-middle mb-3">
      <thead>
        <tr>
          <th><?= t('label_title') ?></th>
          <?php foreach (EDITABLE_ROLES as $role): ?>
          <th class="text-center"><?= t('role_' . $role) ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach (ALL_PERMISSION_KEYS as $permKey): ?>
        <tr>
          <td><?= e(permLabel($permKey, $permLabelMap)) ?></td>
          <?php foreach (EDITABLE_ROLES as $role): ?>
          <td class="text-center">
            <input type="checkbox"
                   class="form-check-input"
                   name="perms[<?= $role ?>][]"
                   value="<?= $permKey ?>"
                   <?= in_array($permKey, $permissions[$role] ?? [], true) ? 'checked' : '' ?>>
          </td>
          <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <button type="submit" class="btn btn-forum">
    <i class="bi bi-floppy me-1"></i><?= t('btn_save') ?>
  </button>
</form>
<?php endif; ?>
