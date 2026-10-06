<?php
/**
 * =============================================================================
 *  app/custom/demo.php — PUBLIC DEMO MODE
 * =============================================================================
 *
 *  Switched on by 'demo' => ['enabled' => true] in app/config.php.
 *
 *  THE PROBLEM WITH A PUBLIC DEMO
 *  ------------------------------
 *  A demo anyone can use is a demo anyone can abuse. Three risks, and what
 *  this file does about each:
 *
 *  1. Vandalism. If every visitor shared one demo account, the first person
 *     to type something offensive would greet everyone after them.
 *     → Each "Try the demo" click creates a PRIVATE throwaway account with
 *       its own sample data. Nobody sees anyone else's.
 *
 *  2. Spam. A password-reset form that emails any address typed into it can
 *     be used to pester strangers, and gets your domain blacklisted.
 *     → Mail stays on the 'log' driver, so nothing is ever sent.
 *
 *  3. Clutter and personal data. Throwaway accounts pile up, and people type
 *     real names into demos.
 *     → bin/demo-reset.php wipes everything; schedule it nightly.
 *
 *  Loaded by app/custom.php. Nothing in app/lib/ was edited to build this.
 * =============================================================================
 */

declare(strict_types=1);

function demo_enabled(): bool
{
    return (bool) config('demo.enabled', false);
}

/** True when the signed-in account was made by the "Try the demo" button. */
function demo_is_throwaway(?array $user): bool
{
    return $user !== null && str_ends_with($user['email'], '@demo.invalid');
}

/**
 * Create a throwaway account filled with sample data, and return it.
 *
 * ".invalid" is a domain name reserved for exactly this: it can never exist,
 * so these addresses can't collide with a real person's and can't receive
 * mail. The password is long, random and never shown; the account can only
 * be entered through auth_login() below, and only by the visitor who made it.
 */
function demo_create_account(): array
{
    $email  = 'visitor-' . bin2hex(random_bytes(6)) . '@demo.invalid';
    $userId = auth_register('Demo Visitor', $email, bin2hex(random_bytes(24)));

    demo_seed((int) $userId);

    return user_find_by_id((int) $userId);
}

/**
 * Sample clients and appointments, dated relative to today so the demo always
 * looks current: some in the past (completed, one cancelled), some coming up.
 */
function demo_seed(int $userId): void
{
    $clients = [
        ['Amara Okafor',   '07700 900101', 'amara@example.com',   'Prefers mornings.'],
        ['Daniel Whitlock', '07700 900102', 'daniel@example.com', ''],
        ['Priya Nair',     '07700 900103', '',                    'Allergic to latex gloves.'],
        ['Tom Brennan',    '07700 900104', 'tom@example.com',     ''],
        ['Sofia Marchetti', '07700 900105', 'sofia@example.com',  'Parking round the back.'],
    ];

    // [client index, service, days from today, hour, minutes long, price in pence, status]
    $appointments = [
        [0, 'Cut and blow dry',        -12, 10, 60,  4500, 'completed'],
        [1, 'Beard trim',               -9, 14, 30,  1800, 'completed'],
        [2, 'Full colour',              -6,  9, 120, 9500, 'completed'],
        [3, 'Cut and blow dry',         -4, 16, 60,  4500, 'cancelled'],
        [4, 'Wash, cut and style',      -2, 11, 90,  6000, 'completed'],
        [0, 'Root touch-up',             1, 10, 90,  6500, 'scheduled'],
        [2, 'Cut and blow dry',          2, 13, 60,  4500, 'scheduled'],
        [1, 'Cut and beard trim',        4, 15, 45,  3200, 'scheduled'],
        [4, 'Highlights',                6,  9, 120, 11000, 'scheduled'],
        [3, 'Cut and blow dry',         11, 12, 60,  4500, 'scheduled'],
    ];

    // One transaction: either the whole sample set is created or none of it.
    db_transaction(function () use ($userId, $clients, $appointments): void {
        $ids = [];
        foreach ($clients as [$name, $phone, $email, $notes]) {
            client_create($userId, compact('name', 'phone', 'email', 'notes'));
            $ids[] = (int) db()->lastInsertId();
        }

        $zone = new DateTimeZone(date_default_timezone_get());
        foreach ($appointments as [$client, $service, $days, $hour, $minutes, $pence, $status]) {
            $start = (new DateTimeImmutable('today', $zone))
                ->modify(($days >= 0 ? '+' : '') . $days . ' days')
                ->setTime($hour, 0)
                ->setTimezone(new DateTimeZone('UTC'));

            appointment_create($userId, [
                'client_id'        => $ids[$client],
                'service'          => $service,
                'starts_at'        => $start->format('Y-m-d H:i:s'),
                'duration_minutes' => $minutes,
                'price_pence'      => $pence,
                'status'           => $status,
                'notes'            => '',
            ]);
        }
    });
}

/**
 * Wipe the demo back to empty. Called by bin/demo-reset.php.
 * Returns how many accounts were removed.
 */
function demo_reset(): int
{
    // Deleting users removes their clients, appointments and reset tokens
    // through ON DELETE CASCADE. Rate-limit counters are cleared separately.
    $removed = db_run('DELETE FROM users');
    db_run('DELETE FROM throttle');

    // Sign everyone out by removing the session files, and empty the mail log
    // (it holds the addresses people typed into the reset form).
    foreach (glob(STORAGE_PATH . '/sessions/sess_*') ?: [] as $file) {
        @unlink($file);
    }
    @file_put_contents(STORAGE_PATH . '/logs/mail.log', '');

    return $removed;
}
