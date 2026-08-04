<?php
/**
 * Pak-Everests — core configuration
 *
 * Live credentials belong in app/config.local.php (git-ignored).
 * Copy app/config.sample.php → app/config.local.php on the server and edit it.
 */

declare(strict_types=1);

if (!defined('PE_ROOT')) {
    define('PE_ROOT', dirname(__DIR__));
}

/* ---------------------------------------------------------------------------
 |  Defaults — overridden by config.local.php
 * ------------------------------------------------------------------------ */
$config = [
    // --- Database (Hostinger MySQL) ---------------------------------------
    'db_host'    => 'localhost',
    'db_name'    => 'u237845628_Pakeverests',
    'db_user'    => 'u237845628_Pakeverests',
    'db_pass'    => '',
    'db_charset' => 'utf8mb4',

    // --- Site -------------------------------------------------------------
    'site_url'   => 'https://pakeverests.site',
    'site_name'  => 'Pak-Everests Bottled Drinking Water',
    'timezone'   => 'Asia/Karachi',
    'locale'     => 'en_PK',
    'currency'   => 'PKR',

    // --- Environment ------------------------------------------------------
    'debug'      => false,

    // --- Paths ------------------------------------------------------------
    'uploads_dir' => PE_ROOT . '/uploads',
    'uploads_url' => '/uploads',

    // --- Security ---------------------------------------------------------
    'session_name' => 'PEV_SESSID',
];

$localConfig = PE_ROOT . '/app/config.local.php';
if (is_file($localConfig)) {
    /** @var array $override */
    $override = require $localConfig;
    if (is_array($override)) {
        $config = array_merge($config, $override);
    }
}

/* Allow environment variables to win (useful for staging / CI) */
foreach (['db_host', 'db_name', 'db_user', 'db_pass', 'site_url'] as $envKey) {
    $envVal = getenv('PE_' . strtoupper($envKey));
    if ($envVal !== false && $envVal !== '') {
        $config[$envKey] = $envVal;
    }
}

define('PE_CONFIG', $config);
define('PE_DEBUG', (bool) $config['debug']);
define('SITE_URL', rtrim($config['site_url'], '/'));
define('UPLOADS_DIR', $config['uploads_dir']);
define('UPLOADS_URL', $config['uploads_url']);

date_default_timezone_set($config['timezone']);

if (PE_DEBUG) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}

/* ---------------------------------------------------------------------------
 |  Session
 * ------------------------------------------------------------------------ */
if (session_status() === PHP_SESSION_NONE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    session_name($config['session_name']);
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once PE_ROOT . '/app/db.php';
require_once PE_ROOT . '/app/helpers.php';
require_once PE_ROOT . '/app/settings.php';
require_once PE_ROOT . '/app/seo.php';
require_once PE_ROOT . '/app/mailer.php';
require_once PE_ROOT . '/app/analytics.php';
