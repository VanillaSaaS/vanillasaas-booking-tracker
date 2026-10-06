<?php
/**
 * =============================================================================
 *  app/config.local.example.php — SETTINGS FOR THE LIVE DEMO SERVER
 * =============================================================================
 *  Copy to app/config.local.php on the server and set your demo's address.
 *  Never commit config.local.php.
 * =============================================================================
 */

return [
    'app' => [
        // 'production' hides error details from visitors.
        'env' => 'production',
        // Required in production. No trailing slash.
        'url' => 'https://demo.your-domain.com',
    ],

    // Demo mode is OFF in app/config.php. This turns it on for the demo
    // server only: the Try-the-demo button, the banner, and permission for
    // bin/demo-reset.php to wipe the database. Never on a site with real users.
    'demo' => [
        'enabled' => true,
    ],

    // Leave mail on 'log' for a public demo: nothing is ever sent, so the
    // reset form can't be used to email strangers. (This is the default in
    // app/config.php; it's repeated here so nobody "fixes" it by accident.)
    'mail' => [
        'driver' => 'log',
    ],
];
