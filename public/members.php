<?php
require_once __DIR__ . '/../bootstrap.php';
$userId = requireAuth('members');
$canManage = hasPermission('members_manage');

$errors  = [];
$success = '';

// ── Handle POST actions (admin only) ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canManage) {
    verifyCsrf();
    $action = post('action');

    try {
        if ($action === 'save') {
            $id       = (int) post('id');
            $name     = post('name');
            $email    = post('email');
            $password = post('password');

            if ($name === '' || $email === '') {
                throw new RuntimeException('نام اور ای میل لازمی ہیں۔');
            }

            $data = [
                'name'        => $name,
                'father_name' => post('father_name'),
                'email'       => $email,
                'cnic'        => post('cnic'),
                'mobile'      => post('mobile'),
                'address'     => post('address'),
                'district'    => post('district'),
                'tehsil'      => post('tehsil'),
                'village'     => post('village'),
                'education'   => post('education'),
                'occupation'  => post('occupation'),
                'blood_group' => post('blood_group'),
                'position'    => post('position'),
                'status'      => post('status', 'active'),
                'joined_at'   => post('joined_at') ?: date('Y-m-d'),
            ];

            // Photo upload (optional)
            $photoPath = handleImageUpload('photo', 'avatars');
            if ($photoPath) {
                $data['photo'] = $photoPath;
            }

            if ($id > 0) {
                // Editing existing member
                $existing = Member::find($id);
                if (!$existing) throw new RuntimeException('ممبر نہیں ملا۔');

                // Email uniqueness check (excluding self)
                $dupe = Member::findByEmail($email);
                if ($dupe && (int)$dupe['id'] !== $id) {
                    throw new RuntimeException('یہ ای میل پہلے سے ایک اور ممبر کے پاس ہے۔');
                }

                Member::update($id, $data);
                if ($password !== '') {
                    Member::updatePassword($id, $password);
                }
                $success = t_raw('msg_saved');
            } else {
                // Creating new member
                $dupe = Member::findByEmail($email);
                if ($dupe) {
                    throw new RuntimeException('یہ ای میل پہلے سے موجود ہے۔');
                }
                $data['password'] = password_hash($password !== '' ? $password : 'Welcome@123', PASSWORD_BCRYPT);
                $data['role']     = 'member';
                if (empty($data['photo']) && $photoPath) {
                    $data['photo'] = $photoPath;
                }
                Member::create($data);
                $success = t_raw('msg_saved');
            }

        } elseif ($action === 'delete') {
            $id = (int) post('id');
            if ($id === $userId) {
                throw new RuntimeException('آپ اپنا اکاؤنٹ خود حذف نہیں کر سکتے۔');
            }
            Member::delete($id);
            $success = t_raw('msg_deleted');

        } elseif ($action === 'approve') {
            $id = (int) post('id');
            Member::approve($id);
            $success = t_raw('msg_approved');

        } elseif ($action === 'reject') {
            $id = (int) post('id');
            Member::delete($id);
            $success = t_raw('msg_rejected');
        }
    } catch (RuntimeException $ex) {
        $errors[] = $ex->getMessage();
    }

    if ($success && empty($errors)) {
        sessionFlash('success', $success);
        redirect(BASE_URL . '/members');
    }
}

// ── Data for view ──────────────────────────────────────────────────────────────
$search        = get('q');
$statusFilter  = get('status');
$directory     = Member::all(); // public directory — unfiltered by status, search applied client-side via JS OR server; we'll do server-side too
$adminSearch   = $search;
$adminList     = $canManage ? Member::all($adminSearch, $statusFilter) : [];
$positions     = positionOptions();

$pageTitle   = t_raw('members_heading');
$pageHero    = t('members_heading');
$activePage  = 'members';
$content     = function () use ($errors, $directory, $adminList, $canManage, $positions, $search, $statusFilter, $userId) {
    require ROOT_PATH . '/views/pages/members.view.php';
};
require ROOT_PATH . '/views/layouts/main.php';
