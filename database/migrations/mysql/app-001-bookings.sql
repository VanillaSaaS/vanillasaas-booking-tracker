-- =============================================================================
--  app-001-bookings.sql (MySQL / MariaDB) — clients and appointments
-- =============================================================================
--  Mirrors the SQLite file of the same name. See that file for the reasoning
--  behind user_id cascades, UTC start times and prices in pence.
-- =============================================================================

CREATE TABLE `clients` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`     INT UNSIGNED NOT NULL,
    `name`        VARCHAR(100) NOT NULL,
    `phone`       VARCHAR(30)  NOT NULL DEFAULT '',
    `email`       VARCHAR(254) NOT NULL DEFAULT '',
    `notes`       TEXT         NOT NULL,
    `created_at`  DATETIME     NOT NULL,
    `updated_at`  DATETIME     NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_clients_user` (`user_id`, `name`),
    CONSTRAINT `fk_clients_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `appointments` (
    `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`           INT UNSIGNED NOT NULL,
    `client_id`         INT UNSIGNED NOT NULL,
    `service`           VARCHAR(120) NOT NULL,
    `starts_at`         DATETIME     NOT NULL,
    `duration_minutes`  SMALLINT UNSIGNED NOT NULL DEFAULT 60,
    `price_pence`       INT UNSIGNED NOT NULL DEFAULT 0,
    `status`            VARCHAR(20)  NOT NULL DEFAULT 'scheduled',
    `notes`             TEXT         NOT NULL,
    `created_at`        DATETIME     NOT NULL,
    `updated_at`        DATETIME     NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_appointments_user_start` (`user_id`, `starts_at`),
    KEY `idx_appointments_client` (`client_id`),
    CONSTRAINT `fk_appointments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_appointments_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
