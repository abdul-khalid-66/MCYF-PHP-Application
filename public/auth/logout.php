<?php
// public/auth/logout.php
require_once __DIR__ . '/../../bootstrap.php';
authLogout();
redirect(BASE_URL . '/auth/login');
