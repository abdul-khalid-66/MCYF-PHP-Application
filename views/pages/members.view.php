<?php /* views/pages/members.view.php */ ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
  <?php foreach ($errors as $err): ?><div><i class="bi bi-exclamation-triangle me-1"></i><?= e($err) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Search -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
  <div class="input-group" style="max-width:340px;">
    <span class="input-group-text bg-forum-soft"><i class="bi bi-search"></i></span>
    <input type="text" class="form-control" id="memberSearch"
           placeholder="<?= t('member_search_placeholder') ?>">
  </div>
</div>

<!-- Public directory -->
<div class="row g-3" id="memberDirectory">
  <?php foreach ($directory as $m): ?>
  <div class="col-sm-6 col-lg-4 col-xl-3 member-directory-card"
       data-search="<?= e(mb_strtolower($m['name'] . ' ' . $m['position'] . ' ' . $m['district'])) ?>">
    <div class="card-forum h-100 text-center p-3">
      <img src="<?= e(memberPhotoUrl($m['photo'])) ?>" class="member-avatar mx-auto mt-2" alt="<?= e($m['name']) ?>">
      <div class="card-body">
        <h6 class="mb-1"><?= e($m['name']) ?></h6>
        <span class="badge bg-forum-soft text-forum-green border mb-2"><?= e($m['position'] ?: '—') ?></span>
        <p class="small text-muted mb-1"><i class="bi bi-geo-alt me-1"></i><?= e($m['district'] ?: '—') ?></p>
        <span class="badge <?= $m['status'] === 'active' ? 'badge-forum' : ($m['status'] === 'pending' ? 'badge-pending' : 'badge-inactive') ?>">
          <?= t('member_' . $m['status']) ?>
        </span>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if (empty($directory)): ?>
  <div class="col-12"><p class="text-muted text-center"><?= t('msg_no_records') ?></p></div>
  <?php endif; ?>
</div>

<?php if ($canManage): ?>
<!-- ══════════════════ Admin management section ══════════════════ -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-5 mb-3">
  <h5 class="section-title mb-0"><?= t('admin_members') ?></h5>
  <button class="btn btn-forum" data-bs-toggle="modal" data-bs-target="#memberModal" onclick="openMemberModal()">
    <i class="bi bi-plus-lg me-1"></i><?= t('member_add') ?>
  </button>
</div>

<!-- Filter form -->
<form method="GET" action="" class="row g-2 mb-3">
  <div class="col-sm-5 col-md-4">
    <input type="text" name="q" class="form-control form-control-sm"
           placeholder="<?= t('member_search_placeholder') ?>" value="<?= e($search) ?>">
  </div>
  <div class="col-sm-4 col-md-3">
    <select name="status" class="form-select form-select-sm">
      <option value=""><?= t('label_all') ?></option>
      <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>><?= t('member_active') ?></option>
      <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>><?= t('member_inactive') ?></option>
      <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>><?= t('member_pending') ?></option>
    </select>
  </div>
  <div class="col-sm-3 col-md-2">
    <button class="btn btn-outline-forum btn-sm w-100"><?= t('label_filter') ?></button>
  </div>
</form>

