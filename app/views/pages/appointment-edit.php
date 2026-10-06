<?php
/**
 * app/views/pages/appointment-edit.php — edit or delete one appointment.
 * Variables: $form, $id, $clients, $errors
 */
$url = 'appointment-edit.php?id=' . (int) $id;
?>
<div class="bookings">
    <section class="panel">
        <header class="panel-header panel-header-row">
            <h2>Appointment details</h2>
            <a class="btn btn-secondary btn-small" href="appointments.php">Back to appointments</a>
        </header>
        <form method="post" action="<?= e($url) ?>" class="form panel-body" data-once>
            <?= csrf_field() ?>
            <?= partial('appointment-fields', ['form' => $form, 'errors' => $errors, 'clients' => $clients, 'withStatus' => true]) ?>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save appointment</button>
            </div>
        </form>
    </section>

    <section class="panel panel-danger">
        <header class="panel-header">
            <h2>Delete appointment</h2>
            <p class="muted">To keep a record, set the status to Cancelled instead.</p>
        </header>
        <form method="post" action="<?= e($url) ?>" class="form panel-body" data-once
              data-confirm="Delete this appointment?">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <div class="form-actions">
                <button type="submit" class="btn btn-danger">Delete appointment</button>
            </div>
        </form>
    </section>
</div>
