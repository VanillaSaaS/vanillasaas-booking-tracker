<?php
/**
 * =============================================================================
 *  bin/demo-reset.php — WIPE THE DEMO (run nightly)
 * =============================================================================
 *  Deletes every account and everything belonging to it, clears rate limits,
 *  signs everyone out and empties the mail log.
 *
 *  Schedule it in cPanel → Cron Jobs, e.g. at 03:00 every night:
 *
 *      0 3 * * * /usr/local/bin/php /home/YOURUSER/vanillasaas-demo/bin/demo-reset.php
 *
 *  It refuses to run unless demo mode is on, so it can never be pointed at a
 *  real site's database by accident.
 * =============================================================================
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../app/bootstrap.php';

if (!demo_enabled()) {
    fwrite(STDERR, "Refusing to run: 'demo.enabled' is not true in the config.\n");
    exit(1);
}

$removed = demo_reset();
echo gmdate('Y-m-d H:i:s') . " UTC — demo reset, {$removed} account(s) removed.\n";
