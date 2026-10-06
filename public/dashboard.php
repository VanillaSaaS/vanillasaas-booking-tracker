<?php
/**
 * =============================================================================
 *  public/dashboard.php — SIGNED-IN HOME (COPY THIS FILE TO ADD PAGES)
 * =============================================================================
 *  The smallest possible protected page:
 *
 *      require bootstrap  →  require_auth()  →  gather data  →  view()
 *
 *  require_auth() runs on the SERVER before any HTML is produced. A visitor
 *  who isn't signed in receives a redirect and zero bytes of this page —
 *  unlike JavaScript-only "protection", which still sends the page first.
 *
 *  TO ADD A NEW PAGE (e.g. "Projects"):
 *    1. Copy this file to public/projects.php
 *    2. Create app/views/pages/projects.php
 *    3. Change the view() call below to 'pages/projects', 'active' => 'projects'
 *    4. Add 'projects' to the $nav array in app/views/layouts/app.php
 * =============================================================================
 */

declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

allow_methods('GET');
$user = require_auth();

$userId = (int) $user['id'];
$totals = booking_stats($userId);

view('pages/dashboard', [
    'title'  => 'Dashboard',
    'active' => 'dashboard',
    'user'   => $user,
    'stats'  => [
        'Booked, next 7 days'   => $totals['upcoming'],
        'Clients'               => $totals['clients'],
        'Earned, last 30 days'  => money($totals['earned']),
    ],
    'next'   => appointment_upcoming($userId, 5),
]);
