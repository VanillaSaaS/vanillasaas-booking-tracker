<?php
/**
 * =============================================================================
 *  public/appointment-edit.php?id=7 — EDIT, RESCHEDULE, COMPLETE OR DELETE
 * =============================================================================
 *  Same ownership rule as client-edit.php: not yours means not found.
 * =============================================================================
 */

declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

allow_methods('GET', 'POST');
$user   = require_auth();
$userId = (int) $user['id'];

$id          = id_from_query();
$appointment = appointment_find($id, $userId) ?? abort(404);

// What the form shows: stored values converted back to what a person types.
$form = [
    'client_id'        => (string) $appointment['client_id'],
    'service'          => $appointment['service'],
    'starts_at'        => utc_to_local_input($appointment['starts_at']),
    'duration_minutes' => (string) $appointment['duration_minutes'],
    'price'            => pence_to_input((int) $appointment['price_pence']),
    'status'           => $appointment['status'],
    'notes'            => $appointment['notes'],
];

$errors = [];
$status = 200;

if (is_post()) {
    csrf_verify();

    if (input('action') === 'delete') {
        appointment_delete($id, $userId);
        flash('success', 'Appointment deleted.');
        redirect('appointments.php');
    }

    [$errors, $clean] = appointment_validate($userId, withStatus: true);

    if (!$errors) {
        appointment_update($id, $userId, $clean);
        flash('success', 'Appointment saved.');
        redirect('appointments.php');
    }

    foreach (array_keys($form) as $field) {
        $form[$field] = input($field);
    }
    $status = 422;
}

view('pages/appointment-edit', [
    'title'   => 'Edit appointment',
    'active'  => 'appointments',
    'clients' => client_all($userId),
    'form'    => $form,
    'id'      => $id,
    'errors'  => $errors,
], 'app', $status);
