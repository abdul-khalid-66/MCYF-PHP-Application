<?php
/**
 * bootstrap.php — Global entry point
 *
 * Every public PHP page starts with:
 *   require_once __DIR__ . '/../bootstrap.php';
 *   (or the appropriate relative path)
 *
 * This file:
 *   1. Defines BASE_URL and ROOT_PATH constants
 *   2. Loads all helper files
 *   3. Starts session
 */

// ── Constants ─────────────────────────────────────────────────────────────────

define('ROOT_PATH', __DIR__);                    // /path/to/mcyf-php

/**
 * BASE_URL must always point to the /public folder itself — never to a
 * sub-folder like /public/auth or /public/admin. dirname(SCRIPT_NAME) broke
 * on every nested page (returned .../public/auth instead of .../public), so
 * instead we locate the "/public" segment in the current script path and
 * cut there. Works no matter how deep the page is nested.
 */
$__scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$__publicPos  = strpos($__scriptName, '/public');
$__basePath   = $__publicPos !== false
    ? substr($__scriptName, 0, $__publicPos + strlen('/public'))
    : rtrim(dirname($__scriptName), '/\\'); // fallback

define('BASE_URL', rtrim(
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
    . $__basePath
    , '/'
));
unset($__scriptName, $__publicPos, $__basePath);

// ── Autoload helpers ──────────────────────────────────────────────────────────

require_once ROOT_PATH . '/app/Helpers/DB.php';
require_once ROOT_PATH . '/app/Helpers/Auth.php';   // starts session
require_once ROOT_PATH . '/app/Helpers/Lang.php';   // loads language + settings
require_once ROOT_PATH . '/app/Helpers/Positions.php';
require_once ROOT_PATH . '/app/Models/Member.php';

// ── Utility functions ─────────────────────────────────────────────────────────

/** Sanitise and return a string from $_POST or default */
function post(string $key, string $default = ''): string
{
    return isset($_POST[$key]) ? trim((string)$_POST[$key]) : $default;
}

/** Sanitise and return a string from $_GET or default */
function get(string $key, string $default = ''): string
{
    return isset($_GET[$key]) ? trim((string)$_GET[$key]) : $default;
}

/** Redirect and stop execution */
function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

/** Simple HTML escape */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/** Format a date string for display */
function formatDate(string $date, string $format = 'd M Y'): string
{
    if (!$date) return '—';
    try {
        return (new DateTime($date))->format($format);
    } catch (Exception) {
        return $date;
    }
}

/** Return flash message HTML if set, empty string otherwise */
function flashHtml(string $key): string
{
    $msg  = sessionGetFlash($key);
    $type = sessionGetFlash($key . '_type') ?? 'success';
    if (!$msg) return '';
    $icon = $type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
    return <<<HTML
<div class="alert alert-{$type} d-flex align-items-center gap-2 mb-3" role="alert">
  <i class="bi {$icon}"></i>
  <span>{$msg}</span>
</div>
HTML;
}

/**
 * Handle an uploaded image file (from $_FILES[$fieldName]).
 * Saves it under public/assets/uploads/{subfolder}/ with a unique name.
 * Returns the relative path (e.g. "assets/uploads/avatars/xyz.jpg") on
 * success, null if no file was uploaded, or throws a RuntimeException
 * with a user-facing message on validation failure.
 */
function handleImageUpload(string $fieldName, string $subfolder): ?string
{
    if (empty($_FILES[$fieldName]['name']) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES[$fieldName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('فائل اپ لوڈ کرنے میں خرابی پیش آئی۔');
    }
    if ($file['size'] > MAX_UPLOAD_MB * 1024 * 1024) {
        throw new RuntimeException('فائل کا سائز ' . MAX_UPLOAD_MB . ' MB سے کم ہونا چاہیے۔');
    }
    if (!in_array($file['type'], ALLOWED_IMAGE_TYPES, true)) {
        throw new RuntimeException('صرف JPG، PNG، WEBP یا GIF فائلیں قابل قبول ہیں۔');
    }

    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) ?: 'jpg';
    $filename = bin2hex(random_bytes(12)) . '.' . $ext;
    $destDir  = ROOT_PATH . '/public/assets/uploads/' . trim($subfolder, '/');
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }
    $dest = $destDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        throw new RuntimeException('فائل محفوظ کرنے میں ناکامی۔');
    }
    return 'assets/uploads/' . trim($subfolder, '/') . '/' . $filename;
}

/** Resolve a member's photo to a full URL, falling back to a placeholder avatar. */
function memberPhotoUrl(?string $photo): string
{
    if ($photo && str_starts_with($photo, 'http')) {
        return $photo; // seeded demo data uses external URLs
    }
    if ($photo && file_exists(ROOT_PATH . '/public/' . $photo)) {
        return BASE_URL . '/' . ltrim($photo, '/');
    }
    return 'https://ui-avatars.com/api/?background=145A32&color=C9A227&name=' . urlencode('?');
}
