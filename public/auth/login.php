<?php
require_once __DIR__ . '/../../bootstrap.php';
redirectIfAuthenticated();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $email    = post('email');
    $password = post('password');

    $pdo  = DB::connection();
    $stmt = $pdo->prepare("SELECT * FROM members WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        authLogin($user);
        $dest = $user['role'] === 'pending'
            ? BASE_URL . '/pending'
            : BASE_URL . '/dashboard';
        redirect($dest);
    } else {
        $error = t_raw('auth_error_invalid');
    }
}

$pageTitle = t_raw('auth_login_heading');
$content   = function () use ($error) {
    require ROOT_PATH . '/views/auth/login.view.php';
};
require ROOT_PATH . '/views/layouts/auth.php';
