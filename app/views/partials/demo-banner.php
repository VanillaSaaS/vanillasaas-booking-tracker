<?php
/**
 * app/views/partials/demo-banner.php — strip across the top in demo mode.
 * Prints nothing unless 'demo.enabled' is true.
 */
if (!demo_enabled()) {
    return;
}
$siteUrl = (string) config('demo.site_url', '');
?>
<div class="demo-banner" role="note">
    <strong>Demo.</strong>
    Everything here is wiped every night. No emails are sent, so a made-up address is fine.
    <?php if ($siteUrl !== ''): ?>
        Built on <a href="<?= e($siteUrl) ?>" rel="noopener">VanillaSaaS Core</a>, which is free and open source.
    <?php endif; ?>
</div>
