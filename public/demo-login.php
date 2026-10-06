<?php
/**
 * =============================================================================
 *  public/demo-login.php — "TRY THE DEMO" (POST ONLY)
 * =============================================================================
 *  Creates a private throwaway account with sample data and signs the visitor
 *  straight in. See app/custom/demo.php for why each visitor gets their own.
 *
 *  POST + CSRF token, like every state-changing action: a plain link could be
 *  triggered by other sites or by search-engine crawlers, each hit creating
 *  an account.
 * =============================================================================
 */

declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

allow_methods('POST');
csrf_verify();
require_guest();

if (!demo_enabled()) {
    abort(404);
}

// Each click writes about 16 rows. Ten per hour per IP is plenty for a
// curious visitor and stops a script filling the database.
$key = throttle_key('demo-login', client_ip());
if (throttle_too_many($key, 10)) {
    flash('error', 'Too many demo sessions from your network. Please try again in '
        . throttle_wait_text(throttle_seconds_left($key)) . '.');
    redirect('login.php');
}
throttle_hit($key, 3600);

auth_login(demo_create_account());
flash('success', 'You are in a private demo account with sample data. Change anything you like.');
redirect('dashboard.php');
