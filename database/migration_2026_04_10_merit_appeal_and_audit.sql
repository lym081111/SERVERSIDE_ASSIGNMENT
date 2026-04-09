-- Merit appeal + audit trail upgrade
-- Safe to run on existing cocu_db databases.

START TRANSACTION;

ALTER TABLE merits
  ADD COLUMN IF NOT EXISTS appeal_note TEXT NULL AFTER evidence_path;

ALTER TABLE merits
  ADD COLUMN IF NOT EXISTS appealed_at DATETIME NULL AFTER appeal_note;

ALTER TABLE merits
  ADD COLUMN IF NOT EXISTS resubmission_count INT NOT NULL DEFAULT 0 AFTER appealed_at;

ALTER TABLE merits
  ADD COLUMN IF NOT EXISTS last_resubmitted_at DATETIME NULL AFTER resubmission_count;

CREATE TABLE IF NOT EXISTS merit_status_logs (
  logID INT NOT NULL AUTO_INCREMENT,
  meritID INT NOT NULL,
  from_status ENUM('pending','approved','rejected') NULL,
  to_status ENUM('pending','approved','rejected') NOT NULL,
  changed_by INT NULL,
  change_note TEXT NULL,
  change_source VARCHAR(40) NOT NULL DEFAULT 'system',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (logID),
  KEY idx_merit_status_logs_merit (meritID),
  KEY idx_merit_status_logs_changed_by (changed_by),
  KEY idx_merit_status_logs_created_at (created_at),
  CONSTRAINT fk_merit_status_logs_merit FOREIGN KEY (meritID) REFERENCES merits (meritID) ON DELETE CASCADE,
  CONSTRAINT fk_merit_status_logs_user FOREIGN KEY (changed_by) REFERENCES users (userID) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO merit_status_logs (meritID, from_status, to_status, changed_by, change_note, change_source, created_at)
SELECT
  m.meritID,
  NULL,
  m.status,
  COALESCE(m.reviewed_by, m.userID),
  m.review_note,
  CASE
    WHEN m.status = 'pending' THEN 'student_submission'
    ELSE 'migration_backfill'
  END,
  COALESCE(m.reviewed_at, m.submitted_at, CURRENT_TIMESTAMP)
FROM merits m
WHERE NOT EXISTS (
  SELECT 1
  FROM merit_status_logs l
  WHERE l.meritID = m.meritID
);

COMMIT;

