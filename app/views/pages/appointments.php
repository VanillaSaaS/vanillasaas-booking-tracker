<?php
/**
 * app/views/pages/appointments.php — upcoming, history and "book" form.
 * Variables: $clients, $upcoming, $history, $errors, $old
 */
?>
<div class="bookings">
    <section class="panel" aria-labelledby="upcoming-heading">
        <header class="panel-header">
            <h2 id="upcoming-heading">Upcoming</h2>
        </header>
        <?php if (!$upcoming): ?>
            <div class="empty-state">
                <div class="empty-icon" aria-hidden="true"></div>
                <p><strong>Nothing booked</strong></p>
                <p class="muted">Appointments you book below appear here.</p>
            </div>
        <?php else: ?>
            <?= partial('appointment-table', ['rows' => $upcoming, 'showStatus' => false]) ?>
        <?php endif; ?>
    </section>

    <section class="panel" aria-labelledby="book-heading">
        <header class="panel-header">
            <h2 id="book-heading">Book an appointment</h2>
        </header>
        <?php if (!$clients): ?>
            <div class="panel-body">
                <p class="muted">You need a client first. <a href="clients.php">Add a client</a>.</p>
            </div>
        <?php else: ?>
            <form method="post" action="appointments.php" class="form panel-body" data-once>
                <?= csrf_field() ?>
                <?= partial('appointment-fields', ['form' => $old, 'errors' => $errors, 'clients' => $clients, 'withStatus' => false]) ?>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Book appointment</button>
                </div>
            </form>
        <?php endif; ?>
    </section>

    <?php if ($history): ?>
        <section class="panel" aria-labelledby="history-heading">
            <header class="panel-header">
                <h2 id="history-heading">History</h2>
                <p class="muted">Completed, cancelled and past appointments, newest first.</p>
            </header>
            <?= partial('appointment-table', ['rows' => $history, 'showStatus' => true]) ?>
        </section>
    <?php endif; ?>
</div>
