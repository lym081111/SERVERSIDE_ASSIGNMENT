-- Migration: club workflow refactor
-- 1) clubs.request_type to distinguish join vs role-change requests
-- 2) club_catalog table so only admin-defined clubs can be selected by students
--
-- Run once on existing cocu_db databases.

START TRANSACTION;

ALTER TABLE clubs
  ADD COLUMN IF NOT EXISTS request_type ENUM('join','role_change') NOT NULL DEFAULT 'join' AFTER roleDescription;

UPDATE clubs
SET request_type = 'join'
WHERE request_type IS NULL OR request_type = '';

CREATE TABLE IF NOT EXISTS club_catalog (
  clubCatalogID INT(11) NOT NULL AUTO_INCREMENT,
  clubName VARCHAR(150) NOT NULL,
  description TEXT DEFAULT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_by INT(11) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (clubCatalogID),
  UNIQUE KEY uk_club_catalog_name (clubName),
  KEY idx_club_catalog_active (is_active),
  KEY idx_club_catalog_created_by (created_by),
  CONSTRAINT fk_club_catalog_created_by
    FOREIGN KEY (created_by) REFERENCES users(userID) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO club_catalog (clubName, description, is_active)
SELECT DISTINCT c.clubName, NULL, 1
FROM clubs c
WHERE c.clubName IS NOT NULL AND TRIM(c.clubName) <> ''
ON DUPLICATE KEY UPDATE clubName = VALUES(clubName);

COMMIT;
