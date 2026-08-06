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
define('BASE_URL',  rtrim(
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
    . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\')   // auto-detects sub-folder
    , '/'
));

// ── Autoload helpers ──────────────────────────────────────────────────────────

require_once ROOT_PATH . '/app/Helpers/DB.php';
require_once ROOT_PATH . '/app/Helpers/Auth.php';   // starts session
require_once ROOT_PATH . '/app/Helpers/Lang.php';   // loads language + settings

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