<div class="table-responsive">
  <table class="table table-forum align-middle mb-0" id="adminMembersTable">
    <thead>
      <tr>
        <th></th>
        <th><?= t('member_name') ?></th>
        <th><?= t('member_position') ?></th>
        <th><?= t('member_mobile') ?></th>
        <th><?= t('member_district') ?></th>
        <th><?= t('member_status') ?></th>
        <th><?= t('label_actions') ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($adminList as $m): ?>
      <tr>
        <td><img src="<?= e(memberPhotoUrl($m['photo'])) ?>" class="member-avatar-sm" alt=""></td>
        <td>
          <?= e($m['name']) ?>
          <?php if ($m['role'] !== 'member' && $m['role'] !== 'pending'): ?>
            <span class="badge bg-forum-soft text-forum-green border ms-1"><?= t('role_' . $m['role']) ?></span>
          <?php endif; ?>
        </td>
        <td><?= e($m['position'] ?: '—') ?></td>
        <td><?= e($m['mobile'] ?: '—') ?></td>
        <td><?= e($m['district'] ?: '—') ?></td>
        <td>
          <span class="badge <?= $m['status'] === 'active' ? 'badge-forum' : ($m['status'] === 'pending' ? 'badge-pending' : 'badge-inactive') ?>">
            <?= t('member_' . $m['status']) ?>
          </span>
        </td>
        <td class="text-nowrap">
          <?php if ($m['role'] === 'pending'): ?>
            <form method="POST" action="" class="d-inline">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="approve">
              <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
              <button class="btn btn-sm btn-success" title="<?= t('member_approve') ?>"><i class="bi bi-check-lg"></i></button>
            </form>
            <form method="POST" action="" class="d-inline" data-confirm="<?= t('msg_confirm_delete') ?>">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="reject">
              <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
              <button class="btn btn-sm btn-outline-danger" title="<?= t('member_reject') ?>"><i class="bi bi-x-lg"></i></button>
            </form>
          <?php endif; ?>

          <button class="btn btn-sm btn-outline-forum js-edit-member"
                  title="<?= t('btn_edit') ?>"
                  data-bs-toggle="modal" data-bs-target="#memberModal"
                  data-item='<?= htmlspecialchars(json_encode($m, JSON_UNESCAPED_UNICODE), ENT_QUOTES, "UTF-8") ?>'>
            <i class="bi bi-pencil"></i>
          </button>

          <?php if ((int)$m['id'] !== $userId): ?>
          <form method="POST" action="" class="d-inline" data-confirm="<?= t('msg_confirm_delete') ?>">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
            <button class="btn btn-sm btn-outline-danger" title="<?= t('btn_delete') ?>"><i class="bi bi-trash"></i></button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($adminList)): ?>
      <tr><td colspan="7" class="text-center text-muted py-4"><?= t('msg_no_records') ?></td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Add/Edit Member Modal -->
<div class="modal fade" id="memberModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form method="POST" action="" enctype="multipart/form-data">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" id="f_id" value="">

        <div class="modal-header">
          <h5 class="modal-title" id="memberModalLabel"><?= t('member_add') ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="max-height:70vh;overflow-y:auto;">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label"><?= t('auth_full_name') ?></label>
              <input required class="form-control" name="name" id="f_name">
            </div>
            <div class="col-md-6">
              <label class="form-label"><?= t('member_father') ?></label>
              <input class="form-control" name="father_name" id="f_father_name">
            </div>
            <div class="col-md-6">
              <label class="form-label"><?= t('member_photo_url_or_upload') ?></label>
              <input type="file" class="form-control" name="photo" id="f_photo" accept="image/*">
            </div>
            <div class="col-md-6">
              <label class="form-label"><?= t('member_cnic') ?></label>
              <input class="form-control" name="cnic" id="f_cnic">
            </div>
            <div class="col-md-6">
              <label class="form-label"><?= t('member_mobile') ?></label>
              <input class="form-control" name="mobile" id="f_mobile">
            </div>
            <div class="col-md-6">
              <label class="form-label"><?= t('auth_email_label') ?></label>
              <input required type="email" class="form-control" name="email" id="f_email">
            </div>
            <div class="col-12">
              <label class="form-label"><?= t('member_address') ?></label>
              <input class="form-control" name="address" id="f_address">
            </div>
            <div class="col-md-4">
              <label class="form-label"><?= t('member_district') ?></label>
              <input class="form-control" name="district" id="f_district">
            </div>
            <div class="col-md-4">
              <label class="form-label"><?= t('member_tehsil') ?></label>
              <input class="form-control" name="tehsil" id="f_tehsil">
            </div>
            <div class="col-md-4">
              <label class="form-label"><?= t('member_village') ?></label>
              <input class="form-control" name="village" id="f_village">
            </div>
            <div class="col-md-6">
              <label class="form-label"><?= t('member_education') ?></label>
              <input class="form-control" name="education" id="f_education">
            </div>
            <div class="col-md-6">
              <label class="form-label"><?= t('member_occupation') ?></label>
              <input class="form-control" name="occupation" id="f_occupation">
            </div>
            <div class="col-md-4">
              <label class="form-label"><?= t('member_blood_group') ?></label>
              <input class="form-control" name="blood_group" id="f_blood_group">
            </div>
            <div class="col-md-4">
              <label class="form-label"><?= t('member_join_date') ?></label>
              <input type="date" class="form-control" name="joined_at" id="f_joined_at">
            </div>
            <div class="col-md-4">
              <label class="form-label"><?= t('member_status') ?></label>
              <select class="form-select" name="status" id="f_status">
                <option value="active"><?= t('member_active') ?></option>
                <option value="inactive"><?= t('member_inactive') ?></option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label"><?= t('member_position') ?></label>
              <select class="form-select" name="position" id="f_position">
                <?php foreach ($positions as $key => $label): ?>
                <option value="<?= e($label) ?>"><?= e($label) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label" id="f_password_label"><?= t('member_new_password') ?></label>
              <input type="password" class="form-control" name="password" id="f_password" autocomplete="new-password">
              <div class="form-text" id="f_password_hint"><?= t('member_password_hint_new') ?></div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-forum" data-bs-dismiss="modal"><?= t('btn_cancel') ?></button>
          <button type="submit" class="btn btn-forum"><?= t('btn_save') ?></button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<?php
