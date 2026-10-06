<?php
/**
 * app/views/partials/client-fields.php — the client form's fields.
 * Shared by the "add" form (clients.php) and the "edit" form
 * (client-edit.php), so a new field is added in one place.
 *
 * Variables: $client (values to show), $errors
 */
?>
<div class="form-row">
    <div class="field">
        <label for="name">Name</label>
        <input id="name" name="name" type="text" required maxlength="100"
               value="<?= e($client['name'] ?? '') ?>"<?= field_attrs($errors, 'name') ?>>
        <?= field_error($errors, 'name') ?>
    </div>

    <div class="field">
        <label for="phone">Phone <span class="muted">(optional)</span></label>
        <input id="phone" name="phone" type="tel" maxlength="30" autocomplete="off"
               value="<?= e($client['phone'] ?? '') ?>"<?= field_attrs($errors, 'phone') ?>>
        <?= field_error($errors, 'phone') ?>
    </div>
</div>

<div class="field">
    <label for="email">Email <span class="muted">(optional)</span></label>
    <input id="email" name="email" type="email" inputmode="email" maxlength="254" autocomplete="off"
           value="<?= e($client['email'] ?? '') ?>"<?= field_attrs($errors, 'email') ?>>
    <?= field_error($errors, 'email') ?>
</div>

<div class="field">
    <label for="notes">Notes <span class="muted">(optional)</span></label>
    <textarea id="notes" name="notes" maxlength="1000"<?= field_attrs($errors, 'notes') ?>><?= e($client['notes'] ?? '') ?></textarea>
    <?= field_error($errors, 'notes') ?>
</div>
