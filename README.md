# Booking Tracker

An example app built on VanillaSaaS Core: a booking tracker for someone who works for themselves. Clients, appointments, and a dashboard with three numbers.

Run it with one command. It contains VanillaSaaS Core 1.0.1, so there is nothing else to download.

It exists for two reasons: to show what Core looks like with a real feature on top, and to prove that you can add one without editing Core. Read it, run it, or start your own app from it. Core and this example are both free and open source under the MIT licence. `README-CORE.md` is Core's own README, unchanged.

## Run it

```bash
php -S localhost:8001 -t public
```

Open <http://localhost:8001>, create an account, add a client, then book them in. On XAMPP, put the folder in `htdocs` and visit `http://localhost/your-folder/` instead.

The SQLite database is created in `storage/database/` on the first visit. Email is written to `storage/logs/mail.log`, not sent.

## What was added to Core

Nothing in `app/lib/` or `app/bootstrap.php` was edited. Everything below is in files Core leaves to you.

| What                                                               | Where                                                                                   |
| ------------------------------------------------------------------ | --------------------------------------------------------------------------------------- |
| Clients and appointments tables                                    | `database/migrations/{sqlite,mysql}/app-001-bookings.sql`                               |
| Booking functions (queries, validation, money and date converters) | `app/custom/bookings.php`                                                               |
| Four pages                                                         | `public/clients.php`, `client-edit.php`, `appointments.php`, `appointment-edit.php`     |
| Templates                                                          | `app/views/pages/` (4 new, dashboard and home rewritten), `app/views/partials/` (3 new) |
| Sidebar links and icons                                            | `app/views/layouts/app.php`                                                             |
| Settings: app name, timezone                                       | `app/config.php`                                                                        |
| A few styles                                                       | section 8 at the end of `public/assets/css/app.css`                                     |

The pattern behind it (a table with `user_id`, functions that always filter by it, a page that guards, validates, saves and redirects) is walked through in Core's docs at <https://vanillasaas.dev/docs/build-a-feature>.

## Starting your own app from it

Change `app.name` in `app/config.php`, then follow the going-live checklist in `README-CORE.md`. Set a real mail driver so password resets are delivered.

You can delete demo mode (next section) if you'll never use it: `app/custom/demo.php` and its `require` line in `app/custom.php`, `public/demo-login.php`, `bin/demo-reset.php`, the two `demo-*` files in `app/views/partials/` and the lines that include them in the layouts, the home page and the sign-in page, the `demo_enabled()` check in `public/forgot-password.php`, and the `demo` block in `app/config.php`. Left alone, it does nothing.

## Demo mode (off by default)

The same code runs the public demo at <https://demo.vanillasaas.dev>. Demo mode is what makes that safe, and it is switched off unless you turn it on:

```php
// app/config.local.php
return ['demo' => ['enabled' => true]];
```

With it on, a **Try the demo** button appears and a banner says the visitor is in a demo. It is also the quickest way to see the app with data in it on your own computer.

A public demo invites three kinds of trouble. Each has one answer in the code.

**Vandalism.** If visitors shared one demo account, anything one person typed would be shown to the next. So each press of **Try the demo** creates a private throwaway account (`visitor-xxxx@demo.invalid`) with its own sample clients and appointments. Nobody sees anyone else's data, and nothing needs locking: visitors can change the password or delete the account and only affect themselves. Ten demo sessions per hour per IP address stops a script filling the database.

**Spam.** Mail stays on the `log` driver, so the app never sends email. Without this, anyone could use the password-reset form to send messages to strangers from your domain. The reset page says plainly that no email is sent.

**Clutter and personal data.** `bin/demo-reset.php` deletes every account and everything belonging to it, clears the rate limits, signs everyone out and empties the mail log. Run it nightly. It refuses to run unless demo mode is on, so it can't wipe a real site by mistake.

Normal sign-up stays open in demo mode, because the sign-up flow is part of what's being shown.

**Never turn demo mode on for a site with real users.** The wipe script deletes every account.

## Putting a public demo online

For showing your own app to a prospective client, or running a demo like the one above. It runs on the cheapest shared cPanel hosting.

1. **Create the subdomain** (for example `demo.your-domain.com`) and set its document root to this project's `public` folder. If the host won't let you choose, upload the whole project into the subdomain's folder; the root `.htaccess` routes requests into `public`.
2. **Upload everything** except `storage/database/*`, `storage/logs/*` and `storage/sessions/*`.
3. **Create `app/config.local.php`** from `app/config.local.example.php`. It turns demo mode on and keeps mail on `log`; set `url` to the demo's address.
4. **Turn on HTTPS** for the subdomain (free with Let's Encrypt on most hosts).
5. **Make `storage/` writable** by the web server. SQLite creates its database there on the first visit. There is no database to set up.
6. **Schedule the wipe.** cPanel → Cron Jobs, once a day:

   ```text
   0 3 * * * /usr/local/bin/php /home/YOURUSER/path-to/your-folder/bin/demo-reset.php
   ```

   The PHP path varies by host; cPanel shows the right one on the Cron Jobs page.

7. **Check the private folders are private.** Each must give 403 or 404:
   - `/app/config.php`
   - `/storage/database/app.sqlite`
   - `/storage/logs/app.log`
8. **Try it as a stranger**: press Try the demo, book an appointment, sign out, sign up with a made-up address.

## What it doesn't do

It's a private diary: the owner types every appointment in. There is no public booking page, no services or opening hours, no emails to clients and no protection against double booking, because nobody but the owner can book.

The version a business's customers book themselves into is [VanillaSaaS Bookings](https://vanillasaas.dev/blueprints/bookings), a paid blueprint that starts from this code. The first database script is the same file, so data made here carries over.

## Licence

MIT, the same as Core: see `LICENSE.md`. The folder can go in a public repository as it is. `app/config.local.php` and everything under `storage/` are excluded by the `.gitignore`.

If you add VanillaSaaS Pro or a paid blueprint to a copy of this, that copy must move to a private repository.
