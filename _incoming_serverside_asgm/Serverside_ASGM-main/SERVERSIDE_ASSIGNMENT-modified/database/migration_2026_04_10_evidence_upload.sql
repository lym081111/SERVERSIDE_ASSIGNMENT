-- Adds optional proof-document columns for student submissions.
-- Safe to run on existing cocu_db databases.

START TRANSACTION;

ALTER TABLE merits
  ADD COLUMN IF NOT EXISTS evidence_path VARCHAR(255) NULL AFTER review_note;

ALTER TABLE events
  ADD COLUMN IF NOT EXISTS evidence_path VARCHAR(255) NULL AFTER review_note;

ALTER TABLE clubs
  ADD COLUMN IF NOT EXISTS evidence_path VARCHAR(255) NULL AFTER review_note;

ALTER TABLE achievements
  ADD COLUMN IF NOT EXISTS evidence_path VARCHAR(255) NULL AFTER review_note;

COMMIT;
