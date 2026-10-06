<?php
/**
 * =============================================================================
 *  app/custom/bookings.php — THE BOOKING TRACKER (clients + appointments)
 * =============================================================================
 *
 *  This is the example product built on VanillaSaaS Core: a booking tracker
 *  for a sole trader (a mobile hairdresser, a tutor, a personal trainer).
 *
 *  It is deliberately ordinary. Two tables, four pages, and every function
 *  below follows the one rule that matters in a multi-user app:
 *
 *      EVERY QUERY INCLUDES  user_id = ?
 *
 *  A client or appointment id comes from the URL (?id=12), and anyone can
 *  type a different number. Scoping by user_id means "12" only ever matches
 *  a row the signed-in user owns; somebody else's row simply isn't found.
 *
 *  Loaded by app/custom.php. Nothing in app/lib/ was edited to build this.
 * =============================================================================
 */

declare(strict_types=1);

/** Statuses an appointment can have: value stored => [label, badge class]. */
const APPOINTMENT_STATUSES = [
    'scheduled' => ['Scheduled', 'badge-info'],
    'completed' => ['Completed', 'badge-success'],
    'cancelled' => ['Cancelled', 'badge-error'],
];

/** Lengths offered in the "Duration" dropdown, in minutes. */
const APPOINTMENT_DURATIONS = [15, 30, 45, 60, 90, 120, 180];

// -----------------------------------------------------------------------------
// Clients
// -----------------------------------------------------------------------------

/** Every client of this user, A–Z, with how many appointments each has. */
function client_all(int $userId): array
{
    return db_all(
        'SELECT c.*, COUNT(a.id) AS appointment_count
           FROM clients c
           LEFT JOIN appointments a ON a.client_id = c.id
          WHERE c.user_id = ?
          GROUP BY c.id
          ORDER BY c.name',
        [$userId]
    );
}

/** One client, only if it belongs to this user. Otherwise null. */
function client_find(int $id, int $userId): ?array
{
    return db_one('SELECT * FROM clients WHERE id = ? AND user_id = ?', [$id, $userId]);
}

/**
 * Read and check the client form.
 * Returns [$errors, $clean]: field => message, and the tidied values.
 */
function client_validate(): array
{
    $clean = [
        'name'  => input('name'),
        'phone' => input('phone'),
        'email' => strtolower(input('email')),
        'notes' => input('notes'),
    ];

    $errors = array_filter([
        'name'  => validate_name($clean['name']),
        'phone' => str_length($clean['phone']) > 30 ? 'Phone number must be 30 characters or fewer.' : null,
        // Email is optional here, so only check it when something was typed.
        'email' => $clean['email'] !== '' ? validate_email($clean['email']) : null,
        'notes' => str_length($clean['notes']) > 1000 ? 'Notes must be 1,000 characters or fewer.' : null,
    ]);

    return [$errors, $clean];
}

function client_create(int $userId, array $c): void
{
    $now = now_utc();
    db_run(
        'INSERT INTO clients (user_id, name, phone, email, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
        [$userId, $c['name'], $c['phone'], $c['email'], $c['notes'], $now, $now]
    );
}

function client_update(int $id, int $userId, array $c): void
{
    db_run(
        'UPDATE clients SET name = ?, phone = ?, email = ?, notes = ?, updated_at = ? WHERE id = ? AND user_id = ?',
        [$c['name'], $c['phone'], $c['email'], $c['notes'], now_utc(), $id, $userId]
    );
}

/** Deletes the client and, through ON DELETE CASCADE, their appointments. */
function client_delete(int $id, int $userId): void
{
    db_run('DELETE FROM clients WHERE id = ? AND user_id = ?', [$id, $userId]);
}

// -----------------------------------------------------------------------------
// Appointments
// -----------------------------------------------------------------------------

/** One appointment with its client's name, only if this user owns it. */
function appointment_find(int $id, int $userId): ?array
{
    return db_one(
        'SELECT a.*, c.name AS client_name
           FROM appointments a
           JOIN clients c ON c.id = a.client_id
          WHERE a.id = ? AND a.user_id = ?',
        [$id, $userId]
    );
}

