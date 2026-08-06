<?php
/**
 * Localization & Settings Helper
 *
 * Loads the active language file and the platform settings from the DB
 * (or falls back to config/app.php defaults if the table doesn't exist yet).
 *
 * Functions available everywhere after this file is included:
 *   t(string $key, array $replace = []) : string   — translate a key
 *   setting(string $key, $default = '')             — platform setting value
 *   themeVars() : string                            — <style> block with CSS vars
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/DB.php';

// ── 1. Load language ──────────────────────────────────────────────────────────

function _loadLang(): array
{
    // Active language comes from DB settings first, then config constant
    $lang = _getSetting('app_lang', APP_LANG);
    $file = __DIR__ . '/../../lang/' . $lang . '/lang.php';

    if (!file_exists($file)) {
        // Graceful fallback to Urdu
        $file = __DIR__ . '/../../lang/ur/lang.php';
    }
    return require $file;
}

$GLOBALS['__lang'] = _loadLang();

/**
 * Translate a key. Supports simple placeholder replacement:
 *   t('welcome_user', ['name' => 'Ahmad'])
 *   where lang key value is "خوش آمدید :name"
 */
function t(string $key, array $replace = []): string
{
    $value = $GLOBALS['__lang'][$key] ?? $key; // return key itself if missing
    foreach ($replace as $placeholder => $val) {
        $value = str_replace(':' . $placeholder, $val, $value);
    }
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/** Raw translation — no HTML escaping (for use inside HTML attributes set via JS etc.) */
function t_raw(string $key, array $replace = []): string
{
    $value = $GLOBALS['__lang'][$key] ?? $key;
    foreach ($replace as $placeholder => $val) {
        $value = str_replace(':' . $placeholder, $val, $value);
    }
    return $value;
}

// ── 2. Load platform settings ─────────────────────────────────────────────────

function _getSetting(string $key, $default = '')
{
    static $cache = null;
    if ($cache === null) {
        try {
            $pdo  = DB::connection();
            $rows = $pdo->query("SELECT `key`, `value` FROM settings")->fetchAll();
            $cache = array_column($rows, 'value', 'key');
        } catch (Throwable $e) {
            $cache = [];
        }
    }
    return $cache[$key] ?? $default;
}

/** Public helper used in views */
function setting(string $key, $default = ''): string
{
    return htmlspecialchars((string)_getSetting($key, $default), ENT_QUOTES, 'UTF-8');
}

function setting_raw(string $key, $default = ''): string
{
    return (string)_getSetting($key, $default);
}

// ── 3. Theme helpers ──────────────────────────────────────────────────────────

/**
 * Returns a <style> block injecting the active theme colours as CSS variables.
 * Call this in the <head> of every layout, after the static stylesheet.
 */
function themeVars(): string
{
    $primary   = setting_raw('theme_primary',   THEME_PRIMARY);
    $secondary = setting_raw('theme_secondary', THEME_SECONDARY);
    $accent    = setting_raw('theme_accent',    THEME_ACCENT);
    $extra     = setting_raw('theme_extra',     THEME_EXTRA);

    $extraLine = $extra ? "--forum-extra: {$extra};" : '';

    return <<<HTML
<style>
:root {
  --forum-green:      {$primary};
  --forum-green-dark: {$secondary};
  --forum-gold:       {$accent};
  {$extraLine}
}
</style>
HTML;
}

// ── 4. Platform name / meta helpers ──────────────────────────────────────────

function appName(): string
{
    $lang = $GLOBALS['__lang']['lang_code'] ?? 'ur';
    if ($lang === 'en' || $lang === 'roman_ur') {
        return setting('app_name_en', APP_NAME);
    }
    return setting('app_name_ur', APP_NAME_UR);
}

function appSubtitle(): string
{
    return setting('app_subtitle', APP_SUBTITLE);
}

function appIcon(): string
{
    return setting('app_icon', APP_ICON);
}

function appLogoPath(): string
{
    $logo = setting_raw('app_logo', '');
    if ($logo && file_exists(__DIR__ . '/../../public/' . $logo)) {
        return BASE_URL . '/' . ltrim($logo, '/');
    }
    return ''; // empty = use icon instead
}

// ── 5. Language meta helpers (used in <html> tag and Bootstrap CSS) ───────────

function langCode(): string  { return $GLOBALS['__lang']['lang_code'] ?? 'ur'; }
function langDir(): string   { return $GLOBALS['__lang']['dir'] ?? 'rtl'; }
function fontUrl(): string   { return $GLOBALS['__lang']['font_url'] ?? ''; }
function bootstrapCss(): string { return $GLOBALS['__lang']['bootstrap_css'] ?? 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css'; }
function fontDisplay(): string  { return $GLOBALS['__lang']['font_display'] ?? 'Noto Nastaliq Urdu'; }
function fontBody(): string     { return $GLOBALS['__lang']['font_body'] ?? 'Noto Naskh Urdu'; }
