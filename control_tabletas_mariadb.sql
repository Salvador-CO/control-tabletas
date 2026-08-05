-- ============================================================
--  BASE DE DATOS: control_tabletas
--  Motor: MariaDB / MySQL  (compatible con phpMyAdmin / XAMPP)
--  Generado automáticamente desde las migraciones de Laravel
-- ============================================================

CREATE DATABASE IF NOT EXISTS `control_tabletas`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `control_tabletas`;

-- ============================================================
-- 1. USUARIOS (Laravel Auth)
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`              VARCHAR(255)    NOT NULL,
    `email`             VARCHAR(255)    NOT NULL,
    `email_verified_at` TIMESTAMP       NULL     DEFAULT NULL,
    `password`          VARCHAR(255)    NOT NULL,
    `remember_token`    VARCHAR(100)    NULL     DEFAULT NULL,
    `created_at`        TIMESTAMP       NULL     DEFAULT NULL,
    `updated_at`        TIMESTAMP       NULL     DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. TOKENS DE RESET DE CONTRASEÑA
-- ============================================================
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    `email`      VARCHAR(255) NOT NULL,
    `token`      VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP    NULL DEFAULT NULL,
    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. SESIONES
-- ============================================================
CREATE TABLE IF NOT EXISTS `sessions` (
    `id`            VARCHAR(255)    NOT NULL,
    `user_id`       BIGINT UNSIGNED NULL    DEFAULT NULL,
    `ip_address`    VARCHAR(45)     NULL    DEFAULT NULL,
    `user_agent`    TEXT            NULL,
    `payload`       LONGTEXT        NOT NULL,
    `last_activity` INT             NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. CACHE
-- ============================================================
CREATE TABLE IF NOT EXISTS `cache` (
    `key`        VARCHAR(255) NOT NULL,
    `value`      MEDIUMTEXT   NOT NULL,
    `expiration` INT          NOT NULL,
    PRIMARY KEY (`key`),
    KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
    `key`        VARCHAR(255) NOT NULL,
    `owner`      VARCHAR(255) NOT NULL,
    `expiration` INT          NOT NULL,
    PRIMARY KEY (`key`),
    KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. JOBS / COLAS
-- ============================================================
CREATE TABLE IF NOT EXISTS `jobs` (
    `id`           BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `queue`        VARCHAR(255)        NOT NULL,
    `payload`      LONGTEXT            NOT NULL,
    `attempts`     TINYINT UNSIGNED    NOT NULL,
    `reserved_at`  INT UNSIGNED        NULL     DEFAULT NULL,
    `available_at` INT UNSIGNED        NOT NULL,
    `created_at`   INT UNSIGNED        NOT NULL,
    PRIMARY KEY (`id`),
    KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_batches` (
    `id`              VARCHAR(255) NOT NULL,
    `name`            VARCHAR(255) NOT NULL,
    `total_jobs`      INT          NOT NULL,
    `pending_jobs`    INT          NOT NULL,
    `failed_jobs`     INT          NOT NULL,
    `failed_job_ids`  LONGTEXT     NOT NULL,
    `options`         MEDIUMTEXT   NULL DEFAULT NULL,
    `cancelled_at`    INT          NULL DEFAULT NULL,
    `created_at`      INT          NOT NULL,
    `finished_at`     INT          NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid`       VARCHAR(255)    NOT NULL,
    `connection` TEXT            NOT NULL,
    `queue`      TEXT            NOT NULL,
    `payload`    LONGTEXT        NOT NULL,
    `exception`  LONGTEXT        NOT NULL,
    `failed_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. TABLA DE MIGRACIONES (Laravel la requiere)
-- ============================================================
CREATE TABLE IF NOT EXISTS `migrations` (
    `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `migration` VARCHAR(255) NOT NULL,
    `batch`     INT          NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. CATEGORÍAS DE DISPOSITIVOS
-- ============================================================
CREATE TABLE IF NOT EXISTS `categories` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(255)    NOT NULL,
    `description` VARCHAR(255)    NULL DEFAULT NULL,
    `created_at`  TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`  TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. EVENTOS
-- ============================================================
CREATE TABLE IF NOT EXISTS `events` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(255)    NOT NULL,
    `start_date` DATE            NULL DEFAULT NULL,
    `end_date`   DATE            NULL DEFAULT NULL,
    `created_at` TIMESTAMP       NULL DEFAULT NULL,
    `updated_at` TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. UBICACIONES / LOCACIONES
-- ============================================================
CREATE TABLE IF NOT EXISTS `locations` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(255)    NOT NULL,
    `state`      VARCHAR(255)    NULL DEFAULT NULL,
    `created_at` TIMESTAMP       NULL DEFAULT NULL,
    `updated_at` TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. DISPOSITIVOS (depende de categories)
-- ============================================================
CREATE TABLE IF NOT EXISTS `devices` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id`     BIGINT UNSIGNED NOT NULL,
    `brand`           VARCHAR(255)    NOT NULL DEFAULT 'XIAOMI',
    `model`           VARCHAR(255)    NOT NULL DEFAULT 'Pad 6',
    `serial_number`   VARCHAR(255)    NOT NULL,
    `status`          ENUM('disponible','en_resguardo','asignado_fijo','mantenimiento')
                                      NOT NULL DEFAULT 'disponible',
    `is_charged`      TINYINT(1)      NOT NULL DEFAULT 1,
    `charger_details` VARCHAR(255)    NULL DEFAULT NULL,
    `notes`           TEXT            NULL DEFAULT NULL,
    `created_at`      TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`      TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `devices_serial_number_unique` (`serial_number`),
    CONSTRAINT `devices_category_id_foreign`
        FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 11. PERSONAL / STAFF (depende de locations)
-- ============================================================
CREATE TABLE IF NOT EXISTS `staff` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `full_name`   VARCHAR(255)    NOT NULL,
    `role`        VARCHAR(255)    NOT NULL,
    `location_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `created_at`  TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`  TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `staff_location_id_foreign`
        FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 12. ASIGNACIONES (depende de events, locations, staff)
-- ============================================================
CREATE TABLE IF NOT EXISTS `assignments` (
    `id`                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `event_id`             BIGINT UNSIGNED NULL DEFAULT NULL,
    `location_id`          BIGINT UNSIGNED NULL DEFAULT NULL,
    `coordinator_id`       BIGINT UNSIGNED NOT NULL,
    `delivery_person_name` VARCHAR(255)    NOT NULL DEFAULT 'MARCELA PEÑA ORDOÑEZ',
    `chargers_count`       INT             NOT NULL DEFAULT 0,
    `start_date`           DATE            NOT NULL,
    `end_date`             DATE            NOT NULL,
    `status`               ENUM('activo','completado','cancelado') NOT NULL DEFAULT 'activo',
    `observations`         TEXT            NULL DEFAULT NULL,
    `created_at`           TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`           TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `assignments_event_id_foreign`
        FOREIGN KEY (`event_id`) REFERENCES `events` (`id`),
    CONSTRAINT `assignments_location_id_foreign`
        FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
    CONSTRAINT `assignments_coordinator_id_foreign`
        FOREIGN KEY (`coordinator_id`) REFERENCES `staff` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 13. ÍTEMS DE ASIGNACIÓN (depende de assignments, devices, staff)
-- ============================================================
CREATE TABLE IF NOT EXISTS `assignment_items` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `assignment_id`  BIGINT UNSIGNED NOT NULL,
    `device_id`      BIGINT UNSIGNED NOT NULL,
    `staff_id`       BIGINT UNSIGNED NULL DEFAULT NULL,
    `role_in_period` VARCHAR(255)    NULL DEFAULT NULL,
    `has_case_strap` TINYINT(1)      NOT NULL DEFAULT 1,
    `is_returned`    TINYINT(1)      NOT NULL DEFAULT 0,
    `returned_at`    TIMESTAMP       NULL DEFAULT NULL,
    `created_at`     TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`     TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `assignment_items_assignment_id_foreign`
        FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `assignment_items_device_id_foreign`
        FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`),
    CONSTRAINT `assignment_items_staff_id_foreign`
        FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 14. ASIGNACIONES PERMANENTES (depende de devices, staff)
-- ============================================================
CREATE TABLE IF NOT EXISTS `permanent_assignments` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `device_id`       BIGINT UNSIGNED NOT NULL,
    `staff_id`        BIGINT UNSIGNED NOT NULL,
    `role`            VARCHAR(255)    NOT NULL,
    `assigned_date`   DATE            NOT NULL,
    `notes`           TEXT            NULL DEFAULT NULL,
    `released_date`   DATE            NULL DEFAULT NULL,
    `released_reason` VARCHAR(255)    NULL DEFAULT NULL,
    `created_at`      TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`      TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `permanent_assignments_device_id_foreign`
        FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `permanent_assignments_staff_id_foreign`
        FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- FIN DEL SCRIPT
-- ============================================================