/** Scheduled appointments from now on, soonest first. */
function appointment_upcoming(int $userId, int $limit = 100): array
{
    // LIMIT can't be a ? placeholder on every database, so we cast to int
    // ourselves. (int) guarantees nothing but a number reaches the SQL.
    return db_all(
        'SELECT a.*, c.name AS client_name
           FROM appointments a
           JOIN clients c ON c.id = a.client_id
          WHERE a.user_id = ? AND a.status = ? AND a.starts_at >= ?
          ORDER BY a.starts_at
          LIMIT ' . (int) $limit,
        [$userId, 'scheduled', now_utc()]
    );
}

/** Everything that is finished, cancelled or in the past, newest first. */
function appointment_history(int $userId, int $limit = 50): array
{
    return db_all(
        'SELECT a.*, c.name AS client_name
           FROM appointments a
           JOIN clients c ON c.id = a.client_id
          WHERE a.user_id = ? AND (a.status <> ? OR a.starts_at < ?)
          ORDER BY a.starts_at DESC
          LIMIT ' . (int) $limit,
        [$userId, 'scheduled', now_utc()]
    );
}

/**
 * Read and check the appointment form.
 * Returns [$errors, $clean]. $clean holds values ready for the database:
 * the start time converted to UTC and the price converted to pence.
 */
function appointment_validate(int $userId, bool $withStatus = false): array
{
    $errors = [];

    // The client must exist AND belong to this user. Without the ownership
    // check, a forged form could attach an appointment to a stranger's client.
    $clientId = (int) input('client_id');
    if ($clientId === 0 || client_find($clientId, $userId) === null) {
        $errors['client_id'] = 'Choose a client.';
    }

    $service = input('service');
    if ($service === '') {
        $errors['service'] = 'Enter the service, for example "Cut and blow dry".';
    } elseif (str_length($service) > 120) {
        $errors['service'] = 'Service must be 120 characters or fewer.';
    }

    $startsAt = local_input_to_utc(input('starts_at'));
    if ($startsAt === null) {
        $errors['starts_at'] = 'Enter a valid date and time.';
    }

    $duration = (int) input('duration_minutes');
    if (!in_array($duration, APPOINTMENT_DURATIONS, true)) {
        $errors['duration_minutes'] = 'Choose a duration from the list.';
    }

    $pricePence = pounds_to_pence(input('price'));
    if ($pricePence === null) {
        $errors['price'] = 'Enter a price like 45 or 45.50, up to 10,000.';
    }

    $notes = input('notes');
    if (str_length($notes) > 1000) {
        $errors['notes'] = 'Notes must be 1,000 characters or fewer.';
    }

    // Status comes from a dropdown, but a dropdown is only a suggestion: the
    // browser will send whatever it's told to. Accept only values we know.
    $status = 'scheduled';
    if ($withStatus) {
        $status = input('status');
        if (!isset(APPOINTMENT_STATUSES[$status])) {
            $errors['status'] = 'Choose a status from the list.';
        }
    }

    return [$errors, [
        'client_id'        => $clientId,
        'service'          => $service,
        'starts_at'        => $startsAt,
        'duration_minutes' => $duration,
        'price_pence'      => $pricePence,
        'status'           => $status,
        'notes'            => $notes,
    ]];
}

