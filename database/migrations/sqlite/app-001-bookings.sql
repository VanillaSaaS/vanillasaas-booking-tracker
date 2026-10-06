-- =============================================================================
--  app-001-bookings.sql (SQLite) — clients and appointments
-- =============================================================================
--  Both tables carry user_id with ON DELETE CASCADE: deleting an account
--  deletes its clients and appointments. Deleting a client deletes that
--  client's appointments the same way.
--
--  starts_at is stored in UTC ('Y-m-d H:i:s') and converted to the app's
--  timezone for display. price_pence is a whole number of pence: never store
--  money as a decimal fraction, because 0.1 + 0.2 is not 0.3 in floating point.
-- =============================================================================

CREATE TABLE clients (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id     INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    name        TEXT    NOT NULL,
    phone       TEXT    NOT NULL DEFAULT '',
    email       TEXT    NOT NULL DEFAULT '',
    notes       TEXT    NOT NULL DEFAULT '',
    created_at  TEXT    NOT NULL,
    updated_at  TEXT    NOT NULL
);

CREATE INDEX idx_clients_user ON clients (user_id, name);

CREATE TABLE appointments (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id           INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    client_id         INTEGER NOT NULL REFERENCES clients(id) ON DELETE CASCADE,
    service           TEXT    NOT NULL,
    starts_at         TEXT    NOT NULL,
    duration_minutes  INTEGER NOT NULL DEFAULT 60,
    price_pence       INTEGER NOT NULL DEFAULT 0,
    status            TEXT    NOT NULL DEFAULT 'scheduled',
    notes             TEXT    NOT NULL DEFAULT '',
    created_at        TEXT    NOT NULL,
    updated_at        TEXT    NOT NULL
);

CREATE INDEX idx_appointments_user_start ON appointments (user_id, starts_at);

CREATE INDEX idx_appointments_client ON appointments (client_id);
