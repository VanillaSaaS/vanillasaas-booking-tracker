<?php
/**
 * app/views/pages/client-edit.php — edit or delete one client.
 * Variables: $client, $errors
 *
 * Two forms post to the same URL. The delete form carries a hidden
 * action=delete so the controller can tell them apart, and data-confirm so
 * app.js asks before submitting (it still works, unprompted, without JS).
 */
$url = 'client-edit.php?id=' . (int) $client['id'];
?>
<div class="bookings">
    <section class="panel">
        <header class="panel-header panel-header-row">
            <h2><?= e($client['name']) ?></h2>
            <a class="btn btn-secondary btn-small" href="clients.php">Back to clients</a>
        </header>
        <form method="post" action="<?= e($url) ?>" class="form panel-body" data-once>
            <?= csrf_field() ?>
            <?= partial('client-fields', ['client' => $client, 'errors' => $errors]) ?>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save client</button>
            </div>
        </form>
    </section>

    <section class="panel panel-danger">
        <header class="panel-header">
            <h2>Delete client</h2>
            <p class="muted">Also deletes every appointment for this client. This cannot be undone.</p>
        </header>
        <form method="post" action="<?= e($url) ?>" class="form panel-body" data-once
              data-confirm="Delete this client and all their appointments?">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <div class="form-actions">
                <button type="submit" class="btn btn-danger">Delete client</button>
            </div>
        </form>
    </section>
</div>
