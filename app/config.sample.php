<?php
/**
 * Copy this file to app/config.local.php on the live server and fill in the
 * real values. app/config.local.php is git-ignored so credentials never leave
 * the server.
 *
 *      cp app/config.sample.php app/config.local.php
 */

return [
    /* ---- Hostinger MySQL ------------------------------------------------ */
    'db_host' => 'localhost',
    'db_name' => 'u237845628_Pakeverests',
    'db_user' => 'u237845628_Pakeverests',
    'db_pass' => 'PUT-YOUR-DATABASE-PASSWORD-HERE',

    /* ---- Site ----------------------------------------------------------- */
    'site_url' => 'https://pakeverests.site',

    /* ---- Set to true only while troubleshooting ------------------------- */
    'debug' => false,
];
