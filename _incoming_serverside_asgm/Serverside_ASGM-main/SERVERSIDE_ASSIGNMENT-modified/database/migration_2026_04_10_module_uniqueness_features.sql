-- Adds module-specific fields:
-- 1) events: eventType + reflection (reflection log)
-- 2) achievements: achievementLevel (level-based highlights)
--
-- Run once on existing cocu_db databases.

START TRANSACTION;

ALTER TABLE events
  ADD COLUMN IF NOT EXISTS eventType VARCHAR(50) NULL AFTER eventTitle,
  ADD COLUMN IF NOT EXISTS reflection TEXT NULL AFTER description;

UPDATE events
SET eventType = 'Leadership'
WHERE eventType IS NULL OR eventType = '';

ALTER TABLE achievements
  ADD COLUMN IF NOT EXISTS achievementLevel VARCHAR(30) NULL DEFAULT 'Faculty' AFTER category;

UPDATE achievements
SET achievementLevel = 'Faculty'
WHERE achievementLevel IS NULL OR achievementLevel = '';

COMMIT;
