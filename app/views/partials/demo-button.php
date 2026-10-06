<?php
/**
 * app/views/partials/demo-button.php — the "Try the demo" form.
 * A form, not a link, so it can carry a CSRF token (see public/demo-login.php).
 */
if (!demo_enabled()) {
    return;
}
?>
<form method="post" action="demo-login.php" class="demo-try" data-once>
    <?= csrf_field() ?>
    <button type="submit" class="btn btn-secondary btn-block">Try the demo — no sign-up</button>
    <p class="hint">Opens a private account filled with sample bookings.</p>
</form>
