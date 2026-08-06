<?php
/**
 * App Configuration
 *
 * These are the DEFAULTS that ship with the app.
 * A super-admin can override all of these via Admin → Platform Settings
 * and they will be stored in the `settings` database table.
 * This file is the fallback when the DB is not yet seeded.
 */

define('APP_NAME',       'Masood Community Youth Forum');
define('APP_NAME_UR',    '(abc) کمیونٹی یوتھ فورم');
define('APP_SUBTITLE',   'ایک غیر سیاسی، غیر منافع بخش تنظیم');
define('APP_ICON',       'bi-mosque');          // Bootstrap Icons class
define('APP_URL',        'http://localhost/mcyf-php/public');

// Default active language (must match a folder name inside /lang/)
// Supported: 'ur', 'en', 'roman_ur'
// Change this one value to switch the entire app's static text.
define('APP_LANG', 'ur');

// Theme colours  (CSS custom properties injected into every page)
// Primary  = deep masjid green
// Secondary = lighter green
// Accent   = gold (the "forum gold" that was originally golden)
// Optional extra theme colour (e.g. maroon, teal — empty string = unused)
define('THEME_PRIMARY',   '#145A32');
define('THEME_SECONDARY', '#0D3D22');
define('THEME_ACCENT',    '#C9A227');   // golden
define('THEME_EXTRA',     '');          // optional 3rd theme colour

// Session
define('SESSION_NAME',    'mcyf_session');
define('SESSION_LIFETIME', 86400);      // seconds (24 h)

// Pagination
define('PER_PAGE', 15);

// Upload limits
define('MAX_UPLOAD_MB', 5);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
