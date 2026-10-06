<?php
/**
 * =============================================================================
 *  app/custom.php — YOUR FUNCTIONS GO HERE
 * =============================================================================
 *
 *  This file belongs to you. VanillaSaaS Core updates never touch it.
 *
 *  In this demo it loads two files, and that is the whole of the "product":
 *
 *    custom/bookings.php   the booking tracker (clients + appointments)
 *    custom/demo.php       public-demo mode (throwaway accounts, nightly wipe)
 *
 *  Everything in app/lib/ is exactly as VanillaSaaS Core ships it. That is
 *  the point of the demo: a real feature added without editing Core.
 * =============================================================================
 */

declare(strict_types=1);

require __DIR__ . '/custom/bookings.php';
require __DIR__ . '/custom/demo.php';
