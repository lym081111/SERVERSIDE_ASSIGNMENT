-- Migration: event capacity + waitlist system
-- Adds seat planning fields to event records.

START TRANSACTION;

ALTER TABLE events
  ADD COLUMN IF NOT EXISTS participantCapacity INT(11) NULL AFTER eventHours,
  ADD COLUMN IF NOT EXISTS registeredCount INT(11) NOT NULL DEFAULT 0 AFTER participantCapacity,
  ADD COLUMN IF NOT EXISTS waitlistEnabled TINYINT(1) NOT NULL DEFAULT 1 AFTER registeredCount,
  ADD COLUMN IF NOT EXISTS waitlistCount INT(11) NOT NULL DEFAULT 0 AFTER waitlistEnabled;

UPDATE events
SET registeredCount = 0
WHERE registeredCount IS NULL OR registeredCount < 0;

UPDATE events
SET waitlistCount = 0
WHERE waitlistCount IS NULL OR waitlistCount < 0;

UPDATE events
SET waitlistEnabled = 0
WHERE participantCapacity IS NULL;

UPDATE events
SET waitlistCount = 0
WHERE waitlistEnabled = 0;

UPDATE events
SET waitlistCount = 0
WHERE participantCapacity IS NOT NULL
  AND registeredCount < participantCapacity;

COMMIT;

SET @idx_events_capacity_exists := (
  SELECT COUNT(*)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name = 'events'
    AND index_name = 'idx_events_capacity'
);
SET @sql := IF(@idx_events_capacity_exists = 0,
  'ALTER TABLE events ADD KEY idx_events_capacity (participantCapacity)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_events_registered_exists := (
  SELECT COUNT(*)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name = 'events'
    AND index_name = 'idx_events_registered'
);
SET @sql := IF(@idx_events_registered_exists = 0,
  'ALTER TABLE events ADD KEY idx_events_registered (registeredCount)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_events_waitlist_exists := (
  SELECT COUNT(*)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name = 'events'
    AND index_name = 'idx_events_waitlist'
);
SET @sql := IF(@idx_events_waitlist_exists = 0,
  'ALTER TABLE events ADD KEY idx_events_waitlist (waitlistCount)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

