-- Migration: link modules by business flow
-- club -> event -> merit / achievement
--
-- 1) events link to club catalog and carry eventHours
-- 2) merits link to events
-- 3) achievements link to events

START TRANSACTION;

ALTER TABLE events
  ADD COLUMN IF NOT EXISTS clubCatalogID INT(11) NULL AFTER userID,
  ADD COLUMN IF NOT EXISTS eventHours DECIMAL(6,2) NOT NULL DEFAULT 1.00 AFTER eventDate;

UPDATE events
SET eventHours = 1.00
WHERE eventHours IS NULL OR eventHours <= 0;

ALTER TABLE merits
  ADD COLUMN IF NOT EXISTS eventID INT(11) NULL AFTER userID;

ALTER TABLE achievements
  ADD COLUMN IF NOT EXISTS eventID INT(11) NULL AFTER userID;

COMMIT;

-- Indexes and FKs with existence checks
SET @idx_events_club_exists := (
  SELECT COUNT(*)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name = 'events'
    AND index_name = 'idx_events_clubCatalogID'
);
SET @sql := IF(@idx_events_club_exists = 0,
  'ALTER TABLE events ADD KEY idx_events_clubCatalogID (clubCatalogID)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_merits_event_exists := (
  SELECT COUNT(*)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name = 'merits'
    AND index_name = 'idx_merits_eventID'
);
SET @sql := IF(@idx_merits_event_exists = 0,
  'ALTER TABLE merits ADD KEY idx_merits_eventID (eventID)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_achievements_event_exists := (
  SELECT COUNT(*)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name = 'achievements'
    AND index_name = 'idx_achievements_eventID'
);
SET @sql := IF(@idx_achievements_event_exists = 0,
  'ALTER TABLE achievements ADD KEY idx_achievements_eventID (eventID)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk_events_club_exists := (
  SELECT COUNT(*)
  FROM information_schema.table_constraints
  WHERE table_schema = DATABASE()
    AND table_name = 'events'
    AND constraint_name = 'fk_events_club_catalog'
    AND constraint_type = 'FOREIGN KEY'
);
SET @sql := IF(@fk_events_club_exists = 0,
  'ALTER TABLE events ADD CONSTRAINT fk_events_club_catalog FOREIGN KEY (clubCatalogID) REFERENCES club_catalog(clubCatalogID) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk_merits_event_exists := (
  SELECT COUNT(*)
  FROM information_schema.table_constraints
  WHERE table_schema = DATABASE()
    AND table_name = 'merits'
    AND constraint_name = 'fk_merits_event'
    AND constraint_type = 'FOREIGN KEY'
);
SET @sql := IF(@fk_merits_event_exists = 0,
  'ALTER TABLE merits ADD CONSTRAINT fk_merits_event FOREIGN KEY (eventID) REFERENCES events(eventID) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk_achievements_event_exists := (
  SELECT COUNT(*)
  FROM information_schema.table_constraints
  WHERE table_schema = DATABASE()
    AND table_name = 'achievements'
    AND constraint_name = 'fk_achievements_event'
    AND constraint_type = 'FOREIGN KEY'
);
SET @sql := IF(@fk_achievements_event_exists = 0,
  'ALTER TABLE achievements ADD CONSTRAINT fk_achievements_event FOREIGN KEY (eventID) REFERENCES events(eventID) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
