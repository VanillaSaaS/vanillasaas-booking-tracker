<?php
/**
 * =============================================================================
 *  app/views/pages/dashboard.php — THE SIGNED-IN HOME SCREEN
 * =============================================================================
 *  Three numbers and the next few appointments. Built entirely from Core's
 *  component classes (stat cards, panel, table, empty state).
 *
 *  Variables: $user, $stats (label => value), $next (upcoming appointments)
 * =============================================================================
 */
$firstName = explode(' ', trim($user['name']))[0];
?>
<section class="welcome">
    <h2>Welcome, <?= e($firstName) ?></h2>
    <p class="muted">Here's your week at a glance.</p>
</section>

<section class="stats" aria-label="Summary">
    <?php foreach ($stats as $label => $value): ?>
        <div class="stat">
            <p class="stat-label"><?= e($label) ?></p>
            <p class="stat-value"><?= e($value) ?></p>
        </div>
    <?php endforeach; ?>
</section>

<section class="panel" aria-labelledby="next-heading">
    <header class="panel-header panel-header-row">
        <h3 id="next-heading">Next appointments</h3>
        <a class="btn btn-primary btn-small" href="appointments.php">Book an appointment</a>
    </header>

    <?php if (!$next): ?>
        <div class="empty-state">
            <div class="empty-icon" aria-hidden="true"></div>
            <p><strong>Nothing booked yet</strong></p>
            <p class="muted">Start by <a href="clients.php">adding a client</a>, then book them in.</p>
        </div>
    <?php else: ?>
        <?= partial('appointment-table', ['rows' => $next, 'showStatus' => false]) ?>
    <?php endif; ?>
</section>