function appointment_create(int $userId, array $a): void
{
    $now = now_utc();
    db_run(
        'INSERT INTO appointments
            (user_id, client_id, service, starts_at, duration_minutes, price_pence, status, notes, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [$userId, $a['client_id'], $a['service'], $a['starts_at'], $a['duration_minutes'],
         $a['price_pence'], $a['status'], $a['notes'], $now, $now]
    );
}

function appointment_update(int $id, int $userId, array $a): void
{
    db_run(
        'UPDATE appointments
            SET client_id = ?, service = ?, starts_at = ?, duration_minutes = ?, price_pence = ?,
                status = ?, notes = ?, updated_at = ?
          WHERE id = ? AND user_id = ?',
        [$a['client_id'], $a['service'], $a['starts_at'], $a['duration_minutes'], $a['price_pence'],
         $a['status'], $a['notes'], now_utc(), $id, $userId]
    );
}

function appointment_delete(int $id, int $userId): void
{
    db_run('DELETE FROM appointments WHERE id = ? AND user_id = ?', [$id, $userId]);
}

/** The three numbers on the dashboard. */
function booking_stats(int $userId): array
{
    $now       = now_utc();
    $weekAhead = gmdate('Y-m-d H:i:s', time() + 7 * 86400);

    $monthAgo  = gmdate('Y-m-d H:i:s', time() - 30 * 86400);

    $upcoming = db_one(
        'SELECT COUNT(*) AS n FROM appointments WHERE user_id = ? AND status = ? AND starts_at BETWEEN ? AND ?',
        [$userId, 'scheduled', $now, $weekAhead]
    );
    $clients = db_one('SELECT COUNT(*) AS n FROM clients WHERE user_id = ?', [$userId]);
    $earned  = db_one(
        'SELECT COALESCE(SUM(price_pence), 0) AS pence FROM appointments WHERE user_id = ? AND status = ? AND starts_at >= ?',
        [$userId, 'completed', $monthAgo]
    );

    return [
        'upcoming' => (int) $upcoming['n'],
        'clients'  => (int) $clients['n'],
        'earned'   => (int) $earned['pence'],
    ];
}

// -----------------------------------------------------------------------------
// Small converters: form text <-> database values
// -----------------------------------------------------------------------------

/**
 * A <input type="datetime-local"> sends "2026-10-14T09:30" in the user's own
 * wall-clock time. Convert it to a UTC 'Y-m-d H:i:s' string for storage.
 * Returns null if the text isn't a real date and time.
 */
function local_input_to_utc(string $value): ?string
{
    $zone = new DateTimeZone(date_default_timezone_get());
    $date = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $value, $zone);

    // createFromFormat happily "fixes" 31 February into 3 March. Formatting it
    // back and comparing catches anything that isn't exactly what was typed.
    if ($date === false || $date->format('Y-m-d\TH:i') !== $value) {
        return null;
    }
    return $date->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
}

/** The reverse: a stored UTC time as the value for a datetime-local input. */
function utc_to_local_input(?string $utc): string
{
    return $utc ? format_date($utc, 'Y-m-d\TH:i') : '';
}

/**
 * "45", "45.5" or "45.50" → 4500, 4550, 4550. Null if it isn't a sensible
 * price. We work on the text, not on floats, so no rounding can creep in.
 */
function pounds_to_pence(string $value): ?int
{
    $value = ltrim($value, '£ ');
    if ($value === '') {
        return 0;
    }
    if (!preg_match('/^(\d{1,5})(?:\.(\d{1,2}))?$/', $value, $m)) {
        return null;
    }
    $pence = (int) $m[1] * 100 + (int) str_pad($m[2] ?? '0', 2, '0');
    return $pence <= 1_000_000 ? $pence : null;
}

/** 4550 → "£45.50". */
function money(int $pence): string
{
    return '£' . number_format($pence / 100, 2);
}

/** 4550 → "45.50", for putting back into the price field. */
function pence_to_input(int $pence): string
{
    return number_format($pence / 100, 2, '.', '');
}

/** 90 → "1h 30m", 45 → "45m". */
function duration_label(int $minutes): string
{
    $h = intdiv($minutes, 60);
    $m = $minutes % 60;
    return trim(($h ? "{$h}h " : '') . ($m ? "{$m}m" : ''));
}

/** The coloured status label for a table cell. Returns safe HTML. */
function status_badge(string $status): string
{
    [$label, $class] = APPOINTMENT_STATUSES[$status] ?? [ucfirst($status), ''];
    return '<span class="badge ' . e($class) . '">' . e($label) . '</span>';
}

/** Read ?id= as a positive whole number, or stop with a 404. */
function id_from_query(): int
{
    $id = query('id');
    if (!ctype_digit($id) || (int) $id < 1) {
        abort(404);
    }
    return (int) $id;
}
