<?php
require_once __DIR__ . '/../../bootstrap.php';
redirectIfAuthenticated();

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $name     = post('name');
    $mobile   = post('mobile');
    $email    = post('email');
    $password = post('password');
    $confirm  = post('confirm_password');

    if ($password !== $confirm) {
        $error = 'پاس ورڈ اور تصدیق ایک جیسی نہیں ہیں۔';
    } elseif (strlen($password) < 6) {
        $error = 'پاس ورڈ کم از کم 6 حروف کا ہونا چاہیے۔';
    } else {
        $pdo  = DB::connection();
        $chk  = $pdo->prepare("SELECT id FROM members WHERE email = ?");
        $chk->execute([$email]);
        if ($chk->fetch()) {
            $error = 'یہ ای میل پہلے سے موجود ہے۔';
        } else {
            $ins = $pdo->prepare(
                "INSERT INTO members (name, mobile, email, password, role, status)
                 VALUES (:name, :mobile, :email, :password, 'pending', 'pending')"
            );
            $ins->execute([
                ':name'     => $name,
                ':mobile'   => $mobile,
                ':email'    => $email,
                ':password' => password_hash($password, PASSWORD_BCRYPT),
            ]);
            $success = t_raw('auth_success_signup');
        }
    }
}

$pageTitle = t_raw('auth_signup_heading');
$content   = function () use ($success, $error) {
    require ROOT_PATH . '/views/auth/signup.view.php';
};
require ROOT_PATH . '/views/layouts/auth.php';
