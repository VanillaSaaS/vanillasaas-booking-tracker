<?php
/**
 * =============================================================================
 *  public/appointments.php — UPCOMING + HISTORY, ADD AN APPOINTMENT
 * =============================================================================
 */

declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

allow_methods('GET', 'POST');
$user   = require_auth();
$userId = (int) $user['id'];

$errors = [];
$old    = [];
$status = 200;

if (is_post()) {
    csrf_verify();

    [$errors, $clean] = appointment_validate($userId);

    if (!$errors) {
        appointment_create($userId, $clean);
        flash('success', 'Appointment booked.');
        redirect('appointments.php');
    }

    // Re-show the raw text the user typed (not the converted values), so a
    // mistyped price or date is still there for them to correct.
    $old = [
        'client_id'        => input('client_id'),
        'service'          => input('service'),
        'starts_at'        => input('starts_at'),
        'duration_minutes' => input('duration_minutes'),
        'price'            => input('price'),
        'notes'            => input('notes'),
    ];
    $status = 422;
}

view('pages/appointments', [
    'title'    => 'Appointments',
    'active'   => 'appointments',
    'clients'  => client_all($userId),
    'upcoming' => appointment_upcoming($userId),
    'history'  => appointment_history($userId),
    'errors'   => $errors,
    'old'      => $old,
], 'app', $status);