$jsLabels = json_encode([
    'addTitle'  => t_raw('member_add'),
    'editTitle' => t_raw('member_edit'),
    'pwHintNew' => t_raw('member_password_hint_new'),
    'pwHintEdit'=> t_raw('member_password_hint_edit'),
], JSON_UNESCAPED_UNICODE);

$extraJs = <<<JS
<script>
const MCYF_MEMBER_LABELS = {$jsLabels};

// Client-side live filter for the public directory (server already has full list)
document.getElementById('memberSearch')?.addEventListener('input', function () {
  const q = this.value.trim().toLowerCase();
  document.querySelectorAll('.member-directory-card').forEach(card => {
    card.style.display = card.dataset.search.includes(q) ? '' : 'none';
  });
});

// Edit button — read JSON safely from data-item (avoids breaking on quotes in names/addresses)
document.addEventListener('click', function (e) {
  const btn = e.target.closest('.js-edit-member');
  if (!btn) return;
  const member = JSON.parse(btn.dataset.item);
  openMemberModal(member);
});

function openMemberModal(member) {
  const form = document.querySelector('#memberModal form');
  if (!form) return;
  form.reset();
  const label  = document.getElementById('memberModalLabel');
  const pwHint = document.getElementById('f_password_hint');

  if (member && member.id) {
    document.getElementById('f_id').value = member.id;
    document.getElementById('f_name').value = member.name || '';
    document.getElementById('f_father_name').value = member.father_name || '';
    document.getElementById('f_cnic').value = member.cnic || '';
    document.getElementById('f_mobile').value = member.mobile || '';
    document.getElementById('f_email').value = member.email || '';
    document.getElementById('f_address').value = member.address || '';
    document.getElementById('f_district').value = member.district || '';
    document.getElementById('f_tehsil').value = member.tehsil || '';
    document.getElementById('f_village').value = member.village || '';
    document.getElementById('f_education').value = member.education || '';
    document.getElementById('f_occupation').value = member.occupation || '';
    document.getElementById('f_blood_group').value = member.blood_group || '';
    document.getElementById('f_joined_at').value = member.joined_at || '';
    document.getElementById('f_status').value = member.status === 'pending' ? 'active' : member.status;
    if (member.position) document.getElementById('f_position').value = member.position;
    label.textContent    = MCYF_MEMBER_LABELS.editTitle;
    pwHint.textContent    = MCYF_MEMBER_LABELS.pwHintEdit;
  } else {
    document.getElementById('f_id').value = '';
    label.textContent    = MCYF_MEMBER_LABELS.addTitle;
    pwHint.textContent    = MCYF_MEMBER_LABELS.pwHintNew;
  }
}
</script>
JS;
