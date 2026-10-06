<?php
/**
 * app/views/pages/clients.php — client list and "add client" form.
 * Variables: $clients, $errors, $old
 */
?>
<div class="bookings">
    <section class="panel" aria-labelledby="clients-heading">
        <header class="panel-header">
            <h2 id="clients-heading">Your clients</h2>
        </header>

        <?php if (!$clients): ?>
            <div class="empty-state">
                <div class="empty-icon" aria-hidden="true"></div>
                <p><strong>No clients yet</strong></p>
                <p class="muted">Add your first client below, then book them in.</p>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Email</th>
                            <th scope="col" class="cell-right">Appointments</th>
                            <th scope="col"><span class="visually-hidden">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td><?= e($client['name']) ?></td>
                                <td><?= e($client['phone'] ?: '—') ?></td>
                                <td><?= e($client['email'] ?: '—') ?></td>
                                <td class="cell-right"><?= e($client['appointment_count']) ?></td>
                                <td class="cell-right">
                                    <a class="btn btn-secondary btn-small" href="client-edit.php?id=<?= e($client['id']) ?>">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="panel" aria-labelledby="add-client-heading">
        <header class="panel-header">
            <h2 id="add-client-heading">Add a client</h2>
        </header>
        <form method="post" action="clients.php" class="form panel-body" data-once>
            <?= csrf_field() ?>
            <?= partial('client-fields', ['client' => $old, 'errors' => $errors]) ?>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add client</button>
            </div>
        </form>
    </section>
</div>
