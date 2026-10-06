<?php
/**
 * =============================================================================
 *  public/client-edit.php?id=12 — EDIT OR DELETE ONE CLIENT
 * =============================================================================
 *  The id in the URL is untrusted. client_find() only returns the row when it
 *  belongs to the signed-in user; anything else is a 404. We answer "not
 *  found" rather than "forbidden" so the response doesn't confirm that
 *  client 12 exists in someone else's account.
 * =============================================================================
 */

declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

allow_methods('GET', 'POST');
$user   = require_auth();
$userId = (int) $user['id'];

$id     = id_from_query();
$client = client_find($id, $userId) ?? abort(404);

$errors = [];
$status = 200;

if (is_post()) {
    csrf_verify();

    if (input('action') === 'delete') {
        client_delete($id, $userId);
        flash('success', "{$client['name']} and their appointments were deleted.");
        redirect('clients.php');
    }

    [$errors, $clean] = client_validate();

    if (!$errors) {
        client_update($id, $userId, $clean);
        flash('success', 'Client saved.');
        redirect('clients.php');
    }
    $client = array_merge($client, $clean);   // re-show what they typed
    $status = 422;
}

view('pages/client-edit', [
    'title'  => 'Edit client',
    'active' => 'clients',
    'client' => $client,
    'errors' => $errors,
], 'app', $status);
