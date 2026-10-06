<?php
/**
 * app/views/partials/appointment-table.php — a table of appointments.
 * Used three times: dashboard, upcoming list, history list.
 *
 * Variables: $rows (appointments with client_name), $showStatus (bool)
 */
?>
<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th scope="col">When</th>
                <th scope="col">Client</th>
                <th scope="col">Service</th>
                <th scope="col">Length</th>
                <th scope="col" class="cell-right">Price</th>
                <?php if (!empty($showStatus)): ?><th scope="col">Status</th><?php endif; ?>
                <th scope="col"><span class="visually-hidden">Actions</span></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= e(format_date($row['starts_at'], 'D j M, H:i')) ?></td>
                    <td><?= e($row['client_name']) ?></td>
                    <td><?= e($row['service']) ?></td>
                    <td><?= e(duration_label((int) $row['duration_minutes'])) ?></td>
                    <td class="cell-right"><?= e(money((int) $row['price_pence'])) ?></td>
                    <?php if (!empty($showStatus)): ?>
                        <td><?= status_badge($row['status']) /* returns escaped HTML */ ?></td>
                    <?php endif; ?>
                    <td class="cell-right">
                        <a class="btn btn-secondary btn-small" href="appointment-edit.php?id=<?= e($row['id']) ?>">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
