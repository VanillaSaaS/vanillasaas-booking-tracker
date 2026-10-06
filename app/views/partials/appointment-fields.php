<?php
/**
 * app/views/partials/appointment-fields.php — the appointment form's fields.
 * Shared by the "add" and "edit" forms.
 *
 * Variables: $form (values to show, as typed), $errors, $clients,
 *            $withStatus (bool: show the status dropdown; edit form only)
 *
 * Note the `selected` checks compare strings: values coming back from a form
 * are always text, so we compare text with text.
 */
?>
<div class="form-row">
    <div class="field">
        <label for="client_id">Client</label>
        <select id="client_id" name="client_id" required<?= field_attrs($errors, 'client_id') ?>>
            <option value="">Choose…</option>
            <?php foreach ($clients as $client): ?>
                <option value="<?= e($client['id']) ?>"<?= (string) $client['id'] === (string) ($form['client_id'] ?? '') ? ' selected' : '' ?>>
                    <?= e($client['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?= field_error($errors, 'client_id') ?>
    </div>

    <div class="field">
        <label for="service">Service</label>
        <input id="service" name="service" type="text" required maxlength="120"
               value="<?= e($form['service'] ?? '') ?>"<?= field_attrs($errors, 'service') ?>>
        <?= field_error($errors, 'service') ?>
    </div>
</div>

<div class="form-row">
    <div class="field">
        <label for="starts_at">Date and time</label>
        <input id="starts_at" name="starts_at" type="datetime-local" required
               value="<?= e($form['starts_at'] ?? '') ?>"<?= field_attrs($errors, 'starts_at') ?>>
        <?= field_error($errors, 'starts_at') ?>
    </div>

    <div class="field">
        <label for="duration_minutes">Duration</label>
        <select id="duration_minutes" name="duration_minutes"<?= field_attrs($errors, 'duration_minutes') ?>>
            <?php foreach (APPOINTMENT_DURATIONS as $minutes): ?>
                <option value="<?= e($minutes) ?>"<?= (string) $minutes === (string) ($form['duration_minutes'] ?? '60') ? ' selected' : '' ?>>
                    <?= e(duration_label($minutes)) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?= field_error($errors, 'duration_minutes') ?>
    </div>

    <div class="field">
        <label for="price">Price (£)</label>
        <input id="price" name="price" type="text" inputmode="decimal" maxlength="9" placeholder="0.00"
               value="<?= e($form['price'] ?? '') ?>"<?= field_attrs($errors, 'price') ?>>
        <?= field_error($errors, 'price') ?>
    </div>
</div>

<?php if (!empty($withStatus)): ?>
    <div class="field">
        <label for="status">Status</label>
        <select id="status" name="status"<?= field_attrs($errors, 'status') ?>>
            <?php foreach (APPOINTMENT_STATUSES as $value => [$label]): ?>
                <option value="<?= e($value) ?>"<?= $value === ($form['status'] ?? '') ? ' selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
        <?= field_error($errors, 'status') ?>
    </div>
<?php endif; ?>

<div class="field">
    <label for="notes">Notes <span class="muted">(optional)</span></label>
    <textarea id="notes" name="notes" maxlength="1000"<?= field_attrs($errors, 'notes') ?>><?= e($form['notes'] ?? '') ?></textarea>
    <?= field_error($errors, 'notes') ?>
</div>
