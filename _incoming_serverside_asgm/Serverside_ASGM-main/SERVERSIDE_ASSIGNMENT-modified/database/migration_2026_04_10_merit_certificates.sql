-- Adds merit certificate automation table.
-- Run once on existing cocu_db databases.

START TRANSACTION;

CREATE TABLE IF NOT EXISTS merit_certificates (
  certificateID INT(11) NOT NULL AUTO_INCREMENT,
  userID INT(11) NOT NULL,
  milestone_hours INT(11) NOT NULL,
  approved_hours_snapshot INT(11) NOT NULL,
  certificate_code VARCHAR(40) NOT NULL,
  issued_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  source_meritID INT(11) NULL,
  issued_by INT(11) NULL,
  PRIMARY KEY (certificateID),
  UNIQUE KEY certificate_code (certificate_code),
  UNIQUE KEY userID_milestone_hours (userID, milestone_hours),
  KEY source_meritID (source_meritID),
  KEY issued_by (issued_by),
  CONSTRAINT merit_certificates_ibfk_1 FOREIGN KEY (userID) REFERENCES users(userID) ON DELETE CASCADE,
  CONSTRAINT merit_certificates_ibfk_2 FOREIGN KEY (source_meritID) REFERENCES merits(meritID) ON DELETE SET NULL,
  CONSTRAINT merit_certificates_ibfk_3 FOREIGN KEY (issued_by) REFERENCES users(userID) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;
