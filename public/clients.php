<?php
/**
 * =============================================================================
 *  public/clients.php — LIST CLIENTS, ADD A CLIENT
 * =============================================================================
 *  The standard page-controller shape, start to finish:
 *    bootstrap → require_auth → (POST: verify, validate, save, redirect) → view
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

    [$errors, $clean] = client_validate();

    if (!$errors) {
        client_create($userId, $clean);
        flash('success', "{$clean['name']} added.");
        redirect('clients.php');          // Post/Redirect/Get: refresh won't re-submit
    }
    $old    = $clean;
    $status = 422;
}

view('pages/clients', [
    'title'   => 'Clients',
    'active'  => 'clients',
    'clients' => client_all($userId),
    'errors'  => $errors,
    'old'     => $old,
], 'app', $status);
