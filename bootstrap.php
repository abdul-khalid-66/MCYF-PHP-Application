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
 * sub-folder like /public/auth or /public/admin.
 *
 * Two supported deployment styles:
 *  1. DocumentRoot is a parent folder and the URL includes ".../public/..."
 *     → find that "/public" segment and cut there.
 *  2. DocumentRoot points directly AT the /public folder (recommended,
 *     cleaner URLs) → there's no "/public" segment in SCRIPT_NAME at all,
 *     so the base is simply the site root ('').
 * dirname(SCRIPT_NAME) was used as a fallback previously, but that breaks
 * on every nested page (e.g. /auth/login.php) in deployment style 2 —
 * it returned ".../auth" instead of the true root.
 */
$__scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$__publicPos  = strpos($__scriptName, '/public');
$__basePath   = $__publicPos !== false
    ? substr($__scriptName, 0, $__publicPos + strlen('/public'))
    : ''; // DocumentRoot already IS /public — base is the site root

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
require_once ROOT_PATH . '/app/Models/Announcement.php';
require_once ROOT_PATH . '/app/Models/NotificationItem.php';
require_once ROOT_PATH . '/app/Models/EventItem.php';
require_once ROOT_PATH . '/app/Models/Committee.php';
require_once ROOT_PATH . '/app/Models/Gallery.php';
require_once ROOT_PATH . '/app/Models/EmergencyService.php';
require_once ROOT_PATH . '/app/Models/ContactMessage.php';

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
function formatDate(?string $date, string $format = 'd M Y'): string
{
    if (!$date) return '—';
    try {
        return (new DateTime($date))->format($format);
    } catch (Exception) {
        return $date;
    }
}

/**
 * Safe lowercase that works even if the mbstring extension is not enabled.
 * Without mbstring, non-ASCII (e.g. Urdu) text is returned unchanged rather
 * than crashing — comparisons still work since we lowercase both sides.
 */
function safe_strtolower(string $str): string
{
    if (function_exists('mb_strtolower')) {
        return mb_strtolower($str, 'UTF-8');
    }
    return strtolower($str);
}

/**
 * Safe string truncation that works even if the mbstring extension is not
 * enabled. Falls back to byte-based substr(), which may very rarely cut a
 * multibyte character at the boundary (cosmetic only) instead of crashing
 * the whole page like the raw mb_strimwidth() call did.
 */
function safe_strimwidth(string $str, int $start, int $width, string $trimMarker = '…'): string
{
    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($str, $start, $width, $trimMarker, 'UTF-8');
    }
    if (strlen($str) <= $width) return $str;
    return substr($str, $start, $width) . $trimMarker;
}

/**
 * Extra-JS registry.
 *
 * Views are require()'d inside a closure in each public/*.php controller
 * (the $content closure), so a plain `$extraJs = '...'` assignment inside
 * a view file is local to that closure and never reaches views/layouts/main.php,
 * which echoes it in the outer scope. Using a global registry instead sidesteps
 * PHP's closure scoping entirely — call addExtraJs() from any view file and
 * the layout will always find it via getExtraJs().
 */
function addExtraJs(string $js): void
{
    $GLOBALS['__extra_js'][] = $js;
}

function getExtraJs(): string
{
    return implode("\n", $GLOBALS['__extra_js'] ?? []);
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

/**
 * Handle an uploaded video file (from $_FILES[$fieldName]).
 * Same pattern as handleImageUpload but validates against video mime types
 * and the larger MAX_VIDEO_MB limit.
 */
function handleVideoUpload(string $fieldName, string $subfolder): ?string
{
    if (empty($_FILES[$fieldName]['name']) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES[$fieldName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('فائل اپ لوڈ کرنے میں خرابی پیش آئی۔');
    }
    if ($file['size'] > MAX_VIDEO_MB * 1024 * 1024) {
        throw new RuntimeException('ویڈیو کا سائز ' . MAX_VIDEO_MB . ' MB سے کم ہونا چاہیے۔');
    }
    if (!in_array($file['type'], ALLOWED_VIDEO_TYPES, true)) {
        throw new RuntimeException('صرف MP4، WEBM یا OGG ویڈیو فائلیں قابل قبول ہیں۔');
    }

    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) ?: 'mp4';
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

/** Extracts an 11-character YouTube video ID from a URL or raw ID string. Returns null if not found. */
function extractYoutubeId(string $input): ?string
{
    $input = trim($input);
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $input, $m)) {
        return $m[1];
    }
    if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input)) {
        return $input;
    }
    return null;
}
