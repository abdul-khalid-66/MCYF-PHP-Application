<?php
/**
 * Session & Auth Helper
 *
 * Centralises all session start/read/write and permission checks.
 * No view logic here — pure PHP helpers only.
 */

require_once __DIR__ . '/../../config/app.php';

// Start session once, safely
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/',
        'secure'   => false,   // set true when running HTTPS
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// ── Session read/write ────────────────────────────────────────────────────────

function sessionSet(string $key, $value): void
{
    $_SESSION[$key] = $value;
}

function sessionGet(string $key, $default = null)
{
    return $_SESSION[$key] ?? $default;
}

function sessionForget(string $key): void
{
    unset($_SESSION[$key]);
}

function sessionFlash(string $key, $value): void
{
    $_SESSION['__flash'][$key] = $value;
}

function sessionGetFlash(string $key, $default = null)
{
    $val = $_SESSION['__flash'][$key] ?? $default;
    unset($_SESSION['__flash'][$key]);
    return $val;
}

// ── Auth helpers ──────────────────────────────────────────────────────────────

function authLogin(array $user): void
{
    session_regenerate_id(true);
    sessionSet('auth_user_id', $user['id']);
    sessionSet('auth_user_role', $user['role']);
}

function authLogout(): void
{
    session_unset();
    session_destroy();
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['auth_user_id']);
}

function authUserId(): ?int
{
    return isset($_SESSION['auth_user_id']) ? (int)$_SESSION['auth_user_id'] : null;
}

function authUserRole(): string
{
    return $_SESSION['auth_user_role'] ?? 'visitor';
}

// ── Permission map (default / fallback values — seeded into the
//    `role_permissions` DB table so admins can edit them from Admin →
//    Users & Roles. If the DB table is empty or unreachable, this array
//    is used as-is.) Super admin always bypasses everything.

const ROLE_PERMISSIONS = [
    'visitor'       => [],
    'pending'       => ['profile'],
    'member'        => [
        'dashboard', 'members', 'announcements', 'gallery', 'events',
        'committees', 'notifications', 'emergency', 'about', 'contact', 'profile',
    ],
    'committee_head'=> [
        'dashboard', 'members', 'announcements', 'gallery', 'events',
        'committees', 'notifications', 'emergency', 'about', 'contact', 'profile',
        'committees_manage', 'events_manage',
    ],
    'admin'         => [
        'dashboard', 'members', 'announcements', 'gallery', 'events',
        'committees', 'notifications', 'emergency', 'about', 'contact', 'profile',
        'admin', 'user_management', 'members_manage', 'announcements_manage',
        'events_manage', 'committees_manage', 'emergency_manage',
        'gallery_manage', 'roles_manage', 'messages_manage',
    ],
    'super_admin'   => ['*'], // wildcard — see hasPermission()
];

/** All permission keys that exist in the app (used to render the admin matrix). */
const ALL_PERMISSION_KEYS = [
    'dashboard', 'members', 'announcements', 'gallery', 'events', 'committees',
    'notifications', 'emergency', 'about', 'contact', 'profile', 'admin',
    'user_management', 'members_manage', 'announcements_manage', 'events_manage',
    'committees_manage', 'emergency_manage', 'gallery_manage', 'roles_manage',
    'messages_manage',
];

/** Editable roles shown in the admin permissions matrix (super_admin is always full-access). */
const EDITABLE_ROLES = ['pending', 'member', 'committee_head', 'admin'];

function _loadRolePermissions(): array
{
    static $cache = null;
    if ($cache === null) {
        try {
            $rows = DB::connection()->query("SELECT role, permission FROM role_permissions")->fetchAll();
            if (empty($rows)) {
                $cache = ROLE_PERMISSIONS; // table not seeded yet — use defaults
            } else {
                $cache = [];
                foreach ($rows as $r) {
                    $cache[$r['role']][] = $r['permission'];
                }
            }
        } catch (Throwable) {
            $cache = ROLE_PERMISSIONS;
        }
    }
    return $cache;
}

function hasPermission(string $key): bool
{
    $role = authUserRole();
    if ($role === 'super_admin') return true;
    $perms = _loadRolePermissions()[$role] ?? [];
    return in_array($key, $perms, true);
}

/**
 * Require login + optional permission key.
 * Redirects and exits if the check fails.
 * Returns the user id on success.
 */
function requireAuth(?string $permissionKey = null): int
{
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }

    $role = authUserRole();

    if ($role === 'pending' && $permissionKey !== 'profile') {
        header('Location: ' . BASE_URL . '/pending');
        exit;
    }

    if ($permissionKey && !hasPermission($permissionKey)) {
        header('Location: ' . BASE_URL . '/errors/access-denied');
        exit;
    }

    return authUserId();
}

/**
 * Redirect logged-in users away from guest-only pages (login, signup).
 */
function redirectIfAuthenticated(): void
{
    if (isLoggedIn()) {
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }
}

// ── CSRF ──────────────────────────────────────────────────────────────────────

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!hash_equals(csrfToken(), $token)) {
        http_response_code(403);
        die('CSRF token mismatch.');
    }
}
